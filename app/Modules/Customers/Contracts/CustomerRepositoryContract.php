<?php

namespace App\Modules\Customers\Contracts;

use App\Modules\Customers\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function kycSummary(array $filters = []): array;

    public function findById(int $id): Customer;

    public function create(array $data): Customer;

    public function update(Customer $customer, array $data): Customer;

    public function delete(Customer $customer): bool;
}
