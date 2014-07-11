<?php
/**
 * Application class.
 *
 * @package sija-framework
 * @author  Alex Chermenin <alex@chermenin.ru>
 */

class Application {

    /**
     * Get current application user
     *
     * @return User
     */
    public static function CurrentUser() {
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
    public static function IsAdmin() {
        $user = self::CurrentUser();
        return $user ? $user->is_admin : false;
    }

    /**
     * Current application user is guest
     *
     * @return bool
     */
    public static function IsGuest() {
        return self::CurrentUser() == null;
    }

}