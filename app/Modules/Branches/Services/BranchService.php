<?php

namespace App\Modules\Branches\Services;

use App\Modules\Branches\Contracts\BranchRepositoryContract;
use App\Modules\Branches\Enums\BranchStatus;
use App\Modules\Branches\Models\Branch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BranchService
{
    public function __construct(
        private BranchRepositoryContract $branches,
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->branches->paginate($filters, $perPage);
    }

    public function summary(array $filters = []): array
    {
        return $this->branches->summary($filters);
    }

    public function create(array $data): Branch
    {
        return DB::transaction(function () use ($data): Branch {
            return $this->branches->create([
                'code' => $data['code'],
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'manager_id' => $data['manager_id'] ?? null,
                'status' => BranchStatus::Open,
                'opened_at' => now(),
            ]);
        });
    }

    public function open(Branch $branch): Branch
    {
        $this->assertStatus($branch, BranchStatus::Closed, 'Only closed branches can be opened.');

        return $this->transition($branch, [
            'status' => BranchStatus::Open,
            'opened_at' => now(),
            'closed_at' => null,
        ]);
    }

    public function close(Branch $branch): Branch
    {
        if ($branch->status === BranchStatus::Closed) {
            throw ValidationException::withMessages(['branch' => 'The branch is already closed.']);
        }

        return $this->transition($branch, [
            'status' => BranchStatus::Closed,
            'closed_at' => now(),
        ]);
    }

    public function update(Branch $branch, array $data): Branch
    {
        return DB::transaction(fn (): Branch => $this->branches->update($branch, $data));
    }

    private function transition(Branch $branch, array $data): Branch
    {
        return DB::transaction(fn (): Branch => $this->branches->update($branch, $data));
    }

    private function assertStatus(Branch $branch, BranchStatus $expected, string $message): void
    {
        if ($branch->status !== $expected) {
            throw ValidationException::withMessages(['branch' => $message]);
        }
    }
}
