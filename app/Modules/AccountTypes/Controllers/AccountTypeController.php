<?php

namespace App\Modules\AccountTypes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AccountTypes\Enums\AccountTypeStatus;
use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\AccountTypes\Requests\StoreAccountTypeRequest;
use App\Modules\AccountTypes\Requests\UpdateAccountTypeRequest;
use App\Modules\AccountTypes\Services\AccountTypeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AccountTypeController extends Controller
{
    public function __construct(private AccountTypeService $service) {}

    public function index(): View
    {
        return view('account_types::index', [
            'accountTypes' => $this->service->paginate(),
        ]);
    }

    public function create(): View
    {
        return view('account_types::create', ['statuses' => AccountTypeStatus::cases()]);
    }

    public function store(StoreAccountTypeRequest $request): RedirectResponse
    {
        $accountType = $this->service->create($request->validated());

        return redirect()->route('account-types.index', absolute: false)->with('status', 'Account type created.');
    }

    public function edit(AccountType $account_type): View
    {
        return view('account_types::edit', [
            'accountType' => $account_type,
            'statuses' => AccountTypeStatus::cases(),
        ]);
    }

    public function update(UpdateAccountTypeRequest $request, AccountType $account_type): RedirectResponse
    {
        $this->service->update($account_type, $request->validated());

        return redirect()->route('account-types.index', absolute: false)->with('status', 'Account type updated.');
    }

    public function destroy(AccountType $account_type): RedirectResponse
    {
        $this->service->delete($account_type);

        return redirect()->route('account-types.index', absolute: false)->with('status', 'Account type deleted.');
    }
}
