<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Receta\RecetaController;

// Nota: routes/api.php ya define el prefijo '/api/recetas'
Route::get('/', [RecetaController::class, 'index']);
Route::post('/', [RecetaController::class, 'store']);
Route::get('/estadisticas', [RecetaController::class, 'estadisticas']);
Route::get('/{id}', [RecetaController::class, 'show']);
Route::put('/{id}', [RecetaController::class, 'update']);
Route::delete('/{id}', [RecetaController::class, 'destroy']);
Route::post('/{id}/renovar', [RecetaController::class, 'renovar']);