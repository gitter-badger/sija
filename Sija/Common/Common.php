<?php
/**
 * Class for common functions.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

use Sija\Models\Session, Sija\Models\User;

class Common {

    /**
     * Salts.
     */
    private static $salt = array(
        'login' => 'f44ede452e30880122a1124b1d0bf565',
        'password' => '40736945fa40bbf42ac215a9ac5ba76e',
        'end' => '15dd5e67ff4c541c071359a0e22890c4',
    );

    /**
     * Get hash for password and login with salts.
     *
     * @param  string $password
     * @param  string $login
     * @return string
     */
    public static function getPasswordHash($password, $login = '') {
        return md5(md5(md5($password).self::$salt['password'].md5($login).self::$salt['login']).self::$salt['end']);
    }

    /**
     * Check user authorisation.
     *
     * @return bool
     */
    public static function checkAuthorization() {
        if (isset($_SESSION['user']) && isset($_SESSION['login'])) {
            return true;
        } else {
            if (isset($_COOKIE['u']) and isset($_COOKIE['s'])) {
                $session = Session::find_by_user_and_agent($_COOKIE['u'], $_SERVER['HTTP_USER_AGENT']);
                if ($session) {
                    if (md5($session->id) == $_COOKIE['s']) {
                        $user = User::find_by_id($_COOKIE['u']);
                        if ($user) {
                            $_SESSION['session'] = $session->id;
                            $_SESSION['user'] = $user->id;
                            $_SESSION['login'] = $user->login;
                            setcookie("u", $user->id, time()+3600*24*14);
                            setcookie("s", md5($session->id), time()+3600*24*14);
                            return true;
                        }
                    } else {
                        $session->delete();
                    }
                }
            }
        }
        return false;
    }

    /**
     * Perform user authorisation.
     *
     * @param string $login
     * @param string $password
     * @return bool
     */
    public static function doAuthorisation($login = '', $password = '') {
        $user = User::find_by_login_and_password($login, Common::getPasswordHash($password, $login));
        if ($user) {
            $_SESSION['user'] = $user->id;
            $_SESSION['login'] = $user->login;
            $session = Session::find_by_user_and_agent($user->id, $_SERVER['HTTP_USER_AGENT']);
            if (!$session) {
                $session = Session::create(array(
                    'user' => $user->id,
                    'agent' => $_SERVER['HTTP_USER_AGENT'],
                ));
            }
            $session->save();
            $_SESSION['session'] = $session->id;
            setcookie("u", $user->id, time()+3600*24*14);
            setcookie("s", md5($session->id), time()+3600*24*14);
            return true;
        }
        return false;
    }

}