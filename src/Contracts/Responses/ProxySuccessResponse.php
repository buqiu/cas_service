<?php

namespace Buqiu\Cas\Contracts\Responses;

interface ProxySuccessResponse extends BaseResponse
{
    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:59 下午
     * @param $ticket
     * @return $this
     */
    public function setProxyTicket($ticket);
}
