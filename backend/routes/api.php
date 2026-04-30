<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GamificationController;

Route::post('/update-xp', [GamificationController::class, 'updateXp']);
Route::get('/progress', [GamificationController::class, 'getProgress']);
