<?php
/**
 * JSON response class.
 * 
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

class ResponseJson extends Response {

    /**
     * Render response as JSON.
     * 
     * @return string
     */
    public function render()
    {
        header('Content-Type: application/json');
        return json_encode(array('status' => $this->status, 'response' => $this->data));
    }

}