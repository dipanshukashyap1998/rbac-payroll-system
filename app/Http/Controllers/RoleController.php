<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    private const ALLOWED_ROLES = ['super_admin', 'admin', 'employee'];

    public function index(): View
    {
        $roles = Role::query()
            ->whereIn('name', self::ALLOWED_ROLES)
            ->withCount(['permissions', 'userRoles'])
            ->orderByRaw("CASE name WHEN 'super_admin' THEN 1 WHEN 'admin' THEN 2 WHEN 'employee' THEN 3 ELSE 99 END")
            ->paginate(20);

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        abort(403, 'Only three system roles are allowed: SuperAdmin, Admin, Employee.');
    }

    public function matrix(): View
    {
        $roles = Role::query()
            ->whereIn('name', self::ALLOWED_ROLES)
            ->with(['permissions:id'])
            ->orderByRaw("CASE name WHEN 'super_admin' THEN 1 WHEN 'admin' THEN 2 WHEN 'employee' THEN 3 ELSE 99 END")
            ->get();
        $permissions = Permission::query()->orderBy('name')->get();

        return view('roles.matrix', compact('roles', 'permissions'));
    }

    public function syncMatrix(Request $request): RedirectResponse
    {
        $payload = $request->input('permissions', []);

        $roles = Role::query()->whereIn('name', self::ALLOWED_ROLES)->with('permissions:id')->get();

        foreach ($roles as $role) {
            $selectedPermissionIds = collect($payload[$role->id] ?? [])
                ->map(fn ($value) => (int) $value)
                ->filter()
                ->values()
                ->all();

            $role->permissions()->sync($selectedPermissionIds);
        }

        return redirect()->route('roles.matrix')->with('status', 'Role permission matrix updated successfully.');
    }

    public function store(Request $request): RedirectResponse
    {
        abort(403, 'Only three system roles are allowed: SuperAdmin, Admin, Employee.');
    }

    public function edit(Role $role): View
    {
        abort_unless(in_array($role->name, self::ALLOWED_ROLES, true), 404);

        $permissions = Permission::query()->orderBy('name')->get();

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        abort_unless(in_array($role->name, self::ALLOWED_ROLES, true), 404);

        if (($data['name'] ?? null) !== $role->name) {
            return back()->withErrors(['name' => 'Role name cannot be changed.'])->withInput();
        }

        $role->update([
            'name' => $data['name'],
        ]);

        $role->permissions()->sync($data['permission_ids'] ?? []);

        return redirect()->route('roles.index')->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        return back()->withErrors(['role' => 'System roles cannot be deleted.']);
    }
}
