<?php

namespace App\Modules\Installments\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InstallmentRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function summary(): array;
}
