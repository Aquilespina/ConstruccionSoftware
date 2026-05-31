<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Recepcion\DashboardController;

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

    // Grupo API para profesionales - MANTENIENDO EL FORMATO REQUIRE
    Route::prefix('profesionales')->name('api.profesionales.')->group(function () {
        require __DIR__.'/profesionales/index.php';
    });
    // Grupo API para recetas
    Route::prefix('recetas')->name('api.recetas.')->group(function () {
        require __DIR__.'/recetas/index.php';
    });

    // Dashboard de recepción
    Route::get('/dashboard/recepcion', [DashboardController::class, 'stats'])->name('api.dashboard.recepcion');
});