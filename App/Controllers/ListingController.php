<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\OwnershipAuthorization;

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

        $listings = $this->db->query("SELECT * FROM listings ORDER BY created_at DESC;")->fetchAll();

        loadView('Listings/index', [
            'listings' => $listings
        ]);
    }

    /**
     * Show a specific listing
     *
     * @param array $params
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


        //this listing gets associated with the ID of the authenticated user that made it
        $newListingData['user_id'] = Session::get('user')['id'];




        //another layer of security is to deactivate any html tags
        //that might have been placed into the form values

        //sanitize each value in newListingData
        //using 'sanitize' function in helpers.php
        $newListingData = array_map('sanitize', $newListingData);




        //now validation is done on it to make sure
        //that the required fields are not empty
        //and that they are for sure strings as well
        $requiredFields = ['title', 'description', 'salary', 'city', 'state', 'email'];

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

            //build up parts of the SQL query in a dynamic way

            //create a string of fields separated by comma and space to create the INSERT INTO arguments
            $sqlFields = [];

            //create a string of the placeholder text to put for each field separated by commas and spaces to make the VALUES arguments
            $sqlValues = [];

            foreach($newListingData as $field => $value) {

                $sqlFields[] = $field;
                $sqlValues[] = ':' . $field;

                //convert any empty string value to a null value
                if ($value === '') {
                    $newListingData[$field] = null;
                }

            }

            $sqlFields = implode(', ', $sqlFields);
            $sqlValues = implode(', ', $sqlValues);

            $query = "INSERT INTO listings ({$sqlFields}) VALUES ({$sqlValues});";

            //this query is handed in to the database object
            //and it will map the values in $newListingData
            //to the placeholders set in the $sqlValues string
            $this->db->query($query, $newListingData);

            //set flash message
            Session::setFlashMessage('success_message', 'Listing successfully created');

            //with the new database entry complete, redirect to listings page
            //but it has to be a redirect Header() method, 
            //as using loadView() on it was causing an error
            redirect('/listings');
        }

    }


    /**
     * Delete a listing
     * 
     * @param array $params
     * @return void
     */
    public function destroy($params) {

        //prepare a sql query with placeholder for id of listing to delete from listings

        //in this case $params should be ['id' => $id]

        $id = $params['id'];

        $query = "SELECT * FROM listings WHERE id = :id";

        //here as an explicit step to show intention
        $params = ['id' => $id];


        //check for listing first before deleting it
        $listing = $this->db->query($query, $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        //make sure that no one can delete this post if they're not the user in userId
        if(!OwnershipAuthorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this listing');
            return redirect('/listings/' . $listing->id);
        }


        //Delete listing if it was found
        $this->db->query("DELETE FROM listings WHERE id = :id", $params);

        //set flash message
        Session::setFlashMessage('success_message', 'Listing successfully deleted');

        redirect('/listings');
    }


    /**
     * Show the listing edit form
     *
     * @param array $params
     * @return void
     */
    public function edit($params = []) {

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

        //make sure that no one can edit this post if they're not the user in userId
        if(!OwnershipAuthorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to edit this listing');
            return redirect('/listings/' . $listing->id);
        }


        loadView('Listings/edit', [
            'listing' => $listing
        ]);
    }


    /**
     * Update an existing listing 
     * 
     * @param array $params
     * @return void
     */
    public function update($params) {

        
        //first it checks that the listing exists
        $id = $params['id'];

        $query = "SELECT * FROM listings WHERE id = :id";

        //here as an explicit overwrite step to show intention
        $params = ['id' => $id];

        //check for listing first before updating it
        $listing = $this->db->query($query, $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }
        
        //make sure that no one can edit this post if they're not the user in userId
        if(!OwnershipAuthorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to edit this listing');
            return redirect('/listings/' . $listing->id);
        }
        

        //now it has to get the values from $_POST but only the exact ones wanted
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $allowedFields = array_flip($allowedFields);

        //make an array from the values in $_POST, only where 
        //they have keys that are the same as the keys in $allowedFields
        $updateValues = array_intersect_key($_POST, $allowedFields);


        $updateValues = array_map('sanitize', $updateValues);



        $requiredFields = ['title', 'description', 'salary', 'city', 'state', 'email'];

        $errors = [];

        foreach($requiredFields as $field){
            if(empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }


        if (!empty($errors)) {

            //Reload view with errors listed
            loadView('listings/edit', [
                //this gives a list of error messages for the form to check for and display
                'errors' => $errors,
                //passing the listing object back into edit like this is compatible with the 
                //page but loses any valid updated edits which will have to be retyped
                //however this case will only arise if a user deletes a required field that
                //once was already correctly placed in the original creation and tries to save
                'listing' => $listing   
            ]);
            

        } else {

            //Submit update data to database

            $updateFields = [];

            foreach($updateValues as $field => $value) {

                $updateFields[] = "{$field} = :{$field}";

                //convert any empty string value to a null value
                if ($value === '') {
                    $updateValues[$field] = null;
                }

            }

            //now make a SQL UPDATE query with the field names and their :placeholder parts
            $updateFields = implode(', ', $updateFields);

            $query = "UPDATE listings SET {$updateFields} WHERE id=:id;";

            $updateValues['id'] = $id;
            
            $this->db->query($query, $updateValues);

            Session::setFlashMessage('success_message', 'Listing successfully updated');

            redirect('/listings/' . $id);
            
        }

    }
}