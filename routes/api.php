<?php

use App\Http\Middleware\RequestTiming;
use Illuminate\Support\Facades\Route;

Route::middleware(RequestTiming::class)->group(function () {
    Route::get('/people/search', [App\Http\Controllers\PeopleController::class, 'search']);
    Route::get('/people/{id}', [App\Http\Controllers\PeopleController::class, 'details']);

    Route::get('/films/search', [App\Http\Controllers\FilmsController::class, 'search']);
    Route::get('/films/{id}', [App\Http\Controllers\FilmsController::class, 'details']);
});

Route::get('/average-request-timing', App\Http\Controllers\MetricsController::class);
