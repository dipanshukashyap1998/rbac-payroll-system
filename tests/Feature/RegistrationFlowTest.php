<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_can_register_as_admin_and_is_sent_back_to_login(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jane Admin',
            'email' => 'jane@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Registration successful. Please log in to continue.');

        $this->assertDatabaseHas('users', [
            'name' => 'Jane Admin',
            'email' => 'jane@example.com',
        ]);

        $user = User::query()->where('email', 'jane@example.com')->firstOrFail();

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->ownedCompany()->exists());
    }

    public function test_admin_without_company_is_redirected_to_company_creation_after_login(): void
    {
        [$adminRole] = $this->seedRolesAndPermissions();

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $user->userRoles()->create([
            'role_id' => $adminRole->id,
            'company_id' => null,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('companies.create'));
    }

    public function test_admin_with_company_can_reach_dashboard_after_login(): void
    {
        [$adminRole, $dashboardPermission] = $this->seedRolesAndPermissions();

        $adminRole->permissions()->sync([$dashboardPermission->id]);

        $user = User::factory()->create([
            'email' => 'owner@example.com',
            'password' => 'secret123',
        ]);

        $user->userRoles()->create([
            'role_id' => $adminRole->id,
            'company_id' => null,
        ]);

        Company::query()->create([
            'name' => 'Northwind Labs',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'owner@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    private function seedRolesAndPermissions(): array
    {
        $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);
        $superAdminRole = Role::query()->firstOrCreate(['name' => 'super_admin']);
        $dashboardPermission = Permission::query()->firstOrCreate(['name' => 'dashboard.view']);
        Permission::query()->firstOrCreate(['name' => 'company.create']);
        Permission::query()->firstOrCreate(['name' => 'company.view']);

        return [$adminRole, $dashboardPermission, $superAdminRole];
    }
}
