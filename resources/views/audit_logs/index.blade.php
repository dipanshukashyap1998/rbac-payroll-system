@extends('layouts.app', ['title' => 'Audit Logs'])

@section('content')
    <div class="card">
        <h3 style="margin-top:0;">Audit Logs</h3>

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
                <tr><td colspan="4">No logs found.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:12px;">{{ $auditLogs->links() }}</div>
    </div>
@endsection
