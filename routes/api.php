<?php

use App\Http\Controllers\HouseController;
use App\Http\Controllers\FlyerLocationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::apiResource('houses', HouseController::class);
    Route::apiResource('flyer-locations', FlyerLocationController::class);
});
