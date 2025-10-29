<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

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
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        require __DIR__.'/usuarios/index.php';
    });
});

Route::middleware(['auth', 'role:medico'])->group(function () {
    Route::get('/medico', function () {
        return view('dash.medico');
    })->name('medico.home');
});
Route::middleware(['auth', 'role:recepcionista'])->group(function () {
    Route::prefix('recepcion')->group(function () {
        // Dashboard principal
        Route::get('/', function () {
            return view('dash.recepcion.home');
        })->name('recepcion.home');

        // Rutas para propietarios
        Route::prefix('propietarios')->name('propietarios.')->group(function () {
            require __DIR__.'/propietarios/index.php';
        });

        // Rutas para mascotas
        Route::prefix('mascotas')->name('mascotas.')->group(function () {
            Route::get('/', function () {
                return view('dash.recepcion.mascotas');
            })->name('index');
            require __DIR__.'/mascotas/index.php';
        });

        // Rutas para médicos
        Route::get('/medicos', function () {
            return view('dash.recepcion.medicos');
        })->name('recepcion.medicos');

        // Rutas para citas
        Route::prefix('citas')->name('citas.')->group(function () {
            require __DIR__.'/citas/index.php';
        });

        // Agrega más rutas según necesites
        Route::get('/expedientes', function () {
            return view('dash.recepcion.expedientes');
        })->name('recepcion.expedientes');

        Route::get('/recetas', function () {
            return view('dash.recepcion.recetas');
        })->name('recepcion.recetas');

        Route::get('/honorarios', function () {
            return view('dash.recepcion.honorarios');
        })->name('recepcion.honorarios');

        Route::get('/hospitalizaciones', function () {
            return view('dash.recepcion.hospitalizaciones');
        })->name('recepcion.hospitalizaciones');
    });
});
