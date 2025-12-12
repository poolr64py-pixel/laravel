<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function upversion()
    {
        return redirect()->route('admin.dashboard')->with('warning', 'Update feature not available');
    }
}
