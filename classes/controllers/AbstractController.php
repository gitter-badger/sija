<?php
/**
 * Abstract Controller class.
 *
 * @package api-framework
 * @author  Alex Chermenin <alex@chermenin.ru>
 * @abstract
 */

abstract class AbstractController {

    /**
     * GET method.
     *
     * @param $request
     * @return string
     */
    abstract public function get($request);

    /**
     * POST method.
     *
     * @param $request
     * @return string
     */
    abstract public function post($request);

    /**
     * PUT method.
     *
     * @param $request
     * @return string
     */
    abstract public function put($request);

    /**
     * DELETE method.
     *
     * @param $request
     * @return string
     */
    abstract public function delete($request);

}