<?php

namespace Buqiu\Cas\Responses;

use Buqiu\Cas\Contracts\Responses\ProxyFailureResponse;

/**
 * Class XmlProxyFailureResponse
 * @package Buqiu\Cas\Responses
 */
class XmlProxyFailureResponse extends BaseXmlResponse implements ProxyFailureResponse
{

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:49 下午
     * @param  string  $code
     * @param  string  $description
     * @return $this|XmlProxyFailureResponse
     */
    public function setFailure(string $code, string $description)
    {
        $this->removeByXPath($this->node, 'cas:proxyFailure');
        $authNode = $this->node->addChild('cas:proxyFailure', $description);
        $authNode->addAttribute('code', $code);;

        return $this;
    }
}
