<?php

use App\Http\Controllers\Profesional\ProfesionalController;
use Illuminate\Support\Facades\Route;

// Rutas para profesionales (sin prefix porque ya se incluye desde web.php)
Route::post('/', [ProfesionalController::class, 'store'])->name('store');
Route::get('/activos', [ProfesionalController::class, 'activos'])->name('activos');
Route::get('/especialidades', [ProfesionalController::class, 'especialidades'])->name('especialidades');
Route::get('/especialidad/{especialidad}', [ProfesionalController::class, 'porEspecialidad'])->name('porEspecialidad');
Route::get('/{id}', [ProfesionalController::class, 'show'])->name('show');
Route::put('/{id}', [ProfesionalController::class, 'update'])->name('update');
Route::patch('/{id}/estado', [ProfesionalController::class, 'cambiarEstado'])->name('cambiarEstado');
Route::delete('/{id}', [ProfesionalController::class, 'destroy'])->name('destroy');