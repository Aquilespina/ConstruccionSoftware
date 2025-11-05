<?php

use App\Http\Controllers\Honorarios\HonorariosController;
use Illuminate\Support\Facades\Route;

// Rutas para el módulo de honorarios

    Route::get('/', [HonorariosController::class, 'index'])->name('honorarios.index');
    Route::post('/', [HonorariosController::class, 'store'])->name('honorarios.store');
    Route::get('/conceptos/receta', [HonorariosController::class, 'getConceptosReceta'])->name('honorarios.conceptos');
    Route::get('/{id_honorario}', [HonorariosController::class, 'show'])->name('honorarios.show');
    Route::get('/{id_honorario}/edit', [HonorariosController::class, 'edit'])->name('honorarios.edit');
    Route::put('/{id_honorario}', [HonorariosController::class, 'update'])->name('honorarios.update');
    Route::get('/{id_honorario}/info-pago', [HonorariosController::class, 'getInfoPago'])->name('honorarios.info-pago');
    Route::post('/{id_honorario}/pago', [HonorariosController::class, 'registrarPago'])->name('honorarios.pago');
    Route::get('/{id_honorario}/pdf', [HonorariosController::class, 'generarPDF'])->name('honorarios.pdf');
