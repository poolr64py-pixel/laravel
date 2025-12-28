<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        return redirect()->route('user-dashboard')->with('info', 'Feature management not available');
    }
}
