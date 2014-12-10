<?php
/**
 * Abstract controller class.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

use Exception;

class AbstractController {

    /**
     * GET method.
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function get($request) { throw new Exception("Not implemented method.", 500); }

    /**
     * POST method.
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function post($request) { throw new Exception("Not implemented method.", 500); }

    /**
     * PUT method.
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function put($request) { throw new Exception("Not implemented method.", 500); }

    /**
     * DELETE method.
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function delete($request) { throw new Exception("Not implemented method.", 500); }

}