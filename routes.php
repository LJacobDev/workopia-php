<?php

//the new way to use controller classes with the refactored router:
$router->get('/', 'HomeController@index');
$router->get('/listings', 'ListingController@index');
$router->get('/listings/create', 'ListingController@create');
$router->get('/listings/{id}', 'ListingController@show');

$router->post('/listings', 'ListingController@store');

$router->delete('/listings/{id}', 'ListingController@destroy');