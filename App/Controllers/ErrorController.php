<?php

namespace App\Controllers;

class ErrorController {

    /**
     * 404 not found error
     *
     * @param string $message
     * @return void
     */
    public static function notFound($message = 'Resource not found') {
        http_response_code(404);
        loadView('error', [
            'status' => '404',
            'message' => $message
        ]);
    }
    
    /**
     * 403 not found error
     *
     * @param string $message
     * @return void
     */
    public static function notAuthorized($message = 'Not authorized') {
        http_response_code(403);
        loadView('error', [
            'status' => '403',
            'message' => $message
        ]);

    }

}