<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Authentication routes

use App\Http\Controllers\Auth\GitHubSocialiteController;

Auth::routes();

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

// Include Wave Routes
Wave::routes();

Route::get('auth/github/redirect', [GitHubSocialiteController::class, 'redirect'])->name('github.login');
Route::get('auth/github/callback', [GitHubSocialiteController::class, 'callback']);
