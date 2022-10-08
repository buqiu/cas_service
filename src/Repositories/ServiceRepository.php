<?php
/**
 * Name:
 * User : smallK
 * Date : 2022/1/7
 * Time : 5:22 下午
 */

namespace Buqiu\Cas\Repositories;

use Buqiu\Cas\Models\Service;
use Buqiu\Cas\Models\ServiceHost;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;

class ServiceRepository
{
    /**
     * @var Service
     */
    protected $service;
    /**
     * @var ServiceHost
     */
    protected $serviceHost;

    /**
     * ServiceRepository constructor.
     * @param  Service  $service
     * @param  ServiceHost  $serviceHost
     */
    public function __construct(Service $service, ServiceHost $serviceHost)
    {
        $this->service = $service;
        $this->serviceHost = $serviceHost;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:41 下午
     * @param $url
     * @return HigherOrderBuilderProxy|mixed|null
     */
    public function getServiceByUrl($url)
    {

        $host = parse_url($url, PHP_URL_HOST);

        $record = $this->serviceHost->newQuery()->where('host', $host)->first();
        if ( ! $record) {
            return null;
        }

        return $record->service;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/7
     * Time : 5:42 下午
     * @param $url
     * @return bool
     */
    public function isUrlValue($url)
    {
        $service = $this->getServiceByUrl($url);

        return $service !== null && $service->enabled;
    }
}
