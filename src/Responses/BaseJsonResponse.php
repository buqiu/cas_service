<?php

namespace Buqiu\Cas\Responses;

use Symfony\Component\HttpFoundation\Response;

class BaseJsonResponse
{
    /**
     * @var array
     */
    protected $data;

    public function toResponse()
    {
        return new Response(json_encode($this->data), 200, ['Content-Type' => 'application/json']);
    }
}
