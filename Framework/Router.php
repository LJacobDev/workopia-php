<?php

namespace Framework;

use App\Controllers\ErrorController;

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
     * @param string $action
     * @return void
     */
    public function registerRoutes($method, $uri, $action) {

        list($controller, $controllerMethod) = explode('@', $action);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
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
                
                //extract the controller and controller method
                //these controllers are all known to be in the App\Controllers\ namespace
                $controller = 'App\\Controllers\\' . $route['controller'];
                $controllerMethod = $route['controllerMethod'];

                //so now this holds something like HomeController and index in those variables

                //Instantiate this controller class and call this method on it
                //this 'new $controller' holds it like a variable
                //it might be really instantiating 'new HomeController()' class here
                $controllerInstance = new $controller();

                //this runs something such as:
                //HomeController->index() method
                $controllerInstance->$controllerMethod();

                return;
            }
        }

        //if no routes were found that match the current request then give a 404
        ErrorController::notFound();
    }
}