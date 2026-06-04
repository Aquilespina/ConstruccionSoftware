<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use App\Models\Cita\Cita;
use App\Models\Hospitalizaciones\Hospitalizaciones;
use App\Models\Mascota\Mascota;
use Carbon\Carbon;

class ExpedienteController extends Controller
{
    public function index()
    {
        $mascotas = Mascota::with('propietario')
            ->withCount('citas')
            ->withMax('citas', 'fecha')
            ->orderBy('nombre')
            ->get();

        return view('dash.recepcion.expedientes', compact('mascotas'));
    }

    public function historial(string $id)
    {
        $mascota = Mascota::with('propietario')->find($id);

        if (!$mascota) {
            return response()->json([
                'success' => false,
                'message' => 'Mascota no encontrada',
            ], 404);
        }

        // Consultas (citas) con recetas
        $consultas = Cita::with(['profesional', 'recetas'])
            ->where('id_mascota', $id)
            ->orderByDesc('fecha')
            ->orderByDesc('horario')
            ->get()
            ->map(fn (Cita $c) => [
                'tipo'          => 'consulta',
                'fecha_orden'   => ($c->fecha instanceof Carbon
                    ? $c->fecha->format('Y-m-d')
                    : Carbon::parse($c->fecha)->format('Y-m-d'))
                    . ' ' . substr((string) ($c->horario ?? '00:00'), 0, 5),
                'id_cita'       => $c->id_cita,
                'fecha'         => $c->fecha instanceof Carbon
                    ? $c->fecha->format('Y-m-d')
                    : Carbon::parse($c->fecha)->format('Y-m-d'),
                'hora'          => substr((string) ($c->horario ?? ''), 0, 5),
                'tipo_cita'     => $c->tipo_cita ?? 'Consulta',
                'tipo_servicio' => $c->tipo_servicio,
                'profesional'   => optional($c->profesional)->nombre ?? '-',
                'diagnostico'   => $c->diagnostico,
                'observaciones' => $c->observaciones,
                'peso_mascota'  => $c->peso_mascota,
                'estado'        => $c->estado,
                'recetas'       => $c->recetas->map(fn ($r) => [
                    'id_receta'    => $r->id_receta,
                    'medicamento'  => $r->medicamento,
                    'dosis'        => $r->dosis,
                    'indicaciones' => $r->indicaciones,
                    'fecha'        => $r->fecha ? Carbon::parse($r->fecha)->format('Y-m-d') : null,
                ])->values(),
            ]);

        // Hospitalizaciones
        $hospitalizaciones = Hospitalizaciones::where('id_mascota', $id)
            ->orderByDesc('fecha_ingreso')
            ->get()
            ->map(fn (Hospitalizaciones $h) => [
                'tipo'          => 'hospitalizacion',
                'fecha_orden'   => Carbon::parse($h->fecha_ingreso)->format('Y-m-d H:i:s'),
                'id'            => $h->id_hospitalizacion,
                'fecha_ingreso' => Carbon::parse($h->fecha_ingreso)->format('Y-m-d'),
                'fecha_egreso'  => $h->fecha_egreso
                    ? Carbon::parse($h->fecha_egreso)->format('Y-m-d')
                    : null,
                'estado'        => $h->estado,
                'observaciones' => $h->observaciones,
            ]);

        // Timeline unificado ordenado por fecha descendente
        $timeline = $consultas->merge($hospitalizaciones)
            ->sortByDesc('fecha_orden')
            ->values();

        return response()->json([
            'success'   => true,
            'mascota'   => [
                'id_mascota'  => $mascota->id_mascota,
                'nombre'      => $mascota->nombre,
                'especie'     => ucfirst($mascota->especie ?? ''),
                'raza'        => $mascota->raza,
                'propietario' => optional($mascota->propietario)->nombre ?? '-',
                'alergias'    => $mascota->alergias,
            ],
            'timeline'  => $timeline,
        ]);
    }
}
