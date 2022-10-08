<?php

namespace Buqiu\Cas\Contracts\Responses;

interface AuthenticationSuccessResponse extends BaseResponse
{
    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:57 下午
     * @param $user
     * @return $this
     */
    public function setUser($user);

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:57 下午
     * @param $proxies
     * @return $this
     */
    public function setProxies($proxies);

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:57 下午
     * @param $attributes
     * @return $this
     */
    public function setAttributes($attributes);

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:57 下午
     * @param $ticket
     * @return $this
     */
    public function setProxyGrantingTicket($ticket);
}
