<?php
/**
 * Application class.
 *
 * @package sija-framework
 * @author  Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija;

use Sija\Models\User;

class Application {

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