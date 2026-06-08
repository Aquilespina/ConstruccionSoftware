<?php

namespace App\Http\Controllers\Hospitalizaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mascota\Mascota;
use App\Models\Profesional;
use Carbon\Carbon;

class HospitalizacionesController extends Controller
{
    public function index()
    {
        try {
            $hospitalizaciones = DB::table('hospitalizacion as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->select(
                    'h.id_hospitalizacion',
                    'h.fecha_ingreso',
                    'h.fecha_egreso',
                    'h.estado',
                    'h.observaciones',
                    'm.nombre as mascota_nombre',
                    'm.especie',
                    'p.nombre as propietario_nombre'
                )
                ->orderBy('h.fecha_ingreso', 'desc')
                ->get();

            $stats = [
                'internados'  => DB::table('hospitalizacion')->where('estado', 'Internado')->count(),
                'tratamiento' => DB::table('hospitalizacion')->where('estado', 'Tratamiento')->count(),
                'alta_mes'    => DB::table('hospitalizacion')
                    ->where('estado', 'Alta')
                    ->whereMonth('fecha_egreso', now()->month)
                    ->whereYear('fecha_egreso', now()->year)
                    ->count(),
                'total' => DB::table('hospitalizacion')->count(),
            ];

            $mascotas = Mascota::getMascotasConPropietarios();

            return view('dash.recepcion.hospitalizaciones', compact('hospitalizaciones', 'mascotas', 'stats'));
        } catch (\Exception $e) {
            return view('dash.recepcion.hospitalizaciones', [
                'hospitalizaciones' => collect(),
                'mascotas'          => collect(),
                'stats'             => ['internados'=>0,'tratamiento'=>0,'alta_mes'=>0,'total'=>0],
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_mascota'    => 'required|exists:mascota,id_mascota',
                'fecha_ingreso' => 'required|date',
                'estado'        => 'required|in:Internado,Alta,Tratamiento',
                'observaciones' => 'required|string|max:1000',
                'id_cita'       => 'nullable|exists:cita,id_cita',
            ], [
                'id_mascota.required'    => 'Debe seleccionar una mascota.',
                'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
                'estado.required'        => 'Debe seleccionar un estado.',
                'observaciones.required' => 'Las observaciones son obligatorias.',
                'observaciones.max'      => 'Las observaciones no pueden exceder 1000 caracteres.',
            ]);

            DB::table('hospitalizacion')->insert([
                'id_mascota'    => $validated['id_mascota'],
                'fecha_ingreso' => Carbon::parse($validated['fecha_ingreso'])->format('Y-m-d H:i:s'),
                'fecha_egreso'  => null,
                'estado'        => $validated['estado'],
                'observaciones' => $validated['observaciones'],
            ]);

            if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => true, 'message' => 'Hospitalización registrada exitosamente']);
            }

            return redirect()->route('hospitalizaciones.index')->with('success', 'Hospitalización registrada');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'Datos inválidos', 'errors' => $e->errors()], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(string $id)
    {
        try {
            $hospitalizacion = DB::table('hospitalizacion as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->where('h.id_hospitalizacion', $id)
                ->select(
                    'h.*',
                    'm.nombre as mascota_nombre',
                    'm.especie',
                    'm.raza',
                    'm.años as edad',
                    'p.nombre as propietario_nombre',
                    'p.telefono as propietario_telefono'
                )
                ->first();

            if (!$hospitalizacion) {
                if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => 'Hospitalización no encontrada'], 404);
                }
                return redirect()->route('hospitalizaciones.index')->with('error', 'No encontrada');
            }

            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => true, 'hospitalizacion' => $hospitalizacion]);
            }

            return redirect()->route('hospitalizaciones.index');
        } catch (\Exception $e) {
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('hospitalizaciones.index')->with('error', 'Error al cargar');
        }
    }

    public function edit(string $id)
    {
        try {
            $hospitalizacion = DB::table('hospitalizacion as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->where('h.id_hospitalizacion', $id)
                ->select('h.*', 'm.nombre as mascota_nombre', 'p.nombre as propietario_nombre')
                ->first();

            if (!$hospitalizacion) {
                if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => 'No encontrada'], 404);
                }
                return redirect()->route('hospitalizaciones.index')->with('error', 'No encontrada');
            }

            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => true, 'hospitalizacion' => $hospitalizacion]);
            }

            return redirect()->route('hospitalizaciones.index');
        } catch (\Exception $e) {
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('hospitalizaciones.index')->with('error', 'Error');
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'estado'           => 'required|in:Internado,Alta,Tratamiento',
                'nuevo_comentario' => 'required|string|max:800',
                'fecha_egreso'     => 'nullable|date',
            ], [
                'estado.required'           => 'El estado es obligatorio.',
                'nuevo_comentario.required' => 'El comentario es obligatorio para registrar el cambio.',
                'nuevo_comentario.max'      => 'El comentario no puede exceder 800 caracteres.',
                'fecha_egreso.date'         => 'La fecha de egreso no es válida.',
            ]);

            // Obtener registro actual
            $actual = DB::table('hospitalizacion')->where('id_hospitalizacion', $id)->first();
            if (!$actual) {
                throw new \Exception('Hospitalización no encontrada');
            }

            // Concatenar nueva entrada al historial (no reemplazar)
            $observacionesActualizadas = trim($actual->observaciones ?? '');
            if ($observacionesActualizadas !== '') {
                $observacionesActualizadas .= "\n\n";
            }
            $observacionesActualizadas .= $validated['nuevo_comentario'];

            // fecha_egreso: si el nuevo estado es Alta y se envió una fecha, guardarla.
            // Si ya tenía fecha y sigue en Alta, conservarla.
            // Si cambia a otro estado, limpiar la fecha.
            $fechaEgreso = $actual->fecha_egreso;
            if ($validated['estado'] === 'Alta') {
                if (!empty($validated['fecha_egreso'])) {
                    $fechaEgreso = Carbon::parse($validated['fecha_egreso'])->format('Y-m-d H:i:s');
                } elseif (!$fechaEgreso) {
                    // Fallback: si no vino del JS, usar ahora
                    $fechaEgreso = Carbon::now()->format('Y-m-d H:i:s');
                }
            } else {
                $fechaEgreso = null;
            }

            DB::table('hospitalizacion')
                ->where('id_hospitalizacion', $id)
                ->update([
                    'estado'        => $validated['estado'],
                    'observaciones' => $observacionesActualizadas,
                    'fecha_egreso'  => $fechaEgreso,
                ]);

            if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => true, 'message' => 'Hospitalización actualizada exitosamente']);
            }

            return redirect()->route('hospitalizaciones.index')->with('success', 'Actualizada');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'Datos inválidos', 'errors' => $e->errors()], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::table('hospitalizacion')->where('id_hospitalizacion', $id)->delete();
            return redirect()->route('hospitalizaciones.index')->with('success', 'Hospitalización eliminada');
        } catch (\Exception $e) {
            return redirect()->route('hospitalizaciones.index')->with('error', 'Error al eliminar');
        }
    }

    public function create()
    {
        return redirect()->route('hospitalizaciones.index');
    }

    // ── Reporte diario ────────────────────────────────────────────────

    private function buildReporteDiario(): array
    {
        $hoy = now()->toDateString();

        // Pacientes actualmente internados o en tratamiento
        $activos = DB::table('hospitalizacion as h')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->whereIn('h.estado', ['Internado', 'Tratamiento'])
            ->select(
                'h.id_hospitalizacion', 'h.fecha_ingreso', 'h.estado', 'h.observaciones',
                'm.nombre as mascota', 'm.especie', 'm.raza',
                'p.nombre as propietario', 'p.telefono'
            )
            ->orderBy('h.fecha_ingreso', 'asc')
            ->get()
            ->map(function ($r) {
                $r->dias = (int) now()->startOfDay()
                    ->diffInDays(Carbon::parse($r->fecha_ingreso)->startOfDay());
                return $r;
            });

        // Ingresos de hoy
        $ingresosHoy = DB::table('hospitalizacion as h')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->whereDate('h.fecha_ingreso', $hoy)
            ->select(
                'h.id_hospitalizacion', 'h.fecha_ingreso', 'h.estado', 'h.observaciones',
                'm.nombre as mascota', 'm.especie',
                'p.nombre as propietario', 'p.telefono'
            )
            ->orderBy('h.fecha_ingreso', 'asc')
            ->get();

        // Altas de hoy
        $altasHoy = DB::table('hospitalizacion as h')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->where('h.estado', 'Alta')
            ->whereDate('h.fecha_egreso', $hoy)
            ->select(
                'h.id_hospitalizacion', 'h.fecha_ingreso', 'h.fecha_egreso', 'h.observaciones',
                'm.nombre as mascota', 'm.especie',
                'p.nombre as propietario', 'p.telefono'
            )
            ->orderBy('h.fecha_egreso', 'asc')
            ->get()
            ->map(function ($r) {
                $r->dias_internado = (int) Carbon::parse($r->fecha_ingreso)->startOfDay()
                    ->diffInDays(Carbon::parse($r->fecha_egreso)->startOfDay());
                return $r;
            });

        return compact('activos', 'ingresosHoy', 'altasHoy', 'hoy');
    }

    public function reporteDiario()
    {
        $data = $this->buildReporteDiario();
        return view('hospitalizaciones.reporte-diario', $data);
    }

    public function reporteDiarioPDF()
    {
        $data = $this->buildReporteDiario();
        $pdf  = \Barryvdh\DomPDF\Facade\Pdf::loadView('hospitalizaciones.reporte-diario-pdf', $data)
            ->setPaper('a4', 'portrait');
        return $pdf->download('reporte_hospitalizaciones_' . now()->format('Y-m-d') . '.pdf');
    }
}
