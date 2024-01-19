<?php


//the $router variable is presumed to be created already in index.php by the time this is running
//these method callss are adding multiple valid routes to the $router->routes property
$router->get('/', 'controllers/home.php');
$router->get('/listings', 'controllers/listings/index.php');
$router->get('/listings/create', 'controllers/listings/create.php');