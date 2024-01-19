<?php 

require '../helpers.php';   //this brings in the loadView() function

//loadView('home');   //this replaces require basePath('/Views/home.view.php');


//at first this will route to files
//but in a later refactor it will be able to reference controller classes
$routes = [

    '/' => 'controllers/home.php',
    '/listings' => 'controllers/listings/index.php',
    '/listings/create' => 'controllers/listings/create.php',
    '404' => 'controllers/error/404.php'

];



//basic router logic here:

$uri = $_SERVER['REQUEST_URI'];


if(array_key_exists($uri, $routes)) {
    require basePath($routes[$uri]);
} else {
    require basePath($routes['404']);
}