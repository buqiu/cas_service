<?php

namespace Buqiu\Cas\Responses;

use Buqiu\Cas\Contracts\Responses\ProxySuccessResponse;

/**
 * Class JsonProxySuccessResponse
 * @package Buqiu\Cas\Responses
 */
class JsonProxySuccessResponse extends BaseJsonResponse implements ProxySuccessResponse
{

    /**
     * JsonProxySuccessResponse constructor.
     */
    public function __construct()
    {
        $this->data = ['serviceResponse' => ['proxySuccess' => []]];
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:53 下午
     * @param $ticket
     * @return $this|JsonProxySuccessResponse
     */
    public function setProxyTicket($ticket)
    {
        $this->data['serviceResponse']['proxySuccess']['proxyTicket'] = $ticket;

        return $this;
    }


}
