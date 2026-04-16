<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayslipController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $companyId = $user->ownedCompany()->value('id');

            $payslips = Payslip::query()
                ->with(['employee.user', 'payrollRun'])
                ->whereHas('payrollRun', fn ($query) => $query->where('company_id', $companyId))
                ->latest()
                ->paginate(12);

            return view('payslips.index', [
                'payslips' => $payslips,
                'isEmployeeView' => false,
            ]);
        }

        if ($user->isEmployee()) {
            $employee = $user->employeeProfile;

            $current = now();
            $previous = now()->subMonth();

            $payslips = Payslip::query()
                ->with(['payrollRun'])
                ->where('employee_id', $employee?->id)
                ->whereHas('payrollRun', function ($query) use ($current, $previous) {
                    $query->whereIn('status', ['processed', 'paid'])
                        ->where(function ($monthQuery) use ($current, $previous) {
                            $monthQuery
                                ->where(function ($q) use ($current) {
                                    $q->where('year', $current->year)
                                        ->where('month', $current->month);
                                })
                                ->orWhere(function ($q) use ($previous) {
                                    $q->where('year', $previous->year)
                                        ->where('month', $previous->month);
                                });
                        });
                })
                ->latest()
                ->get();

            return view('payslips.index', [
                'payslips' => $payslips,
                'isEmployeeView' => true,
            ]);
        }

        abort(403, 'You are not allowed to view payslips.');
    }
}
