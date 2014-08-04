<?php
/**
 * Response class.
 * 
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

use Exception;

class Response {

    /**
     * Response status.
     *
     * @var int
     */
    protected $status;
    
    /**
     * Response data.
     *
     * @var object
     */
    protected $data;
    
    /**
     * Constructor.
     *
     * @param int $status
     * @param mixed|object|string $data
     */
    public function __construct($status, $data)
    {
        $this->status = $status;
        $this->data = $data;
        return $this;
    }
    
    /**
     * Render response.
     * 
     * @return string
     * @throws Exception
     */
    public function render()
    {
        throw new Exception("Not implemented.");
    }

}