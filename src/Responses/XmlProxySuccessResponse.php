<?php

namespace Buqiu\Cas\Responses;

use Buqiu\Cas\Contracts\Responses\ProxySuccessResponse;

/**
 * Class XmlProxySuccessResponse
 * @package Buqiu\Cas\Responses
 */
class XmlProxySuccessResponse extends BaseXmlResponse implements ProxySuccessResponse
{

    /**
     * XmlProxySuccessResponse constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->node->addChild('cas:proxySuccess');
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:47 下午
     * @param $ticket
     * @return XmlProxySuccessResponse|void
     */
    public function setProxyTicket($ticket)
    {
        $proxyNode = $this->getProxyNode();
        $this->removeByXPath($proxyNode, 'cas:proxyTicket');
        $proxyNode->addChild('cas:proxyTicket', $ticket);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:47 下午
     * @return \SimpleXMLElement
     */
    public function getProxyNode()
    {
        $authNodes = $this->node->addChild('cas:proxySuccess');
        if (count($authNodes) < 1) {
            return $this->node->addChild('cas:proxySuccess');
        }

        return $authNodes[0];
    }
}
