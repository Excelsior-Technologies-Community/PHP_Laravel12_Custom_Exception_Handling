<?php

use Illuminate\Support\Facades\Route;
use App\exception_hendling\CustomException;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/exception', function () {
    throw new CustomException("This is the custome exception");
});