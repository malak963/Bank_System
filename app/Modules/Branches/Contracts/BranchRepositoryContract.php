<?php

namespace App\Modules\Branches\Contracts;

use App\Modules\Branches\Models\Branch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BranchRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function summary(array $filters = []): array;

    public function create(array $data): Branch;

    public function update(Branch $branch, array $data): Branch;
}
