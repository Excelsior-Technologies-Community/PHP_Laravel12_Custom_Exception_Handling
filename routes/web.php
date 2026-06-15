<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\exception_hendling\CustomException;
use App\Models\ExceptionLog;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Test Exception
|--------------------------------------------------------------------------
*/

Route::get('/exception', function () {
    throw new CustomException("This is the custom exception");
});


/*
|--------------------------------------------------------------------------
| Exception History
|--------------------------------------------------------------------------
*/

Route::get('/exception-history', function (Request $request) {

    $query = ExceptionLog::query();

    if ($request->search) {
        $query->where('message', 'like', '%' . $request->search . '%')
              ->orWhere('url', 'like', '%' . $request->search . '%')
              ->orWhere('exception_type', 'like', '%' . $request->search . '%');
    }

    $logs = $query->oldest()->paginate(5);

    $totalExceptions = ExceptionLog::count();

    $todayExceptions = ExceptionLog::whereDate(
        'created_at',
        Carbon::today()
    )->count();

    $latestException = ExceptionLog::latest()->first();

    return view('history', compact(
        'logs',
        'totalExceptions',
        'todayExceptions',
        'latestException'
    ));
});

/*
|--------------------------------------------------------------------------
| Delete Log
|--------------------------------------------------------------------------
*/

Route::delete('/exception-delete/{id}', function ($id) {

    ExceptionLog::findOrFail($id)->delete();

    return redirect()
        ->back()
        ->with('success', 'Exception log deleted successfully.');
});