<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            $companies = Company::query()->latest()->paginate(10);
        } else {
            $companies = Company::query()
                ->where('created_by', $user->id)
                ->latest()
                ->paginate(10);
        }

        return view('companies.index', compact('companies'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin()) {
            abort(403, 'Only admin can create company profile.');
        }

        if ($user->ownedCompany()->exists()) {
            return redirect()->route('companies.index')->with('status', 'Company profile already exists.');
        }

        return view('companies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin()) {
            abort(403, 'Only admin can create company profile.');
        }

        if ($user->ownedCompany()->exists()) {
            return redirect()->route('companies.index')->with('status', 'Only one company is allowed per admin.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $data['created_by'] = $user->id;

        Company::query()->create($data);

        return redirect()->route('companies.index')->with('status', 'Company created successfully.');
    }

    public function edit(Request $request, Company $company): View
    {
        $this->authorizeCompanyAccess($request, $company);

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $this->authorizeCompanyAccess($request, $company);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $company->update($data);

        return redirect()->route('companies.index')->with('status', 'Company updated successfully.');
    }

    public function destroy(Request $request, Company $company): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403, 'Only superadmin can delete company.');
        }

        $company->delete();

        return redirect()->route('companies.index')->with('status', 'Company deleted successfully.');
    }

    private function authorizeCompanyAccess(Request $request, Company $company): void
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            abort(403, 'Superadmin can view company but cannot edit company-specific data.');
        }

        if (! $user->isAdmin() || (int) $company->created_by !== (int) $user->id) {
            abort(403, 'You cannot access this company.');
        }
    }
}
