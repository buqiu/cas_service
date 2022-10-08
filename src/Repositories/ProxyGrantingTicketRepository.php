<?php

namespace Buqiu\Cas\Repositories;

use Buqiu\Cas\Contracts\Models\UserModel;
use Buqiu\Cas\Exceptions\Cas\CasException;
use Buqiu\Cas\Models\ProxyGrantingTicket;
use Buqiu\Cas\Services\TicketGenerator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProxyGrantingTicketRepository
{
    protected $proxyGrantingTicket;

    protected $serviceRepository;

    protected $ticketGenerator;

    public function __construct(ProxyGrantingTicket $proxyGrantingTicket, ServiceRepository $serviceRepository, TicketGenerator $ticketGenerator)
    {
        $this->proxyGrantingTicket = $proxyGrantingTicket;
        $this->serviceRepository = $serviceRepository;
        $this->ticketGenerator = $ticketGenerator;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:34 下午
     * @param $ticket
     * @param  bool  $checkExpired
     * @return Builder|Model|object|null
     */
    public function getByTicket($ticket, $checkExpired = true)
    {
        $record = $this->proxyGrantingTicket->newQuery()->where('ticket', $ticket)->first();
        if ( ! $record) {
            return null;
        }

        return ($checkExpired && $record->isExpired()) ? null : $record;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:37 下午
     * @param  UserModel  $user
     */
    public function invalidTicketByUser(UserModel $user)
    {
        $this->proxyGrantingTicket->newQuery()->where('user_id', $user->getEloquentModel()->getKey())->delete();
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 6:01 下午
     * @param  UserModel  $user
     * @param $proxyGrantingUrl
     * @param  array  $proxies
     * @return ProxyGrantingTicket
     * @throws CasException
     */
    public function applyTicket(UserModel $user, $proxyGrantingUrl, $proxies = [])
    {
        $service = $this->serviceRepository->getServiceByUrl($proxyGrantingUrl);
        if ( ! $service || ! $service->allow_proxy) {
            throw  new CasException(CasException::UNAUTHORIZED_SERVICE_PROXY);
        }
        $ticket = $this->getAvailableTicket(config('cas.pg_ticket_len', 64));
        if ($ticket) {
            throw  new CasException(CasException::INTERNAL_ERROR, 'apply proxy-granting ticket failed');
        }
        $record = $this->proxyGrantingTicket->newInstance([
            'ticket' => $ticket,
            'expire_at' => new Carbon(sprintf('+%dsec', config('cas.pg_ticket_expire', 7200))),
            'created_at' => new Carbon(),
            'pgt_url' => $proxyGrantingUrl,
            'proxies' => $proxies,
        ]);
        $record->user()->associate($user->getEloquentModel());
        $record->service()->associate($service);
        $record->save();
        return $record;

    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:45 下午
     * @param $totalLength
     * @return false|string
     */
    private function getAvailableTicket($totalLength)
    {
        return $this->ticketGenerator->generate($totalLength, 'PGT-', function ($ticket) {
            return is_null($this->getByTicket($ticket, false));
        }, 10);
    }
}
