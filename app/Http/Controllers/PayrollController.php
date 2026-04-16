<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Only admin can view company payroll runs.');

        $companyId = $user->ownedCompany()->value('id');

        $payrolls = Payroll::query()
            ->where('company_id', $companyId)
            ->latest('year')
            ->latest('month')
            ->paginate(12);

        return view('payrolls.index', compact('payrolls'));
    }
}
