<?php
/**
 * Base configuration class.
 *
 * @package Sija
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

/**
 * Default properties.
 *
 * @property Parameter debug
 * @property Parameter ajax_only
 * @property Parameter default_offset
 * @property Parameter default_limit
 * @property Parameter connections
 * @property Parameter connection
 * @property Parameter directories
 */
class Config {

    private $__options = array();

    /**
     * Base configuration constructor.
     *
     * @param array $options
     * @return Config
     */
    public function __construct($options = array()) {

        // Default options
        $this->__options['debug'] = new Parameter(false);
        $this->__options['ajax_only'] = new Parameter(true);
        $this->__options['default_offset'] = new Parameter(0);
        $this->__options['default_limit'] = new Parameter(10);
        $this->__options['connections'] = new Parameter(array('dev' => 'mysql://username:password@localhost/database_name'));
        $this->__options['connection'] = new Parameter('dev');

        // Applying options
        if (isset($options)) {
            foreach ($options as $name => $value) {
                $this->__options[$name] = is_object($value) && get_class($value) == get_class(new Parameter()) ? $value : new Parameter($value);
            }
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
        return isset($this->__options[$name]) ? $this->__options[$name] : new Parameter();
    }

}