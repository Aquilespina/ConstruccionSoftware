<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cita\Cita;

class ExpedienteController extends Controller
{
    // Lista de expedientes agregados desde citas + mascota + propietario + profesional
    public function index()
    {
        $expedientes = Cita::with(['mascota.propietario', 'profesional'])
            ->orderByDesc('fecha')
            ->orderByDesc('horario')
            ->get();

        return view('dash.recepcion.expedientes', compact('expedientes'));
    }
}
