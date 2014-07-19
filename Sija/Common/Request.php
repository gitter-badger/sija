<?php
/**
 * Request class.
 * 
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

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
     * @var ParametersList
     */
    public $parameters;

    /**
     * JSON sent with the request.
     *
     * @var object
     */
    public $json;
}
