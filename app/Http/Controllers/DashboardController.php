<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
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
        $dashboardStats = [];
        $userGrowth = [];

        if ($user->isSuperAdmin()) {
            $modules = [
                ['label' => 'Companies', 'route' => route('companies.index'), 'icon' => 'apartment', 'description' => 'Review every company across the platform.'],
                ['label' => 'Roles', 'route' => route('roles.index'), 'icon' => 'security', 'description' => 'Adjust access architecture and policies.'],
                ['label' => 'Permissions', 'route' => route('permissions.index'), 'icon' => 'key', 'description' => 'Keep module permissions clean and auditable.'],
                ['label' => 'Users', 'route' => route('users.index'), 'icon' => 'group', 'description' => 'Inspect all accounts, roles, and company links.'],
            ];

            $dashboardStats = [
                ['label' => 'Total users', 'value' => User::query()->count(), 'icon' => 'group', 'tone' => 'teal'],
                ['label' => 'New users in 3 days', 'value' => User::query()->where('created_at', '>=', now()->subDays(2)->startOfDay())->count(), 'icon' => 'trending_up', 'tone' => 'amber'],
                ['label' => 'Companies onboarded', 'value' => Company::query()->count(), 'icon' => 'domain', 'tone' => 'rose'],
                ['label' => 'Admins without company', 'value' => User::query()->whereHas('roles', fn ($query) => $query->where('name', 'admin'))->whereDoesntHave('ownedCompany')->count(), 'icon' => 'policy_alert', 'tone' => 'slate'],
            ];

            $dates = collect(range(2, 0))->map(fn (int $offset) => now()->subDays($offset)->startOfDay());
            $countsByDate = User::query()
                ->where('created_at', '>=', now()->subDays(2)->startOfDay())
                ->get()
                ->groupBy(fn (User $dashboardUser) => $dashboardUser->created_at->toDateString());

            $userGrowth = [
                'labels' => $dates->map(fn (Carbon $date) => $date->format('d M'))->values()->all(),
                'values' => $dates->map(fn (Carbon $date) => $countsByDate->get($date->toDateString(), collect())->count())->values()->all(),
            ];
        }

        if ($user->isAdmin()) {
            $modules = [
                ['label' => 'Company Profile', 'route' => route('companies.index'), 'icon' => 'apartment', 'description' => 'Manage the company profile and operational status.'],
                ['label' => 'Employees', 'route' => route('employees.index'), 'icon' => 'badge', 'description' => 'Maintain employee records and staffing data.'],
                ['label' => 'Salary Structures', 'route' => route('salary-structures.index'), 'icon' => 'account_balance_wallet', 'description' => 'Define compensation templates and policies.'],
                ['label' => 'Payrolls', 'route' => route('payrolls.index'), 'icon' => 'receipt_long', 'description' => 'Review payroll cycles and run status.'],
                ['label' => 'Payslips', 'route' => route('payslips.index'), 'icon' => 'description', 'description' => 'Track published employee pay statements.'],
                ['label' => 'Audit Logs', 'route' => route('audit-logs.index'), 'icon' => 'history', 'description' => 'Follow sensitive actions in one timeline.'],
            ];

            $dashboardStats = [
                ['label' => 'My company', 'value' => optional($user->ownedCompany)->name ?? 'Pending setup', 'icon' => 'business_center', 'tone' => 'teal'],
                ['label' => 'Employees', 'value' => $user->ownedCompany?->employees()->count() ?? 0, 'icon' => 'groups', 'tone' => 'amber'],
                ['label' => 'Salary structures', 'value' => $user->ownedCompany?->salaryStructures()->count() ?? 0, 'icon' => 'payments', 'tone' => 'rose'],
                ['label' => 'Payroll runs', 'value' => $user->ownedCompany?->payrolls()->count() ?? 0, 'icon' => 'calendar_month', 'tone' => 'slate'],
            ];
        }

        if ($user->isEmployee()) {
            $modules = [
                ['label' => 'My Salary Structure', 'route' => route('salary-structures.index'), 'icon' => 'account_balance_wallet', 'description' => 'See your assigned compensation structure.'],
                ['label' => 'My Payslips', 'route' => route('payslips.index'), 'icon' => 'description', 'description' => 'Access your latest released payslips.'],
            ];

            $dashboardStats = [
                ['label' => 'Role', 'value' => 'Employee', 'icon' => 'badge', 'tone' => 'teal'],
                ['label' => 'Company', 'value' => optional($user->employeeProfile?->company)->name ?? 'Not assigned', 'icon' => 'apartment', 'tone' => 'amber'],
                ['label' => 'Designation', 'value' => $user->employeeProfile?->designation ?? 'Not assigned', 'icon' => 'work', 'tone' => 'rose'],
            ];
        }

        return view('dashboard.index', [
            'visibleModules' => collect($modules),
            'dashboardStats' => collect($dashboardStats),
            'userGrowth' => $userGrowth,
        ]);
    }
}
