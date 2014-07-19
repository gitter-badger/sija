<?php
/**
 * Configuration file.
 *
 * @package Sija
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija;

class Config {

    /**
     * Debug mode switcher.
     */
    static $debug = true;

    /**
     * Default values.
     */
    static $defaultOffset = 0;
    static $defaultLimit = 10;

    /**
     * Database settings.
     */
    static $connections = array(
        'development' => 'mysql://username:password@localhost/database_name',
        'production' => 'mysql://username:password@localhost/database_name',
    );
    static $connection = 'development';

}