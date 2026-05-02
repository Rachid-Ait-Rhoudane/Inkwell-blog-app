<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('/logout', [SessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index')
    ->middleware('auth');

Route::get('/categories/create', [CategoryController::class, 'create'])
    ->name('categories.create')
    ->middleware('auth');

Route::post('/categories', [CategoryController::class, 'store'])
    ->name('categories.store')
    ->middleware('auth');

Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
    ->name('categories.edit')
    ->middleware('auth');

Route::put('/categories/{category}', [CategoryController::class, 'update'])
    ->name('categories.update')
    ->middleware('auth');

Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
    ->name('categories.destroy')
    ->middleware('auth');

Route::get('/posts', [PostController::class, 'index'])
    ->name('posts.index')
    ->middleware('auth');

Route::get('/posts/create', [PostController::class, 'create'])
    ->name('posts.create')
    ->middleware('auth');

Route::post('/posts', [PostController::class, 'store'])
    ->name('posts.store')
    ->middleware('auth');

Route::get('/posts/{post}/edit', [PostController::class, 'edit'])
    ->name('posts.edit')
    ->middleware('auth');

Route::put('/posts/{post}', [PostController::class, 'update'])
    ->name('posts.update')
    ->middleware('auth');

Route::get('/posts/{post:slug}', [PostController::class, 'show'])
    ->name('posts.show');

Route::delete('/posts/{post}', [PostController::class, 'destroy'])
    ->name('posts.destroy')
    ->middleware('auth');

Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])
    ->name('comments.store')
    ->middleware('auth');

Route::get('/media', [MediaController::class, 'index'])
    ->name('media.index')
    ->middleware('auth');

Route::post('/media', [MediaController::class, 'store'])
    ->name('media.store')
    ->middleware('auth');

Route::delete('/media/{medium}', [MediaController::class, 'destroy'])
    ->name('media.destroy')
    ->middleware('auth');

Route::get('/comments', [CommentController::class, 'index'])
    ->name('comments.index')
    ->middleware('auth');

Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
    ->name('comments.destroy')
    ->middleware('auth');
