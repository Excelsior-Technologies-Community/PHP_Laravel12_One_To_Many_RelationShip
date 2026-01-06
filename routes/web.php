<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/test', [PostController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});
