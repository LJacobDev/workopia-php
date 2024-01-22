<?php

namespace Framework;


class Session {

    /**
     * Start the session
     * 
     * @return void
     */
    public static function start() {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

    }

    
    /**
     * Set a session key/value pair
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }



    /**
     * Get a session value by the key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed returns value if key is found, or returns null by default, or can optionally return other specified value instead of null
     */
    public static function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Check if key exists in session
     * 
     * @param string $key
     * @return bool
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }


    /**
     * Clear session by key
     * 
     * @param string $key
     * @return void
     */
    public static function clear($key) {
        if(isset($_SESSION[$key])) {
           unset($_SESSION[$key]);
        }
    }

    /**
     * Clear all session data and destroy session
     * 
     * @return void
     */
    public static function clearAllAndDestroy() {
        session_unset();
        session_destroy();
    }

}