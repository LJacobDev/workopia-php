<?php


namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

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
        $user['password_confirmation'] = $_POST['password_confirmation'];


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

        if(!Validation::match($user['password'], $user['password_confirmation'])) {
            $errors['password_confirmation'] = "Password entries do not match";
        }


        if(!empty($errors)) {

            loadView('users/create', [
                
                'errors' => $errors,
                'user' => $user

            ]);

            exit;
        } 


        //ensure that there is no other user with the same email address

        //and if not, add them to the users table in the database

        $query = "SELECT * FROM users WHERE email = :email";
        $params = [
            'email' => $user['email']
        ];

        $emailExistsAlready = $this->db->query($query, $params)->fetch();

        if ($emailExistsAlready) {

            $errors['useremail'] = "That email address is already associated with an existing account";
            loadView('users/create', [

                'errors' => $errors,
                'user' => $user

            ]);

            exit;
        } 



            //unset the password_confirmation field in $user and it will match the columns in the table
            unset($user['password_confirmation']);

            //turn the user's password into a hash of that password before storing it
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);



            //at this point data is ready to insert

            $userFields = [];
            $userPlaceholders = [];


            foreach($user as $field => $value){

                $userFields[] = $field;
                $userPlaceholders[] = ':' . $field;

                if($value === ''){
                    $user[$field] = null;
                }
            }

            $userFields = implode(', ', $userFields);
            $userPlaceholders = implode(', ', $userPlaceholders);

            $query = "INSERT INTO users ({$userFields}) VALUES ({$userPlaceholders});";


            //running the query and passing in the user array itself as the 
            //keys and values for the placeholder prepared params
            $this->db->query($query, $user);



            //now a session for the user will be created but the user ID isn't known
            //however the db->conn property can give a last insert ID which will have it
            $userID = $this->db->conn->lastInsertId();

            //using 'user' as the session key for this user but something else might be better
            Session::set('user', [
                'id' => $userID,
                'name' => $user['name'],
                'email' => $user['email'],
                'city' => $user['city'],
                'state' => $user['state']
            ]);
            


            redirect('/');


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