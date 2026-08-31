<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [PostController::class, 'index']
)->name('posts.index');


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get(
    'login',
    [AuthController::class, 'showLogin']
)->name('login');

Route::get(
    'register',
    [AuthController::class, 'showRegister']
)->name('register');

Route::post(
    'register',
    [AuthController::class, 'register']
)->name('register.store');

Route::post(
    'login',
    [AuthController::class, 'login']
)->name('login.store');

Route::post(
    'logout',
    [AuthController::class, 'logout']
)->name('logout');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Posts
    |--------------------------------------------------------------------------
    */

    Route::get(
        'posts/create',
        [PostController::class, 'create']
    )->name('posts.create');

    Route::post(
        'posts',
        [PostController::class, 'store']
    )->name('posts.store');

    Route::get(
        'posts/{post}/edit',
        [PostController::class, 'edit']
    )->name('posts.edit');

    Route::put(
        'posts/{post}',
        [PostController::class, 'update']
    )->name('posts.update');

    Route::delete(
        'posts/{post}',
        [PostController::class, 'destroy']
    )->name('posts.destroy');


    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */

    Route::post(
        'posts/{post}/comments',
        [CommentController::class, 'store']
    )->name('comments.store');

    Route::put(
        'comments/{comment}',
        [CommentController::class, 'update']
    )->name('comments.update');

    Route::delete(
        'comments/{comment}',
        [CommentController::class, 'destroy']
    )->name('comments.destroy');


    /*
    |--------------------------------------------------------------------------
    | Likes
    |--------------------------------------------------------------------------
    */

    Route::post(
        'posts/{post}/like',
        [LikeController::class, 'toggle']
    )->name('posts.like');
});


/*
|--------------------------------------------------------------------------
| Post Details
|--------------------------------------------------------------------------
*/

Route::get(
    'posts/{post}',
    [PostController::class, 'show']
)->name('posts.show');