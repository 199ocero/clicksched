<?php

use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;

/**
 * Uses web and auth middleware to ensure that the user is authenticated.
 */
Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/posts', [PostsController::class, 'getPosts']);
});
