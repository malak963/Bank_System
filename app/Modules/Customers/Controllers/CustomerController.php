<?php

namespace App\Modules\Customers\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Customers\Enums\CustomerStatus;
use App\Modules\Customers\Enums\IdentityDocumentType;
use App\Modules\Customers\Enums\KycStatus;
use App\Modules\Customers\Enums\RiskLevel;
use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Requests\IndexCustomerRequest;
use App\Modules\Customers\Requests\StoreCustomerRequest;
use App\Modules\Customers\Requests\UpdateCustomerRequest;
use App\Modules\Customers\Services\CustomerService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $service
    ) {}

    public function index(IndexCustomerRequest $request): View
    {
        $filters = $request->filters();

        return view(
            'customers::index',
            [
                'customers' => $this->service->paginate($filters),
                'filters' => $filters,
                'kycSummary' => $this->service->kycSummary($filters),
                'customerStatuses' => CustomerStatus::cases(),
                'kycStatuses' => KycStatus::cases(),
                'riskLevels' => RiskLevel::cases(),
                'identityDocumentTypes' => IdentityDocumentType::cases(),
            ]
        );
    }

    public function create(): View
    {
        return view('customers::create', $this->formData());
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $this->service->create($this->completeKycReviewData(
            $request->validated(),
            (int) $request->user()->id
        ));

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', 'Customer created.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['user', 'kycReviewer']);

        return view('customers::show', [
            'customer' => $customer,
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('customers::edit', [
            'customer' => $customer,
            ...$this->formData($customer),
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer = $this->service->update($customer, $this->completeKycReviewData(
            $request->validated(),
            (int) $request->user()->id
        ));

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->service->delete($customer);

        return redirect()
            ->route('customers.index')
            ->with('status', 'Customer deleted.');
    }

    private function formData(?Customer $customer = null): array
    {
        return [
            'users' => User::query()
                ->where(function (Builder $query) use ($customer): void {
                    $query->whereDoesntHave('customer', function (Builder $query): void {
                        $query->withTrashed();
                    });

                    if ($customer !== null) {
                        $query->orWhereKey($customer->user_id);
                    }
                })
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'customerStatuses' => CustomerStatus::cases(),
            'kycStatuses' => KycStatus::cases(),
            'riskLevels' => RiskLevel::cases(),
            'identityDocumentTypes' => IdentityDocumentType::cases(),
            'kycReviewers' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ];
    }

    private function completeKycReviewData(array $data, int $reviewerId): array
    {
        if (($data['kyc_status'] ?? null) === KycStatus::Pending->value) {
            $data['kyc_reviewed_by'] = null;
            $data['kyc_reviewed_at'] = null;
            $data['kyc_rejection_reason'] = null;

            return $data;
        }

        if (in_array($data['kyc_status'] ?? null, [
            KycStatus::Approved->value,
            KycStatus::Rejected->value,
        ], true)) {
            $data['kyc_reviewed_by'] = $data['kyc_reviewed_by'] ?? $reviewerId;
            $data['kyc_reviewed_at'] = $data['kyc_reviewed_at'] ?? now();
        }

        if (($data['kyc_status'] ?? null) === KycStatus::Approved->value) {
            $data['kyc_rejection_reason'] = null;
        }

        return $data;
    }
}
