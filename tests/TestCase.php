<?php

namespace Tests;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createRole(string $name): Role
    {
        return Role::query()->firstOrCreate(['name' => $name]);
    }

    protected function createPermission(string $name): Permission
    {
        return Permission::query()->firstOrCreate(['name' => $name]);
    }

    protected function assignPermissionsToRole(Role $role, array $permissionNames): void
    {
        $permissionIds = collect($permissionNames)
            ->map(fn (string $permission) => $this->createPermission($permission)->id)
            ->all();

        $role->permissions()->sync($permissionIds);
    }

    protected function assignRoleToUser(User $user, Role $role, ?Company $company = null): void
    {
        $user->userRoles()->create([
            'role_id' => $role->id,
            'company_id' => $company?->id,
        ]);
    }

    protected function createAdminUser(array $userAttributes = []): User
    {
        $adminRole = $this->createRole('admin');
        $user = User::factory()->create($userAttributes);
        $this->assignRoleToUser($user, $adminRole);

        return $user;
    }

    protected function createAdminWithCompany(array $userAttributes = [], array $companyAttributes = []): User
    {
        $user = $this->createAdminUser($userAttributes);

        Company::query()->create(array_merge([
            'name' => 'Company Test',
            'status' => 'active',
            'created_by' => $user->id,
        ], $companyAttributes));

        return $user;
    }

    protected function createSuperAdminUser(array $userAttributes = []): User
    {
        $role = $this->createRole('super_admin');
        $user = User::factory()->create($userAttributes);
        $this->assignRoleToUser($user, $role);

        return $user;
    }

    protected function createEmployeeUser(Company $company, array $userAttributes = [], array $employeeAttributes = []): User
    {
        $role = $this->createRole('employee');
        $user = User::factory()->create($userAttributes);
        $this->assignRoleToUser($user, $role, $company);

        $user->employeeProfile()->create(array_merge([
            'company_id' => $company->id,
            'designation' => 'Staff',
            'joining_date' => now()->toDateString(),
            'status' => 'active',
        ], $employeeAttributes));

        return $user;
    }
}
