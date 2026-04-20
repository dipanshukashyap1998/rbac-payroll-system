@extends('layouts.app', ['title' => 'Payrolls'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">receipt_long</span> Payroll operations</span>
                <h2>Payroll runs</h2>
                <p>Track payroll batches by month, year, and processing status.</p>
            </div>
        </section>

        <div class="card">
            <div class="toolbar-card mb-4">
                <div>
                    <h3 class="panel-title">Company payroll runs</h3>
                    <p class="panel-copy">Every cycle is listed chronologically for quick operational review.</p>
                </div>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Month</th><th>Year</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($payrolls as $payroll)
                        <tr>
                            <td>{{ str_pad($payroll->month, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $payroll->year }}</td>
                            <td><span class="badge {{ $payroll->status === 'paid' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($payroll->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-slate-500">No payroll runs found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $payrolls->links() }}</div>
        </div>
    </div>
@endsection
