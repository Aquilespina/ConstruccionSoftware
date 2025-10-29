<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Usuario\UsuarioController;

Route::get('/', [UsuarioController::class, 'index'])->name('index');
Route::get('/crear', [UsuarioController::class, 'create'])->name('create');
Route::get('/buscar', [UsuarioController::class, 'index'])->name('search');
Route::post('/', [UsuarioController::class, 'store'])->name('store');

Route::prefix('{id_usuario}')->group(function(){
    Route::get('', [UsuarioController::class, 'show'])->name('show');
    Route::put('', [UsuarioController::class, 'update'])->name('update');
    Route::delete('', [UsuarioController::class, 'destroy'])->name('destroy');
});