<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\PostViewController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Home / Posts
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


/*
|--------------------------------------------------------------------------
| Login Page
|--------------------------------------------------------------------------
*/

Route::get(
    'login',
    [AuthController::class, 'showLogin']
)->name('login');


/*
|--------------------------------------------------------------------------
| Login Submit
|--------------------------------------------------------------------------
*/

Route::post(
    'login',
    [AuthController::class, 'login']
)->name('login.store');


/*
|--------------------------------------------------------------------------
| Register Page
|--------------------------------------------------------------------------
*/

Route::get(
    'register',
    [AuthController::class, 'showRegister']
)->name('register');


/*
|--------------------------------------------------------------------------
| Register Submit
|--------------------------------------------------------------------------
*/

Route::post(
    'register',
    [AuthController::class, 'register']
)->name('register.store');


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

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
    | Create Post
    |--------------------------------------------------------------------------
    */

    Route::get(
        'posts/create',
        [PostController::class, 'create']
    )->name('posts.create');


    /*
    |--------------------------------------------------------------------------
    | Store Post
    |--------------------------------------------------------------------------
    */

    Route::post(
        'posts',
        [PostController::class, 'store']
    )->name('posts.store');


    /*
    |--------------------------------------------------------------------------
    | CSV Export
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | This must come BEFORE posts/{post}.
    |
    */

    Route::get(
        'posts/export',
        [PostController::class, 'export']
    )->name('posts.export');


    /*
    |--------------------------------------------------------------------------
    | Post Like
    |--------------------------------------------------------------------------
    */

    Route::post(
        'posts/{post}/like',
        [LikeController::class, 'toggle']
    )->name('posts.like');


    /*
    |--------------------------------------------------------------------------
    | Post Bookmark
    |--------------------------------------------------------------------------
    */

    Route::post(
        'posts/{post}/bookmark',
        [BookmarkController::class, 'toggle']
    )->name('posts.bookmark');


    /*
    |--------------------------------------------------------------------------
    | Post View
    |--------------------------------------------------------------------------
    */

    Route::post(
        'posts/{post}/view',
        [PostViewController::class, 'store']
    )->name('posts.view');


    /*
    |--------------------------------------------------------------------------
    | Edit Post
    |--------------------------------------------------------------------------
    */

    Route::get(
        'posts/{post}/edit',
        [PostController::class, 'edit']
    )->name('posts.edit');


    /*
    |--------------------------------------------------------------------------
    | Update Post
    |--------------------------------------------------------------------------
    */

    Route::put(
        'posts/{post}',
        [PostController::class, 'update']
    )->name('posts.update');


    /*
    |--------------------------------------------------------------------------
    | Delete Post
    |--------------------------------------------------------------------------
    */

    Route::delete(
        'posts/{post}',
        [PostController::class, 'destroy']
    )->name('posts.destroy');


    /*
    |--------------------------------------------------------------------------
    | Store Comment / Reply
    |--------------------------------------------------------------------------
    */

    Route::post(
        'posts/{post}/comments',
        [CommentController::class, 'store']
    )->name('comments.store');


    /*
    |--------------------------------------------------------------------------
    | Update Comment
    |--------------------------------------------------------------------------
    */

    Route::put(
        'comments/{comment}',
        [CommentController::class, 'update']
    )->name('comments.update');


    /*
    |--------------------------------------------------------------------------
    | Delete Comment
    |--------------------------------------------------------------------------
    */

    Route::delete(
        'comments/{comment}',
        [CommentController::class, 'destroy']
    )->name('comments.destroy');


    /*
    |--------------------------------------------------------------------------
    | Comment Like
    |--------------------------------------------------------------------------
    */

    Route::post(
        'comments/{comment}/like',
        [CommentLikeController::class, 'toggle']
    )->name('comments.like');
});


/*
|--------------------------------------------------------------------------
| Post Details
|--------------------------------------------------------------------------
|
| IMPORTANT:
| This wildcard route is LAST.
|
| Otherwise /posts/export could be treated as:
|
| /posts/{post}
|
*/

Route::get(
    'posts/{post}',
    [PostController::class, 'show']
)->name('posts.show');
