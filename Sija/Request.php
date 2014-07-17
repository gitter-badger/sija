<?php
/**
 * Request class.
 * 
 * @package sija-framework
 * @author  Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija;

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

    /**
     * JSON sent with the request.
     *
     * @var object
     */
    public $json;
}
