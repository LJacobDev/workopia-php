<?php 

require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';   //this brings in the loadView() function

use Framework\Router;

//require basePath('Framework/Router.php');
//require basePath('Framework/Database.php');

//custom autoloader using spl_autoload
//should find all files in Framework/ that have 
//file names which match class definitions inside of them
//But this doesn't handle namespaces as well as PSR-4 autoloader will
//so composer is used to set up an autoload.php in /../vendor/
// spl_autoload_register(function ($class) {
//     $path = basePath('Framework/' . $class . '.php');
//     if (file_exists($path)) {
//         require $path;
//     }
// });


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

