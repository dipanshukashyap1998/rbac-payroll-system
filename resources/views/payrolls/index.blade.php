@extends('layouts.app', ['title' => 'Payrolls'])

@section('content')
    <div class="card">
        <h3 style="margin-top:0;">Company Payroll Runs</h3>
        <table class="table">
            <thead><tr><th>Month</th><th>Year</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($payrolls as $payroll)
                <tr>
                    <td>{{ str_pad($payroll->month, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $payroll->year }}</td>
                    <td>{{ ucfirst($payroll->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No payroll runs found.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:12px;">{{ $payrolls->links() }}</div>
    </div>
@endsection
