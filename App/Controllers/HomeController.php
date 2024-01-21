<?php


namespace App\Controllers;

use Framework\Database;


class HomeController {

    protected $db;

    public function __construct() {

        $config = require basePath('config/db.php');
        $this->db = new Database($config);

    }

    /**
     * Show home page
     *
     * @return void
     */
    public function index() {
        
        //get the latest 6 listings into an array in order to pass it to loadView
        $listings = $this->db->query("SELECT * FROM listings ORDER BY created_at DESC LIMIT 6;")->fetchAll();
        
        loadView('home', [
            'listings' => $listings
        ]);   
    }
        
}