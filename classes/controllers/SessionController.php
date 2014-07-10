<?php

class SessionController extends AbstractController {


    /**
     * GET method: Information about current session.
     *
     * @param $request
     * @throws Exception
     * @return string
     */
    public function get($request) {
        if (count($request->url_elements) == 1) {
            if (Common::checkAuthorization()) {
                $session = Session::find_by_id($_SESSION['session']);
                if ($session) {
                    return $session->to_json();
                } else {
                    throw new Exception("Session not found.", 404);
                }
            } else {
                throw new Exception("Authorisation required.", 403);
            }
        } else {
            throw new Exception("Unknown request.", 500);
        }
    }

    /**
     * POST method: create new session.
     *
     * @param $request
     * @throws Exception
     * @return string
     */
    public function post($request) {

        // Check incoming parameters
        $login = isset($request->parameters['login']) ? $request->parameters['login'] : false;
        $password = isset($request->parameters['password']) ? $request->parameters['password'] : false;
        if (!($login && $password)) {
            throw new Exception("Missing required parameter.", 400);
        }

        if (Common::doAuthorisation($login, $password)) {
            $session = Session::find_by_id($_SESSION['session']);
            return $session->to_json();
        } else {
            throw new Exception("Incorrect login or password.", 403);
        }
    }

    /**
     * PUT method: unused method.
     *
     * @param $request
     * @throws Exception
     * @return string
     */
    public function put($request) {
        throw new Exception("Unknown request.", 500);
    }

    /**
     * DELETE method: Close current session.
     *
     * @param $request
     * @throws Exception
     * @return string
     */
    public function delete($request) {
        if (Common::checkAuthorization()) {
            $session = Session::find_by_id($_SESSION['session']);
            if ($session) {
                $session->delete();
                session_destroy();
                setcookie("u", '', time()-3600);
                setcookie("s", '', time()-3600);
                return $session->to_json();
            } else {
                throw new Exception("Session not found.", 404);
            }
        } else {
            throw new Exception("Authorisation required.", 403);
        }
    }

}