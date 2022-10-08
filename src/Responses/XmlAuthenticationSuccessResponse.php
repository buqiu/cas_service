<?php

namespace Buqiu\Cas\Responses;

use Buqiu\Cas\Contracts\Responses\AuthenticationSuccessResponse;

/**
 * Class XmlAuthenticationSuccessResponse
 * @package Buqiu\Cas\Responses
 */
class XmlAuthenticationSuccessResponse extends BaseXmlResponse implements AuthenticationSuccessResponse
{
    /**
     * XmlAuthenticationSuccessResponse constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->node->addChild('cas:authenticationSuccess');
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:33 下午
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $authNode = $this->getAuthNode();
        $this->removeByXPath($authNode, 'cas.user');
        $authNode->addChild('cas:user', $user);

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:33 下午
     * @param $proxies
     * @return $this
     */
    public function setProxies($proxies)
    {
        $authNode = $this->getAuthNode();
        $this->removeByXPath($authNode, 'cas:proxies');;
        $proxiesNode = $authNode->addChild('cas:proxies');
        foreach ($proxiesNode as $proxy) {
            $proxiesNode->addChild('cas:proxy', $proxy);
        }

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:33 下午
     * @param $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $authNode = $this->getAuthNode();
        $this->removeByXPath($authNode, 'cas:attributes');
        $attributesNode = $authNode->addChild('cas:attributes');
        foreach ($attributes as $key => $value) {
            $valueArr = (array) $value;
            foreach ($valueArr as $v) {
                $str = $this->stringify($v);
                if (is_string($str)) {
                    $attributesNode->addChild('cas:'.$key, $str);
                }
            }
        }

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:33 下午
     * @param $ticket
     * @return $this
     */
    public function setProxyGrantingTicket($ticket)
    {
        $authNode = $this->getAuthNode();
        $this->removeByXPath($authNode, 'cas:proxyGrantingTicket');
        $authNode->addChild('cas:proxyGrantingTicket', $ticket);

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 9:48 上午
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $authNode = $this->getAuthNode();
        $this->removeByXPath($authNode, 'cas:token');
        $authNode->addChild('cas:token', $token);

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 11:06 上午
     * @param $status
     * @return $this
     */
    public function setAuthentication($status = false)
    {
        $authNode = $this->getAuthNode();
        $this->removeByXPath($authNode, 'cas:authentication');
        $authNode->addChild('cas:authentication', $status);

        return $this;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/11
     * Time : 2:27 下午
     * @return \SimpleXMLElement
     */
    protected function getAuthNode()
    {
        $authNode = $this->node->xpath('cas:authenticationSuccess');
        if (count($authNode) < 1) {
            return $this->node->addChild('cas:authenticationSuccess');
        }

        return $authNode[0];
    }

}
