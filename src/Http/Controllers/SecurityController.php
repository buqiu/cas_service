<?php

namespace Buqiu\Cas\Http\Controllers;

use Buqiu\Cas\Contracts\Interactions\UserLogin;
use Buqiu\Cas\Contracts\Models\UserModel;
use Buqiu\Cas\Events\CasUserLoginEvent;
use Buqiu\Cas\Events\CasUserLogoutEvent;
use Buqiu\Cas\Exceptions\Cas\CasException;
use Buqiu\Cas\Repositories\ClientTicketRepository;
use Buqiu\Cas\Repositories\ProxyGrantingTicketRepository;
use Buqiu\Cas\Repositories\ServiceRepository;
use Buqiu\Cas\Repositories\TicketRepository;
use Buqiu\Cas\Request\UserInfoValidation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;
use function Buqiu\Cas\cas_route;

class SecurityController extends Controller
{
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    /**
     * @var TicketRepository
     */
    protected $ticketRepository;

    /**
     * @var ProxyGrantingTicketRepository
     */
    protected $proxyGrantingTicketRepository;

    /**
     * @var UserLogin
     */
    protected $loginInteraction;

    /**
     * @var ClientTicketRepository
     */
    protected $clientTicketRepository;

    /**
     * SecurityController constructor.
     * @param  ServiceRepository  $serviceRepository
     * @param  TicketRepository  $ticketRepository
     * @param  ProxyGrantingTicketRepository  $proxyGrantingTicketRepository
     * @param  UserLogin  $loginInteraction
     * @param  ClientTicketRepository  $clientTicketRepository
     */
    public function __construct(ServiceRepository $serviceRepository, TicketRepository $ticketRepository, ProxyGrantingTicketRepository $proxyGrantingTicketRepository, UserLogin $loginInteraction, ClientTicketRepository $clientTicketRepository)
    {
        $this->serviceRepository = $serviceRepository;
        $this->ticketRepository = $ticketRepository;
        $this->proxyGrantingTicketRepository = $proxyGrantingTicketRepository;
        $this->loginInteraction = $loginInteraction;
        $this->clientTicketRepository = $clientTicketRepository;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 10:19 上午
     * @param  Request  $request
     * @return mixed|Response
     */
    public function showLogin(Request $request)
    {
        $service = $request->get('service', '');

        $errors = [];

        if (strpos($service, '%3A')) {
            $service = urldecode($service);
        }
        if ( ! empty($service)) {
            if ( ! $this->serviceRepository->isUrlValue($service)) {
                $errors[] = (new CasException(CasException::INVALID_SERVICE))->getMessage();
            }
        }

        $user = $this->loginInteraction->getCurrentUser($request);

        // 如果用户已拥有 sso 会话
        // user already has sso session

        if ($user) {

            // 有错误，不应重定向到目标url
            // has errors, should not be redirected to target url
            if ( ! empty($errors)) {

                return $this->loginInteraction->redirectToHome($errors);
            }
            // 不可以是 transparent
            // must not be transparent
            if ($request->get('true') === 'true' && ! empty($service)) {
                $query = $request->query->all();
                unset($query['true']);
                $url = cas_route('login_page', $query);
                return $this->loginInteraction->showLoginWarnPage($request, $url, $service);
            }
            return $this->authenticated($request, $user);
        }
        return $this->loginInteraction->showLoginPage($request, $errors);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 10:40 上午
     * @param  Request  $request
     * @return Application|RedirectResponse|Redirector|mixed|Response
     */
    public function login(Request $request)
    {
        $userInfo = $request->post();
        $user = $this->loginInteraction->login($request);
        if (is_null($user)) {
            return $this->loginInteraction->showLoginPage($request);
        }
        //验证用户信息
        if(!(new UserInfoValidation())->validationUser($userInfo,$user)){
            return redirect('cas/login')->withErrors(['信息有误重新输入']);
        }
        return $this->authenticated($request, $user);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 10:40 上午
     * @param  Request  $request
     * @param  UserModel  $user
     * @return Application|RedirectResponse|Redirector|mixed|Response
     */
    public function authenticated(Request $request, UserModel $user)
    {
        event(new CasUserLoginEvent($request, $user));
        //处理客户端URL
        $serviceUrl = $request->get('service', '');
        if (!empty($serviceUrl)) {
            if (strpos($serviceUrl, '%3A')) {
                $serviceUrl = urldecode($serviceUrl);
            }

            $serviceUrl = urldecode($serviceUrl);
            $query = parse_url($serviceUrl,PHP_URL_QUERY);
            try {
                //生成用户登录票据
                $ticket = $this->ticketRepository->applyTicket($user, $serviceUrl, '');
            } catch (CasException$exception) {
                //检查票据存在跳转主页
                return $this->loginInteraction->redirectToHome([$exception->getMessage()]);
            }
            //客户端域名携带ticket跳转
            $finalUrl = $serviceUrl.($query ? '&' : '?').'ticket='.$ticket->ticket;
            return redirect($finalUrl);
        }
        //登陆成功跳转主页
        return $this->loginInteraction->redirectToHome();
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 10:47 上午
     * @param  Request  $request
     * @return Application|RedirectResponse|Redirector|mixed|Response
     */
    public function logout(Request $request)
    {
        $user = $this->loginInteraction->getCurrentUser($request);
        if ($user) {
            $this->loginInteraction->logout($request);
            //DB 清空 票据
            $this->proxyGrantingTicketRepository->invalidTicketByUser($user);

            $this->clientTicketRepository->invalidToken($user->getEloquentModel()->getKey());
            event(new CasUserLogoutEvent($request, $user));
            return redirect("cas/login")->withErrors(['登录已成功退出']);
        }
        $service = $request->get('service');
        if ($service && $this->serviceRepository->isUrlValue($service)) {
            return redirect($service);
        }
        return $this->loginInteraction->showLoggedOut($request);
    }
}
