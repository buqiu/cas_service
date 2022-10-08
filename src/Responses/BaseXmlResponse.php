<?php

namespace Buqiu\Cas\Responses;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use SimpleXMLElement;

class BaseXmlResponse
{
    /**
     * @var SimpleXMLElement
     */
    protected $node;

    /**
     * BaseXmlResponse constructor.
     */
    public function __construct()
    {
        $this->node = $this->getRootNode();
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 4:50 下午
     * @return SimpleXMLElement
     */
    protected function getRootNode()
    {
        return simplexml_load_string("<cas:serviceResponse xmlns:cas='https://www.yale.edu/tp/cas'/>");
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 4:50 下午
     * @param  SimpleXMLElement  $xml
     * @param $xpath
     */
    protected function removeByXPath(SimpleXMLElement $xml, $xpath)
    {
        $nodes = $xml->xpath($xpath);
        foreach ($nodes as $node) {
            $dom = dom_import_simplexml($node);
            $dom->parentNode->removeChild($dom);
        }
    }

    /**
     * Notes: 删除xml字符串的第一行 remove the first line of xml string
     * User : smallK
     * Date : 2022/1/10
     * Time : 4:50 下午
     * @param $str
     * @return string
     */
    protected function removeXmlFirstLine($str)
    {
        $first = '<?xml version="1.0"?>';
        if (Str::startsWith($str, $first)) {
            return trim(substr($str, strlen($first)));
        }

        return $str;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 4:50 下午
     * @param $value
     * @return false|string
     */
    public function stringify($value)
    {
        if (is_string($value)) {
            $str = $value;
        } else {
            if (is_object($value) && method_exists($value, '__toString')) {
                $str = $value->__toString();
            } else {
                if ($value instanceof \Stringable) {
                    $str = serialize($value);
                } else {
                    $str = json_encode($value);
                }
            }
        }

        return $str;
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/10
     * Time : 4:49 下午
     * @return Response
     */
    public function toResponse()
    {
        $content = $this->removeXmlFirstLine($this->node->asXML());

        return new Response($content, 200, ['Content-Type' => 'application/xml']);
    }
}
