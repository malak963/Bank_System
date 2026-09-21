<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming admin authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if (isset($admin->status) && !$admin->status) {
            throw ValidationException::withMessages([
                'email' => [__('This admin account is deactivated.')],
            ]);
        }

        // Check if two-factor authentication is active on this admin account
        if (!empty($admin->two_factor_secret)) {
            $request->session()->put([
                'admin.login.id' => $admin->id,
                'admin.login.remember' => $request->boolean('remember'),
            ]);

            return redirect()->route('admin.two-factor.login');
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    /**
     * Show the admin two-factor authentication challenge form.
     */
    public function createTwoFactor(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('admin.login.id')) {
            return redirect()->route('admin.login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the admin two-factor authentication challenge.
     */
    public function storeTwoFactor(Request $request): RedirectResponse
    {
        if (!$request->session()->has('admin.login.id')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        $admin = Admin::findOrFail($request->session()->get('admin.login.id'));

        $authenticated = false;

        if ($request->filled('code')) {
            $provider = app(TwoFactorAuthenticationProvider::class);
            $secret = decrypt($admin->two_factor_secret);
            if ($provider->verify($secret, $request->code)) {
                $authenticated = true;
            }
        } elseif ($request->filled('recovery_code')) {
            $recoveryCodes = $admin->recoveryCodes();
            if (is_array($recoveryCodes) && in_array($request->recovery_code, $recoveryCodes)) {
                $admin->replaceRecoveryCode($request->recovery_code);
                $authenticated = true;
            }
        }

        if (!$authenticated) {
            throw ValidationException::withMessages([
                'code' => [__('The provided two-factor authentication code was invalid.')],
            ]);
        }

        $remember = $request->session()->get('admin.login.remember', false);
        $request->session()->forget(['admin.login.id', 'admin.login.remember']);

        Auth::guard('admin')->login($admin, $remember);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy an authenticated admin session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
