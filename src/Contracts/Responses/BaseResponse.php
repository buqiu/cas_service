<?php
/**
 * Name:
 * User : smallK
 * Date : 2022/1/7
 * Time : 3:54 下午
 */

namespace Buqiu\Cas\Contracts\Responses;

use Symfony\Component\HttpFoundation\Response;

interface BaseResponse
{
    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 3:54 下午
     * @return Response
     */
    public function toResponse();
}
