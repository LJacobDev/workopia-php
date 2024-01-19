<?php 

require '../helpers.php';   //this brings in the loadView() function



$uri = $_SERVER['REQUEST_URI'];

//run router.php which gathers routes from routes.php and checks how $uri relates to them
require basePath('router.php');
