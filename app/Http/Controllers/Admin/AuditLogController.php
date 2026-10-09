<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $action = (string) $request->query('action');

        $logs = AuditLog::with('actor:id,name,email')
            ->when($action !== '', fn ($q) => $q->where('action', $action))
            ->latest('created_at')
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit.index', compact('logs', 'actions', 'action'));
    }
}
