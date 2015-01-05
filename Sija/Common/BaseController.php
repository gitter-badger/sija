<?php
/**
 * Base controller class.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

use Exception;

class BaseController extends AbstractController {

    private $__model_class;

    public function __construct($model_class) {
        $this->__model_class = $model_class;
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
                if (method_exists($this->__model_class, "find")) {
                    $limit = $request->json->limit ? $request->json->limit : Application::$config->default_limit->int;
                    $offset = $request->json->offset ? $request->json->offset : Application::$config->default_offset->int;
                    $items = call_user_func($this->__model_class . "::find", 'all', array('limit' => $limit, 'offset' => $offset));
                    foreach ($items as $item) {
                        $response[] = json_decode($item->to_json());
                    }
                    if (isset($response)) {
                        return $response;
                    } else {
                        throw new Exception("Objects not found.", 404);
                    }
                } else {
                    throw new Exception("Unknown method.", 500);
                }

            case 2:
                if (method_exists($this->__model_class, "find")) {
                    $item = call_user_func($this->__model_class . "::find_by_id", $request->url_elements[1]);
                    if ($item) {
                        return json_decode($item->to_json());
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
                if (method_exists($this->__model_class, "create")) {
                    $item = call_user_func($this->__model_class . "::create", $request->parameters->toArray() + json_decode(json_encode($request->json), true));
                    if ($item) {
                        return json_decode($item->to_json());
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
                if (method_exists($this->__model_class, "find")) {
                    $item = call_user_func($this->__model_class . "::find_by_id", $request->url_elements[1]);
                    if ($item) {
                        foreach (json_decode(json_encode($request->json), true) as $key => $value) {
                            $item->{$key} = $value;
                        }
                        $item->save();
                        return json_decode($item->to_json());
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
                if (method_exists($this->__model_class, "find")) {
                    $item = call_user_func($this->__model_class . "::find_by_id", $request->url_elements[1]);
                    if ($item) {
                        $item->delete();
                        return json_decode($item->to_json());
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
