<?php
use App\Http\Controllers\Cita\CitaController;
use Illuminate\Support\Facades\Route;

Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');
Route::get('/citas/create', [CitaController::class, 'create'])->name('citas.create');
Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');
Route::get('/citas/{cita}/edit', [CitaController::class, 'edit'])->name('citas.edit');
Route::put('/citas/{cita}', [CitaController::class, 'update'])->name('citas.update');
Route::delete('/citas/{cita}', [CitaController::class, 'destroy'])->name('citas.destroy');
