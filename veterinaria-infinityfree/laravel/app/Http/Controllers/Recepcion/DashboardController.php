<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Cita\Cita;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        // Conteos
        $citasHoy = Cita::whereDate('fecha', $hoy)->count();
        $consultasPendientes = Cita::whereDate('fecha', $hoy)
            ->where('estado', 'Programada')
            ->count();

        // Pacientes "nuevos" del mes: mascotas con al menos una cita este mes
        $pacientesNuevos = Cita::whereBetween('fecha', [$inicioMes, $hoy])
            ->distinct('id_mascota')
            ->count('id_mascota');

        // Hospitalizaciones aún no implementado
        $hospitalizaciones = 0;

        // Próximas citas (hoy en adelante)
        $proximasCitas = Cita::with(['mascota.propietario'])
            ->whereDate('fecha', '>=', $hoy)
            ->orderBy('fecha')
            ->orderBy('horario')
            ->limit(5)
            ->get()
            ->map(function ($c) {
                return [
                    'hora' => $c->horario,
                    'fecha' => optional($c->fecha)->format('Y-m-d'),
                    'paciente' => $c->mascota->nombre ?? '-',
                    'propietario' => optional(optional($c->mascota)->propietario)->nombre_completo ?? '-',
                    'estado' => $c->estado,
                ];
            });

        // Pacientes en espera (asumimos Programada hoy)
        $pacientesEspera = Cita::with(['mascota.propietario'])
            ->whereDate('fecha', $hoy)
            ->where('estado', 'Programada')
            ->orderBy('horario')
            ->limit(5)
            ->get()
            ->map(function ($c) {
                return [
                    'llego' => $c->horario, // placeholder de llegada
                    'paciente' => $c->mascota->nombre ?? '-',
                    'propietario' => optional(optional($c->mascota)->propietario)->nombre_completo ?? '-',
                    'estado' => 'En espera',
                ];
            });

        return response()->json([
            'citas_hoy' => $citasHoy,
            'pacientes_nuevos' => $pacientesNuevos,
            'consultas_pendientes' => $consultasPendientes,
            'hospitalizaciones' => $hospitalizaciones,
            'proximas_citas' => $proximasCitas,
            'pacientes_espera' => $pacientesEspera,
        ]);
    }
}
