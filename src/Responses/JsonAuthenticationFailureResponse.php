<?php

namespace Buqiu\Cas\Responses;

use Buqiu\Cas\Contracts\Responses\AuthenticationFailureResponse;

class JsonAuthenticationFailureResponse extends BaseJsonResponse implements AuthenticationFailureResponse
{
    public function __construct()
    {
        $this->data = ['serviceResponse' => ['authenticationFailure' => []]];
    }

    public function setFailure(string $code, string $description)
    {
        $this->data['serviceResponse']['authenticationFailure']['code'] = $code;
        $this->data['serviceResponse']['authenticationFailure']['description'] = $description;

        return $this;
    }
}
