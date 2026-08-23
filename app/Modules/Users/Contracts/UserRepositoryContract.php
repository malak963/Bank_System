<?php

namespace App\Modules\Users\Contracts;

use App\Models\User;
use App\Modules\Users\Enums\UserRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryContract
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function deactivate(User $user): User;

    public function countActiveByRole(UserRole $role): int;
}
