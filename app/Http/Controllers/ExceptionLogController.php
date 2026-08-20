<?php

namespace App\Http\Controllers;

use App\Models\ExceptionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExceptionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ExceptionLog::query();

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('message', 'like', "%$s%")
                  ->orWhere('url', 'like', "%$s%")
                  ->orWhere('exception_type', 'like', "%$s%")
                  ->orWhere('ip_address', 'like', "%$s%");
            });
        }

        // Filters
        if ($request->filled('severity'))       $query->where('severity', $request->severity);
        if ($request->filled('status'))         $query->where('status', $request->status);
        if ($request->filled('http_method'))    $query->where('http_method', $request->http_method);
        if ($request->filled('exception_type')) $query->where('exception_type', $request->exception_type);

        // Date range
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        $perPage = in_array($request->per_page, [5, 10, 25, 50]) ? $request->per_page : 10;
        $logs    = $query->latest()->paginate($perPage)->withQueryString();

        // Stats
        $totalExceptions  = ExceptionLog::count();
        $todayExceptions  = ExceptionLog::whereDate('created_at', Carbon::today())->count();
        $latestException  = ExceptionLog::latest()->first();
        $resolvedCount    = ExceptionLog::where('status', 'resolved')->count();

        // Chart data — last 7 days
        $chartData = collect(range(6, 0))->map(fn($d) => [
            'date'  => Carbon::today()->subDays($d)->format('d M'),
            'count' => ExceptionLog::whereDate('created_at', Carbon::today()->subDays($d))->count(),
        ]);

        // Severity breakdown
        $severityData = ExceptionLog::selectRaw('severity, count(*) as count')
            ->groupBy('severity')->pluck('count', 'severity');

        // Exception types for filter dropdown
        $exceptionTypes = ExceptionLog::distinct()->pluck('exception_type');

        return view('history', compact(
            'logs', 'totalExceptions', 'todayExceptions', 'latestException',
            'resolvedCount', 'chartData', 'severityData', 'exceptionTypes', 'perPage'
        ));
    }

    public function destroy($id)
    {
        ExceptionLog::findOrFail($id)->delete();
        return back()->with('success', 'Log deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        if ($request->filled('ids')) {
            ExceptionLog::whereIn('id', $request->ids)->delete();
            return back()->with('success', count($request->ids) . ' logs deleted.');
        }
        return back()->with('error', 'No logs selected.');
    }

    public function clearAll()
    {
        ExceptionLog::truncate();
        return back()->with('success', 'All logs cleared.');
    }

    public function clearOld()
    {
        $deleted = ExceptionLog::where('created_at', '<', Carbon::now()->subDays(30))->delete();
        return back()->with('success', "$deleted old logs deleted.");
    }

    public function updateStatus(Request $request, $id)
    {
        $log = ExceptionLog::findOrFail($id);
        $log->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }

    public function exportCsv(Request $request)
    {
        $query = ExceptionLog::query();
        if ($request->filled('severity'))  $query->where('severity', $request->severity);
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->latest()->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="exception_logs.csv"'];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Message', 'URL', 'Type', 'Severity', 'Status', 'IP', 'Method', 'Date']);
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id, $log->message, $log->url, $log->exception_type,
                    $log->severity, $log->status, $log->ip_address,
                    $log->http_method, $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
