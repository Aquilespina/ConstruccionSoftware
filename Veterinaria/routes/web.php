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

// ─── Setup inicial: crea el usuario recepcionista ───────────────────────────
// Visita /setup/vetclinic2024 para generar el usuario.
// ELIMINA esta ruta del hosting una vez creado el usuario.
Route::get('/setup/{token}', function (string $token) {
    if ($token !== 'vetclinic2024') {
        abort(403);
    }

    $existe = \App\Models\User::where('correo_electronico', 'recep@vet.com')->first();

    if ($existe) {
        return response('<h2 style="font-family:sans-serif;color:#059669">&#10003; El usuario ya existe.<br><br>
            <b>Correo:</b> recep@vet.com<br>
            <b>Usuario:</b> Recepción<br>
            <small style="color:#6b7280">Inicia sesión con tu contraseña.</small></h2>', 200);
    }

    \App\Models\User::create([
        'correo_electronico' => 'recep@vet.com',
        'nombre_usuario'     => 'Recepción',
        'password'           => \Illuminate\Support\Facades\Hash::make('recep123'),
        'tipo_permiso'       => 'recepcionista',
        'estado'             => 'activo',
    ]);

    return response('<h2 style="font-family:sans-serif;color:#059669">&#10003; Usuario creado correctamente.<br><br>
        <b>Correo:</b> recep@vet.com<br>
        <b>Usuario:</b> Recepción<br>
        <b>Contraseña:</b> recep123<br>
        <b>Rol:</b> recepcionista<br><br>
        <b style="color:#dc2626">&#9888; Elimina esta ruta del hosting después de usarla.</b></h2>', 201);
})->name('setup');
// ────────────────────────────────────────────────────────────────────────────

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
