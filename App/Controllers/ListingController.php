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
    public function show() {

        $id = $_GET['id'] ?? '1';

        //use a placeholder in the query string to filter out SQL injections in user inputs
        $query = "SELECT * FROM listings WHERE id=:id";

        //set params mapping to give to loadView for the prepared statement to bindValue with it
        $params = [
            'id' => $id
        ];

        $listing = $this->db->query($query, $params)->fetch();


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