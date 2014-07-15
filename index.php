<?php
/**
 * sija framework.
 * 
 * @package sija-framework
 * @author  Alex Chermenin <alex@chermenin.ru>
 */

/**
 * Init sessions.
 */
session_start();

/**
 * Init general autoload class.
 * 
 * @param string $class_name
 */
function sija_autoloader($class_name) {
    $directories = array(
        'classes/common/',
        'classes/controllers/',
    );
    foreach ($directories as $directory) {
        $filename = "$directory/$class_name.php";
        if (file_exists($filename)) {
            require_once($filename);
            break;
        }
    }
}
spl_autoload_register('sija_autoloader');

/**
 * Init debug mode.
 */
error_reporting(Config::$debug ? E_ALL : 0);

/**
 * Init Active Record.
 */
require_once('classes/common/ActiveRecord.php');
ActiveRecord\Config::initialize(function($cfg)
{
    $cfg->set_model_directory(Config::$modelsDirectory);
    $cfg->set_connections(Config::$connections);
    $cfg->set_default_connection(Config::$connection);
});

/**
 * Parse only AJAX requests.
 */
if(!Config::$debug && (
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')) {
        header('HTTP/1.1 500 Internal server error');
        $response_obj = Response::create(500, "This API allow only AJAX requests.", $_SERVER['HTTP_ACCEPT']);
        echo $response_obj->render();
        die();
    }

/**
 * Parse incoming request.
 */
$request = new Request();
if (isset($_SERVER['PATH_INFO'])) {
    $request->url_elements = explode('/', trim($_SERVER['PATH_INFO'], '/'));
}
$request->method = strtoupper($_SERVER['REQUEST_METHOD']);
switch ($request->method) {
    case 'GET':
        $request->parameters = $_GET;
    break;
    case 'POST':
        $request->parameters = $_POST;
    break;
    case 'PUT':
    case 'DELETE':
        $parameters = array();
        $request_data = file_get_contents('php://input');
        $exploded_data = explode('&', $request_data);
        foreach($exploded_data as $request_pair) {
            $request_item = explode('=', $request_pair);
            if (count($request_item) > 1) {
                $parameters[urldecode($request_item[0])] = urldecode($request_item[1]);
            }
        }
        $request->parameters = $parameters;
    break;
}

/**
 * Route the request.
 */
if (!empty($request->url_elements)) {
    $controller_name = ucfirst($request->url_elements[0]) . 'Controller';
    if ($controller_name != "AbstractController" && class_exists($controller_name)) {
        $controller = new $controller_name;
        $action_name = strtolower($request->method);
        try {
            $response_str = json_decode(call_user_func_array(array($controller, $action_name), array($request)));
            $response_int = 0;
        } catch (Exception $e) {
            $response_str = $e->getMessage();
            $response_int = $e->getCode();
        }
    }
    else {
        header('HTTP/1.1 404 Not Found');
        $response_int = 404;
        $response_str = 'Unknown request: ' . $request->url_elements[0];
    }
}
else {
    header('HTTP/1.1 500 Internal server error');
	  $response_int = 500;
    $response_str = 'Unknown request';
}

/**
 * Send the response to the client.
 */
$response_obj = Response::create($response_int, $response_str, $_SERVER['HTTP_ACCEPT']);
echo $response_obj->render();