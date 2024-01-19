<?php

class Router{

    protected $routes = [];


    public function registerRoutes($method, $uri, $controller) {

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];

    }


    /**
     * Add a GET route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function get($uri, $controller) {

        $this->registerRoutes('GET', $uri, $controller);

    }

     /**
     * Add a POST route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post($uri, $controller) {

        $this->registerRoutes('POST', $uri, $controller);

    }

     /**
     * Add a PUT route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put($uri, $controller) {

        $this->registerRoutes('PUT', $uri, $controller);

    }

     /**
     * Add a DELETE route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete($uri, $controller) {

        $this->registerRoutes('DELETE', $uri, $controller);

    }


    /**
     * Load error page
     * @param int $httpcode
     * @return void
     */
    public function error($httpCode = 404) {

        http_response_code($httpCode);
        loadView("error/{$httpCode}"); //this is ignoring the 404.php controller and directly loads 404.view.php
        exit;

    }

    /**
     * Route the request
     * 
     * @param string $uri
     * @param string $method
     * @return void
     */
    public function route($uri, $method) {

        //if the requested uri and method exist in the predefined possible routes then run its controller
        foreach($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                require basePath($route['controller']);
                return;
            }
        }

        //if no routes were found that match the current request then give a 404
        $this->error();
    }
}