<?php

namespace App\Modules\CustomerPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cards\Enums\CardBrand;
use App\Modules\Cards\Enums\CardStatus;
use App\Modules\Cards\Enums\CardType;
use App\Modules\Cards\Models\Card;
use App\Modules\CustomerPortal\Services\CustomerPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerProfileController extends Controller
{
    public function __construct(
        protected CustomerPortalService $portalService
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $customer = $this->portalService->ensureCustomerProfile($user);

        // Load accounts and cards
        $customer->load(['accounts.accountType', 'branch', 'cards']);

        // If customer has account but no card, automatically generate active debit card
        if ($customer->accounts->isNotEmpty() && $customer->cards->isEmpty()) {
            $primaryAccount = $customer->accounts->first();
            Card::create([
                'card_number' => '4532' . rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
                'card_holder_name' => strtoupper($customer->full_name ?: $user->name),
                'card_type' => CardType::Debit,
                'card_brand' => CardBrand::Visa,
                'expiry_month' => (int) now()->addYears(3)->format('m'),
                'expiry_year' => (int) now()->addYears(3)->format('Y'),
                'cvv' => (string) rand(100, 999),
                'pin' => Hash::make('1234'),
                'status' => CardStatus::Active,
                'account_id' => $primaryAccount->id,
                'customer_id' => $customer->id,
                'daily_limit' => 5000,
                'monthly_limit' => 20000,
                'international_enabled' => true,
                'online_enabled' => true,
                'contactless_enabled' => true,
                'issued_at' => now(),
                'expires_at' => now()->addYears(3),
                'activated_at' => now(),
            ]);

            $customer->load('cards');
        }

        return view('customer-portal::profile', [
            'user' => $user,
            'customer' => $customer,
            'accounts' => $customer->accounts,
            'cards' => $customer->cards,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $customer = $this->portalService->ensureCustomerProfile($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        $user->save();

        if (isset($validated['address']) || isset($validated['phone'])) {
            if (isset($validated['address'])) {
                $customer->address = $validated['address'];
            }
            if (isset($validated['phone'])) {
                $customer->phone_number = $validated['phone'];
            }
            $customer->save();
        }

        return back()->with('status', __('Profile information updated successfully.'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', __('Password updated successfully.'));
    }
}
