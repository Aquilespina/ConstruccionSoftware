<?php
use Illuminate\Support\Facades\Route;

Route::group([], function () {
    // Grupo API para mascotas
    Route::prefix('mascotas')->name('api.mascotas.')->group(function () {
        require __DIR__.'/mascotas/index.php';
    });

    // Grupo API para propietarios
    Route::prefix('propietarios')->name('api.propietarios.')->group(function () {
        require __DIR__.'/propietarios/index.php';
    });

    // Grupo API para citas
    Route::prefix('citas')->name('api.citas.')->group(function () {
        require __DIR__.'/citas/index.php';
    });

    Route::prefix('usuarios')->name('api.usuarios.')->group(function () {
        require __DIR__.'/usuarios/index.php';
    });
});