<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        session()->destroy();
        return view('authentication');
    }
}
