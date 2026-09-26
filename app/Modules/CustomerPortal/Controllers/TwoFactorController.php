<?php

namespace App\Modules\CustomerPortal\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;

class TwoFactorController extends Controller
{
    /**
     * Show the customer's dedicated two-factor authentication management page.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('customer-portal::two-factor', [
            'user' => $user,
        ]);
    }

    /**
     * Enable two-factor authentication for the customer.
     */
    public function enable(Request $request, EnableTwoFactorAuthentication $enable): RedirectResponse
    {
        $user = $request->user();
        $enable($user, $request->boolean('force', false));

        return redirect()->route('portal.2fa')
            ->with('success', __('Two-factor authentication has been enabled. Please scan the QR code below using your authenticator app.'));
    }

    /**
     * Disable two-factor authentication for the customer.
     */
    public function disable(Request $request, DisableTwoFactorAuthentication $disable): RedirectResponse
    {
        $user = $request->user();
        $disable($user);

        return redirect()->route('portal.2fa')
            ->with('success', __('Two-factor authentication has been disabled.'));
    }

    /**
     * Regenerate customer recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request, GenerateNewRecoveryCodes $generate): RedirectResponse
    {
        $user = $request->user();
        $generate($user);

        return redirect()->route('portal.2fa')
            ->with('success', __('New emergency recovery codes have been generated.'));
    }
}
