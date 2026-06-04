<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cita\CitaController;

Route::get('/', [CitaController::class, 'index'])->name('index');
Route::get('/calendario', [CitaController::class, 'calendario'])->name('calendario');
Route::get('/crear', [CitaController::class, 'create'])->name('create');
Route::post('/', [CitaController::class, 'store'])->name('store');

Route::prefix('{id_cita}')->group(function () {
    Route::get('', [CitaController::class, 'show'])->name('show');
    Route::get('/editar', [CitaController::class, 'edit'])->name('edit');
    Route::put('', [CitaController::class, 'update'])->name('update');
    Route::patch('/estado', [CitaController::class, 'cambiarEstado'])->name('cambiarEstado');
    Route::delete('', [CitaController::class, 'destroy'])->name('destroy');
});