<?php

namespace App\Http\Controllers\KepalaPustaka;

use App\Http\Controllers\Controller;

class KepalaPustakaController extends Controller
{
    public function dashboard()
    {
        return view('kepalapustaka.dashboard');
    }
}
