<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Propietario\PropietarioController;

Route::get('/', [PropietarioController::class, 'index'])->name('index');
Route::get('/crear', [PropietarioController::class, 'create'])->name('create');
Route::post('/', [PropietarioController::class, 'store'])->name('store');
Route::get('/exportar', [PropietarioController::class, 'exportar'])->name('exportar');
Route::get(
    '/buscar',
    [PropietarioController::class, 'buscar']
)->name('propietarios.buscar');

Route::prefix('{id_propietario}')->group(function(){
    Route::get('', [PropietarioController::class, 'show'])->name('show');
    Route::put('', [PropietarioController::class, 'update'])->name('update');
    Route::delete('', [PropietarioController::class, 'destroy'])->name('destroy');
});