<?php

namespace Buqiu\Cas\Contracts;

interface TicketLocker
{
    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 4:01 下午
     * @param $key
     * @param $timeout
     * @return bool
     */
    public function acquireLock($key, $timeout);

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 4:01 下午
     * @param $key
     * @return bool
     */
    public function releaseLock($key);
}
