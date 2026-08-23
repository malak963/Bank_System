<?php

namespace App\Modules\Customers\Repositories;

use App\Modules\Customers\Contracts\CustomerRepositoryContract;
use App\Modules\Customers\Enums\KycStatus;
use App\Modules\Customers\Enums\RiskLevel;
use App\Modules\Customers\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CustomerRepository implements CustomerRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['user', 'kycReviewer'])
            ->paginate($perPage)
            ->withQueryString();
    }

    public function kycSummary(array $filters = []): array
    {
        $query = $this->filteredQuery($filters, false);

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('kyc_status', KycStatus::Pending->value)->count(),
            'approved' => (clone $query)->where('kyc_status', KycStatus::Approved->value)->count(),
            'rejected' => (clone $query)->where('kyc_status', KycStatus::Rejected->value)->count(),
            'high_risk' => (clone $query)->where('risk_level', RiskLevel::High->value)->count(),
            'expiring_documents' => (clone $query)
                ->whereNotNull('identity_document_expires_at')
                ->whereDate('identity_document_expires_at', '<=', now()->addDays(30)->toDateString())
                ->count(),
        ];
    }

    public function findById(int $id): Customer
    {
        return Customer::findOrFail($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->fill($data);
        $customer->save();

        return $customer->refresh();
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }

    private function filteredQuery(array $filters = [], bool $applySort = true): Builder
    {
        $query = Customer::query();

        $this->applySearch($query, $filters['search'] ?? null);

        foreach ([
            'status',
            'kyc_status',
            'risk_level',
            'identity_document_type',
            'identity_document_country',
        ] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        if (! empty($filters['verified_from'])) {
            $query->whereDate('kyc_reviewed_at', '>=', $filters['verified_from']);
        }

        if (! empty($filters['verified_to'])) {
            $query->whereDate('kyc_reviewed_at', '<=', $filters['verified_to']);
        }

        if ($applySort) {
            $this->applySort($query, $filters['sort'] ?? 'latest');
        }

        return $query;
    }

    private function applySearch(Builder $query, ?string $search): void
    {
        if ($search === null || trim($search) === '') {
            return;
        }

        $like = '%'.mb_strtolower(trim($search)).'%';

        $query->where(function (Builder $query) use ($like): void {
            $query
                ->whereRaw('LOWER(customer_number) LIKE ?', [$like])
                ->orWhereRaw('LOWER(first_name) LIKE ?', [$like])
                ->orWhereRaw('LOWER(last_name) LIKE ?', [$like])
                ->orWhereRaw('LOWER(national_id) LIKE ?', [$like])
                ->orWhereRaw('LOWER(identity_document_number) LIKE ?', [$like])
                ->orWhereRaw('LOWER(kyc_reference) LIKE ?', [$like])
                ->orWhereRaw('LOWER(phone_number) LIKE ?', [$like])
                ->orWhereHas('user', function (Builder $query) use ($like): void {
                    $query
                        ->whereRaw('LOWER(name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$like]);
                });
        });
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'name' => $query->orderBy('first_name')->orderBy('last_name')->orderByDesc('id'),
            'kyc_oldest' => $query->orderByRaw('kyc_reviewed_at IS NULL')->orderBy('kyc_reviewed_at')->orderByDesc('id'),
            'verification_newest' => $query->orderByDesc('kyc_reviewed_at')->orderByDesc('id'),
            'risk' => $query
                ->orderByRaw("CASE risk_level WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
                ->orderByDesc('id'),
            'document_expiry' => $query
                ->orderByRaw('identity_document_expires_at IS NULL')
                ->orderBy('identity_document_expires_at')
                ->orderByDesc('id'),
            default => $query->latest('id'),
        };
    }
}
