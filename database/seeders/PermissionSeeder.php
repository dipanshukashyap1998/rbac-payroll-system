<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

            'leave_type.view',
            'leave_type.create',
            'leave_type.edit',
            'leave_type.delete',
            'leave.calendar.view',
            'leave.balance.view',
            'leave.view',
            'leave.create',
            'leave.approve',

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
                'leave_type.view',
                'leave_type.create',
                'leave_type.edit',
                'leave_type.delete',
                'leave.calendar.view',
                'leave.balance.view',
                'leave.view',
                'leave.create',
                'leave.approve',
                'payroll.view',
                'payslip.view',
                'audit_log.view',
            ])->pluck('id')
        );

        $employeeRole->permissions()->sync(
            Permission::query()->whereIn('name', [
                'dashboard.view',
                'leave.calendar.view',
                'leave.balance.view',
                'salary_structure.view',
                'payslip.view',
                'leave.view',
                'leave.create',
            ])->pluck('id')
        );
    }
}
