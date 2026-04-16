<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PermissionController extends Controller
{
    private const PROTECTED_PERMISSIONS = [
        'dashboard.view',
        'company.view', 'company.create', 'company.edit', 'company.delete',
        'employee.view', 'employee.create', 'employee.edit', 'employee.delete',
        'salary_structure.view', 'salary_structure.create', 'salary_structure.edit', 'salary_structure.delete',
        'payroll.view', 'payslip.view', 'audit_log.view',
        'role.view', 'role.create', 'role.edit', 'role.delete',
        'permission.view', 'permission.create', 'permission.edit', 'permission.delete',
        'user.view', 'user.create', 'user.edit', 'user.delete',
    ];

    public function index(): View
    {
        $permissions = Permission::query()->withCount('roles')->latest()->paginate(30);

        return view('permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        return view('permissions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::query()->create($data);

        return redirect()->route('permissions.index')->with('status', 'Permission created successfully.');
    }

    public function edit(Permission $permission): View
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($permission->id)],
        ]);

        if (in_array($permission->name, self::PROTECTED_PERMISSIONS, true) && $data['name'] !== $permission->name) {
            return back()->withErrors(['name' => 'System permission cannot be renamed.'])->withInput();
        }

        $permission->update($data);

        return redirect()->route('permissions.index')->with('status', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        if (in_array($permission->name, self::PROTECTED_PERMISSIONS, true)) {
            return back()->withErrors(['permission' => 'System permission cannot be deleted.']);
        }

        if ($permission->roles()->exists()) {
            return back()->withErrors(['permission' => 'Permission is assigned to roles and cannot be deleted.']);
        }

        $permission->delete();

        return redirect()->route('permissions.index')->with('status', 'Permission deleted successfully.');
    }
}
