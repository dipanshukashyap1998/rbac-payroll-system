<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Only admin can view audit logs.');

        $companyId = $user->ownedCompany()->value('id');

        $auditLogs = AuditLog::query()
            ->with('user')
            ->where(function ($query) use ($companyId, $user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('user.userRoles', fn ($roleQuery) => $roleQuery->where('company_id', $companyId));
            })
            ->latest()
            ->paginate(20);

        return view('audit_logs.index', compact('auditLogs'));
    }
}
