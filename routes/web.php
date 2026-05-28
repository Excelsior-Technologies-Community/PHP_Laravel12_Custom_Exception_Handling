<?php

use Illuminate\Support\Facades\Route;
use App\exception_hendling\CustomException;
use App\Models\ExceptionLog;

Route::get('/', function () {

    return view('welcome');
});


// Custom Exception Route
Route::get('/exception', function () {

    throw new CustomException("This is the custom exception");
});


// Exception History Page
Route::get('/exception-history', function () {

    $logs = ExceptionLog::latest()->get();

    return view('history', compact('logs'));
});