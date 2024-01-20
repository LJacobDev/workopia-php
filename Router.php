<?php

class Router{

    //this holds the possible routes to handle and they are given at runtime
    //by having this class' methods to add them in
    //the router will look through these and compare them to what is currently incoming
    //if it sees a match for a method and uri it is expecting, it will send it to that controller
    //otherwise it will produce a 404 http response code and load the 404 error page
    protected $routes = [];

    /**
     * Add a route to the list of expected routes
     *
     * @param string $method
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function registerRoutes($method, $uri, $controller) {

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];

    }


    /**
     * Add a GET route to routes collection
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function get($uri, $controller) {

        $this->registerRoutes('GET', $uri, $controller);

    }

     /**
     * Add a POST route to routes collection
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post($uri, $controller) {

        $this->registerRoutes('POST', $uri, $controller);

    }

     /**
     * Add a PUT route to routes collection
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put($uri, $controller) {

        $this->registerRoutes('PUT', $uri, $controller);

    }

     /**
     * Add a DELETE route to routes collection
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