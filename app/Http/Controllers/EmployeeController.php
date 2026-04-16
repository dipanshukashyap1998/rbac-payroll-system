<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Employee::query()->with(['user', 'company'])->latest();

        if ($user->isAdmin()) {
            $companyId = $user->ownedCompany()->value('id');
            $query->where('company_id', $companyId);
        }

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        }

        $employees = $query->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Only admin can create employees.');

        $users = User::query()->orderBy('name')->get();

        return view('employees.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Only admin can create employees.');

        $companyId = $user->ownedCompany()->value('id');

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'employee_code' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Employee::query()->create([
            ...$data,
            'company_id' => $companyId,
        ]);

        return redirect()->route('employees.index')->with('status', 'Employee created successfully.');
    }

    public function edit(Request $request, Employee $employee): View
    {
        $this->authorizeEmployeeEdit($request, $employee);

        $users = User::query()->orderBy('name')->get();

        return view('employees.edit', compact('employee', 'users'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $this->authorizeEmployeeEdit($request, $employee);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'employee_code' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $employee->update($data);

        return redirect()->route('employees.index')->with('status', 'Employee updated successfully.');
    }

    public function destroy(Request $request, Employee $employee): RedirectResponse
    {
        $this->authorizeEmployeeEdit($request, $employee);

        $employee->delete();

        return redirect()->route('employees.index')->with('status', 'Employee deleted successfully.');
    }

    private function authorizeEmployeeEdit(Request $request, Employee $employee): void
    {
        $user = $request->user();

        if (! $user->isAdmin()) {
            abort(403, 'Only admin can manage employees.');
        }

        $companyId = $user->ownedCompany()->value('id');

        if ((int) $employee->company_id !== (int) $companyId) {
            abort(403, 'You can manage only employees in your company.');
        }
    }
}
