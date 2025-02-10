<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
  public function index()
  {
    $user = auth()->user();

    // Pass the authenticated user to the view
    return view('dashboard.index', compact('user'));
  }
}
