<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Profesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProfesionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profesionales = Profesional::orderBy('nombre')->get();
        return view('dash.recepcion.profesionales', compact('profesionales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'message' => 'Use el método store para crear un nuevo profesional'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Validación de datos
            $validator = Validator::make($request->all(), [
                'rfc' => 'required|string|max:13|unique:profesional,rfc',
                'nombre' => 'required|string|max:100',
                'correo' => 'nullable|email|max:100',
                'especialidad' => 'nullable|string|max:100',
                'turno' => 'nullable|in:Matutino,Vespertino,Nocturno',
                'activo' => 'sometimes|boolean',
            ], [
                'rfc.required' => 'El RFC es obligatorio',
                'rfc.unique' => 'Ya existe un profesional con ese RFC',
                'nombre.required' => 'El nombre es obligatorio',
                'correo.email' => 'El correo debe tener un formato válido',
                'turno.in' => 'El turno debe ser Matutino, Vespertino o Nocturno',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear el profesional
            $data = $validator->validated();
            // Normalizar checkbox 'activo' (cuando no viene marcado)
            $data['activo'] = (bool)($data['activo'] ?? true);
            $profesional = Profesional::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profesional creado correctamente',
                'data' => $profesional
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el profesional: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $profesional = Profesional::find($id);
            
            if (!$profesional) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profesional no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $profesional,
                'message' => 'Profesional obtenido correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el profesional: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response()->json([
            'message' => 'Use el método show para obtener datos del profesional'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        
        try {
            $profesional = Profesional::find($id);
            
            if (!$profesional) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profesional no encontrado'
                ], 404);
            }

            // Validación de datos (correo único excepto para el mismo RFC)
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100',
                'correo' => 'nullable|email|max:100|unique:profesional,correo,' . $id . ',rfc',
                'especialidad' => 'nullable|string|max:100',
                'turno' => 'nullable|in:Matutino,Vespertino,Nocturno',
                'activo' => 'sometimes|boolean',
            ], [
                'nombre.required' => 'El nombre es obligatorio',
                'correo.email' => 'El correo debe tener un formato válido',
                'turno.in' => 'El turno debe ser Matutino, Vespertino o Nocturno',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar el profesional
            $data = $validator->validated();
            if (array_key_exists('activo', $data)) {
                $data['activo'] = (bool)$data['activo'];
            }
            $profesional->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profesional actualizado correctamente',
                'data' => $profesional
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el profesional: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        
        try {
            $profesional = Profesional::find($id);
            
            if (!$profesional) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profesional no encontrado'
                ], 404);
            }

            // Verificar si el profesional tiene citas asociadas
            $tieneCitas = DB::table('citas')->where('id_profesional', $id)->exists();
            
            if ($tieneCitas) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el profesional porque tiene citas asociadas'
                ], 422);
            }

            $profesional->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profesional eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el profesional: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener profesionales por especialidad
     */
    public function porEspecialidad($especialidad)
    {
        try {
            $profesionales = Profesional::where('especialidad', $especialidad)
                            ->where('activo', true)
                            ->orderBy('nombre')
                            ->get();

            return response()->json([
                'success' => true,
                'data' => $profesionales,
                'message' => 'Profesionales de ' . $especialidad . ' obtenidos correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener profesionales por especialidad: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener profesionales activos
     */
    public function activos()
    {
        try {
            $profesionales = Profesional::where('activo', true)
                            ->orderBy('nombre')
                            ->get();

            return response()->json([
                'success' => true,
                'data' => $profesionales,
                'message' => 'Profesionales activos obtenidos correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener profesionales activos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del profesional
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $profesional = Profesional::find($id);
            
            if (!$profesional) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profesional no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'activo' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $profesional->update(['activo' => (bool)$request->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del profesional actualizado correctamente',
                'data' => $profesional
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todas las especialidades disponibles
     */
    public function especialidades()
    {
        try {
            $especialidades = Profesional::select('especialidad')
                            ->distinct()
                            ->whereNotNull('especialidad')
                            ->orderBy('especialidad')
                            ->pluck('especialidad');

            return response()->json([
                'success' => true,
                'data' => $especialidades,
                'message' => 'Especialidades obtenidas correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener especialidades: ' . $e->getMessage()
            ], 500);
        }
    }
}