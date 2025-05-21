<?php namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // redirect straight to /survey
        return redirect()->to('/survey');
    }
}
