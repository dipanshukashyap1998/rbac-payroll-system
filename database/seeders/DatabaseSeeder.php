<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Payslip;
use App\Models\Permission;
use App\Models\Payroll;
use App\Models\Role;
use App\Models\SalaryStructure;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'super_admin',
            'admin',
            'employee',
        ];

        foreach ($roles as $roleName) {
            Role::query()->firstOrCreate(['name' => $roleName]);
        }

        $permissions = [
            'dashboard.view',

            'company.view',
            'company.create',
            'company.edit',
            'company.delete',

            'employee.view',
            'employee.create',
            'employee.edit',
            'employee.delete',

            'salary_structure.view',
            'salary_structure.create',
            'salary_structure.edit',
            'salary_structure.delete',

            'payroll.view',
            'payslip.view',
            'audit_log.view',

            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
        ];

        foreach ($permissions as $permissionName) {
            Permission::query()->firstOrCreate(['name' => $permissionName]);
        }

        $superAdmin = Role::query()->where('name', 'super_admin')->firstOrFail();
        $admin = Role::query()->where('name', 'admin')->firstOrFail();
        $employeeRole = Role::query()->where('name', 'employee')->firstOrFail();

        $superAdmin->permissions()->sync(
            Permission::query()->whereIn('name', [
                'dashboard.view',
                'company.view',
                'employee.view',
                'role.view',
                'role.create',
                'role.edit',
                'role.delete',
                'permission.view',
                'permission.create',
                'permission.edit',
                'permission.delete',
                'user.view',
                'user.create',
                'user.edit',
                'user.delete',
            ])->pluck('id')
        );

        $admin->permissions()->sync(
            Permission::query()->whereIn('name', [
                'dashboard.view',
                'company.view',
                'company.create',
                'company.edit',
                'employee.view',
                'employee.create',
                'employee.edit',
                'employee.delete',
                'salary_structure.view',
                'salary_structure.create',
                'salary_structure.edit',
                'salary_structure.delete',
                'payroll.view',
                'payslip.view',
                'audit_log.view',
            ])->pluck('id')
        );

        $employeeRole->permissions()->sync(
            Permission::query()->whereIn('name', [
                'dashboard.view',
                'salary_structure.view',
                'payslip.view',
            ])->pluck('id')
        );

        $superAdminUser = User::query()->firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        $superAdminUser->userRoles()->firstOrCreate([
            'role_id' => $superAdmin->id,
            'company_id' => null,
        ]);

        $adminUser = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Default Admin',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        $adminUser->userRoles()->firstOrCreate([
            'role_id' => $admin->id,
            'company_id' => null,
        ]);

        $company = Company::query()->firstOrCreate(
            ['name' => 'Acme Private Ltd'],
            [
                'email' => 'info@acme.test',
                'status' => 'active',
                'created_by' => $adminUser->id,
            ]
        );

        $employeeUser = User::query()->firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Default Employee',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        $employeeUser->userRoles()->firstOrCreate([
            'role_id' => $employeeRole->id,
            'company_id' => $company->id,
        ]);

        $employee = Employee::query()->firstOrCreate(
            ['user_id' => $employeeUser->id, 'company_id' => $company->id],
            [
                'designation' => 'Software Engineer',
                'status' => 'active',
            ]
        );

        $salaryStructure = SalaryStructure::query()->firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Engineering Standard'],
            [
                'base_salary' => 50000,
                'hra' => 8000,
                'allowances' => 4000,
                'deductions' => 2500,
            ]
        );

        EmployeeSalary::query()->firstOrCreate(
            ['employee_id' => $employee->id, 'salary_structure_id' => $salaryStructure->id],
            ['effective_from' => now()->startOfMonth()->toDateString()]
        );

        $payrollCurrent = Payroll::query()->firstOrCreate(
            ['company_id' => $company->id, 'month' => now()->month, 'year' => now()->year],
            ['status' => 'processed', 'created_by' => $adminUser->id]
        );

        $payrollPrevious = Payroll::query()->firstOrCreate(
            ['company_id' => $company->id, 'month' => now()->subMonth()->month, 'year' => now()->subMonth()->year],
            ['status' => 'paid', 'created_by' => $adminUser->id]
        );

        Payslip::query()->firstOrCreate(
            ['payroll_run_id' => $payrollCurrent->id, 'employee_id' => $employee->id],
            [
                'base_salary' => 50000,
                'hra' => 8000,
                'allowances' => 4000,
                'deductions' => 2500,
                'net_salary' => 59500,
                'generated_at' => now(),
            ]
        );

        Payslip::query()->firstOrCreate(
            ['payroll_run_id' => $payrollPrevious->id, 'employee_id' => $employee->id],
            [
                'base_salary' => 50000,
                'hra' => 8000,
                'allowances' => 4000,
                'deductions' => 2500,
                'net_salary' => 59500,
                'generated_at' => now()->subMonth(),
            ]
        );
    }
}
