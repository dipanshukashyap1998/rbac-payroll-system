<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_cannot_access_employee_section(): void
    {
        [$superAdmin, $employeeViewPermission] = $this->seedRolesAndPermissions();

        $superAdmin->permissions()->sync([$employeeViewPermission->id]);

        $user = User::factory()->create();
        $user->userRoles()->create([
            'role_id' => $superAdmin->id,
            'company_id' => null,
        ]);

        $response = $this->actingAs($user)->get(route('employees.index'));

        $response->assertForbidden();
    }

    public function test_superadmin_user_listing_shows_roles_and_company_context(): void
    {
        [$superAdmin, $employeeRole, $adminRole, $userViewPermission] = $this->seedRolesAndPermissionsForUsers();

        $superAdmin->permissions()->sync([$userViewPermission->id]);

        $owner = User::factory()->create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
        ]);
        $owner->userRoles()->create([
            'role_id' => $adminRole->id,
            'company_id' => null,
        ]);

        $company = Company::query()->create([
            'name' => 'Atlas Corp',
            'status' => 'active',
            'created_by' => $owner->id,
        ]);

        $employee = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
        ]);
        $employee->userRoles()->create([
            'role_id' => $employeeRole->id,
            'company_id' => $company->id,
        ]);
        $employee->employeeProfile()->create([
            'company_id' => $company->id,
            'designation' => 'Analyst',
            'status' => 'active',
        ]);

        $superAdminUser = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
        ]);
        $superAdminUser->userRoles()->create([
            'role_id' => $superAdmin->id,
            'company_id' => null,
        ]);

        $response = $this->actingAs($superAdminUser)->get(route('users.index'));

        $response->assertOk();
        $response->assertSee('Owner User');
        $response->assertSee('owner@example.com');
        $response->assertSee('Admin');
        $response->assertSee('Atlas Corp');
        $response->assertSee('Employee');
    }

    private function seedRolesAndPermissions(): array
    {
        $superAdmin = Role::query()->firstOrCreate(['name' => 'super_admin']);
        $employeeViewPermission = Permission::query()->firstOrCreate(['name' => 'employee.view']);

        return [$superAdmin, $employeeViewPermission];
    }

    private function seedRolesAndPermissionsForUsers(): array
    {
        $superAdmin = Role::query()->firstOrCreate(['name' => 'super_admin']);
        $employeeRole = Role::query()->firstOrCreate(['name' => 'employee']);
        $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);
        $userViewPermission = Permission::query()->firstOrCreate(['name' => 'user.view']);

        return [$superAdmin, $employeeRole, $adminRole, $userViewPermission];
    }
}
