<?php
/**
 * Request parameter class.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

use Exception;

/**
 * Properties to type check.
 * 
 * @property bool bool
 * @property bool boolean
 * @property int int
 * @property int integer
 * @property float float
 * @property float double
 * @property string url
 * @property string email
 * @property string ip
 * @property string regexp
 * @property string string
 * @property mixed value
 */
class Parameter {

    private $__value;

    /**
     * Parameter constructor
     *
     * @param mixed $value
     * @return Parameter
     */
    public function __construct($value = null) {
        $this->__value = $value;
        return $this;
    }

    /**
     * Default getter.
     * 
     * @param string $name
     * @throws Exception
     * @return mixed|null
     */
    public function __get($name) {
        switch ($name) {

            case 'bool':
            case 'boolean':
                return filter_var($this->__value, FILTER_VALIDATE_BOOLEAN);

            case 'int':
            case 'integer':
                return filter_var($this->__value, FILTER_VALIDATE_INT);

            case 'float':
            case 'double':
                return filter_var($this->__value, FILTER_VALIDATE_FLOAT);

            case 'url':
                return filter_var($this->__value, FILTER_VALIDATE_URL);

            case 'email':
                return filter_var($this->__value, FILTER_VALIDATE_EMAIL);

            case 'ip':
                return filter_var($this->__value, FILTER_VALIDATE_IP);

            case 'string':
                return $this->__toString();

            case 'value':
                return $this->__value;

            default:
                throw new Exception("Undefined property: $name");
        }
    }

    /**
     * Default converter to string.
     * 
     * @return string
     */
    public function __toString() {
        return print_r($this->__value, true);
    }

    /**
     * Check is parameter empty.
     * 
     * @return bool
     */
    public function isEmpty() {
        return empty($this->__value);
    }

    /**
     * Filter value via regular expression.
     *
     * @param string $regexp
     * @return string|bool
     */
    public function filter($regexp) {
        return filter_var($this->__value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>$regexp)));
    }

}