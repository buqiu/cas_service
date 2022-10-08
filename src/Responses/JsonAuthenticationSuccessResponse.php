<?php

namespace Buqiu\Cas\Responses;

use Buqiu\Cas\Contracts\Responses\AuthenticationSuccessResponse;

/**
 * Class JsonAuthenticationSuccessResponse
 * @package Buqiu\Cas\Responses
 */
class JsonAuthenticationSuccessResponse extends BaseJsonResponse implements AuthenticationSuccessResponse
{
    /**
     * JsonAuthenticationSuccessResponse constructor.
     */
    public function __construct()
    {
        $this->data = ['serviceResponse' => ['authenticationSuccess' => []]];
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:23 下午
     * @param $user
     * @return $this|JsonAuthenticationSuccessResponse
     */
    public function setUser($user)
    {
        $this->data['serviceResponse']['authenticationSuccess']['user'] = $user;

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:23 下午
     * @param $proxies
     * @return $this|JsonAuthenticationSuccessResponse
     */
    public function setProxies($proxies)
    {
        $this->data['serviceResponse']['authenticationSuccess']['proxies'] = $proxies;

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:23 下午
     * @param $attributes
     * @return $this|JsonAuthenticationSuccessResponse
     */
    public function setAttributes($attributes)
    {
        $this->data['serviceResponse']['authenticationSuccess']['attributes'] = $attributes;

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:23 下午
     * @param $ticket
     * @return $this|JsonAuthenticationSuccessResponse
     */
    public function setProxyGrantingTicket($ticket)
    {
        $this->data['serviceResponse']['authenticationSuccess']['ticket'] = $ticket;

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 9:47 上午
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->data['serviceResponse']['authenticationSuccess']['token'] = $token;

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 11:05 上午
     * @param $status
     * @return $this
     */
    public function setAuthentication($status = false)
    {
        $this->data['serviceResponse']['authenticationSuccess']['authentication'] = $status;

        return $this;
    }
}
