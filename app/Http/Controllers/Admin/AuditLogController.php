<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('admin')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(25)->withQueryString();

        $actions = AuditLog::distinct()->pluck('action');

        return view('admin.audit-logs.index', compact('logs', 'actions'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('admin');
        return view('admin.audit-logs.show', compact('auditLog'));
    }
}
