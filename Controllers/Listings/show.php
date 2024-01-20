<?php

$config = require basePath('Config/db.php');
$db = new Database($config);

$id = $_GET['id'] ?? '1';

//use a placeholder in the query string to filter out SQL injections in user inputs
$query = "SELECT * FROM listings WHERE id=:id";

//set params mapping to give to loadView for the prepared statement to bindValue with it
$params = [
    'id' => $id
];

$listing = $db->query($query, $params)->fetch();


loadView('Listings/show', [
    'listing' => $listing
]);

echo "successfully loaded view";