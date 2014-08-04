<?php
/**
 * XML response class.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

use SimpleXMLElement;

class ResponseXml extends Response {

    /**
     * Convert associative array to XML string.
     *
     * @param array $array
     * @param string $root
     * @param SimpleXMLElement $xml
     * @return string
     */
    private function arrayToXml($array, $root = null, $xml = null) {
        $_xml = $xml === null ? new SimpleXMLElement($root !== null ? $root : '<root></root>') : $xml;

        foreach ($array as $key => $value) {
          if (is_array($value)) {
            $this->arrayToXml($value, $key, $_xml->addChild($key));
          } else {
            $_xml->addChild($key, $value);
          }
        }
        return $_xml->asXML();
    }


    /**
     * Render response as XML.
     *
     * @return string
     */
    public function render()
    {
        header('Content-Type: application/xml');
        $_data = is_object($this->data) ? (array) $this->data : $this->data;
        return $this->arrayToXml(array('status' => $this->status, 'response' => $_data));
    }

}