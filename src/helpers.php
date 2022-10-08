<?php

namespace Buqiu\Cas;

/**
 * Notes:
 * User : smallK
 * Date : 2022/1/10
 * Time : 9:49 上午
 * @param $name
 * @param  array  $parameters
 * @param  bool  $absolute
 * @return string
 */
function cas_route($name, $parameters = [], $absolute = true)
{
    $name = config('cas.router.name_prefix').$name;

    return route($name, $parameters, $absolute);
}
