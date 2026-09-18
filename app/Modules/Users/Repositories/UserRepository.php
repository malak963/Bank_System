<?php

namespace App\Modules\Users\Repositories;

use App\Models\User;
use App\Modules\Users\Contracts\UserRepositoryContract;
use App\Modules\Users\Enums\UserRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryContract
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->latest('id')
            ->paginate($perPage);
    }

    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user->refresh();
    }

    public function deactivate(User $user): User
    {
        $user->forceFill(['is_active' => false])->save();

        return $user->refresh();
    }

    public function countActiveByRole(UserRole $role): int
    {
        return User::query()
            ->where('role', $role->value)
            ->where('is_active', true)
            ->count();
    }
}
