<?php

namespace HolartWeb\HolartCMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard (Vue.js SPA).
     */
    public function index()
    {
        return view('holart-cms::dashboard');
    }
}
