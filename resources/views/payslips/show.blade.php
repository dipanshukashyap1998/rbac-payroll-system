@extends('layouts.app', ['title' => 'Payslip Details'])

@section('content')
    @php
        $incomeTaxSlabs = $breakdown['income_tax_slabs'] ?? [];
    @endphp

    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">receipt_long</span> Payslip breakdown</span>
                <h2>{{ $payslip->employee?->user?->name ?? 'Employee' }}</h2>
                <p>Full payslip details with earnings, statutory deductions, and in-hand salary.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('payslips.index') }}" class="btn btn-secondary">Back to payslips</a>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card lg:col-span-2">
                <h3 class="panel-title">Pay summary</h3>
                <div class="grid gap-4 sm:grid-cols-3 mt-4">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Gross salary</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($breakdown['gross_salary'] ?? 0, 2) }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Total deductions</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($breakdown['total_deductions'] ?? 0, 2) }}</div>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-emerald-700">In-hand salary</div>
                        <div class="mt-1 text-2xl font-semibold text-emerald-900">{{ number_format($breakdown['net_salary'] ?? 0, 2) }}</div>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl border border-slate-200/70 bg-white/60 p-4 text-sm text-slate-600">
                    This is the monthly take-home amount after all shown deductions. Annual in-hand is {{ number_format($breakdown['annual_net_salary'] ?? 0, 2) }}.
                </div>
            </div>

            <div class="card">
                <h3 class="panel-title">Quick figures</h3>
                <div class="mt-4 space-y-4">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Payroll month</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-900">{{ str_pad($payslip->payrollRun?->month ?? 0, 2, '0', STR_PAD_LEFT) }} / {{ $payslip->payrollRun?->year ?? '-' }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Take-home ratio</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($breakdown['take_home_percent'] ?? 0, 2) }}%</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Annual gross</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($breakdown['annual_gross_salary'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2 mt-6">
            <div class="card">
                <h3 class="panel-title">Earnings</h3>
                <p class="panel-copy">All visible salary components before deductions.</p>

                <div class="table-wrap mt-4">
                    <table class="table">
                        <tbody>
                        <tr><th>Basic salary</th><td>{{ number_format($breakdown['base_salary'] ?? 0, 2) }}</td></tr>
                        <tr><th>HRA</th><td>{{ number_format($breakdown['hra'] ?? 0, 2) }}</td></tr>
                        <tr><th>Allowances</th><td>{{ number_format($breakdown['allowances'] ?? 0, 2) }}</td></tr>
                        <tr><th>DA</th><td>{{ number_format($breakdown['da'] ?? 0, 2) }}</td></tr>
                        <tr><th>TA</th><td>{{ number_format($breakdown['ta'] ?? 0, 2) }}</td></tr>
                        <tr><th>Gross salary</th><td><strong>{{ number_format($breakdown['gross_salary'] ?? 0, 2) }}</strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3 class="panel-title">Deductions</h3>
                <p class="panel-copy">Statutory and configured deductions that reduce your take-home pay.</p>

                <div class="table-wrap mt-4">
                    <table class="table">
                        <tbody>
                        <tr><th>Income tax</th><td>{{ number_format($breakdown['income_tax_monthly'] ?? 0, 2) }}</td></tr>
                        <tr><th>Professional tax</th><td>{{ number_format($breakdown['professional_tax_monthly'] ?? 0, 2) }}</td></tr>
                        <tr><th>PF deduction</th><td>{{ number_format($breakdown['pf_deduction'] ?? 0, 2) }} at {{ $breakdown['pf_rate'] ?? 12 }}% of {{ number_format($breakdown['pf_wage_base'] ?? 0, 2) }}</td></tr>
                        <tr>
                            <th>Leave deduction</th>
                            <td>
                                {{ number_format($breakdown['leave_deduction'] ?? 0, 2) }}
                                @if(($breakdown['leave_deduction_days'] ?? 0) > 0)
                                    <span class="text-slate-500">for {{ number_format($breakdown['leave_deduction_days'] ?? 0, 2) }} day(s) at {{ number_format(($breakdown['leave_breakdown']['daily_rate'] ?? 0), 2) }} per day</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>ESIC deduction</th>
                            <td>
                                {{ number_format($breakdown['monthly_esic'] ?? 0, 2) }}
                                @if(!empty($breakdown['esic_applicable']))
                                    <span class="text-slate-500">at {{ $breakdown['esic_rate'] ?? 0 }}% of {{ number_format($breakdown['esic_wage_base'] ?? 0, 2) }}</span>
                                @else
                                    <span class="text-slate-500">not applicable above the ESIC wage ceiling</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Other deductions</th><td>{{ number_format($breakdown['other_deductions'] ?? 0, 2) }}</td></tr>
                        <tr><th>Total deductions</th><td><strong>{{ number_format($breakdown['total_deductions'] ?? 0, 2) }}</strong></td></tr>
                        <tr><th>In-hand salary</th><td><strong>{{ number_format($breakdown['net_salary'] ?? 0, 2) }}</strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(!empty($breakdown['leave_breakdown']['unpaid_leave_details']) || !empty($breakdown['leave_breakdown']['extra_paid_leave_details']))
            <div class="card mt-6">
                <h3 class="panel-title">Leave deduction details</h3>
                <p class="panel-copy">These leave entries were charged because they were unpaid or exceeded the company leave balance.</p>

                <div class="table-wrap mt-4">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Days</th>
                            <th>Reason</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(($breakdown['leave_breakdown']['unpaid_leave_details'] ?? []) as $item)
                            <tr>
                                <td>{{ $item['leave_type'] }}</td>
                                <td>{{ number_format($item['days'] ?? 0, 2) }}</td>
                                <td>Unpaid leave</td>
                            </tr>
                        @endforeach
                        @foreach(($breakdown['leave_breakdown']['extra_paid_leave_details'] ?? []) as $item)
                            <tr>
                                <td>{{ $item['leave_type'] }}</td>
                                <td>{{ number_format($item['days'] ?? 0, 2) }}</td>
                                <td>Exceeded company leave balance</td>
                            </tr>
                        @endforeach
                        @foreach(($breakdown['leave_breakdown']['unauthorized_leave_details'] ?? []) as $item)
                            <tr>
                                <td>{{ $item['leave_type'] }}</td>
                                <td>{{ number_format($item['days'] ?? 0, 2) }}</td>
                                <td>{{ ucfirst($item['status'] ?? 'pending') }} without approval</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="card mt-6">
            <h3 class="panel-title">Tax slab breakdown</h3>
            <p class="panel-copy">Annual income tax is calculated on taxable income after the standard deduction.</p>

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
