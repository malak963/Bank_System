<?php

namespace App\Modules\Accounts\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AccountTypes\Enums\AccountTypeStatus;
use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Accounts\Models\Account;
use App\Modules\Accounts\Requests\FreezeAccountRequest;
use App\Modules\Accounts\Requests\IndexAccountRequest;
use App\Modules\Accounts\Requests\StoreAccountRequest;
use App\Modules\Accounts\Services\AccountService;
use App\Modules\Customers\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    public function __construct(private AccountService $service) {}

    public function index(IndexAccountRequest $request): View
    {
        $filters = $request->filters();

        return view('accounts::index', [
            'accounts' => $this->service->paginate($filters),
            'filters' => $filters,
            'summary' => $this->service->summary($filters),
            'statuses' => AccountStatus::cases(),
            'accountTypes' => AccountType::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('accounts::create', [
            'customers' => Customer::query()
                ->whereHas('user')
                ->where('status', '!=', 'closed')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'accountTypes' => AccountType::query()
                ->where('status', AccountTypeStatus::Active->value)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $account = $this->service->create($request->validated());

        return redirect()->route('accounts.show', $account)->with('status', 'Account created and opened.');
    }

    public function show(Account $account): View
    {
        $account->load(['customer.user', 'accountType']);

        return view('accounts::show', ['account' => $account]);
    }

    public function open(Account $account): RedirectResponse
    {
        $this->service->open($account);

        return redirect()->route('accounts.show', $account)->with('status', 'Account opened.');
    }

    public function close(Account $account): RedirectResponse
    {
        $this->service->close($account);

        return redirect()->route('accounts.show', $account)->with('status', 'Account closed.');
    }

    public function freeze(FreezeAccountRequest $request, Account $account): RedirectResponse
    {
        $this->service->freeze($account, $request->validated('freeze_reason'));

        return redirect()->route('accounts.show', $account)->with('status', 'Account frozen.');
    }

    public function reactivate(Account $account): RedirectResponse
    {
        $this->service->reactivate($account);

        return redirect()->route('accounts.show', $account)->with('status', 'Account reactivated.');
    }
}
