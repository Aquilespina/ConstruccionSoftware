<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Propietario\PropietarioController;
use App\Models\Propietario\Propietario;

Route::prefix('propietarios')->name('propietarios.')->controller(PropietarioController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/crear', 'create')->name('create');
    Route::get('/buscar', 'index')->name('search');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
});