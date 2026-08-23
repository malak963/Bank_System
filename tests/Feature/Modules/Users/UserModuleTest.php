<?php

namespace Tests\Feature\Modules\Users;

use App\Models\User;
use App\Modules\Users\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_routes_require_authentication(): void
    {
        $this->get('/users')
            ->assertRedirect('/login');
    }

    public function test_customer_role_can_not_manage_users(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Customer,
        ]);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_users_index(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Users');
    }

    public function test_admin_can_create_user(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Operations Manager',
                'email' => 'manager@example.com',
                'phone' => '+963944111222',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => UserRole::Manager->value,
                'is_active' => true,
            ]);

        $user = User::query()
            ->where('email', 'manager@example.com')
            ->firstOrFail();

        $response->assertRedirect(route('users.show', $user));
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertDatabaseHas('users', [
            'email' => 'manager@example.com',
            'phone' => '+963944111222',
            'role' => UserRole::Manager->value,
            'is_active' => true,
        ]);
    }

    public function test_user_validation_rejects_duplicate_identity_and_invalid_role(): void
    {
        $admin = $this->admin();

        User::factory()->create([
            'email' => 'taken@example.com',
            'phone' => '+963944111222',
        ]);

        $this->actingAs($admin)
            ->from(route('users.create'))
            ->post(route('users.store'), [
                'name' => 'Duplicate User',
                'email' => 'taken@example.com',
                'phone' => '+963944111222',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'owner',
                'is_active' => true,
            ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors([
                'email',
                'phone',
                'role',
            ]);
    }

    public function test_admin_can_update_user_without_replacing_password(): void
    {
        $admin = $this->admin();
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
            'password' => 'password',
        ]);
        $originalPassword = $employee->password;

        $response = $this->actingAs($admin)
            ->put(route('users.update', $employee), [
                'name' => 'Updated Employee',
                'email' => 'updated@example.com',
                'phone' => '+963944333444',
                'password' => null,
                'password_confirmation' => null,
                'role' => UserRole::Manager->value,
                'is_active' => true,
            ]);

        $response->assertRedirect(route('users.show', $employee));

        $employee->refresh();
        $this->assertSame('Updated Employee', $employee->name);
        $this->assertSame(UserRole::Manager, $employee->role);
        $this->assertSame($originalPassword, $employee->password);
    }

    public function test_destroy_deactivates_user_instead_of_deleting_it(): void
    {
        $admin = $this->admin();
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $employee))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_not_deactivate_self(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from(route('users.edit', $admin))
            ->delete(route('users.destroy', $admin))
            ->assertRedirect(route('users.edit', $admin))
            ->assertSessionHasErrors('user');

        $this->assertTrue($admin->fresh()->is_active);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);
    }
}
