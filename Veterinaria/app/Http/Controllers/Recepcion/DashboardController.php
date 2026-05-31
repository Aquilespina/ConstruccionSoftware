<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Cita\Cita;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        // Conteos
        $citasHoy = Cita::whereDate('fecha', $hoy)->count();
        // Consultas pendientes: citas con fecha estrictamente posterior al día de hoy
        $consultasPendientes = Cita::whereDate('fecha', '>', $hoy->toDateString())->count();

        // Pacientes nuevos del mes: mascotas cuya primera cita registrada cae en el mes actual
        $primerRegistroPacientes = Cita::query()
            ->select('id_mascota', DB::raw('MIN(fecha) as primera_fecha'))
            ->groupBy('id_mascota');

        $pacientesNuevos = DB::query()
            ->fromSub($primerRegistroPacientes, 'primeros_registros')
            ->whereDate('primera_fecha', '>=', $inicioMes)
            ->whereDate('primera_fecha', '<=', $hoy)
            ->count();

        // Hospitalizaciones pendientes/por venir: registros con fecha_ingreso estrictamente posterior al día de hoy
        // (si se desea contar hospitalizaciones activas, cambiar la condición a whereIn('estado', [...]))
        $hospitalizaciones = DB::table('hospitalizacion')
            ->whereDate('fecha_ingreso', '>', $hoy->toDateString())
            ->count();

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

        // Flujo clínico diario (base solicitada): citas + espera
        $citasFlujo = Cita::with(['mascota.propietario'])
            ->whereDate('fecha', '>=', $hoy)
            ->where('estado', '!=', 'Programada')
            ->orderBy('horario')
            ->limit(20)
            ->get();

        $esperaFlujo = Cita::with(['mascota.propietario'])
            ->whereDate('fecha', '>=', $hoy)
            ->where('estado', 'Programada')
            ->orderBy('horario')
            ->limit(20)
            ->get();

        $flujoDiario = $citasFlujo->map(function ($cita) {
            $propietario = optional(optional($cita->mascota)->propietario);
            return [
                'fecha' => optional($cita->fecha)->format('Y-m-d'),
                'hora' => $cita->horario,
                'paciente' => $cita->mascota->nombre ?? '-',
                'propietario' => ($propietario->nombre_completo ?? $propietario->nombre ?? '-'),
                'tipo' => 'CITA',
                'estado' => strtoupper((string) ($cita->estado ?? 'PROGRAMADA')),
            ];
        })->merge(
            $esperaFlujo->map(function ($p) {
                $propietario = optional(optional($p->mascota)->propietario);
                return [
                    'fecha' => optional($p->fecha)->format('Y-m-d'),
                    'hora' => $p->horario,
                    'paciente' => $p->mascota->nombre ?? '-',
                    'propietario' => ($propietario->nombre_completo ?? $propietario->nombre ?? '-'),
                    'tipo' => 'ESPERA',
                    'estado' => 'EN_ESPERA',
                ];
            })
        )->sortBy(function ($item) {
            return ($item['fecha'] ?? '') . ' ' . ($item['hora'] ?? '');
        })->values();

        return response()->json([
            'citas_hoy' => $citasHoy,
            'pacientes_nuevos' => $pacientesNuevos,
            'consultas_pendientes' => $consultasPendientes,
            'hospitalizaciones' => $hospitalizaciones,
            'proximas_citas' => $proximasCitas,
            'pacientes_espera' => $pacientesEspera,
            'flujo_diario' => $flujoDiario,
        ]);
    }
}
