<?php

namespace Framework\Middleware;


use Framework\Session;


class Authorize {

    /**
     * Check if user is authenticated
     * 
     * @return bool returns true if the user is currently authenticated with an active session
     */
    public function isAuthenticated() {
        return Session::has('user');
    }

    /**
     * Handle the user's request
     *
     * @param string $role
     * @return bool
     */
    public function handle($role) {

        //if this user is authenticated but the route is to a page meant for guest
        //then redirect authenticated user to home page instead of guest page
        //if a page is meant for authenticated user but user is guest, redirect to login

        if($role === 'guest' && $this->isAuthenticated()) {

            return redirect('/');

        } 
        elseif ($role === 'auth' && !$this->isAuthenticated()) {
            
            return redirect('/auth/login');

        }

    }

}