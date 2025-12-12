<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MidtransBankNotifyController extends Controller
{
    public function bank_notify(Request $request)
    {
        // TODO: Implementar lógica do Midtrans
        return response()->json(['status' => 'received']);
    }
    
    public function cancel(Request $request)
    {
        return redirect()->route('front.index')->with('error', 'Payment cancelled');
    }
}
