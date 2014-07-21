<?php
/**
 * Request parameters list class.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

class ParametersList {

    private $__parameters = array();

    /**
     * Parameters list constructor.
     *
     * @param array $parameters
     * @return ParametersList
     */
    public function __construct($parameters = array()) {
        foreach ($parameters as $name => $value) {
            $this->__set($name, $value);
        }
        return $this;
    }

    /**
     * Default getter.
     *
     * @param string $name
     * @return Parameter
     */
    public function __get($name) {
        return isset($this->__parameters[$name]) ? $this->__parameters[$name] : new Parameter();
    }

    /**
     * Default setter.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->__parameters[$name] = is_object($value) && get_class($value) == get_class(new Parameter()) ? $value : new Parameter($value);
    }

    /**
     * Function to add new parameter.
     *
     * @param string $name
     * @param mixed $value
     */
    public function add($name, $value = null) {
        $this->__set($name, $value);
    }

    /**
     * Check element existence.
     *
     * @param string $name
     * @return bool
     */
    public function exists($name) {
        return isset($this->__parameters[$name]);
    }

}