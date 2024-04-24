<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/user/create', [ApiController::class, 'createUser'])->middleware('auth:sanctum');

Route::post('/create-link', [ApiController::class, 'createLink'])->middleware('auth:sanctum');

Route::get('/get-links', [ApiController::class, 'getLinks'])->middleware('auth:sanctum');

Route::get('/{user}/{short_token}', [ApiController::class, 'redirectToLink']);


