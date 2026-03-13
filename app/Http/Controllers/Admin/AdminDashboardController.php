<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $tests = \App\Models\Test::with('collection')->get();
        return view('admin.dashboard', compact('tests'));
    }
}
