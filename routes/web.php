<?php

/*
|--------------------------------------------------------------------------
| routes/web.php
|--------------------------------------------------------------------------
| This is where you define URL routes for your Laravel app.
|
| Route::get('/path', [Controller::class, 'method'])
|   → Responds to GET requests (visiting a URL in browser)
|
| Route::post('/path', [Controller::class, 'method'])
|   → Responds to POST requests (form submissions)
|
| ->name('route.name')
|   → Gives the route a name so you can use route('route.name')
|     in Blade instead of hardcoding URLs. If the URL changes,
|     you update it here once, and all views stay correct.
*/

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', [UrlController::class, 'index'])->name('home');

// Handle form submission
Route::post('/shorten', [UrlController::class, 'shorten'])->name('shorten');

// Redirect a short code to the original URL
// {code} is a route parameter — captures anything after the slash
Route::get('/{code}', [UrlController::class, 'redirect'])->name('redirect');
