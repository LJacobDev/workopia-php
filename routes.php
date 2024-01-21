<?php

//the new way to use controller classes with the refactored router:
$router->get('/', 'HomeController@index');
$router->get('/listing/{id}', 'ListingController@show');
$router->get('/listings', 'ListingController@index');
$router->get('/listings/create', 'ListingController@create');

$router->post('/listings', 'ListingController@store');