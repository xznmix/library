<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;

class PimpinanController extends Controller
{
    public function dashboard()
    {
        return view('pimpinan.dashboard');
    }
}
