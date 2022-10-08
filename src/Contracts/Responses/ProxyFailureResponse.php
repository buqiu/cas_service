<?php

namespace Buqiu\Cas\Contracts\Responses;

interface ProxyFailureResponse extends BaseResponse
{
    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:58 下午
     * @param  string  $code
     * @param  string  $description
     * @return $this
     */
    public function setFailure(string $code, string $description);
}
