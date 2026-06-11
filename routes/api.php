<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonaController;

Route::prefix('personas')->group(function () {
    Route::get('/', [PersonaController::class, 'index']);
    Route::post('/', [PersonaController::class, 'store']);
    Route::get('/{id}', [PersonaController::class, 'show']);
    Route::put('/{id}', [PersonaController::class, 'update']);
    Route::delete('/{id}', [PersonaController::class, 'destroy']);
});