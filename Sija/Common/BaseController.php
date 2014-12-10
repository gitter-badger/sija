<?php
/**
 * Base controller class.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

use Exception, ReflectionClass;

class BaseController extends AbstractController {

    private $__model_reflection;

    public function __construct($model_class) {
        $this->__model_reflection = new ReflectionClass($model_class);
    }

    /**
     * GET method: Get items.
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function get($request) {
        switch (count($request->url_elements)) {
            
            case 1:
                if ($this->__model_reflection->hasMethod("find")) {
                    $limit = $request->json->limit ? $request->json->limit : Application::$config->default_limit->int;
                    $offset = $request->json->offset ? $request->json->offset : Application::$config->default_offset->int;
                    $model_find = $this->__model_reflection->getMethod("find");
                    $items = $model_find->invoke(null, 'all', array('limit' => $limit, 'offset' => $offset));
                    if ($items) {
                        return $items;
                    } else {
                        throw new Exception("Objects not found.", 404);
                    }
                } else {
                    throw new Exception("Unknown method.", 500);
                }

            case 2:
                if ($this->__model_reflection->hasMethod("find_by_id")) {
                    $model_find_by_id = $this->__model_reflection->getMethod("find_by_id");
                    $item = $model_find_by_id->invoke(null, $request->url_elements[1]);
                    if ($item) {
                        return $item;
                    } else {
                        throw new Exception("Object not found.", 404);
                    }
                } else {
                    throw new Exception("Unknown method.", 500);
                }

            default:
                throw new Exception("Unknown request.", 500);
        }
    }

    /**
     * POST method: Create item.
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function post($request) {

        switch (count($request->url_elements)) {

            case 1:
                if ($this->__model_reflection->hasMethod("create")) {
                    $model_create = $this->__model_reflection->getMethod("create");
                    $item = $model_create->invoke(null, $request->parameters->toArray());
                    if ($item) {
                        return $item;
                    } else {
                        throw new Exception("Internal error.", 500);
                    }
                } else {
                    throw new Exception("Unknown method.", 500);
                }

            default:
                throw new Exception("Unknown request.", 500);

        }

    }

    /**
     * PUT method: Update item.
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function put($request) {

        switch (count($request->url_elements)) {

            case 2:
                if ($this->__model_reflection->hasMethod("find_by_id")) {
                    $model_find_by_id = $this->__model_reflection->getMethod("find_by_id");
                    $item = $model_find_by_id->invoke(null, $request->url_elements[1]);
                    if ($item) {
                        foreach ($request->parameters->toArray() as $key => $value) {
                            $item_reflection = new ReflectionClass($item);
                            if ($item_reflection->hasProperty($key)) {
                                $item_reflection->getProperty($key)->setValue($item, $value);
                            }
                        }
                        $item->save();
                        return $item;
                    } else {
                        throw new Exception("Object not found.", 404);
                    }
                } else {
                    throw new Exception("Unknown method.", 500);
                }

            default:
                throw new Exception("Unknown request.", 500);

        }

    }

    /**
     * DELETE method: Delete item.
     *
     * @param Request $request
     * @throws Exception
     * @return mixed
     */
    public function delete($request) {

        switch (count($request->url_elements)) {

            case 2:
                if ($this->__model_reflection->hasMethod("find_by_id")) {
                    $model_find_by_id = $this->__model_reflection->getMethod("find_by_id");
                    $item = $model_find_by_id->invoke(null, $request->url_elements[1]);
                    if ($item) {
                        $item->delete();
                        return $item;
                    } else {
                        throw new Exception("Object not found.", 404);
                    }
                } else {
                    throw new Exception("Unknown method.", 500);
                }

            default:
                throw new Exception("Unknown request.", 500);

        }

    }

}