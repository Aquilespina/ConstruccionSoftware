<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Profesional\ProfesionalController;
use App\Models\Profesional;
use App\Models\Mascota\Mascota;
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Healthcheck simple (no toca la base de datos)
Route::get('/health', function () {
    return response()->view('health', [
        'app_name' => config('app.name'),
        'app_env' => config('app.env'),
        'app_url' => config('app.url'),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
    ]);
});

// Ping a la BD (solo si DB_PING=true en variables de entorno)
if (env('DB_PING', false)) {
    Route::get('/db-ping', function () {
        try {
            DB::select('select 1');
            return response('ok', 200);
        } catch (\Throwable $e) {
            return response('error: '.$e->getMessage(), 500);
        }
    });
}

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

        // Rutas para profesionales - 
        Route::prefix('profesionales')->name('profesionales.')->group(function () {
            Route::get('/', [ProfesionalController::class, 'index'])->name('index');

            require __DIR__.'/profesionales/index.php';
        });
        // Rutas para citas
        Route::prefix('citas')->name('citas.')->group(function () {
            require __DIR__.'/citas/index.php';
        });

        Route::get('/expedientes', [\App\Http\Controllers\Recepcion\ExpedienteController::class, 'index'])
            ->name('recepcion.expedientes');

        Route::get('/recetas', function () {
            $medicos = Profesional::orderBy('nombre')->get(['rfc as id', 'nombre', 'especialidad']);
            $mascotas = Mascota::with('propietario')
                ->orderBy('nombre')
                ->get(['id_mascota as id', 'nombre', 'especie', 'id_propietario']);
            return view('dash.recepcion.recetas', compact('medicos','mascotas'));
        })->name('recepcion.recetas');

        // Rutas para honorarios
        Route::prefix('honorarios')->name('honorarios.')->group(function () {
            require __DIR__.'/honorarios/index.php';
        });

        // Rutas para hospitalizaciones
        Route::prefix('hospitalizaciones')->name('hospitalizaciones.')->group(function () {
            require __DIR__.'/hospitalizaciones/index.php';
        });
    });
     
    

});
