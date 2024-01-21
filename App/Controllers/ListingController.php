<?php

namespace App\Controllers;

use Framework\Database;

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

}