<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OfferBannerController extends Controller
{
    public function index()
    {
        return redirect()->route('user-dashboard')->with('info', 'Offer banner management not available');
    }
}
