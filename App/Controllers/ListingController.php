<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController {

    protected $db;

    public function __construct() {
        
        $const = require basePath('config/db.php');
        $this->db = new Database($const);

    }

    /**
     * Show all listings
     *
     * @return void
     */
    public function index() {

        $listings = $this->db->query("SELECT * FROM listings;")->fetchAll();

        loadView('Listings/index', [
            'listings' => $listings
        ]);
    }

    /**
     * Show a specific listing
     *
     * @return void
     */
    public function show($params = []) {

        $id = $params['id'] ?? '';

        //use a placeholder in the query string to filter out SQL injections in user inputs
        $query = "SELECT * FROM listings WHERE id=:id";

        //set params mapping to give to loadView for the prepared statement to bindValue with it
        //** when this method was modified to accept incoming params, 
        //this statement below became redundant
        //but it does enforce a fresh reset of $params to equal only the indended ones
        //and not accidentally contain other params that might confuse the prepared statement
        $params = [
            'id' => $id
        ];

        $listing = $this->db->query($query, $params)->fetch();


        if (!$listing) {
            ErrorController::notFound("Listing not found");
            return;
        }


        loadView('Listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Create a job post
     *
     * @return void
     */
    public function create() {

        loadView('Listings/create');
        
    }


    /**
     * Store data in database
     * 
     * @return void
     */
    public function store() {

        //The following fields array and array_intersect_key() call 
        //are one of a few layers of security in validating and sanitizing user input

        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        //flip this array so that the values are now the string keys
        $allowedFields = array_flip($allowedFields);

        //make an array from the values in $_POST, only where 
        //they have keys that are the same as the keys in $allowedFields
        $newListingData = array_intersect_key($_POST, $allowedFields);

        //now the only values in $_POST being looked at are ones with
        //expected keys which can then be validated to be the right kinds of inputs


        //this is hard coded to 1 until the authentication is added
        //and then it can be set to the active user's id
        $newListingData['user_id'] = 1;




        //another layer of security is to deactivate any html tags
        //that might have been placed into the form values

        //sanitize each value in newListingData
        //using 'sanitize' function in helpers.php
        $newListingData = array_map('sanitize', $newListingData);




        //now validation is done on it to make sure
        //that the required fields are not empty
        //and that they are for sure strings as well
        $requiredFields = ['title', 'description', 'city', 'state', 'email'];

        $errors = [];

        foreach($requiredFields as $field){
            if(empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }


        if (!empty($errors)) {

            //Reload view with errors listed
            loadView('listings/create', [
                //this gives a list of error messages for the form to check for and display
                'errors' => $errors,
                //this gives the form the sanitized listing data so it doesn't have to be retyped by user 
                'listing' => $newListingData
            ]);

        } else {

            //Submit data
            echo 'Success';

        }

    }

}