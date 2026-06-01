<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Profesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfesionalController extends Controller
{
    public const ESPECIALIDADES = [
        'Cirugía',
        'Dermatología',
        'Cardiología',
        'Oftalmología',
        'Neurología',
        'Medicina General',
    ];

    private const RFC_REGEX = '/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/';

    private const NOMBRE_REGEX = '/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\.\-]+$/u';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profesionales = Profesional::orderBy('nombre')->get();
        $especialidades = self::ESPECIALIDADES;

        return view('dash.recepcion.profesionales', compact('profesionales', 'especialidades'));
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
            $validator = Validator::make(
                $this->datosParaValidacion($request),
                $this->reglasStore(),
                $this->mensajesValidacion()
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $this->normalizarDatosProfesional($validator->validated());
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
            $profesional = Profesional::with([
                'citas' => function ($query) {
                    $query->with('mascota')
                        ->orderByDesc('fecha')
                        ->orderByDesc('horario');
                },
            ])->find($id);

            if (!$profesional) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profesional no encontrado'
                ], 404);
            }

            $mascotasAtendidas = $profesional->citas
                ->pluck('mascota')
                ->filter()
                ->unique('id_mascota')
                ->values();

            return response()->json([
                'success' => true,
                'data' => $profesional,
                'citas' => $profesional->citas,
                'total_citas' => $profesional->citas->count(),
                'total_mascotas' => $mascotasAtendidas->count(),
                'mascotas_atendidas' => $mascotasAtendidas,
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

            $validator = Validator::make(
                $this->datosParaValidacion($request),
                $this->reglasUpdate($id),
                $this->mensajesValidacion()
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $this->normalizarDatosProfesional($validator->validated());
            if (!array_key_exists('activo', $request->all())) {
                $data['activo'] = false;
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

            $tieneCitas = DB::table('cita')->where('rfc_profesional', $id)->exists();
            
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

    private function reglasStore(): array
    {
        return [
            'rfc' => ['required', 'string', 'min:12', 'max:13', 'regex:' . self::RFC_REGEX, 'unique:profesional,rfc'],
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:' . self::NOMBRE_REGEX],
            'correo' => ['required', 'email', 'max:100', 'unique:profesional,correo'],
            'especialidades' => ['required', 'array', 'min:1'],
            'especialidades.*' => ['string', Rule::in(self::ESPECIALIDADES)],
            'turno' => ['nullable', 'in:Matutino,Vespertino,Nocturno'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    private function reglasUpdate(string $rfc): array
    {
        return [
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:' . self::NOMBRE_REGEX],
            'correo' => ['required', 'email', 'max:100', 'unique:profesional,correo,' . $rfc . ',rfc'],
            'especialidades' => ['required', 'array', 'min:1'],
            'especialidades.*' => ['string', Rule::in(self::ESPECIALIDADES)],
            'turno' => ['nullable', 'in:Matutino,Vespertino,Nocturno'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    private function mensajesValidacion(): array
    {
        return [
            'rfc.required' => 'El RFC es obligatorio.',
            'rfc.min' => 'El RFC debe tener al menos 12 caracteres.',
            'rfc.max' => 'El RFC no puede superar 13 caracteres.',
            'rfc.regex' => 'El RFC no tiene un formato válido (ej. XAXX010101000).',
            'rfc.unique' => 'Ya existe un profesional con ese RFC.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max' => 'El nombre no puede superar 100 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras, espacios, puntos y guiones.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
            'correo.unique' => 'Ese correo ya está registrado para otro profesional.',
            'especialidades.required' => 'Seleccione al menos una especialidad.',
            'especialidades.min' => 'Seleccione al menos una especialidad.',
            'especialidades.*.in' => 'La especialidad seleccionada no es válida.',
            'turno.in' => 'El turno debe ser Matutino, Vespertino o Nocturno.',
        ];
    }

    private function datosParaValidacion(Request $request): array
    {
        $datos = $request->all();

        if (isset($datos['rfc']) && is_string($datos['rfc'])) {
            $datos['rfc'] = strtoupper(trim($datos['rfc']));
        }

        if (isset($datos['nombre']) && is_string($datos['nombre'])) {
            $datos['nombre'] = preg_replace('/\s+/', ' ', trim($datos['nombre']));
        }

        if (isset($datos['correo']) && is_string($datos['correo'])) {
            $datos['correo'] = strtolower(trim($datos['correo']));
        }

        if (!isset($datos['especialidades']) && $request->filled('especialidad')) {
            $datos['especialidades'] = array_values(array_filter(array_map(
                'trim',
                preg_split('/\s*,\s*/', (string) $request->input('especialidad'))
            )));
        }

        return $datos;
    }

    private function normalizarDatosProfesional(array $data): array
    {
        if (isset($data['rfc'])) {
            $data['rfc'] = strtoupper($data['rfc']);
        }

        if (isset($data['nombre'])) {
            $data['nombre'] = preg_replace('/\s+/', ' ', trim($data['nombre']));
        }

        if (isset($data['correo'])) {
            $data['correo'] = strtolower(trim($data['correo']));
        }

        if (isset($data['especialidades'])) {
            $seleccionadas = array_values(array_intersect(
                self::ESPECIALIDADES,
                array_unique($data['especialidades'])
            ));
            $data['especialidad'] = implode(', ', $seleccionadas);
            unset($data['especialidades']);
        }

        $data['activo'] = (bool) ($data['activo'] ?? true);

        return $data;
    }
}