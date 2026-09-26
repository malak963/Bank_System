<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * Show the two-factor authentication management page.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user() ?? Auth::user();

        if (Auth::guard('admin')->check() || ($user instanceof Admin)) {
            return view('dashboard.pages.two-factor-auth', compact('user'));
        }

        if ($user && ($user->role === \App\Modules\Users\Enums\UserRole::Customer || (is_string($user->role) && $user->role === 'customer') || $user->role?->value === 'customer')) {
            return redirect()->route('portal.2fa');
        }

        return view('user.pages.two-factor-auth', compact('user'));
    }

    /**
     * Enable two-factor authentication for the authenticated user/admin.
     */
    public function store(Request $request, EnableTwoFactorAuthentication $enable): RedirectResponse
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user() ?? $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $enable($user, $request->boolean('force', false));

        // Sync secret and recovery codes to counterpart account if email matches
        $this->syncTwoFactorToCounterpart($user);

        return back()->with('status', 'two-factor-authentication-enabled');
    }

    /**
     * Disable two-factor authentication for the authenticated user/admin.
     */
    public function destroy(Request $request, DisableTwoFactorAuthentication $disable): RedirectResponse
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user() ?? $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $disable($user);

        // Sync disable to counterpart account
        $this->syncTwoFactorToCounterpart($user, true);

        return back()->with('status', 'two-factor-authentication-disabled');
    }

    /**
     * Regenerate emergency recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request, GenerateNewRecoveryCodes $generate): RedirectResponse
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user() ?? $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $generate($user);

        // Sync to counterpart account
        $this->syncTwoFactorToCounterpart($user);

        return back()->with('status', 'recovery-codes-generated');
    }

    /**
     * Synchronize 2FA credentials between Admin and User records sharing the same email.
     */
    protected function syncTwoFactorToCounterpart(mixed $user, bool $disable = false): void
    {
        if (empty($user->email)) {
            return;
        }

        if ($user instanceof Admin) {
            $counterpart = User::where('email', $user->email)->first();
        } elseif ($user instanceof User) {
            $counterpart = Admin::where('email', $user->email)->first();
        } else {
            return;
        }

        if (!$counterpart) {
            return;
        }

        if ($disable) {
            $counterpart->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])->save();
        } else {
            $counterpart->forceFill([
                'two_factor_secret' => $user->two_factor_secret,
                'two_factor_recovery_codes' => $user->two_factor_recovery_codes,
                'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
            ])->save();
        }
    }
}
