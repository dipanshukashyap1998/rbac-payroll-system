<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\Role;
use App\Models\SalaryStructure;
use App\Models\User;
use App\Services\LeavePolicyService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leavePolicyService = app(LeavePolicyService::class);
        $superAdmin = Role::query()->where('name', 'super_admin')->firstOrFail();
        $admin = Role::query()->where('name', 'admin')->firstOrFail();
        $employeeRole = Role::query()->where('name', 'employee')->firstOrFail();

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
        $leavePolicyService->seedCompanyLeaveTypes($company);

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
        $leavePolicyService->ensureEmployeeBalances($employee);

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
