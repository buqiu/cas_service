<?php

namespace Buqiu\Cas\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Class ProxyGrantingTicketCaller
 * @package Buqiu\Cas\Services
 */
class ProxyGrantingTicketCaller
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * ProxyGrantingTicketCaller constructor.
     * @param  Client  $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 9:45 上午
     * @param $proxyGrantingTicketUrl
     * @param $proxyGrantingTicket
     * @param $proxyGrantingTicketIou
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function call($proxyGrantingTicketUrl, $proxyGrantingTicket, $proxyGrantingTicketIou)
    {
        $query = [
            'pgtId' => $proxyGrantingTicket,
            'pgtIou' => $proxyGrantingTicketIou,
        ];
        parse_str(parse_url($proxyGrantingTicketUrl, PHP_URL_QUERY), $originQuery);

        try {
            $options = [
                'query' => array_merge($originQuery, $query),
                'verify' => config('cas.verify_ssl', true),
            ];
            $res = $this->client->get($proxyGrantingTicketUrl, $options);

            return $res->getStatusCode() == 200;
        } catch (\Exception$exception) {
            Log::warning('all pgt url failed,mes:'.$exception->getMessage());

            return false;
        }
    }
}
