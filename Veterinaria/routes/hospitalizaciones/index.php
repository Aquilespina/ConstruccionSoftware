<?php
use App\Http\Controllers\Hospitalizaciones\HospitalizacionesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HospitalizacionesController::class, 'index'])->name('index');

Route::get('/crear', [HospitalizacionesController::class, 'create'])->name('create');
Route::post('/', [HospitalizacionesController::class, 'store'])->name('store');

Route::prefix('{id_hospitalizacion}')->group(function() {
    Route::get('', [HospitalizacionesController::class, 'show'])->name('show');
    Route::get('/editar', [HospitalizacionesController::class, 'edit'])->name('edit');
    Route::put('', [HospitalizacionesController::class, 'update'])->name('update');
    Route::delete('', [HospitalizacionesController::class, 'destroy'])->name('destroy');
});
