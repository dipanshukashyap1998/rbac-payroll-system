@extends('layouts.app', ['title' => 'Payslip Details'])

@section('content')
    @php
        $breakdown = $payslip->tax_breakdown ?? [];
        $incomeTaxSlabs = $breakdown['income_tax_slabs'] ?? [];
    @endphp

    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">receipt_long</span> Payslip breakdown</span>
                <h2>{{ $payslip->employee?->user?->name ?? 'Employee' }}</h2>
                <p>Detailed salary, tax slab, and PF deduction breakdown for the selected pay run.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('payslips.index') }}" class="btn btn-secondary">Back to payslips</a>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card lg:col-span-2">
                <h3 class="panel-title">Salary summary</h3>
                <div class="table-wrap mt-4">
                    <table class="table">
                        <tbody>
                        <tr><th>Payroll month</th><td>{{ str_pad($payslip->payrollRun?->month ?? 0, 2, '0', STR_PAD_LEFT) }} / {{ $payslip->payrollRun?->year ?? '-' }}</td></tr>
                        <tr><th>Gross salary</th><td>{{ number_format($payslip->gross_salary ?? 0, 2) }}</td></tr>
                        <tr><th>Standard deduction</th><td>{{ number_format($breakdown['standard_deduction'] ?? 0, 2) }}</td></tr>
                        <tr><th>Taxable income</th><td>{{ number_format($breakdown['taxable_income'] ?? 0, 2) }}</td></tr>
                        <tr><th>Income tax (monthly)</th><td>{{ number_format($payslip->income_tax ?? 0, 2) }}</td></tr>
                        <tr><th>Professional tax (monthly)</th><td>{{ number_format($payslip->professional_tax ?? 0, 2) }}</td></tr>
                        <tr><th>PF deduction</th><td>{{ number_format($payslip->pf_deduction ?? 0, 2) }} at {{ $breakdown['pf_rate'] ?? 12 }}% of {{ number_format($breakdown['pf_wage_base'] ?? 0, 2) }}</td></tr>
                        <tr><th>Other deductions</th><td>{{ number_format($payslip->deductions ?? 0, 2) }}</td></tr>
                        <tr><th>Total deductions</th><td>{{ number_format($payslip->total_deductions ?? 0, 2) }}</td></tr>
                        <tr><th>Net salary</th><td><strong>{{ number_format($payslip->net_salary ?? 0, 2) }}</strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3 class="panel-title">Quick figures</h3>
                <div class="mt-4 space-y-4">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Annual income tax</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($breakdown['annual_income_tax'] ?? 0, 2) }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Annual PF</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($breakdown['annual_pf'] ?? 0, 2) }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Annual professional tax</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($breakdown['annual_professional_tax'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-6">
            <h3 class="panel-title">Income tax slab calculation</h3>
            <p class="panel-copy">The system applies the active slab rates to the taxable annual income after standard deduction.</p>

            <div class="table-wrap mt-4">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Slab</th>
                        <th>Income range</th>
                        <th>Rate</th>
                        <th>Amount in slab</th>
                        <th>Tax</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($incomeTaxSlabs as $slab)
                        <tr>
                            <td>{{ $slab['description'] ?? 'Tax slab' }}</td>
                            <td>{{ number_format($slab['income_from'] ?? 0, 2) }} - {{ number_format($slab['income_to'] ?? 0, 2) }}</td>
                            <td>{{ number_format($slab['tax_rate'] ?? 0, 2) }}%</td>
                            <td>{{ number_format($slab['taxable_amount'] ?? 0, 2) }}</td>
                            <td>{{ number_format($slab['tax_amount'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-slate-500">No tax slab breakdown is available for this payslip.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
