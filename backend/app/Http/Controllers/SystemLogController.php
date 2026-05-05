<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::latest();

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('source')) {
            $query->where('source', 'like', "%{$request->source}%");
        }

        $logs = $query->paginate(25)->withQueryString();

        $stats = [
            'info' => SystemLog::where('level', 'info')->count(),
            'warning' => SystemLog::where('level', 'warning')->count(),
            'error' => SystemLog::where('level', 'error')->count(),
            'critical' => SystemLog::where('level', 'critical')->count(),
        ];

        return view('admin.system-logs', compact('logs', 'stats'));
    }
}
