<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\WaitlistController;
use App\Http\Controllers\Api\V1\DistanceController;

Route::apiResource('waitlist', WaitlistController::class)
    ->only(['index', 'store', 'show']);

Route::post('/calculate-distance', [DistanceController::class, 'calculate']);
