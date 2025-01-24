<?php

namespace App\routes;


class Router {

    private $routes = [];

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }


    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }


    public function put($path, $callback) {
        $this->addRoute('PUT', $path, $callback);
    }


    public function delete($path, $callback) {
        $this->addRoute('DELETE', $path, $callback);
    }



    private function addRoute($method, $path, $callback) {
        $this->routes[] = compact('method', 'path', 'callback');
    }


    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        foreach($this->routes as $route) {
            if($route['method'] === $method && preg_match($this->convertPathToRegex($route['path']), $uri, $matches )) {
                array_shift($matches); // remove the full matches

                $callback = $route['callback'];

                // check if the callback is a [controllerClass, method]
                if(is_array($callback) && count($callback) === 2 ) {
                    [$controllerClass, $method] = $callback;
                    $controller = new $controllerClass(); // instantiate the controller
                    return call_user_func_array([$controller, $method], $matches);
                }

                // for other types of  callbacks
                return call_user_func_array($callback, $matches);
            } else {
                echo 'not found';
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);

    }


    private function convertPathToRegex($path) {

        $path = preg_replace('/\{(\w+)\}/', '(\w+)', $path);
        return '#^' . trim($path, '/') . '$#';
    }

}