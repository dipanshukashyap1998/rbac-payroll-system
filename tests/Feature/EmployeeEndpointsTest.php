<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_employee(): void
    {
        $admin = $this->createAdminWithCompany([
            'email' => 'admin-employee@example.com',
        ], [
            'name' => 'Admin Company',
        ]);

        $this->assignPermissionsToRole($this->createRole('admin'), [
            'employee.view',
            'employee.create',
            'employee.edit',
            'employee.delete',
        ]);

        $user = User::factory()->create([
            'email' => 'new-employee@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $store = $this->actingAs($admin)->post(route('employees.store'), [
            'user_id' => $user->id,
            'employee_code' => 'EMP-001',
            'designation' => 'Developer',
            'joining_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $store->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'company_id' => $admin->ownedCompany()->value('id'),
        ]);

        $employee = Employee::query()->where('user_id', $user->id)->first();

        $update = $this->actingAs($admin)->put(route('employees.update', $employee), [
            'user_id' => $user->id,
            'employee_code' => 'EMP-002',
            'designation' => 'Senior Developer',
            'joining_date' => now()->subWeek()->toDateString(),
            'status' => 'active',
        ]);

        $update->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'employee_code' => 'EMP-002',
        ]);

        $delete = $this->actingAs($admin)->delete(route('employees.destroy', $employee));
        $delete->assertRedirect(route('employees.index'));
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_non_admin_cannot_access_employee_routes(): void
    {
        $user = User::factory()->create([
            'email' => 'employee-no-access@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $employeeRole = $this->createRole('employee');
        $this->assignRoleToUser($user, $employeeRole, null);

        $this->actingAs($user)->get(route('employees.index'))->assertForbidden();
    }
}
