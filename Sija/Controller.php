<?php
/**
 * Controller class.
 *
 * @package Sija
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija;

use Exception;

class Controller {

    /**
     * GET method.
     *
     * @param Request $request
     * @throws Exception
     * @return string
     */
    public function get($request) { throw new Exception("Not implemented method.", 500); }

    /**
     * POST method.
     *
     * @param Request $request
     * @throws Exception
     * @return string
     */
    public function post($request) { throw new Exception("Not implemented method.", 500); }

    /**
     * PUT method.
     *
     * @param Request $request
     * @throws Exception
     * @return string
     */
    public function put($request) { throw new Exception("Not implemented method.", 500); }

    /**
     * DELETE method.
     *
     * @param Request $request
     * @throws Exception
     * @return string
     */
    public function delete($request) { throw new Exception("Not implemented method.", 500); }

}