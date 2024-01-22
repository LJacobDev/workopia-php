<?php

//the new way to use controller classes with the refactored router:

$router->get('/', 'HomeController@index');

$router->get('/listings', 'ListingController@index');
$router->get('/listings/create', 'ListingController@create', ['auth']);
$router->get('/listings/edit/{id}', 'ListingController@edit', ['auth']);

//In order for the search to work it seems to need to be above /listings/{id}
$router->get('/listings/search', 'ListingController@search');   
$router->get('/listings/{id}', 'ListingController@show');

$router->post('/listings', 'ListingController@store', ['auth']);
$router->put('/listings/{id}', 'ListingController@update', ['auth']);
$router->delete('/listings/{id}', 'ListingController@destroy', ['auth']);

$router->get('/auth/register', 'UserController@create', ['guest']);
$router->post('/auth/register', 'UserController@store', ['guest']);

$router->get('/auth/login', 'UserController@login', ['guest']);
$router->post('/auth/login', 'UserController@authenticate', ['guest']);

$router->post('/auth/logout', 'UserController@logout', ['auth']);