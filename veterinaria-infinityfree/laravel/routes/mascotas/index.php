<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mascota\MascotaController;

Route::get('/', [MascotaController::class, 'index'])->name('index');
Route::get('/crear', [MascotaController::class, 'create'])->name('create');
Route::post('/', [MascotaController::class, 'store'])->name('store');

Route::prefix('{id_mascota}')->group(function() {
    Route::get('', [MascotaController::class, 'show'])->name('show');
    Route::get('/editar', [MascotaController::class, 'edit'])->name('edit');
    Route::put('', [MascotaController::class, 'update'])->name('update');
    Route::delete('', [MascotaController::class, 'destroy'])->name('destroy');
    
    // Historial básico
    Route::get('/historial', function ($id_mascota) {
        return response()->view('dash.recepcion', ['historialMascotaId' => $id_mascota]);
    })->name('historial');
});