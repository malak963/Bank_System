<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Users\Enums\UserRole;
use App\Modules\Users\Requests\StoreUserRequest;
use App\Modules\Users\Requests\UpdateUserRequest;
use App\Modules\Users\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserService $service
    ) {}

    public function index(): View
    {
        return view('users::index', [
            'users' => $this->service->paginate(),
        ]);
    }

    public function create(): View
    {
        return view('users::create', $this->formData());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->service->create($request->validated());

        return redirect()
            ->route('users.show', $user)
            ->with('status', 'User created.');
    }

    public function show(User $user): View
    {
        return view('users::show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        return view('users::edit', [
            'user' => $user,
            ...$this->formData(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $user = $this->service->update($user, $request->validated(), $actor);

        return redirect()
            ->route('users.show', $user)
            ->with('status', 'User updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $this->service->deactivate($user, $actor);

        return redirect()
            ->route('users.index')
            ->with('status', 'User deactivated.');
    }

    private function formData(): array
    {
        return [
            'roles' => UserRole::cases(),
        ];
    }
}
