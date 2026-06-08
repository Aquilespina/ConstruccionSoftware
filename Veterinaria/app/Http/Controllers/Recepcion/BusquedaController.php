<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusquedaController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['grupos' => [], 'total' => 0]);
        }

        $like   = '%' . $q . '%';
        $grupos = [];

        // ── 1. Propietarios ────────────────────────────────────────────
        try {
            $items = DB::table('propietario')
                ->where(function ($b) use ($like) {
                    $b->where('nombre',    'LIKE', $like)
                      ->orWhere('telefono','LIKE', $like);
                })
                ->select('nombre', 'telefono')
                ->limit(4)->get();

            if ($items->count()) {
                $grupos[] = [
                    'modulo' => 'Propietarios',
                    'color'  => '#3b82f6',
                    'items'  => $items->map(fn($p) => [
                        'titulo'    => $p->nombre,
                        'subtitulo' => 'Tel: ' . ($p->telefono ?? '—'),
                        'url'       => route('propietarios.index'),
                    ])->values(),
                ];
            }
        } catch (\Exception $e) {}

        // ── 2. Mascotas ────────────────────────────────────────────────
        try {
            $items = DB::table('mascota as m')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->where(function ($b) use ($like) {
                    $b->where('m.nombre',  'LIKE', $like)
                      ->orWhere('m.especie','LIKE', $like)
                      ->orWhere('m.raza',   'LIKE', $like)
                      ->orWhere('p.nombre', 'LIKE', $like);
                })
                ->select('m.nombre', 'm.especie', 'm.raza', 'p.nombre as propietario')
                ->limit(4)->get();

            if ($items->count()) {
                $grupos[] = [
                    'modulo' => 'Mascotas',
                    'color'  => '#10b981',
                    'items'  => $items->map(fn($m) => [
                        'titulo'    => $m->nombre . ' · ' . ucfirst($m->especie ?? '') . ($m->raza ? ' (' . $m->raza . ')' : ''),
                        'subtitulo' => 'Propietario: ' . $m->propietario,
                        'url'       => route('mascotas.index'),
                    ])->values(),
                ];
            }
        } catch (\Exception $e) {}

        // ── 3. Citas ───────────────────────────────────────────────────
        try {
            $items = DB::table('cita as c')
                ->join('mascota as m', 'c.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->where(function ($b) use ($like) {
                    $b->where('m.nombre',        'LIKE', $like)
                      ->orWhere('p.nombre',       'LIKE', $like)
                      ->orWhere('c.motivo',        'LIKE', $like)
                      ->orWhere('c.observaciones', 'LIKE', $like);
                })
                ->select('c.fecha', 'c.estado', 'c.motivo', 'm.nombre as mascota', 'p.nombre as propietario')
                ->orderBy('c.fecha', 'desc')
                ->limit(3)->get();

            if ($items->count()) {
                $grupos[] = [
                    'modulo' => 'Citas',
                    'color'  => '#f59e0b',
                    'items'  => $items->map(fn($c) => [
                        'titulo'    => $c->mascota . ' — ' . Carbon::parse($c->fecha)->format('d/m/Y'),
                        'subtitulo' => ($c->motivo ?? 'Sin motivo') . ' · ' . $c->estado,
                        'url'       => route('citas.index'),
                    ])->values(),
                ];
            }
        } catch (\Exception $e) {}

        // ── 4. Hospitalizaciones ───────────────────────────────────────
        try {
            $items = DB::table('hospitalizacion as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->where(function ($b) use ($like) {
                    $b->where('m.nombre',        'LIKE', $like)
                      ->orWhere('p.nombre',       'LIKE', $like)
                      ->orWhere('h.observaciones','LIKE', $like);
                })
                ->select('h.id_hospitalizacion', 'h.fecha_ingreso', 'h.estado', 'm.nombre as mascota', 'p.nombre as propietario')
                ->orderBy('h.fecha_ingreso', 'desc')
                ->limit(3)->get();

            if ($items->count()) {
                $grupos[] = [
                    'modulo' => 'Hospitalizaciones',
                    'color'  => '#ef4444',
                    'items'  => $items->map(fn($h) => [
                        'titulo'    => $h->mascota . ' — Ingreso: ' . Carbon::parse($h->fecha_ingreso)->format('d/m/Y'),
                        'subtitulo' => 'Propietario: ' . $h->propietario . ' · ' . $h->estado,
                        'url'       => route('hospitalizaciones.index'),
                    ])->values(),
                ];
            }
        } catch (\Exception $e) {}

        // ── 5. Recetas ─────────────────────────────────────────────────
        try {
            $items = DB::table('receta as r')
                ->join('mascota as m', 'r.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->where(function ($b) use ($like) {
                    $b->where('m.nombre',     'LIKE', $like)
                      ->orWhere('p.nombre',    'LIKE', $like)
                      ->orWhere('r.medicamento','LIKE', $like);
                })
                ->select('r.id_receta', 'r.fecha', 'r.medicamento', 'm.nombre as mascota', 'p.nombre as propietario')
                ->orderBy('r.fecha', 'desc')
                ->limit(3)->get();

            if ($items->count()) {
                $grupos[] = [
                    'modulo' => 'Recetas',
                    'color'  => '#8b5cf6',
                    'items'  => $items->map(fn($r) => [
                        'titulo'    => 'Receta #' . $r->id_receta . ' — ' . $r->mascota,
                        'subtitulo' => ($r->medicamento ?? 'Sin medicamento') . ' · ' . Carbon::parse($r->fecha)->format('d/m/Y'),
                        'url'       => route('recepcion.recetas'),
                    ])->values(),
                ];
            }
        } catch (\Exception $e) {}

        // ── 6. Honorarios ──────────────────────────────────────────────
        try {
            $items = DB::table('honorario as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->where(function ($b) use ($like) {
                    $b->where('m.nombre',  'LIKE', $like)
                      ->orWhere('p.nombre', 'LIKE', $like)
                      ->orWhere('h.estado', 'LIKE', $like);
                })
                ->select('h.id_honorario', 'h.fecha_ingreso', 'h.estado', 'h.saldo_pendiente', 'm.nombre as mascota', 'p.nombre as propietario')
                ->orderBy('h.fecha_ingreso', 'desc')
                ->limit(3)->get();

            if ($items->count()) {
                $grupos[] = [
                    'modulo' => 'Honorarios',
                    'color'  => '#0891b2',
                    'items'  => $items->map(fn($h) => [
                        'titulo'    => '#' . $h->id_honorario . ' — ' . $h->mascota . ' (' . $h->estado . ')',
                        'subtitulo' => 'Propietario: ' . $h->propietario . ' · Saldo: $' . number_format($h->saldo_pendiente, 2),
                        'url'       => route('honorarios.honorarios.show', $h->id_honorario),
                    ])->values(),
                ];
            }
        } catch (\Exception $e) {}

        $total = collect($grupos)->sum(fn($g) => count($g['items']));

        return response()->json([
            'grupos' => $grupos,
            'total'  => $total,
            'query'  => $q,
        ]);
    }
}
