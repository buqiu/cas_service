<?php

namespace Buqiu\Cas\Responses;

use Buqiu\Cas\Contracts\Responses\AuthenticationFailureResponse;

class XmlAuthenticationFailureResponse extends BaseXmlResponse implements AuthenticationFailureResponse
{

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 4:52 下午
     * @param  string  $code
     * @param  string  $description
     * @return $this|XmlAuthenticationFailureResponse
     */
    public function setFailure(string $code, string $description)
    {
        $this->removeByXPath($this->node, 'cas:authenticationFailure');
        $authNode = $this->node->addChild('cas:authenticationFailure', $description);
        $authNode->addAttribute('code', $code);

        return $this;
    }
}
