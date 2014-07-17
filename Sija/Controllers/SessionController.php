<?php
/**
 * Session controller.
 *
 * @package sija-framework
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Controllers;

use Sija, Sija\Common, Sija\Request, Sija\Models\Session, Exception;

class SessionController extends Sija\Controller {

    /**
     * GET method: Information about current session.
     *
     * @param Request $request
     * @throws Exception
     * @return string
     */
    public function get($request) {

        switch (count($request->url_elements)) {

            case 1:
                if (!Common::checkAuthorization()) throw new Exception("Authorisation required.", 403);
                $session = Session::find_by_id($_SESSION['session']);
                if ($session) {
                    return $session->to_json();
                } else {
                    throw new Exception("Internal error.", 500);
                }

            default:
                throw new Exception("Unknown request.", 500);

        }

    }

    /**
     * POST method: create new session.
     *
     * @param Request $request
     * @throws Exception
     * @return string
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

                // Failed authorisation - throw Exception
                if (!Common::doAuthorisation($request->json->login, $request->json->password)) {
                    throw new Exception("Incorrect login or password.", 403);
                }

                // Like success - create session & return
                $session = Session::find_by_id($_SESSION['session']);
                if ($session) {
                    return  $session->to_json();
                } else {
                    throw new Exception("Internal error.", 500);
                }

            default:
                throw new Exception("Unknown request.", 500);

        }

    }

    /**
     * DELETE method: Close current session.
     *
     * @param Request $request
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