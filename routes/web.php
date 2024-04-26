<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return response()->json(['errors' => 'Unauthorized. Contact the Superuser and get the API-key'], 401);
})->name('login');