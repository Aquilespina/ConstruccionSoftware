<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Cita\Cita;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $propietarioTable = $this->propietarioTable();
        $timeColumn = $this->citaTimeColumn();
        $propietarioNameSql = $this->propietarioNameSql($propietarioTable);

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

        $citasBase = DB::table('cita as c')
            ->join('mascota as m', 'c.id_mascota', '=', 'm.id_mascota')
            ->join($propietarioTable . ' as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->selectRaw(
                "c.fecha as fecha, c.$timeColumn as hora, m.nombre as paciente, $propietarioNameSql, c.estado as estado"
            )
            ->whereDate('c.fecha', '>=', $hoy->toDateString());

        // Próximas citas (hoy en adelante, cualquier estado)
        $proximasCitas = (clone $citasBase)
            ->orderBy('c.fecha')
            ->orderBy('c.' . $timeColumn)
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'hora' => $c->hora,
                'fecha' => Carbon::parse($c->fecha)->format('Y-m-d'),
                'paciente' => $c->paciente ?? '-',
                'propietario' => $c->propietario ?? '-',
                'estado' => strtoupper((string) ($c->estado ?? '')),
            ]);

        // Pacientes en espera (citas programadas)
        $pacientesEspera = (clone $citasBase)
            ->whereRaw('LOWER(c.estado) = ?', ['programada'])
            ->orderBy('c.fecha')
            ->orderBy('c.' . $timeColumn)
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'llego' => $c->hora,
                'paciente' => $c->paciente ?? '-',
                'propietario' => $c->propietario ?? '-',
                'estado' => 'En espera',
            ]);

        // Flujo clínico diario: todas las citas de hoy ordenadas por hora
        $flujoDebug = null;
        try {
            $flujoDiario = DB::table('cita as c')
                ->leftJoin('mascota as m', 'c.id_mascota', '=', 'm.id_mascota')
                ->leftJoin($propietarioTable . ' as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->leftJoin('profesional as pr', 'c.rfc_profesional', '=', 'pr.rfc')
                ->selectRaw("c.$timeColumn as hora, m.nombre as paciente, $propietarioNameSql, pr.nombre as profesional, c.tipo_cita, c.estado")
                ->whereDate('c.fecha', $hoy->toDateString())
                ->orderBy('c.' . $timeColumn)
                ->limit(30)
                ->get()
                ->map(fn ($c) => [
                    'hora'        => substr((string) ($c->hora ?? ''), 0, 5),
                    'paciente'    => $c->paciente ?? '-',
                    'propietario' => $c->propietario ?? '-',
                    'profesional' => $c->profesional ?? '-',
                    'tipo_cita'   => $c->tipo_cita ?? '-',
                    'estado'      => ucfirst(strtolower((string) ($c->estado ?? 'programada'))),
                ])
                ->values();
        } catch (\Throwable $e) {
            $flujoDiario = collect();
            $flujoDebug = $e->getMessage();
        }

        return response()->json([
            'citas_hoy' => $citasHoy,
            'pacientes_nuevos' => $pacientesNuevos,
            'consultas_pendientes' => $consultasPendientes,
            'hospitalizaciones' => $hospitalizaciones,
            'proximas_citas' => $proximasCitas,
            'pacientes_espera' => $pacientesEspera,
            'flujo_diario' => $flujoDiario,
            '_debug' => [
                'propietario_table' => $propietarioTable,
                'time_column'       => $timeColumn,
                'hoy'               => $hoy->toDateString(),
                'flujo_error'       => $flujoDebug,
            ],
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }

    private function propietarioTable(): string
    {
        return Schema::hasTable('propietarios') ? 'propietarios' : 'propietario';
    }

    private function citaTimeColumn(): string
    {
        return Schema::hasColumn('cita', 'hora') ? 'hora' : 'horario';
    }

    private function propietarioNameSql(string $propietarioTable): string
    {
        if (Schema::hasColumn($propietarioTable, 'apellido')) {
            return "COALESCE(CONCAT(p.nombre, ' ', p.apellido), p.nombre) as propietario";
        }

        return 'p.nombre as propietario';
    }
}
