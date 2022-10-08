<?php

namespace Buqiu\Cas\Responses;

use Buqiu\Cas\Contracts\Responses\ProxyFailureResponse;

class JsonProxyFailureResponse extends BaseJsonResponse implements ProxyFailureResponse
{

    public function __construct()
    {
        $this->data = ['serviceResponse' => ['proxyFailure' => []]];
    }

    public function setFailure(string $code, string $description)
    {
        $this->data['serviceResponse']['proxyFailure']['code'] = $code;
        $this->data['serviceResponse']['proxyFailure']['description'] = $description;

        return $this;
    }
}
