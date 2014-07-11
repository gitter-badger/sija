<?php
/**
 * User controller.
 *
 * @package sija-framework
 * @author  Alex Chermenin <alex@chermenin.ru>
 */

class UserController extends AbstractController {

    /**
     * GET method: Information about users
     *
     * @param  Request $request
     * @throws Exception
     * @return string
     */
    public function get($request) {
        switch (count($request->url_elements)) {
            case 1:
                $limit = isset($request->parameters['limit']) ? $request->parameters['limit'] : Config::$defaultLimit;
                $offset = isset($request->parameters['offset']) ? $request->parameters['offset'] : Config::$defaultOffset;
                $users = User::find('all', array('limit' => $limit, 'offset' => $offset));
                foreach ($users as $user) {
                    $response[] = json_decode($user->to_json(array('except'=>'password')));
                }
                if (isset($response)) {
                    return json_encode($response);
                } else {
                    throw new Exception("Users not found.", 404);
                }
            case 2:
                $user = User::find_by_id($request->url_elements[1]);
                if ($user) {
                    return $user->to_json(array('except'=>'password'));
                } else {
                    throw new Exception("User not found.", 404);
                }
            default:
                throw new Exception("Unknown request.", 500);
        }
    }

    /**
     * POST method: Create new user
     *
     * @param  Request $request
     * @throws Exception
     * @return string
     */
    public function post($request) {
        if (count($request->url_elements) == 1) {

            // Check incoming parameters
            $login = isset($request->parameters['login']) ? $request->parameters['login'] : false;
            $password = isset($request->parameters['password']) ? Common::getPasswordHash($request->parameters['password'], $login) : false;
            if (!($login && $password)) {
                throw new Exception("Missing required parameter.", 400);
            }

            // Create and return user object
            if (count(User::find('all', array('conditions' => array('login=?', $login)))) == 0) {
                $user = User::create(array(
                    'login' => $login,
                    'password' => $password,
                ));
                return $user->to_json(array('except'=>'password'));
            } else {
                throw new Exception("User with this login already exists.", 500);
            }
        } else {
            throw new Exception("Unknown request.", 500);
        }
    }

    /**
     * PUT method: Update information about user
     *
     * @param  Request $request
     * @throws Exception
     * @return string
     */
    public function put($request) {
        if (count($request->url_elements) == 2) {
            if (!Application::IsGuest() && (Application::CurrentUser()->id == $request->url_elements[1] || Application::IsAdmin())) {

                // Check incoming parameters
                $login = isset($request->parameters['login']) ? $request->parameters['login'] : false;
                $password = isset($request->parameters['password']) ? Common::getPasswordHash($request->parameters['password'], $login) : false;
                if (!($login && $password)) {
                    throw new Exception("Missing required parameter.", 400);
                }

                // Update user information
                $user = User::find_by_id($request->url_elements[1]);
                if ($user) {
                    $user->login = $login;
                    $user->password = $password;
                    $user->save();
                    return $user->to_json(array('except'=>'password'));
                } else {
                    throw new Exception("User not found.", 404);
                }
            } else {
                throw new Exception("You don't have required permissions to update user information.", 403);
            }
        } else {
            throw new Exception("Unknown request.", 500);
        }
    }

    /**
     * DELETE method: Delete user
     *
     * @param  Request $request
     * @throws Exception
     * @return string
     */
    public function delete($request) {
        if (count($request->url_elements) == 2) {
            if (Application::IsAdmin()) {
                $user = User::find_by_id($request->url_elements[1]);
                if ($user) {
                    $user->delete();
                    return $user->to_json(array('except'=>'password'));
                } else {
                    throw new Exception("User not found.", 404);
                }
            } else {
                throw new Exception("You don't have required permissions to delete user.", 403);
            }
        } else {
            throw new Exception("Unknown request.", 500);
        }
    }

}