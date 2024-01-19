<?php 

require '../helpers.php';   //this brings in the loadView() function


require basePath('Router.php');

$router = new Router();

//this currently runs the newly instantiated $router's route adding methods
//which sets up the list of valid routes to handle and which controllers they go with
require basePath('routes.php');


//get the current request uri and request method
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

//pass these to the router which will send it
//to the appropriate controller if it has a route defined for it
$router->route($uri, $method);

