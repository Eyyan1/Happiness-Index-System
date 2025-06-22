<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Home extends Controller
{
    public function index()
    {
        // If not logged in, send them to the login page
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // If logged in, send user→survey, admin→survey (or wherever makes sense)
        return redirect()->to('/survey');
    }
}
