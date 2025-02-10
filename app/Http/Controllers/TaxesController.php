<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class TaxesController extends Controller
{

    public function index()
    {
        return view('taxes.index');
    }


}
