<?php

use App\Http\Controllers\HouseController;
use App\Http\Controllers\FlyerLocationController;
use App\Http\Controllers\UserLocationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::apiResource('houses', HouseController::class);
    Route::apiResource('flyer-locations', FlyerLocationController::class);

    // User location sharing
    Route::get('user-locations', [UserLocationController::class, 'index']);
    Route::post('user-location', [UserLocationController::class, 'update']);
    Route::delete('user-location', [UserLocationController::class, 'clear']);
});
