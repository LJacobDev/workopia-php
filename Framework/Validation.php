<?php

namespace Framework;

class Validation {




    /**
     * Validate a string data type as well its as min and max lengh
     * 
     * @param string $value
     * @param int $min
     * @param int $max inclusive
     * @return bool
     */

     public static function string($value, $min = 1, $max = INF){

        if(is_string($value)){
            $value = trim($value);
            $length = strlen($value);

            return $length >= $min && $length <= $max;
        }
        
        return false;
    }


    /**
     * Validate an e-mail
     * 
     * @param string $value
     * @return mixed returns false if not valid, returns email if valid
     */
    public static function email($value) {

        $value = trim($value);

        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }


    /**
     * Match a value against another
     * @param string $value1
     * @param string $value2
     * @return bool
     */
    public static function match($value1, $value2) {
        $value1 = trim($value1);
        $value2 = trim($value2);

        return $value1 === $value2;
    }


}


