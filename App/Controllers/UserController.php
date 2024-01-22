<?php


namespace App\Controllers;

use Framework\Database;
use Framework\Validation;


class UserController {

    protected $db;


    public function __construct() {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show the register screen
     * 
     * @return void
     */
    public function create(){
        loadView('users/create');
    }
 
    
    /**
     * Store a new user in database
     * 
     * @return void
     */
    public function store() {
        
        //sanitize the user input and validate it
        $user = [];
        //the fields are few enough that they can be given explicit variables
        //rather than using an array intersect
        //this method only runs in the POST context
        $user['name'] = $_POST['name'];
        $user['email'] = $_POST['email'];
        $user['city'] = $_POST['city'];
        $user['state'] = $_POST['state'];
        $user['password'] = $_POST['password'];
        $user['passwordConfirmation'] = $_POST['password_confirmation'];


        //make a new array of values that have had special html characters escaped
        $user = array_map('sanitize', $user);


        //check for valid required fields
        $errors = [];
        
        if(!Validation::email($user['email'])) {
            $errors['email'] = "Please enter a valid email address";
        }

        if(!Validation::string($user['name'], 2, 50)) {
            $errors['name'] = "Please enter a name between 2 and 50 characters long";
        }

        if(!Validation::string($user['password'], 8, 100)) {
            $errors['password'] = "Please enter a password at least 8 characters long";
        }

        if(!Validation::match($user['password'], $user['passwordConfirmation'])) {
            $errors['passwordConfirmation'] = "Password entries do not match";
        }


        if(!empty($errors)) {

            loadView('users/create', [
                
                'errors' => $errors,
                'user' => $user

            ]);

            exit;

        } else {

            inspectAndDie('store method');

        }



    }


     /**
     * Show the login screen
     * 
     * @return void
     */
    public function login(){
        loadView('users/login');
    }


}