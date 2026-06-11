<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomerActivityLog;
use App\Models\ExportLog;
use App\Models\SystemLog;
use App\Models\ApiUsageLog;

class LogsController extends Controller
{
    public function customerActivityPage()
    {
        if (request()->ajax()) {
            return view('logs.customer-activity.index');
        }
        return view('logs.customer-activity.full');
    }

    public function exportHistoryPage()
    {
        if (request()->ajax()) {
            return view('logs.export-history.index');
        }
        return view('logs.export-history.full');
    }

    public function systemLogsPage()
    {
        if (request()->ajax()) {
            return view('logs.system-logs.index');
        }
        return view('logs.system-logs.full');
    }

    public function apiUsagePage()
    {
        if (request()->ajax()) {
            return view('logs.api-usage.index');
        }
        return view('logs.api-usage.full');
    }

    public function customerActivityData()
    {
        $request = request();
        $cols = ['id','user_id','activity_type','status','completed_at'];
        $q = CustomerActivityLog::query();
        foreach ($cols as $col) {
            if ($request->filled($col)) {
                $q->where($col, 'like', '%'.$request->get($col).'%');
            }
        }

        return $q->orderByDesc('id')
            ->simplePaginate(20)
            ->through(function ($log) {
                return [
                    'id' => $log->id,
                    'user_id' => $log->user_id,
                    'activity_type' => $log->activity_type,
                    'status' => $log->status,
                    'completed_at' => $log->completed_at,
                ];
            });
    }

    public function exportHistoryData()
    {
        $request = request();
        $cols = ['id','user_id','export_type','status','completed_at'];
        $q = ExportLog::query();
        foreach ($cols as $col) {
            if ($request->filled($col)) {
                $q->where($col, 'like', '%'.$request->get($col).'%');
            }
        }

        return $q->orderByDesc('id')
            ->simplePaginate(20)
            ->through(function ($log) {
                return [
                    'id' => $log->id,
                    'user_id' => $log->user_id,
                    'export_type' => $log->export_type,
                    'status' => $log->status,
                    'completed_at' => $log->completed_at,
                ];
            });
    }

    public function systemLogsData()
    {
        $request = request();
        $cols = ['id','type','command','status','started_at','finished_at'];
        $q = SystemLog::query();
        foreach ($cols as $col) {
            if ($request->filled($col)) {
                $q->where($col, 'like', '%'.$request->get($col).'%');
            }
        }

        return $q->orderByDesc('id')
            ->simplePaginate(20)
            ->through(function ($log) {
                return [
                    'id' => $log->id,
                    'type' => $log->type,
                    'command' => $log->command,
                    'status' => $log->status,
                    'started_at' => $log->started_at,
                    'finished_at' => $log->finished_at,
                ];
            });
    }

    public function apiUsageData()
    {
        $request = request();
        $cols = ['id','user_id','method','endpoint','action','response_status','created_at'];
        $q = ApiUsageLog::query();
        foreach ($cols as $col) {
            if ($request->filled($col)) {
                $q->where($col, 'like', '%'.$request->get($col).'%'); 
            }
        }

        return $q->orderByDesc('id')
            ->simplePaginate(20)
            ->through(function ($log) {
                return [
                    'id' => $log->id,
                    'user_id' => $log->user_id,
                    'method' => $log->method,
                    'endpoint' => $log->endpoint,
                    'action' => $log->action,
                    'response_status' => $log->response_status,
                    'created_at' => $log->created_at,
                ];
            });
    }
}