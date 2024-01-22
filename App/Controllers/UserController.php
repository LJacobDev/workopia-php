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

            $errors['useremail'] = "That email address is already being used";
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
     * Log out user and end session
     * 
     * @return void
     */
    public function logout() {
        
        
        Session::clearAllAndDestroy();
        
        //also clear out session ID cookie
        $params = session_get_cookie_params();

        //set cookie to empty and to expire 1 year ago
        setcookie('PHPSESSID', '', time() - 86400, $params['path'], $params['domain']);

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

     /**
     * Authenticate the user with email and password
     * 
     * @return void
     */
    public function authenticate(){

        $email = $_POST['email'];
        $password = $_POST['password'];

        //these two inputs seem to not need to be escaped because they will not be put on display
        //so passing them through sanitize to remove element tags seems to not be needed this time


        $errors = [];

        //validate email
        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email';
        }

        //validate password length
        if (!Validation::string($password, 8, 100)) {
            $errors['password'] = 'Password must be at least 8 characters';
        }

        //error check before querying database
        if(!empty($errors)) {

            loadView('users/login', [
                'errors' => $errors
                //DO NOT SEND EMAIL TO BE DISPLAYED
                //because it is not sanitized yet this time
            ]);
            
            exit;
        }





        //now that there are no errors
        //see if the email exists in the users table

        $query = "SELECT * FROM users WHERE email = :email";
        $params = [
            'email' => $email
        ];
        
        $user = $this->db->query($query, $params)->fetch();


        //both email and password being wrong should generate 
        //the same error message, so that it can't be guessed at by people probing it
        $incorrectEmailPasswordMessage = "Incorrect credentials";

        //error check of whether a user was found
        if (!$user) {
            $errors['auth'] = $incorrectEmailPasswordMessage;

            loadView('users/login', [
                'errors' => $errors
                //DO NOT SEND EMAIL TO BE DISPLAYED
                //because it is not sanitized yet this time
            ]);
            
            exit;
        }



        //now that a user has been found, check password hash
        //against the currently entered password

        if (!password_verify($password, $user->password)) {

            $errors['auth'] = $incorrectEmailPasswordMessage;

            loadView('users/login', [
                'errors' => $errors
                //DO NOT SEND EMAIL TO BE DISPLAYED
                //because it is not sanitized yet this time
            ]);
            
            exit;
        }

        
        //if no errors then consider authenticated and set a session
        if(empty($errors)) {


            //using 'user' as the session key for this user but something else might be better
            Session::set('user', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'city' => $user->city,
                'state' => $user->state
            ]);
            


            redirect('/');

        }

    }


}