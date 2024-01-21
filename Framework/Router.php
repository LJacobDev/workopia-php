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
    public function route($uri) {

        $requestMethod = $_SERVER['REQUEST_METHOD'];

            //trim off any / forward slashes at beginning or end
            //then split the current uri into segments delimited by /
            $uriSegments = explode('/', trim($uri,'/'));

            //inspect($uriSegments);
            //inspect($uriSegments[1]);

        //if the requested uri and method exist in the predefined possible routes then run its controller
        foreach($this->routes as $route) {

            //split the route uri into segments
            $routeSegments = explode('/', trim( $route['uri'],'/'));

            //  this echoes 1 / true if there is a match
            //  but it won't work on regex so a more elaborate loop is done
            //  echo ($uriSegments == $routeSegments) . "<br>";

            
            if (count($uriSegments) === count($routeSegments) && 
            strtoupper($requestMethod) === strtoupper($route['method'])) {
                
                $params = [];
                
                //match will be assumed true unless inner loop finds unmatching elements
                //that can't be explained by expected regex detected param values
                $match = true;

                //now that the method and number of arguments matches,
                //it will loop through segments and check with regex so listing/1
                //and listing/2 can be used as matches to a listing/{id} route
                for($i = 0; $i < count($uriSegments); $i++){

                    //if a mismatch is found between $routeSegments[i] and $uriSegments[i]
                    //then check if routeSegments[$i] has curly brackets in it using regex
                    //if it is not one of those, then match=false and break out of for loop
                    //But if it does have { } in it, it is expecting a uri parameter

                    if ($uriSegments[$i] !== $routeSegments[$i] && 
                        !preg_match( '/\{(.+?)\}/', $routeSegments[$i])) {
                            $match = false;
                            break;
                    }

                    //if this is still executing then it means a { } param was found
                    if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {

                        //inspect($matches);            //shows ["{id}", "id"]
                        //inspect($uriSegments[$i]);    //shows "2" if writing /listing/2 in browser

                        //params will now hold ['id'=>'2'] for example
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                } // end of for loop

                if ($match) {

                    //extract the controller and controller method
                    //these controllers are all known to be in the App\Controllers\ namespace
                    $controller = 'App\\Controllers\\' . $route['controller'];
                    $controllerMethod = $route['controllerMethod'];

                    //so now this holds something like HomeController and index in those variables

                    //Instantiate this controller class and call this method on it
                    //this 'new $controller' holds it like a variable
                    //it might be really instantiating 'new HomeController()' class here
                    $controllerInstance = new $controller();

                    //this runs something such as
                    //HomeController->index() method
                    $controllerInstance->$controllerMethod($params);

                    return;

                }

            }


        }   //end of foreach

        //if no routes were found that match the current request then give a 404
        ErrorController::notFound();
    }
}