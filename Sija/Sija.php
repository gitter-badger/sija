<?php
/**
 * Sija Framework.
 * 
 * @package Sija
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija;

use Sija\Common\Application, Sija\Common\Config, Sija\Common\Request, Sija\Common\ParametersList, Sija\Common\Response, ActiveRecord, Exception;

/**
 * Init general autoload class.
 * 
 * @param string $class_name
 */
function sija_autoloader($class_name) {
    if (strpos($class_name, '\\') !== false) {
        $namespaces = explode('\\', $class_name);
        $class_name = array_pop($namespaces);
    }
    if (isset($namespaces)) {
        $class_name = implode($namespaces, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $class_name;
    }
    $filename = __DIR__ . "/../$class_name.php";
    if (file_exists($filename)) {
        require_once($filename);
    }
}
spl_autoload_register('Sija\sija_autoloader');

/**
 * Sija general class.
 */

class Sija {

    /**
     * General executor.
     *
     * @todo Create route settings.
     * @param array $options
     * @return string
     */
    public function execute($options = array()) {

        // Init sessions.
        session_start();

        // Apply application config.
        Application::$config = new Config(isset($options['config']) && is_array($options['config']) ? $options['config'] : null);

       // Init debug mode.
        error_reporting(Application::$config->debug->bool ? E_ALL : 0);

        // Init Active Record.
        ActiveRecord\Config::initialize(function($cfg)
        {
            $cfg->set_connections(Application::$config->connections->value);
            $cfg->set_default_connection(Application::$config->connection->string);
            if (!Application::$config->directories->isEmpty() && isset(Application::$config->directories->value['models'])) {
                $base_dir = isset(Application::$config->directories->value['base']) ? Application::$config->directories->value['base'] : __DIR__;
                $cfg->set_model_directory(str_replace('{{base}}', $base_dir, Application::$config->directories->value['models']));
            }
        });

        // Parse only AJAX requests.
        if(Application::$config->ajax_only->bool && (
            !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
        ) {
            header('HTTP/1.1 500 Internal server error');
            $response_obj = Response::create(500, "This API allow only AJAX requests.", $_SERVER['HTTP_ACCEPT']);
            return $response_obj->render();
        }

        // Parse incoming request info.
        $request = new Request();

        // Parse request method & parameters
        $request->method = strtoupper(isset($options['method']) ? $options['method'] : $_SERVER['REQUEST_METHOD']);
        if (isset($options['parameters']) && is_array($options['parameters'])) {
            $request->parameters = new ParametersList($options['parameters']);
        } else {
            if (!isset($options['method'])) {
                switch ($request->method) {
                    case 'GET': $request->parameters = new ParametersList($_GET); break;
                    case 'POST': $request->parameters = new ParametersList($_POST); break;
                    default: $request->parameters = new ParametersList(); break;
                }
            } else {
                $request->parameters = new ParametersList();
            }
        }

        // Parse routes.
        if (isset($options['path']) || isset($_SERVER['PATH_INFO'])) {
            $path = trim(isset($options['path']) ? $options['path'] : $_SERVER['PATH_INFO'], '/');
            if (isset($options['routes']) && is_array($options['routes'])) {
                $routes = $options['routes'];
                if (isset($routes['general']) && is_array($routes['general'])) {
                    foreach($routes['general'] as $key => $route) {
                        $path = preg_replace($key, $route, $path);
                    }
                }
                if (isset($routes[strtolower($request->method)]) && is_array($routes[strtolower($request->method)])) {
                    foreach($routes[strtolower($request->method)] as $key => $route) {
                        $path = preg_replace($key, $route, $path);
                    }
                }
            }
            $path_elements = explode('?', $path);
            if (count($path_elements) > 0) {
                $path = $path_elements[0];
                if (count($path_elements) > 1) {
                    $path_parameters = explode('&', $path_elements[1]);
                    foreach ($path_parameters as $path_parameter) {
                        $path_parameter_pair = explode('=', $path_parameter);
                        switch (count($path_parameter_pair)) {
                            case 1: $request->parameters->add($path_parameter_pair[0]); break;
                            case 2: $request->parameters->add($path_parameter_pair[0], $path_parameter_pair[1]); break;
                        }
                    }
                }

            }

            $request->url_elements = explode('/', trim($path, '/'));
        }

        // Parse incoming data.
        if (isset($options['json'])) {
            $request->json = is_object($options['json']) ? $options['json'] : json_decode($options['json']);
        } else {
            $request_data = file_get_contents('php://input');
            $request->json = json_decode($request_data);
        }

        // Route the request.
        if (!empty($request->url_elements) && !empty($request->url_elements[0])) {
            $controller_name = ucfirst($request->url_elements[0]);
            $controller_classname = ($this->__attachController($controller_name) ? $controller_name : 'Sija\\Controllers\\' . $controller_name) . 'Controller';
            if (class_exists($controller_classname)) {
                $controller = new $controller_classname;
                $action_name = strtolower($request->method);
                try {
                    $response_status = 200;
                    $response_data = json_decode(call_user_func_array(array($controller, $action_name), array($request)));
                } catch (Exception $e) {
                    $response_status = $e->getCode();
                    $response_data = $e->getMessage();
                }
            }
            else {
                header('HTTP/1.1 500 Internal server error');
                $response_status = 500;
                $response_data = 'Unknown request: ' . $request->url_elements[0];
            }
        }
        else {
            header('HTTP/1.1 500 Internal server error');
            $response_status = 500;
            $response_data = 'Unknown request';
        }

        // Return response
        $response_obj = Response::create($response_status, $response_data, $_SERVER['HTTP_ACCEPT']);
        return $response_obj->render();
    }

    /**
     * Private method to search controller file and attach it.
     *
     * @param string $name Controller name.
     * @return bool Attached or not.
     */
    private function __attachController($name) {

        if (!Application::$config->directories->isEmpty() && is_array(Application::$config->directories->value)) {

            // Set controller directories.
            $base_dir = isset(Application::$config->directories->value['base']) ? Application::$config->directories->value['base'] : __DIR__;
            $controller_dirs = Application::$config->directories->value['controllers'];

            // Looking for...
            if ($controller_dirs) {

                // Set controller file name.
                $controller_filename = ucfirst($name) . "Controller.php";

                // Search in directories.
                if (is_array($controller_dirs)) {
                    foreach($controller_dirs as $controller_dir) {
                        $controller_file = rtrim(str_replace('{{base}}', $base_dir, $controller_dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $controller_filename;
                        if(file_exists($controller_file)) {
                            require_once($controller_file); return true;
                        }
                    }
                } else {
                    $controller_file = rtrim(str_replace('{{base}}', $base_dir, $controller_dirs), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $controller_filename;
                    if(file_exists($controller_file)) {
                        require_once($controller_file); return true;
                    }
                }
            }
        }

        // Not found :(
        return false;
    }


}








