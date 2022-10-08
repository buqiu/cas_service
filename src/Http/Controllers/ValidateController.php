<?php

namespace Buqiu\Cas\Http\Controllers;

use Buqiu\Cas\Contracts\TicketLocker;
use Buqiu\Cas\Exceptions\Cas\CasException;
use Buqiu\Cas\Models\Ticket;
use Buqiu\Cas\Repositories\ClientTicketRepository;
use Buqiu\Cas\Repositories\ProxyGrantingTicketRepository;
use Buqiu\Cas\Repositories\TicketRepository;
use Buqiu\Cas\Responses\JsonAuthenticationFailureResponse;
use Buqiu\Cas\Responses\JsonAuthenticationSuccessResponse;
use Buqiu\Cas\Responses\JsonProxyFailureResponse;
use Buqiu\Cas\Responses\JsonProxySuccessResponse;
use Buqiu\Cas\Responses\XmlAuthenticationFailureResponse;
use Buqiu\Cas\Responses\XmlAuthenticationSuccessResponse;
use Buqiu\Cas\Responses\XmlProxyFailureResponse;
use Buqiu\Cas\Responses\XmlProxySuccessResponse;
use Buqiu\Cas\Services\ProxyGrantingTicketCaller;
use Buqiu\Cas\Services\TicketGenerator;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ValidateController extends Controller
{
    /**
     * @var TicketLocker
     */
    protected $ticketLocker;

    /**
     * @var TicketRepository
     */
    protected $ticketRepository;

    /**
     * @var ProxyGrantingTicketRepository
     */
    protected $proxyGrantingTicketRepository;

    /**
     * @var TicketGenerator
     */
    protected $ticketGenerator;

    /**
     * @var ProxyGrantingTicketCaller
     */
    protected $proxyGrantingTicketCaller;

    /**
     * @var ClientTicketRepository
     */
    protected $clientTicketRepository;

    /**
     * ValidateController constructor.
     * @param  TicketLocker  $ticketLocker
     * @param  TicketRepository  $ticketRepository
     * @param  ProxyGrantingTicketRepository  $proxyGrantingTicketRepository
     * @param  TicketGenerator  $ticketGenerator
     * @param  ProxyGrantingTicketCaller  $proxyGrantingTicketCaller
     * @param  ClientTicketRepository  $clientTicketRepository
     */
    public function __construct(
        TicketLocker $ticketLocker,
        TicketRepository $ticketRepository,
        ProxyGrantingTicketRepository $proxyGrantingTicketRepository,
        TicketGenerator $ticketGenerator,
        ProxyGrantingTicketCaller $proxyGrantingTicketCaller,
        ClientTicketRepository $clientTicketRepository
    ) {
        $this->ticketLocker = $ticketLocker;
        $this->ticketRepository = $ticketRepository;
        $this->proxyGrantingTicketRepository = $proxyGrantingTicketRepository;
        $this->ticketGenerator = $ticketGenerator;
        $this->proxyGrantingTicketCaller = $proxyGrantingTicketCaller;
        $this->clientTicketRepository = $clientTicketRepository;
    }

    public function v1ValidateAction(Request $request)
    {
        $service = $request->get('service', '');
        $ticket = $request->get('ticket', '');

        if (empty($service) && empty($ticket)) {
            return new Response('no');
        }
        if ( ! $this->lockTicket($ticket)) {
            return new  Response('no');
        }
        $record = $this->ticketRepository->getByTicket($ticket);
        if ( ! $record || $record->service_url != $service) {
            $this->unlockTicket($ticket);

            return new Response('no');
        }
        $this->ticketRepository->invalidTicket($record);
        $this->unlockTicket($this);

        return new Response('yes');
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:34 下午
     * @param  Request  $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws GuzzleException
     */
    public function v2ServiceValidateAction(Request $request)
    {
        return $this->casValidate($request, true, false);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 11:46 上午
     * @param  Request  $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function checkAuth(Request $request)
    {
        $sessionId = $request->get('session_id', '');
        $token = $request->get('token', '');
        $format = $request->get('format', 'XML');
        if (strtoupper($format) === 'JSON') {
            $resp = app(JsonAuthenticationSuccessResponse::class);
        } else {
            $resp = app(XmlAuthenticationSuccessResponse::class);
        }
        if ($record = $this->clientTicketRepository->getByToken($token, $sessionId)) {
            if ($record->user) {
                $resp->setUser($record->user->getName());
                $resp->setAttributes($record->user->getCASAttributes());
                $resp->setAuthentication(true);
            } else {
                $resp->setAuthentication(false);
            }
        } else {
            $resp->setAuthentication(false);
        }

        return $resp->toResponse();
    }


    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:35 下午
     * @param  Request  $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws GuzzleException
     */
    public function v3ServiceValidateAction(Request $request)
    {
        return $this->casValidate($request, true, false);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:36 下午
     * @param  Request  $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws GuzzleException
     */
    public function v2ProxyValidateAction(Request $request)
    {
        return $this->casValidate($request, false, true);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:36 下午
     * @param  Request  $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws GuzzleException
     */
    public function v3ProxyValidateAction(Request $request)
    {
        return $this->casValidate($request, true, true);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/12
     * Time : 4:52 下午
     * @param  Request  $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function proxyAction(Request $request)
    {
        $pgt = $request->get('pgt', '');
        $target = $request->get('targetService', '');
        $format = strtoupper($request->get('format', 'XML'));
        if (empty($pgt) || empty($target)) {
            return $this->proxyFailureResponse(CasException::INVALID_REQUEST, 'param pgt and targetService can not be empty', $format);
        }
        $record = $this->proxyGrantingTicketRepository->getByTicket($pgt);
        try {
            if ( ! $record) {
                throw new CasException(CasException::INVALID_TICKET, 'ticket is not valid');
            }
            $proxies = $record->proxies;
            array_unshift($proxies, $record->pgt_url);
            $ticket = $this->ticketRepository->applyTicket($record->user, $target, $proxies);
        } catch (CasException$exception) {
            return $this->proxyFailureResponse($exception->getCasErrorCode(), $exception->getMessage(), $format);
        }

        return $this->proxySuccessResponse($ticket->ticket, $format);
    }


    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:34 下午
     * @param  Request  $request
     * @param $returnAttr
     * @param $allowProxy
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws GuzzleException
     */
    protected function casValidate(Request $request, $returnAttr, $allowProxy)
    {
        $service = $request->get('service', '');
        if (strpos($service, '%3A')) {
            $service = urldecode($service);
        }


        $ticket = $request->get('ticket', '');
        $format = strtoupper($request->get('format', 'XML'));
        $sessionId = $request->get('session_id', '');


        if (empty($service) || empty($ticket)) {
            return $this->authFailureResponse(CasException::INVALID_REQUEST, 'param service and ticket can not be empty', $format);
        }

        if ( ! $this->lockTicket($ticket)) {
            return $this->authFailureResponse(CasException::INTERNAL_ERROR, 'try to lock ticket failed', $format);
        }

        $record = $this->ticketRepository->getByTicket($ticket);

        try {
            if ( ! $record || ( ! $allowProxy && $record->isProxy())) {
                throw new CasException(CasException::INVALID_TICKET, 'ticket is not valid');
            }
            if ($record->service_url != $service) {
                throw new CasException(CasException::INVALID_SERVICE, 'service is not valid');
            }
        } catch (CasException $exception) {
            // 如果发生错误，则无效票证 invalid ticket if error occur
            $record instanceof Ticket && $this->ticketRepository->invalidTicket($record);
            $this->unlockTicket($ticket);

            return $this->authFailureResponse($exception->getCasErrorCode(), $exception->getMessage(), $format);
        }
        $proxies = [];
        if ($record->isProxy()) {
            $proxies = $record->proxies;
        }
        $user = $record->user;
        $this->ticketRepository->invalidTicket($record);
        $this->unlockTicket($ticket);
        $iou = null;
        $pgtUrl = $request->get('pgtUrl', '');
        if ($pgtUrl) {
            try {
                $pgTicket = $this->proxyGrantingTicketRepository->applyTicket($user, $pgtUrl, $proxies);
                $iou = $this->ticketGenerator->generateOne(config('cas.pg_ticket_iou_len', 64), 'PGTIOU-');
                if ( ! $this->proxyGrantingTicketCaller->call($pgtUrl, $pgTicket->ticket, $iou)) {
                    $iou = null;
                }
            } catch (CasException $exception) {
                $iou = null;
            }
        }
        $attr = $returnAttr ? $record->user->getCasAttributes() : [];
        $token = null;
        if ($sessionId) {
            try {
                $token = $this->clientTicketRepository->applyToken($user->id, $sessionId) ?? null;
            } catch (CasException $exception) {
                $token = null;
            }
        }

        return $this->authSuccessResponse($record->user->getName(), $format, $attr, $proxies, $iou, $token);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 9:46 上午
     * @param $userName
     * @param $format
     * @param $attributes
     * @param  array  $proxies
     * @param  null  $pgt
     * @param  null  $token
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function authSuccessResponse($userName, $format, $attributes, $proxies = [], $pgt = null, $token = null)
    {
        if (strtoupper($format) === 'JSON') {
            $resp = app(JsonAuthenticationSuccessResponse::class);
        } else {
            $resp = app(XmlAuthenticationSuccessResponse::class);
        }
        $resp->setUser($userName);
        if ( ! empty($attributes)) {
            $resp->setAttributes($attributes);
        }
        if ( ! empty($proxies)) {
            $resp->setProxies($proxies);
        }
        if ($token) {
            $resp->setToken($token);
        }
        if (is_string($pgt)) {
            $resp->setProxyGrantingTicket($pgt);
        }

        return $resp->toResponse();
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 4:54 下午
     * @param $code
     * @param $description
     * @param $format
     * @return Response
     */
    protected function authFailureResponse($code, $description, $format)
    {
        if (strtoupper($format) === 'JSON') {
            $resp = app(JsonAuthenticationFailureResponse::Class);
        } else {
            $resp = app(XmlAuthenticationFailureResponse::class);
        }
        $resp->setFailure($code, $description);

        return $resp->toResponse();
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 3:31 下午
     * @param $code
     * @param $description
     * @param $format
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function proxyFailureResponse($code, $description, $format)
    {
        if (strtoupper($format) === 'JSON') {
            $resp = app(JsonProxyFailureResponse::class);
        } else {
            $resp = app(XmlProxyFailureResponse::class);
        }
        $resp->setFailure($code, $description);

        return $resp->toResponse();
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 3:31 下午
     * @param $ticket
     * @param $format
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function proxySuccessResponse($ticket, $format)
    {
        if (strtoupper($format) === 'JSON') {
            $resp = app(JsonProxySuccessResponse::class);
        } else {
            $resp = app(XmlProxySuccessResponse::class);
        }
        $resp->setProxyTicket($ticket);

        return $resp->toResponse();
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 2:13 下午
     * @param $ticket
     * @return bool
     */
    protected function lockTicket($ticket)
    {
        return $this->ticketLocker->acquireLock($ticket, config('cas.lock_timeout'));
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 2:13 下午
     * @param $ticket
     * @return bool
     */
    protected function unlockTicket($ticket)
    {
        return $this->ticketLocker->releaseLock($ticket);
    }
}
