<?php

use Illuminate\Support\Facades\Route;

Route::get('/people/search', [App\Http\Controllers\PeopleController::class, 'search']);
