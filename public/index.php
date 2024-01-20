<?php 

require '../helpers.php';   //this brings in the loadView() function

require basePath('Router.php');
require basePath('Database.php');



$router = new Router();

//this currently runs the newly instantiated $router's route adding methods
//which sets up the list of valid routes to handle and which controllers they go with
//this needs to stay below the router instantiation because it references $router in it
require basePath('routes.php');


//get the current request uri and request method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

//pass these to the router which will send it
//to the appropriate controller if it has a route defined for it
//and will show a 404 or similar if it doesn't have a route for it
$router->route($uri, $method);

