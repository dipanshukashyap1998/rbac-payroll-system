@extends('layouts.app', ['title' => 'Payslips'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">description</span> Payroll output</span>
                <h2>{{ $isEmployeeView ? 'My payslips' : 'Company payslips' }}</h2>
                <p>{{ $isEmployeeView ? 'Review your released payslips from the current and previous cycles.' : 'Track generated payslips across the company with clean release status visibility.' }}</p>
            </div>
        </section>

        <div class="card">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr>
                        @if(! $isEmployeeView)
                            <th>Employee</th>
                        @endif
                        <th>Month</th>
                        <th>Year</th>
                        <th>Net Salary</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payslips as $payslip)
                        <tr>
                            @if(! $isEmployeeView)
                                <td>{{ $payslip->employee?->user?->name }}</td>
                            @endif
                            <td>{{ str_pad($payslip->payrollRun?->month ?? 0, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $payslip->payrollRun?->year }}</td>
                            <td>{{ number_format($payslip->net_salary, 2) }}</td>
                            <td><span class="badge {{ ($payslip->payrollRun?->status ?? null) === 'paid' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($payslip->payrollRun?->status ?? '-') }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $isEmployeeView ? 4 : 5 }}" class="text-slate-500">No payslips found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(! $isEmployeeView)
                <div class="mt-4">{{ $payslips->links() }}</div>
            @endif
        </div>
    </div>
@endsection
