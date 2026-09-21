<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthenticationController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user() ?? Auth::user();
        return view('dashboard.pages.two-factor-auth', compact('user'));
    }

    public function enable(Request $request)
    {
        $user = $request->user('admin') ?? $request->user();
        $user->two_factor_confirmed_at = now();
        $user->save();
        return redirect()->route('dashboard')->with('success', 'Two factor authentication enabled successfully');
    }
}
