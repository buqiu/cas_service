<?php

namespace Buqiu\Cas\Exceptions\Cas;


use Exception;

class CasException extends Exception
{
    const INVALID_REQUEST = 'INVALID_REQUEST';
    const INVALID_TICKET = 'INVALID_TICKET';
    const INVALID_SERVICE = 'INVALID_SERVICE';
    const INTERNAL_ERROR = 'INTERNAL_ERROR';
    const UNAUTHORIZED_SERVICE_PROXY = 'UNAUTHORIZED_SERVICE_PROXY';
    const INVALID_SESSION_ID = 'INVALID_SESSION_ID';
    protected $casErrorCode;

    /**
     * CasException constructor.
     * @param $casErrorCode
     * @param  string  $message
     */
    public function __construct($casErrorCode, $message = "")
    {
        $this->casErrorCode = $casErrorCode;
        $this->message = $message;
    }


    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 4:14 下午
     * @return string
     */
    public function getCasErrorCode()
    {
        return $this->casErrorCode;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 4:14 下午
     * @return mixed
     */
    public function getCasMsg()
    {
        //todo translate error msg
        return $this->casErrorCode;
    }
}
