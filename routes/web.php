<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google'])
        ->name('social.login');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google'])
        ->name('social.callback');
});

Route::middleware(['auth', 'admin.company'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/companies', [CompanyController::class, 'index'])
        ->middleware('permission:company.view')
        ->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])
        ->middleware('permission:company.create')
        ->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])
        ->middleware('permission:company.create')
        ->name('companies.store');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])
        ->middleware('permission:company.edit')
        ->name('companies.edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])
        ->middleware('permission:company.edit')
        ->name('companies.update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])
        ->middleware('permission:company.delete')
        ->name('companies.destroy');

    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('permission:employee.view')
        ->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])
        ->middleware('permission:employee.create')
        ->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('permission:employee.create')
        ->name('employees.store');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])
        ->middleware('permission:employee.edit')
        ->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])
        ->middleware('permission:employee.edit')
        ->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])
        ->middleware('permission:employee.delete')
        ->name('employees.destroy');

    Route::get('/salary-structures', [SalaryController::class, 'index'])
        ->middleware('permission:salary_structure.view')
        ->name('salary-structures.index');
    Route::get('/salary-structures/create', [SalaryController::class, 'create'])
        ->middleware('permission:salary_structure.create')
        ->name('salary-structures.create');
    Route::post('/salary-structures', [SalaryController::class, 'store'])
        ->middleware('permission:salary_structure.create')
        ->name('salary-structures.store');
    Route::get('/salary-structures/{salaryStructure}/edit', [SalaryController::class, 'edit'])
        ->middleware('permission:salary_structure.edit')
        ->name('salary-structures.edit');
    Route::put('/salary-structures/{salaryStructure}', [SalaryController::class, 'update'])
        ->middleware('permission:salary_structure.edit')
        ->name('salary-structures.update');
    Route::delete('/salary-structures/{salaryStructure}', [SalaryController::class, 'destroy'])
        ->middleware('permission:salary_structure.delete')
        ->name('salary-structures.destroy');

    Route::get('/payrolls', [PayrollController::class, 'index'])
        ->middleware(['role:admin', 'permission:payroll.view'])
        ->name('payrolls.index');

    Route::get('/payslips', [PayslipController::class, 'index'])
        ->middleware(['role:admin,employee', 'permission:payslip.view'])
        ->name('payslips.index');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware(['role:admin', 'permission:audit_log.view'])
        ->name('audit-logs.index');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:role.view')
            ->name('roles.index');
        Route::get('/roles/permission-matrix', [RoleController::class, 'matrix'])
            ->middleware('permission:role.edit')
            ->name('roles.matrix');
        Route::post('/roles/permission-matrix', [RoleController::class, 'syncMatrix'])
            ->middleware('permission:role.edit')
            ->name('roles.matrix.sync');
        Route::get('/roles/create', [RoleController::class, 'create'])
            ->middleware('permission:role.create')
            ->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:role.create')
            ->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:role.edit')
            ->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:role.edit')
            ->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:role.delete')
            ->name('roles.destroy');

        Route::get('/permissions', [PermissionController::class, 'index'])
            ->middleware('permission:permission.view')
            ->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])
            ->middleware('permission:permission.create')
            ->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])
            ->middleware('permission:permission.create')
            ->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
            ->middleware('permission:permission.edit')
            ->name('permissions.edit');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
            ->middleware('permission:permission.edit')
            ->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
            ->middleware('permission:permission.delete')
            ->name('permissions.destroy');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:user.view')
            ->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])
            ->middleware('permission:user.create')
            ->name('users.create');
        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:user.create')
            ->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:user.edit')
            ->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:user.edit')
            ->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:user.delete')
            ->name('users.destroy');
    });
});
