<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Http;
Route::get('/', function () {

    return view('main-form');
});
Route::post('/', [\App\Http\Controllers\SEOApiController::class, 'search'])
    ->name('search');
Route::post('/location-suggest', [\App\Http\Controllers\SEOApiController::class, 'getLocations'])->name('locations-suggest');




