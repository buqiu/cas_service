<?php

namespace Buqiu\Cas\Services;

use Illuminate\Support\Str;

class TicketGenerator
{
    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:26 下午
     * @param $totalLength
     * @param $prefix
     * @param  callable  $checkFunc
     * @param $maxRetry
     * @return false|string
     */
    public function generate($totalLength, $prefix, callable $checkFunc, $maxRetry)
    {
        $ticket = false;
        $flag = false;
        for ($i = 0; $i < $maxRetry; $i++) {
            $ticket = $this->generateOne($totalLength, $prefix);
            if (call_user_func_array($checkFunc, [$ticket])) {
                $flag = true;
                break;
            }
        }
        if ( ! $flag) {
            return false;
        }

        return $ticket;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:25 下午
     * @param $totalLength
     * @param $prefix
     * @return string
     */
    public function generateOne($totalLength, $prefix)
    {
        return $prefix.Str::random($totalLength - strlen($prefix));
    }
}
