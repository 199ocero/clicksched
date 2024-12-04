<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NewPostController;
use App\Http\Controllers\Platform\BlueskyController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/accounts', [AccountsController::class, 'index'])->name('accounts.index');

    // New Post routes
    Route::get('/new-post', [NewPostController::class, 'index'])->name('new-post.index');

    // Posts routes
    Route::get('/posts', [PostsController::class, 'index'])->name('posts.index');

    // Posts routes
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');

    // Bluesky routes
    Route::post('/bluesky/store', [BlueskyController::class, 'store'])->name('bluesky.store');
    Route::post('/bluesky/destroy', [BlueskyController::class, 'destroy'])->name('bluesky.destroy');
});

// Google OAuth routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])
    ->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
    ->name('google.callback');

require __DIR__.'/auth.php';
