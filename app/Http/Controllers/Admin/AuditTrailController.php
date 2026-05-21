<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        // Filters
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $userFilter = $request->input('user_id');
        $actionFilter = $request->input('action');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Base query with eager loading (prevents N+1)
        $logsQuery = AuditTrail::with('user')->orderByDesc('created_at');

        if ($search) {
            $logsQuery->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $logsQuery->whereHas('user', function($q) use ($roleFilter) {
                $q->where('role', $roleFilter);
            });
        }

        if ($userFilter) {
            $logsQuery->where('user_id', $userFilter);
        }

        if ($actionFilter) {
            $logsQuery->where('action', $actionFilter);
        }

        if ($dateFrom) {
            $logsQuery->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $logsQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Pagination
        $logs = $logsQuery->paginate(25)->withQueryString();

        // For filters dropdowns
        $roles = User::select('role')->distinct()->pluck('role');
        $users = User::orderBy('name')->get();

        // Stats: total logs
        $totalLogs = $logsQuery->count();

        // Stats: action counts
        $actionCounts = AuditTrail::select('action', DB::raw('COUNT(*) as total'), DB::raw('MAX(created_at) as last_created'))
            ->groupBy('action')
            ->orderByDesc('last_created') // safe for ONLY_FULL_GROUP_BY
            ->pluck('total', 'action');

        return view('admin.audit_trails.index', compact(
            'logs', 'roles', 'users', 'totalLogs', 'actionCounts', 
            'search', 'roleFilter', 'userFilter', 'actionFilter', 'dateFrom', 'dateTo'
        ));
    }
}
