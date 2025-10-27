<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Mascota\MascotaController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboards por rol
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/admin', function () {
        return view('dash.admin');
    })->name('admin.home');
});

Route::middleware(['auth', 'role:medico'])->group(function () {
    Route::get('/medico', function () {
        return view('dash.medico');
    })->name('medico.home');
});

Route::middleware(['auth', 'role:recepcionista'])->group(function () {
    Route::get('/recepcion', function () {
        return view('dash.recepcion');
    })->name('recepcion.home');

    // Rutas de propietarios (archivo separado)
    require __DIR__.'/propietarios/propietario.php';

    // Rutas de mascotas
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
});
