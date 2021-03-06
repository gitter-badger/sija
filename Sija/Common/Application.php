<?php
/**
 * Application class.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

use Sija\Models\User;

class Application {

    /**
     * Application general configuration
     *
     * @var $config Config
     */
    public static $config = null;

    /**
     * Get current application user
     *
     * @return User
     */
    public static function currentUser() {
        if (Common::checkAuthorization()) {
            $user = User::find_by_id($_SESSION['user']);
            return $user;
        }
        return null;
    }

    /**
     * Current application user is admin
     *
     * @return bool
     */
    public static function isAdmin() {
        $user = self::currentUser();
        return $user ? $user->is_admin : false;
    }

    /**
     * Current application user is guest
     *
     * @return bool
     */
    public static function isGuest() {
        return self::currentUser() == null;
    }

}