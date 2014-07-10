<?php
/**
 * Request class.
 * 
 * @package api-framework
 * @author  Alex Chermenin <alex@chermenin.ru>
 */
class Request
{
    /**
     * URL elements.
     *
     * @var array
     */
    public $url_elements = array();
    
    /**
     * The HTTP method used.
     *
     * @var string
     */
    public $method;
    
    /**
     * Any parameters sent with the request.
     *
     * @var array
     */
    public $parameters;
}
