<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ALLOWED_ROLES = ['super_admin', 'admin', 'employee'];

    public function index(): View
    {
        $users = User::query()
            ->with(['roles', 'ownedCompany', 'employeeProfile.company', 'userRoles.company'])
            ->latest()
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::query()->whereIn('name', self::ALLOWED_ROLES)->orderByRaw("CASE name WHEN 'super_admin' THEN 1 WHEN 'admin' THEN 2 WHEN 'employee' THEN 3 ELSE 99 END")->get();
        $companies = Company::query()->orderBy('name')->get();

        return view('users.create', compact('roles', 'companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['required', 'exists:roles,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'designation' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
        ]);

        $role = Role::query()->findOrFail($data['role_id']);
        [$companyId, $error] = $this->resolveCompanyForRole($role->name, $data['company_id'] ?? null);

        if ($error) {
            return back()->withErrors(['company_id' => $error])->withInput();
        }

        DB::transaction(function () use ($data, $role, $companyId) {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            $user->userRoles()->create([
                'role_id' => $role->id,
                'company_id' => $companyId,
            ]);

            $this->syncEmployeeProfile($user, $role->name, $companyId, $data);
        });

        return redirect()->route('users.index')->with('status', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $roles = Role::query()->whereIn('name', self::ALLOWED_ROLES)->orderByRaw("CASE name WHEN 'super_admin' THEN 1 WHEN 'admin' THEN 2 WHEN 'employee' THEN 3 ELSE 99 END")->get();
        $companies = Company::query()->orderBy('name')->get();
        $selectedRole = $user->roles()->first();
        $selectedCompanyId = $user->userRoles()->value('company_id');

        return view('users.edit', compact('user', 'roles', 'companies', 'selectedRole', 'selectedCompanyId'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role_id' => ['required', 'exists:roles,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'designation' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
        ]);

        $newRole = Role::query()->findOrFail($data['role_id']);
        $currentRole = $user->roles()->first();

        if (
            $currentRole?->name === 'super_admin'
            && $newRole->name !== 'super_admin'
            && $this->superAdminCount() <= 1
        ) {
            return back()->withErrors(['role_id' => 'Cannot remove role from the last superadmin.'])->withInput();
        }

        [$companyId, $error] = $this->resolveCompanyForRole($newRole->name, $data['company_id'] ?? null);

        if ($error) {
            return back()->withErrors(['company_id' => $error])->withInput();
        }

        DB::transaction(function () use ($user, $data, $newRole, $companyId) {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $user->update($payload);

            $user->userRoles()->delete();
            $user->userRoles()->create([
                'role_id' => $newRole->id,
                'company_id' => $companyId,
            ]);

            $this->syncEmployeeProfile($user, $newRole->name, $companyId, $data);
        });

        return redirect()->route('users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($user->hasRole('super_admin') && $this->superAdminCount() <= 1) {
            return back()->withErrors(['user' => 'Cannot delete the last superadmin account.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'User deleted successfully.');
    }

    private function resolveCompanyForRole(string $roleName, ?int $companyId): array
    {
        if ($roleName === 'employee' && ! $companyId) {
            return [null, 'Company is required for employee role.'];
        }

        if (in_array($roleName, ['super_admin', 'admin'], true)) {
            return [null, null];
        }

        return [$companyId, null];
    }

    private function syncEmployeeProfile(User $user, string $roleName, ?int $companyId, array $data): void
    {
        if ($roleName !== 'employee') {
            $user->employeeProfile()->delete();
            return;
        }

        $user->employeeProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_id' => $companyId,
                'designation' => $data['designation'] ?? null,
                'joining_date' => $data['joining_date'] ?? null,
                'status' => 'active',
            ]
        );
    }

    private function superAdminCount(): int
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))
            ->count();
    }
}
