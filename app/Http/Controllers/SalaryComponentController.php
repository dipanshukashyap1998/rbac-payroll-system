<?php

namespace App\Http\Controllers;

use App\Models\SalaryComponent;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    /**
     * Display components for a salary structure
     */
    public function index($salaryStructureId)
    {
        $salaryStructure = SalaryStructure::findOrFail($salaryStructureId);

        $components = $salaryStructure->components()->get();

        return view('salary-components.index', compact('salaryStructure', 'components'));
    }

    /**
     * Show the form for creating a new component
     */
    public function create($salaryStructureId)
    {
        $salaryStructure = SalaryStructure::findOrFail($salaryStructureId);

        return view('salary-components.create', compact('salaryStructure'));
    }

    /**
     * Store a newly created component
     */
    public function store(Request $request, $salaryStructureId)
    {
        $salaryStructure = SalaryStructure::findOrFail($salaryStructureId);

        $validated = $request->validate([
            'component_name' => 'required|string|max:255',
            'component_type' => 'required|in:earning,deduction',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if (!$validated['percentage'] && !$validated['fixed_amount']) {
            return back()->withErrors(['percentage' => 'Either percentage or fixed amount is required']);
        }

        $validated['salary_structure_id'] = $salaryStructure->id;
        $validated['is_active'] = $validated['is_active'] ?? true;

        SalaryComponent::create($validated);

        return redirect()->route('salary-structures.components.index', $salaryStructure)
            ->with('status', 'Component added successfully');
    }

    /**
     * Show the form for editing a component
     */
    public function edit($salaryStructureId, SalaryComponent $component)
    {
        $salaryStructure = SalaryStructure::findOrFail($salaryStructureId);

        if ($component->salary_structure_id !== $salaryStructure->id) {
            abort(404);
        }

        return view('salary-components.edit', compact('salaryStructure', 'component'));
    }

    /**
     * Update the specified component
     */
    public function update(Request $request, $salaryStructureId, SalaryComponent $component)
    {
        $salaryStructure = SalaryStructure::findOrFail($salaryStructureId);

        if ($component->salary_structure_id !== $salaryStructure->id) {
            abort(404);
        }

        $validated = $request->validate([
            'component_name' => 'required|string|max:255',
            'component_type' => 'required|in:earning,deduction',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if (!$validated['percentage'] && !$validated['fixed_amount']) {
            return back()->withErrors(['percentage' => 'Either percentage or fixed amount is required']);
        }

        $validated['is_active'] = $validated['is_active'] ?? true;

        $component->update($validated);

        return redirect()->route('salary-structures.components.index', $salaryStructure)
            ->with('status', 'Component updated successfully');
    }

    /**
     * Delete the specified component
     */
    public function destroy($salaryStructureId, SalaryComponent $component)
    {
        $salaryStructure = SalaryStructure::findOrFail($salaryStructureId);

        if ($component->salary_structure_id !== $salaryStructure->id) {
            abort(404);
        }

        $component->delete();

        return redirect()->route('salary-structures.components.index', $salaryStructure)
            ->with('status', 'Component deleted successfully');
    }
}
