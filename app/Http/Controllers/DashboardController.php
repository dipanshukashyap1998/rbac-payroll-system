<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->isAdmin() && ! $user->ownedCompany()->exists()) {
            return redirect()->route('companies.create')
                ->with('status', 'Create your company first to unlock company modules.');
        }

        $modules = [];

        if ($user->isSuperAdmin()) {
            $modules = [
                ['label' => 'Companies', 'route' => route('companies.index')],
                ['label' => 'Employees', 'route' => route('employees.index')],
                ['label' => 'Roles', 'route' => route('roles.index')],
                ['label' => 'Permissions', 'route' => route('permissions.index')],
                ['label' => 'Users', 'route' => route('users.index')],
            ];
        }

        if ($user->isAdmin()) {
            $modules = [
                ['label' => 'Company Profile', 'route' => route('companies.index')],
                ['label' => 'Employees', 'route' => route('employees.index')],
                ['label' => 'Salary Structures', 'route' => route('salary-structures.index')],
                ['label' => 'Payrolls', 'route' => route('payrolls.index')],
                ['label' => 'Payslips', 'route' => route('payslips.index')],
                ['label' => 'Audit Logs', 'route' => route('audit-logs.index')],
            ];
        }

        if ($user->isEmployee()) {
            $modules = [
                ['label' => 'My Salary Structure', 'route' => route('salary-structures.index')],
                ['label' => 'My Payslips', 'route' => route('payslips.index')],
            ];
        }

        return view('dashboard.index', [
            'visibleModules' => collect($modules),
        ]);
    }
}
