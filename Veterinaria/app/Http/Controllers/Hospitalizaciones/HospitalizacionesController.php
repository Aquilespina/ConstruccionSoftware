<?php

namespace App\Http\Controllers\Hospitalizaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mascota\Mascota;
use App\Models\Profesional;
use App\Models\Cita\Cita;
use Carbon\Carbon;

class HospitalizacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

            // Obtener datos para el formulario usando los getters de los modelos
            $mascotas = Mascota::getMascotasConPropietarios();
            $medicos = Profesional::getMedicosActivos();

            return view('dash.recepcion.hospitalizaciones', compact('hospitalizaciones', 'mascotas', 'medicos'));
        } catch (\Exception $e) {
            $mascotas = collect();
            $medicos = collect();
            return view('dash.recepcion.hospitalizaciones', [
                'hospitalizaciones' => collect(),
                'mascotas' => $mascotas,
                'medicos' => $medicos
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Obtener citas que pueden ser hospitalizadas
            $citas = DB::table('cita as c')
                ->join('mascota as m', 'c.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->leftJoin('hospitalizacion as h', 'c.id_cita', '=', 'h.id_cita')
                ->whereNull('h.id_cita') // Solo citas sin hospitalización
                ->select(
                    'c.id_cita',
                    'c.fecha',
                    'm.nombre as mascota_nombre',
                    'p.nombre as propietario_nombre'
                )
                ->get();

            $mascotas = Mascota::getMascotasConPropietarios();
            $medicos = Profesional::getMedicosActivos();

            return view('hospitalizaciones.create', compact('citas', 'mascotas', 'medicos'));
        } catch (\Exception $e) {
            return redirect()->route('hospitalizaciones.index')
                ->with('error', 'Error al cargar el formulario de hospitalización');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_mascota' => 'required|exists:mascota,id_mascota',
                'fecha_ingreso' => 'required|date',
                'estado' => 'required|in:Internado,Alta,Tratamiento',
                'id_cita' => 'nullable|exists:cita,id_cita',
                'observaciones' => 'nullable|string|max:1000'
            ]);

            // Convertir fecha al formato correcto para MySQL
            $fechaIngreso = \Carbon\Carbon::parse($validatedData['fecha_ingreso'])->format('Y-m-d H:i:s');
            
            DB::table('hospitalizacion')->insert([
                'id_mascota' => $validatedData['id_mascota'],
                'fecha_ingreso' => $fechaIngreso,
                'fecha_egreso' => null, // Se establece cuando se da de alta
                'estado' => $validatedData['estado'],
                'observaciones' => $validatedData['observaciones'] ?? null
            ]);

            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Hospitalización registrada exitosamente'
                ]);
            }
            
            return redirect()->route('hospitalizaciones.index')
                ->with('success', 'Hospitalización registrada exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si es una petición AJAX, devolver errores en JSON
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar la hospitalización: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al registrar la hospitalización: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $hospitalizacion = DB::table('hospitalizacion as h')
                ->join('cita as c', 'h.id_cita', '=', 'c.id_cita')
                ->join('mascota as m', 'c.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->join('profesional as pr', 'c.rfc_profesional', '=', 'pr.rfc')
                ->where('h.id_hospitalizacion', $id)
                ->select(
                    'h.*',
                    'm.nombre as mascota_nombre',
                    'm.especie',
                    'm.raza',
                    'm.edad',
                    'p.nombre as propietario_nombre',
                    'p.telefono as propietario_telefono',
                    'pr.nombre as medico_nombre',
                    'c.fecha as fecha_cita'
                )
                ->first();

            if (!$hospitalizacion) {
                return redirect()->route('hospitalizaciones.index')
                    ->with('error', 'Hospitalización no encontrada');
            }

            return view('hospitalizaciones.show', compact('hospitalizacion'));
        } catch (\Exception $e) {
            return redirect()->route('hospitalizaciones.index')
                ->with('error', 'Error al cargar los detalles de la hospitalización');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $hospitalizacion = DB::table('hospitalizacion')->where('id_hospitalizacion', $id)->first();
            
            if (!$hospitalizacion) {
                return redirect()->route('hospitalizaciones.index')
                    ->with('error', 'Hospitalización no encontrada');
            }

            return view('hospitalizaciones.edit', compact('hospitalizacion'));
        } catch (\Exception $e) {
            return redirect()->route('hospitalizaciones.index')
                ->with('error', 'Error al cargar el formulario de edición');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'fecha_ingreso' => 'required|date',
            'fecha_salida' => 'nullable|date|after:fecha_ingreso',
            'motivo' => 'required|string|max:500',
            'estado' => 'required|in:activa,finalizada'
        ]);

        try {
            $updateData = [
                'fecha_ingreso' => $request->fecha_ingreso,
                'motivo' => $request->motivo,
                'estado' => $request->estado
            ];

            if ($request->fecha_salida) {
                $updateData['fecha_salida'] = $request->fecha_salida;
            }

            DB::table('hospitalizacion')
                ->where('id_hospitalizacion', $id)
                ->update($updateData);

            return redirect()->route('hospitalizaciones.index')
                ->with('success', 'Hospitalización actualizada exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la hospitalización')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::table('hospitalizacion')->where('id_hospitalizacion', $id)->delete();
            
            return redirect()->route('hospitalizaciones.index')
                ->with('success', 'Hospitalización eliminada exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('hospitalizaciones.index')
                ->with('error', 'Error al eliminar la hospitalización');
        }
    }


}
