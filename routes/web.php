<?php

use Illuminate\Support\Facades\Route;
use App\exception_hendling\CustomException;
use App\exception_hendling\ValidationException;
use App\exception_hendling\NotFoundException;
use App\exception_hendling\AuthenticationException;
use App\exception_hendling\CriticalException;
use App\Http\Controllers\ExceptionLogController;

Route::get('/', fn() => view('welcome'));

/*
|--------------------------------------------------------------------------
| Test Exception Routes
|--------------------------------------------------------------------------
*/
Route::get('/exception',            fn() => throw new CustomException('This is a custom exception'));
Route::get('/exception-validation', fn() => throw new ValidationException('Validation failed: email is required'));
Route::get('/exception-notfound',   fn() => throw new NotFoundException('Resource not found'));
Route::get('/exception-auth',       fn() => throw new AuthenticationException('Unauthorized access attempt'));
Route::get('/exception-critical',   fn() => throw new CriticalException('Critical system failure'));

/*
|--------------------------------------------------------------------------
| Exception Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::get('/exception-history',              [ExceptionLogController::class, 'index']);
Route::delete('/exception-delete/{id}',       [ExceptionLogController::class, 'destroy']);
Route::post('/exception-bulk-delete',         [ExceptionLogController::class, 'bulkDelete']);
Route::post('/exception-clear-all',           [ExceptionLogController::class, 'clearAll']);
Route::post('/exception-clear-old',           [ExceptionLogController::class, 'clearOld']);
Route::patch('/exception-status/{id}',        [ExceptionLogController::class, 'updateStatus']);
Route::get('/exception-export-csv',           [ExceptionLogController::class, 'exportCsv']);
