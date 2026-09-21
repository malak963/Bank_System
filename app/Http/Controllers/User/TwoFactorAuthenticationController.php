<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthenticationController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user() ?? Auth::user();
        return view('user.pages.two-factor-auth', compact('user'));
    }
}
