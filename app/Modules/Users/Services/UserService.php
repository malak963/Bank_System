<?php

namespace App\Modules\Users\Services;

use App\Models\User;
use App\Modules\Users\Contracts\UserRepositoryContract;
use App\Modules\Users\Enums\UserRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private UserRepositoryContract $users
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->users->paginate($perPage);
    }

    public function find(int $id): User
    {
        return $this->users->findById($id);
    }

    public function create(array $data): User
    {
        return DB::transaction(
            fn (): User => $this->users->create($data)
        );
    }

    public function update(User $user, array $data, User $actor): User
    {
        $this->guardAgainstUnsafeAccountChanges($user, $data, $actor);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return DB::transaction(
            fn (): User => $this->users->update($user, $data)
        );
    }

    public function deactivate(User $user, User $actor): User
    {
        $this->guardAgainstSelfDeactivation($user, $actor);
        $this->guardAgainstRemovingLastActiveAdmin($user, [
            'is_active' => false,
        ]);

        return DB::transaction(
            fn (): User => $this->users->deactivate($user)
        );
    }

    private function guardAgainstUnsafeAccountChanges(User $user, array $data, User $actor): void
    {
        if ($user->is($actor) && array_key_exists('is_active', $data) && ! (bool) $data['is_active']) {
            $this->throwUnsafeAccountChange();
        }

        $this->guardAgainstRemovingLastActiveAdmin($user, $data);
    }

    private function guardAgainstSelfDeactivation(User $user, User $actor): void
    {
        if ($user->is($actor)) {
            $this->throwUnsafeAccountChange();
        }
    }

    private function guardAgainstRemovingLastActiveAdmin(User $user, array $data): void
    {
        if ($user->role !== UserRole::Admin || $this->users->countActiveByRole(UserRole::Admin) > 1) {
            return;
        }

        $roleWillChange = array_key_exists('role', $data) && $data['role'] !== UserRole::Admin->value;
        $willDeactivate = array_key_exists('is_active', $data) && ! (bool) $data['is_active'];

        if ($roleWillChange || $willDeactivate) {
            $this->throwUnsafeAccountChange();
        }
    }

    private function throwUnsafeAccountChange(): never
    {
        throw ValidationException::withMessages([
            'user' => 'This account change would remove required administrative access.',
        ]);
    }
}
