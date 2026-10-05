<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $logs = ActivityLog::with('user')
            ->when($search, function ($query) use ($search) {
                $query->where('activity', 'LIKE', "%$search%")
                      ->orWhere('module', 'LIKE', "%$search%")
                      ->orWhere('ip_address', 'LIKE', "%$search%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'LIKE', "%$search%");
                      });
            })
            ->latest()
            ->paginate(20);

        return view('activity_logs.index', compact('logs', 'search'));
    }
}
