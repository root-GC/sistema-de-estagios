<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Listar logs de auditoria
     */
    public function index(Request $request)
    {
        $logs = Log::query();

        if ($request->has('user_id')) {
            $logs->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $logs->where('action', $request->action);
        }

        if ($request->has('model')) {
            $logs->where('model', $request->model);
        }

        $logs = $logs->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($logs);
    }

    /**
     * Ver log específico
     */
    public function show(Log $log)
    {
        return response()->json($log->load('user'));
    }
}
