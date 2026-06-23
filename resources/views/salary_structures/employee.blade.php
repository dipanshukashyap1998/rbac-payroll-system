@extends('layouts.app', ['title' => 'My Salary Structure'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">wallet</span> Personal compensation</span>
                <h2>My salary structure</h2>
                <p>Review the salary structure assigned to your employee profile, including earnings, statutory deductions, and estimated in-hand salary.</p>
            </div>
        </section>

        @if($salaryAssignment && $salaryBreakdown)
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="card lg:col-span-2">
                    <h3 class="panel-title">{{ $salaryAssignment->salaryStructure?->name ?? 'Assigned Structure' }}</h3>
                    <p class="panel-copy">Effective from: {{ optional($salaryAssignment->effective_from)->format('d M Y') }}</p>

                    <div class="grid gap-4 sm:grid-cols-3 mt-4">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs uppercase tracking-wide text-slate-500">Gross salary</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($salaryBreakdown['gross_salary'] ?? 0, 2) }}</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs uppercase tracking-wide text-slate-500">Total deductions</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($salaryBreakdown['total_deductions'] ?? 0, 2) }}</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs uppercase tracking-wide text-slate-500">In-hand salary</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($salaryBreakdown['net_salary'] ?? 0, 2) }}</div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl border border-slate-200/70 bg-white/60 p-4 text-sm text-slate-600">
                        PF is calculated at 12% of basic pay plus DA. ESIC applies only when eligible wages are within the monthly ceiling of {{ number_format($salaryBreakdown['esic_wage_ceiling'] ?? 21000, 0) }}.
                    </div>
                </div>

                <div class="card">
                    <h3 class="panel-title">Take-home snapshot</h3>
                    <div class="mt-4 space-y-4">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs uppercase tracking-wide text-slate-500">Take-home ratio</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($salaryBreakdown['take_home_percent'] ?? 0, 2) }}%</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs uppercase tracking-wide text-slate-500">Annual gross</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($salaryBreakdown['annual_gross_salary'] ?? 0, 2) }}</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs uppercase tracking-wide text-slate-500">Annual in-hand</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($salaryBreakdown['annual_net_salary'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="card">
                    <h3 class="panel-title">Earnings</h3>
                    <p class="panel-copy">These lines build up your gross salary before deductions are applied.</p>

                    <div class="table-wrap mt-4">
                        <table class="table">
                            <tbody>
                            <tr><th>Basic salary</th><td>{{ number_format($salaryBreakdown['base_salary'] ?? 0, 2) }}</td></tr>
                            <tr><th>HRA</th><td>{{ number_format($salaryBreakdown['hra'] ?? 0, 2) }}</td></tr>
                            <tr><th>Allowances</th><td>{{ number_format($salaryBreakdown['allowances'] ?? 0, 2) }}</td></tr>
                            <tr><th>DA</th><td>{{ number_format($salaryBreakdown['da_amount'] ?? 0, 2) }}</td></tr>
                            <tr><th>TA</th><td>{{ number_format($salaryBreakdown['ta_amount'] ?? 0, 2) }}</td></tr>
                            @forelse($salaryBreakdown['earning_components'] ?? [] as $component)
                                <tr>
                                    <th>{{ $component['name'] }}</th>
                                    <td>{{ number_format($component['amount'] ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr><th colspan="2" class="text-slate-500">No extra earning components are configured.</th></tr>
                            @endforelse
                            <tr><th>Gross salary</th><td><strong>{{ number_format($salaryBreakdown['gross_salary'] ?? 0, 2) }}</strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <h3 class="panel-title">Deductions</h3>
                    <p class="panel-copy">These are the monthly deductions that reduce your take-home salary.</p>

                    <div class="table-wrap mt-4">
                        <table class="table">
                            <tbody>
                            <tr><th>Income tax</th><td>{{ number_format($salaryBreakdown['income_tax_monthly'] ?? 0, 2) }}</td></tr>
                            <tr><th>Professional tax</th><td>{{ number_format($salaryBreakdown['professional_tax'] ?? 0, 2) }}</td></tr>
                            <tr><th>PF deduction</th><td>{{ number_format($salaryBreakdown['pf_deduction'] ?? 0, 2) }} at 12% of {{ number_format($salaryBreakdown['pf_wage_base'] ?? 0, 2) }}</td></tr>
                            <tr>
                                <th>ESIC deduction</th>
                                <td>
                                    {{ number_format($salaryBreakdown['esic_deduction'] ?? 0, 2) }}
                                    @if(!empty($salaryBreakdown['esic_applicable']))
                                        <span class="text-slate-500">at 0.75% of {{ number_format($salaryBreakdown['esic_wage_base'] ?? 0, 2) }}</span>
                                    @else
                                        <span class="text-slate-500">not applicable above the ESIC wage ceiling</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Leave deduction</th>
                                <td>
                                    {{ number_format($salaryBreakdown['leave_deduction'] ?? 0, 2) }}
                                    @if(($salaryBreakdown['leave_deduction_days'] ?? 0) > 0)
                                        <span class="text-slate-500">for {{ number_format($salaryBreakdown['leave_deduction_days'] ?? 0, 2) }} day(s)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Configured deductions</th><td>{{ number_format($salaryBreakdown['other_deductions'] ?? 0, 2) }}</td></tr>
                            @forelse($salaryBreakdown['deduction_components'] ?? [] as $component)
                                <tr>
                                    <th>{{ $component['name'] }}</th>
                                    <td>{{ number_format($component['amount'] ?? 0, 2) }}</td>
                                </tr>
                            @empty
                            @endforelse
                            <tr><th>Total deductions</th><td><strong>{{ number_format($salaryBreakdown['total_deductions'] ?? 0, 2) }}</strong></td></tr>
                            <tr><th>In-hand salary</th><td><strong>{{ number_format($salaryBreakdown['net_salary'] ?? 0, 2) }}</strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(!empty($salaryBreakdown['leave_breakdown']['unpaid_leave_details']) || !empty($salaryBreakdown['leave_breakdown']['extra_paid_leave_details']) || !empty($salaryBreakdown['leave_breakdown']['unauthorized_leave_details']))
                <div class="card mt-6">
                    <h3 class="panel-title">Leave deduction details</h3>
                    <p class="panel-copy">This section shows approved unpaid leave, leave beyond the allotted balance, and leave taken without approval.</p>

                    <div class="table-wrap mt-4">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Days</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(($salaryBreakdown['leave_breakdown']['unpaid_leave_details'] ?? []) as $item)
                                <tr>
                                    <td>{{ $item['leave_type'] }}</td>
                                    <td>{{ number_format($item['days'] ?? 0, 2) }}</td>
                                    <td>Unpaid approved leave</td>
                                </tr>
                            @endforeach
                            @foreach(($salaryBreakdown['leave_breakdown']['extra_paid_leave_details'] ?? []) as $item)
                                <tr>
                                    <td>{{ $item['leave_type'] }}</td>
                                    <td>{{ number_format($item['days'] ?? 0, 2) }}</td>
                                    <td>Exceeded allotted balance</td>
                                </tr>
                            @endforeach
                            @foreach(($salaryBreakdown['leave_breakdown']['unauthorized_leave_details'] ?? []) as $item)
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

            <div class="card">
                <h3 class="panel-title">Income tax slab breakdown</h3>
                <p class="panel-copy">The annual income tax estimate uses the active slab rates after the standard deduction.</p>

                <div class="table-wrap mt-4">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Slab</th>
                            <th>Range</th>
                            <th>Rate</th>
                            <th>Amount in slab</th>
                            <th>Tax</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($salaryBreakdown['income_tax_slabs'] ?? [] as $slab)
                            <tr>
                                <td>{{ $slab['description'] ?? 'Tax slab' }}</td>
                                <td>{{ number_format($slab['income_from'] ?? 0, 2) }} - {{ number_format($slab['income_to'] ?? 0, 2) }}</td>
                                <td>{{ number_format($slab['tax_rate'] ?? 0, 2) }}%</td>
                                <td>{{ number_format($slab['taxable_amount'] ?? 0, 2) }}</td>
                                <td>{{ number_format($slab['tax_amount'] ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-slate-500">No tax slab breakdown is available.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card">
                <div class="empty-state">No salary structure has been assigned yet.</div>
            </div>
        @endif
    </div>
@endsection
