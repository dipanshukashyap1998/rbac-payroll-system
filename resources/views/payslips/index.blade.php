@extends('layouts.app', ['title' => 'Payslips'])

@section('content')
    <div class="card">
        <h3 style="margin-top:0;">{{ $isEmployeeView ? 'My Payslips (Current + Last Month Released)' : 'Company Payslips' }}</h3>

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
                    <td>{{ $payslip->net_salary }}</td>
                    <td>{{ ucfirst($payslip->payrollRun?->status ?? '-') }}</td>
                </tr>
            @empty
                <tr><td colspan="{{ $isEmployeeView ? 4 : 5 }}">No payslips found.</td></tr>
            @endforelse
            </tbody>
        </table>

        @if(! $isEmployeeView)
            <div style="margin-top:12px;">{{ $payslips->links() }}</div>
        @endif
    </div>
@endsection
