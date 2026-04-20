@extends('layouts.app', ['title' => 'Audit Logs'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">history</span> Traceability</span>
                <h2>Audit logs</h2>
                <p>Follow important actions with a cleaner historical timeline of users, entities, and timestamps.</p>
            </div>
        </section>

        <div class="card">
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>User</th><th>Action</th><th>Entity</th><th>Date</th></tr></thead>
                    <tbody>
                    @forelse($auditLogs as $log)
                        <tr>
                            <td>{{ $log->user?->name ?? '-' }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                            <td>{{ $log->created_at?->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-slate-500">No logs found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $auditLogs->links() }}</div>
        </div>
    </div>
@endsection
