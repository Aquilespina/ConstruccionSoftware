<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mascota\MascotaController;

    Route::prefix('mascotas')->name('mascotas.')->controller(MascotaController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/crear', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/editar', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        // Historial básico
        Route::get('/{id}/historial', function ($id) {
            return response()->view('dash.recepcion', [ 'historialMascotaId' => $id ]);
        })->name('historial');
    });