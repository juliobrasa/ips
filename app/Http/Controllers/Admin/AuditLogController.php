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
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by event type
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->paginate(50)->withQueryString();

        // Get filter options
        $eventTypes = AuditLog::select('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type');

        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => AuditLog::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('admin.audit-logs.index', compact('logs', 'eventTypes', 'stats'));
    }

    public function show(AuditLog $log): View
    {
        $log->load('user');

        // Try to get the related model if it exists
        $relatedModel = null;
        if ($log->model_type && $log->model_id) {
            try {
                $relatedModel = $log->model_type::find($log->model_id);
            } catch (\Exception $e) {
                // Model class doesn't exist or model was deleted
            }
        }

        return view('admin.audit-logs.show', compact('log', 'relatedModel'));
    }
}
