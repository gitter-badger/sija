<?php
/**
 * User controller.
 *
 * @package Sija\Controllers
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Controllers;

use Sija, Sija\Common\Common, Sija\Common\Application, Sija\Common\Request, Sija\Models\User, Exception;

class UserController extends Sija\Common\AbstractController {

    /**
     * GET method: Information about users
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function get($request) {

        switch (count($request->url_elements)) {

            case 1:
                $limit = $request->parameters->exists("limit") ? $request->parameters->limit->int : Application::$config->default_limit->int;
                $offset = $request->parameters->exists("offset") ? $request->parameters->offset->int : Application::$config->default_offset->int;
                $users = User::find('all', array('limit' => $limit, 'offset' => $offset));
                foreach ($users as $user) {
                    $response[] = json_decode($user->to_json(array('except'=>'password')));
                }
                if (isset($response)) {
                    return $response;
                } else {
                    throw new Exception("Users not found.", 404);
                }

            case 2:
                $user = User::find_by_id($request->url_elements[1]);
                if ($user) {
                    return json_decode($user->to_json(array('except'=>'password')));
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
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function post($request) {

        switch (count($request->url_elements)) {

            case 1:
                // Empty request data - throw Exception
                if (empty($request->json)) {
                    throw new Exception("Missing required data.", 400);
                }

                // No login or password - throw Exception
                if (!$request->json->login || !$request->json->password) {
                    throw new Exception("Missing required parameter.", 400);
                }

                // Login already exists - throw Exception
                if (count(User::find('all', array('conditions' => array('login=?', $request->json->login)))) > 0)
                    throw new Exception("User with this login already exists.", 500);

                // Like success - create user & return
                $user = User::create(array(
                    'login' => $request->json->login,
                    'password' => Common::getPasswordHash($request->json->password, $request->json->login),
                ));
                return json_decode($user->to_json(array('except'=>'password')));

            default:
                throw new Exception("Unknown request.", 500);

        }

    }

    /**
     * PUT method: Update information about user
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function put($request) {

        switch (count($request->url_elements)) {

            case 2:
                // Guest - throw Exception
                if (Application::isGuest())
                    throw new Exception("Unknown request.", 500);

                // Don't have permissions - throw Exception
                if (!Application::isAdmin() && Application::currentUser()->id != $request->url_elements[1])
                    throw new Exception("You don't have required permissions to update this user.", 403);

                // Change login to exists one - throw Exception
                if ($request->json->login && count(User::find('all', array('conditions' => array('login=?', $request->json->login)))) > 0)
                    throw new Exception("Change login failed. User with this login already exists.", 500);

                // Like success - update user information & return
                $user = User::find_by_id($request->url_elements[1]);
                if ($user) {
                    $user->login = $request->json->login ? $request->json->login : $user->login;
                    $user->password = $request->json->password ? Common::getPasswordHash($request->json->password, $request->json->login) : $user->password;
                    $user->save();
                    return json_decode($user->to_json(array('except'=>'password')));
                } else {
                    throw new Exception("User not found.", 404);
                }

            default:
                throw new Exception("Unknown request.", 500);

        }

    }

    /**
     * DELETE method: Delete user
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function delete($request) {

        switch (count($request->url_elements)) {

            case 2:
                // Don't have permissions - throw Exception
                if (!Application::isAdmin())
                    throw new Exception("You don't have required permissions to update this user.", 403);

                // Like success - delete user & return
                $user = User::find_by_id($request->url_elements[1]);
                if ($user) {
                    $user->delete();
                    return json_decode($user->to_json(array('except'=>'password')));
                } else {
                    throw new Exception("User not found.", 404);
                }

            default:
                throw new Exception("Unknown request.", 500);

        }

    }

}