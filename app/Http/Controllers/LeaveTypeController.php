<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Services\LeavePolicyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveTypeController extends Controller
{
    public function __construct(private LeavePolicyService $leavePolicyService)
    {
    }

    public function index(Request $request): View
    {
        $companyId = $request->user()->ownedCompany()->value('id');

        $leaveTypes = LeaveType::query()
            ->withCount('balances')
            ->where('company_id', $companyId)
            ->latest()
            ->get();

        return view('leave_types.index', compact('leaveTypes'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403, 'Only admin can manage leave types.');

        return view('leave_types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Only admin can manage leave types.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20'],
            'default_days' => ['required', 'numeric', 'min:0'],
            'is_paid' => ['nullable', 'boolean'],
            'carry_forward' => ['nullable', 'boolean'],
            'max_carry_forward_days' => ['nullable', 'numeric', 'min:0'],
            'requires_approval' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $company = $user->ownedCompany()->firstOrFail();

        $leaveType = LeaveType::query()->create([
            'company_id' => $company->id,
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'default_days' => $data['default_days'],
            'is_paid' => $data['is_paid'] ?? true,
            'carry_forward' => $data['carry_forward'] ?? false,
            'max_carry_forward_days' => $data['max_carry_forward_days'] ?? 0,
            'requires_approval' => $data['requires_approval'] ?? true,
            'is_active' => true,
            'description' => $data['description'] ?? null,
        ]);

        $company->employees()->get()->each(fn ($employee) => $this->leavePolicyService->ensureEmployeeBalances($employee));

        return redirect()->route('leave-types.index')->with('status', "{$leaveType->name} created successfully.");
    }

    public function edit(Request $request, LeaveType $leaveType): View
    {
        $this->authorizeLeaveTypeAccess($request, $leaveType);

        return view('leave_types.edit', compact('leaveType'));
    }

    public function update(Request $request, LeaveType $leaveType): RedirectResponse
    {
        $this->authorizeLeaveTypeAccess($request, $leaveType);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20'],
            'default_days' => ['required', 'numeric', 'min:0'],
            'is_paid' => ['nullable', 'boolean'],
            'carry_forward' => ['nullable', 'boolean'],
            'max_carry_forward_days' => ['nullable', 'numeric', 'min:0'],
            'requires_approval' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $leaveType->update([
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'default_days' => $data['default_days'],
            'is_paid' => $data['is_paid'] ?? false,
            'carry_forward' => $data['carry_forward'] ?? false,
            'max_carry_forward_days' => $data['max_carry_forward_days'] ?? 0,
            'requires_approval' => $data['requires_approval'] ?? true,
            'is_active' => $data['is_active'] ?? true,
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('leave-types.index')->with('status', 'Leave type updated successfully.');
    }

    public function destroy(Request $request, LeaveType $leaveType): RedirectResponse
    {
        $this->authorizeLeaveTypeAccess($request, $leaveType);

        $leaveType->delete();

        return redirect()->route('leave-types.index')->with('status', 'Leave type deleted successfully.');
    }

    private function authorizeLeaveTypeAccess(Request $request, LeaveType $leaveType): void
    {
        $companyId = $request->user()->ownedCompany()->value('id');
        abort_unless((int) $leaveType->company_id === (int) $companyId, 403, 'You cannot access this leave type.');
    }
}
