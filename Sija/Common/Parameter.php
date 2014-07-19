<?php
/**
 * Request parameter class.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

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

            case 'regexp':
                return filter_var($this->__value, FILTER_VALIDATE_REGEXP);

            case 'value':
                return $this->__value;

        }
    }

    /**
     * Default converter to string.
     * 
     * @return string
     */
    public function __toString() {
        return print_r($this->__value);
    }

    /**
     * Check is parameter empty.
     * 
     * @return bool
     */
    public function isEmpty() {
        return empty($this->__value);
    }

}