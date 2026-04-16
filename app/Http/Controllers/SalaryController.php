<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use App\Models\SalaryStructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isEmployee()) {
            $salaryAssignment = EmployeeSalary::query()
                ->with(['salaryStructure.company'])
                ->whereHas('employee', fn ($query) => $query->where('user_id', $user->id))
                ->latest('effective_from')
                ->first();

            return view('salary_structures.employee', compact('salaryAssignment'));
        }

        $companyId = $user->ownedCompany()->value('id');

        $salaryStructures = SalaryStructure::query()
            ->with('company')
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(10);

        return view('salary_structures.index', compact('salaryStructures'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403, 'Only admin can create salary structures.');

        return view('salary_structures.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Only admin can create salary structures.');

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'hra' => ['nullable', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
        ]);

        SalaryStructure::query()->create([
            ...$data,
            'company_id' => $user->ownedCompany()->value('id'),
            'hra' => $data['hra'] ?? 0,
            'allowances' => $data['allowances'] ?? 0,
            'deductions' => $data['deductions'] ?? 0,
        ]);

        return redirect()->route('salary-structures.index')->with('status', 'Salary structure created successfully.');
    }

    public function edit(Request $request, SalaryStructure $salaryStructure): View
    {
        $this->authorizeSalaryStructureAccess($request, $salaryStructure);

        return view('salary_structures.edit', compact('salaryStructure'));
    }

    public function update(Request $request, SalaryStructure $salaryStructure): RedirectResponse
    {
        $this->authorizeSalaryStructureAccess($request, $salaryStructure);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'hra' => ['nullable', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
        ]);

        $salaryStructure->update([
            ...$data,
            'hra' => $data['hra'] ?? 0,
            'allowances' => $data['allowances'] ?? 0,
            'deductions' => $data['deductions'] ?? 0,
        ]);

        return redirect()->route('salary-structures.index')->with('status', 'Salary structure updated successfully.');
    }

    public function destroy(Request $request, SalaryStructure $salaryStructure): RedirectResponse
    {
        $this->authorizeSalaryStructureAccess($request, $salaryStructure);

        $salaryStructure->delete();

        return redirect()->route('salary-structures.index')->with('status', 'Salary structure deleted successfully.');
    }

    private function authorizeSalaryStructureAccess(Request $request, SalaryStructure $salaryStructure): void
    {
        $user = $request->user();

        if (! $user->isAdmin()) {
            abort(403, 'Only admin can manage salary structures.');
        }

        $companyId = $user->ownedCompany()->value('id');

        if ((int) $salaryStructure->company_id !== (int) $companyId) {
            abort(403, 'You can only manage salary structures for your own company.');
        }
    }
}
