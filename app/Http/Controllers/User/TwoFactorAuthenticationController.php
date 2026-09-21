<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\TwoFactorAuthenticationController as BaseController;
use Illuminate\View\View;

class TwoFactorAuthenticationController extends Controller
{
    public function index(): View
    {
        return app(BaseController::class)->index();
    }
}
