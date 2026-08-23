<?php

namespace App\Modules\Customers\Services;

use App\Modules\Customers\Contracts\CustomerRepositoryContract;
use App\Modules\Customers\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function __construct(
        private CustomerRepositoryContract $customers
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->customers->paginate($filters, $perPage);
    }

    public function kycSummary(array $filters = []): array
    {
        return $this->customers->kycSummary($filters);
    }

    public function find(int $id): Customer
    {
        return $this->customers->findById($id);
    }

    public function create(array $data): Customer
    {
        return DB::transaction(
            fn (): Customer => $this->customers->create($data)
        );
    }

    public function update(Customer $customer, array $data): Customer
    {
        return DB::transaction(
            fn (): Customer => $this->customers->update($customer, $data)
        );
    }

    public function delete(Customer $customer): bool
    {
        return DB::transaction(
            fn (): bool => $this->customers->delete($customer)
        );
    }
}
