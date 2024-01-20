<?php

$config = require basePath('config/db.php');
$db = new Database($config);

//get the latest 6 listings into an array in order to pass it to loadView
$listings = $db->query("SELECT * FROM listings LIMIT 6;")->fetchAll();

loadView('home');