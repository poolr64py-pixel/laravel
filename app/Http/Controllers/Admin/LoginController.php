<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        $data['bs'] = \App\Models\BasicSetting::first();
        return view('admin.login', $data);
    }

    public function authenticate(Request $request)
    {
        \Log::info('🔐 Login attempt', [
            'username' => $request->username,
            'has_password' => !empty($request->password)
        ]);

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $attempt = Auth::guard('admin')->attempt([
            'username' => $request->username,
            'password' => $request->password
        ]);

        \Log::info('🔐 Auth attempt result', [
            'success' => $attempt,
            'authenticated' => Auth::guard('admin')->check()
        ]);

        if ($attempt) {
            $request->session()->regenerate();
            \Log::info('✅ Login SUCCESS, redirecting to dashboard');
            return redirect()->intended(route('admin.dashboard'));
        }

        \Log::info('❌ Login FAILED');
        return redirect()->back()->with('alert', __('Username and Password Not Matched'));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
