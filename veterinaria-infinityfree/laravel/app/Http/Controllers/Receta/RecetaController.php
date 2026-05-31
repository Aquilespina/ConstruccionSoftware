<?php

namespace App\Http\Controllers\Receta;

use App\Http\Controllers\Controller;
use App\Models\Receta\Receta as RecetaModel;
use App\Models\Cita\Cita as CitaModel;
use App\Models\Mascota\Mascota;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = RecetaModel::with(['mascota', 'cita', 'mascota.propietario', 'cita.profesional']);
            
            // Filtros
            if ($request->has('mascota_id') && $request->mascota_id != '') {
                $query->where('id_mascota', $request->mascota_id);
            }
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('medicamento', 'like', "%{$search}%")
                      ->orWhereHas('mascota', function($q) use ($search) {
                          $q->where('nombre', 'like', "%{$search}%");
                      })
                      ->orWhereHas('mascota.propietario', function($q) use ($search) {
                          $q->where('nombre', 'like', "%{$search}%");
                      })
                      ->orWhereHas('cita.profesional', function($q) use ($search) {
                          $q->where('nombre', 'like', "%{$search}%");
                      });
                });
            }
            
            if ($request->has('estado') && $request->estado != '') {
                $estado = $request->estado;
                $query->where('estado', $estado);
            }
            
            if ($request->has('medico') && $request->medico != '') {
                // En este sistema 'medico' es el RFC del profesional
                $rfcProfesional = $request->medico;
                $query->whereHas('cita', function($q) use ($rfcProfesional) {
                    $q->where('rfc_profesional', $rfcProfesional);
                });
            }
            
            if ($request->has('fecha') && $request->fecha != '') {
                $query->whereDate('fecha', $request->fecha);
            }
            
            // Paginación
            $perPage = $request->get('per_page', 10);
            $recetas = $query->orderBy('fecha', 'desc')->paginate($perPage);
            
            // Formatear respuesta
            $formattedRecetas = $recetas->getCollection()->map(function($receta) {
                return [
                    'id' => $receta->id_receta,
                    'codigo' => 'REC-' . str_pad($receta->id_receta, 3, '0', STR_PAD_LEFT),
                    'mascota' => [
                        'nombre' => $receta->mascota->nombre ?? 'N/A',
                        'propietario' => $receta->mascota->propietario->nombre ?? 'N/A',
                        'especie' => $receta->mascota->especie ?? 'N/A',
                        'edad' => $receta->mascota->edad ?? null,
                        'peso' => $receta->mascota->peso ?? null,
                    ],
                    'medico' => [
                        'nombre' => $receta->cita->profesional->nombre ?? 'N/A',
                        'especialidad' => $receta->cita->profesional->especialidad ?? 'N/A',
                    ],
                    'fecha_emision' => $receta->fecha,
                    'fecha_vencimiento' => $receta->proxima_cita,
                    'diagnostico' => $receta->cita->diagnostico ?? 'Diagnóstico no disponible',
                    'medicamento' => $receta->medicamento,
                    'tipo_medicamento' => $receta->tipo_medicamento,
                    'dosis' => $receta->dosis,
                    'indicaciones' => $receta->indicaciones,
                    'estado' => $this->calcularEstado($receta->proxima_cita),
                    'medicamentos_count' => 1, // Por la estructura actual
                ];
            });
            
            return response()->json([
                'data' => $formattedRecetas,
                'meta' => [
                    'total' => $recetas->total(),
                    'per_page' => $recetas->perPage(),
                    'current_page' => $recetas->currentPage(),
                    'last_page' => $recetas->lastPage(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar las recetas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'mascota_id' => 'required|exists:mascota,id_mascota',
                // Validar contra profesionales por RFC (PK)
                'medico_id' => 'required|exists:profesional,rfc',
                'diagnostico' => 'required|string|max:500',
                'fecha_emision' => 'required|date',
                'fecha_vencimiento' => 'required|date|after:fecha_emision',
                'instrucciones' => 'required|string',
                'observaciones' => 'nullable|string',
                'medicamentos' => 'required|array|min:1',
                'medicamentos.*.nombre' => 'required|string|max:100',
                'medicamentos.*.dosis' => 'required|string|max:100',
                'medicamentos.*.frecuencia' => 'required|string|max:100',
                'medicamentos.*.duracion' => 'required|string|max:100',
                'medicamentos.*.instrucciones' => 'nullable|string',
            ]);
            
            // Crear cita primero si no existe (campos alineados al modelo Cita)
            $cita = CitaModel::create([
                'id_mascota' => $validated['mascota_id'],
                'rfc_profesional' => $validated['medico_id'],
                'fecha' => $validated['fecha_emision'],
                'horario' => '00:00:00',
                'diagnostico' => $validated['diagnostico'],
                'observaciones' => $validated['observaciones'] ?? null,
                'estado' => 'completada',
            ]);
            
            // Crear recetas para cada medicamento
            $recetasCreadas = [];
            foreach ($validated['medicamentos'] as $medicamento) {
                $receta = RecetaModel::create([
                    'id_mascota' => $validated['mascota_id'],
                    'id_cita' => $cita->id_cita,
                    'medicamento' => $medicamento['nombre'],
                    'tipo_medicamento' => 'Medicamento', // Puedes ajustar esto
                    'dosis' => $medicamento['dosis'],
                    'indicaciones' => $medicamento['instrucciones'] . "\n\nFrecuencia: " . $medicamento['frecuencia'] . "\nDuración: " . $medicamento['duracion'],
                    'proxima_cita' => $validated['fecha_vencimiento'],
                    'fecha' => $validated['fecha_emision'],
                ]);
                
                $recetasCreadas[] = $receta;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Receta creada exitosamente',
                'data' => [
                    'cita_id' => $cita->id_cita,
                    'recetas' => $recetasCreadas
                ]
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al crear la receta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $receta = RecetaModel::with([
                'mascota', 
                'mascota.propietario',
                'cita', 
                'cita.profesional'
            ])->findOrFail($id);
            
            $formattedReceta = [
                'id' => $receta->id_receta,
                'codigo' => 'REC-' . str_pad($receta->id_receta, 3, '0', STR_PAD_LEFT),
                'mascota' => [
                    'nombre' => $receta->mascota->nombre,
                    'especie' => $receta->mascota->especie,
                    'edad' => $receta->mascota->edad,
                    'peso' => $receta->mascota->peso,
                ],
                'propietario' => [
                    'nombre' => $receta->mascota->propietario->nombre,
                    'telefono' => $receta->mascota->propietario->telefono,
                ],
                'medico' => [
                    'nombre' => $receta->cita->profesional->nombre,
                    'especialidad' => $receta->cita->profesional->especialidad,
                ],
                'fecha_emision' => $receta->fecha,
                'fecha_vencimiento' => $receta->proxima_cita,
                'diagnostico' => $receta->cita->diagnostico,
                'instrucciones' => $receta->indicaciones,
                'observaciones' => $receta->cita->notas,
                'estado' => $this->calcularEstado($receta->proxima_cita),
                'medicamentos' => [
                    [
                        'nombre' => $receta->medicamento,
                        'dosis' => $receta->dosis,
                        'frecuencia' => $this->extraerFrecuencia($receta->indicaciones),
                        'duracion' => $this->extraerDuracion($receta->indicaciones),
                        'instrucciones' => $this->extraerInstrucciones($receta->indicaciones),
                    ]
                ]
            ];
            
            return response()->json($formattedReceta);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Receta no encontrada: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $receta = RecetaModel::findOrFail($id);
            
            $validated = $request->validate([
                'mascota_id' => 'sometimes|exists:mascota,id_mascota',
                'medico_id' => 'sometimes|exists:medico,id_medico',
                'diagnostico' => 'sometimes|string|max:500',
                'fecha_emision' => 'sometimes|date',
                'fecha_vencimiento' => 'sometimes|date',
                'instrucciones' => 'sometimes|string',
                'observaciones' => 'nullable|string',
                'medicamento' => 'sometimes|string|max:100',
                'dosis' => 'sometimes|string|max:100',
                'indicaciones' => 'sometimes|string',
            ]);
            
            // Actualizar receta
            if (isset($validated['mascota_id'])) {
                $receta->id_mascota = $validated['mascota_id'];
            }
            
            if (isset($validated['fecha_emision'])) {
                $receta->fecha = $validated['fecha_emision'];
            }
            
            if (isset($validated['fecha_vencimiento'])) {
                $receta->proxima_cita = $validated['fecha_vencimiento'];
            }
            
            if (isset($validated['medicamento'])) {
                $receta->medicamento = $validated['medicamento'];
            }
            
            if (isset($validated['dosis'])) {
                $receta->dosis = $validated['dosis'];
            }
            
            if (isset($validated['indicaciones'])) {
                $receta->indicaciones = $validated['indicaciones'];
            }
            
            $receta->save();
            
            // Actualizar cita relacionada si existe
            if ($receta->cita) {
                if (isset($validated['medico_id'])) {
                    $receta->cita->rfc_profesional = $validated['medico_id'];
                }
                
                if (isset($validated['diagnostico'])) {
                    $receta->cita->diagnostico = $validated['diagnostico'];
                }
                
                if (isset($validated['observaciones'])) {
                    $receta->cita->observaciones = $validated['observaciones'];
                }
                
                $receta->cita->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Receta actualizada exitosamente',
                'data' => $receta
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al actualizar la receta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $receta = RecetaModel::findOrFail($id);
            $receta->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Receta eliminada exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al eliminar la receta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Renovar una receta expirada
     */
    public function renovar(string $id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $recetaOriginal = RecetaModel::with(['mascota', 'cita'])->findOrFail($id);
            
            // Verificar si la receta está expirada
            if ($this->calcularEstado($recetaOriginal->proxima_cita) !== 'expirada') {
                return response()->json([
                    'error' => 'Solo se pueden renovar recetas expiradas'
                ], 400);
            }
            
            // Crear nueva receta basada en la original
            $nuevaReceta = RecetaModel::create([
                'id_mascota' => $recetaOriginal->id_mascota,
                'id_cita' => $recetaOriginal->id_cita,
                'medicamento' => $recetaOriginal->medicamento,
                'tipo_medicamento' => $recetaOriginal->tipo_medicamento,
                'dosis' => $recetaOriginal->dosis,
                'indicaciones' => $recetaOriginal->indicaciones,
                'proxima_cita' => Carbon::now()->addDays(7)->format('Y-m-d'), // 7 días desde hoy
                'fecha' => Carbon::now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Receta renovada exitosamente',
                'data' => $nuevaReceta
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al renovar la receta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de recetas
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $total = RecetaModel::count();
            $activas = RecetaModel::where('proxima_cita', '>=', Carbon::now())->count();
            $expiradas = RecetaModel::where('proxima_cita', '<', Carbon::now())->count();
            
            // Próximas a expirar (en 3 días)
            $tresDias = Carbon::now()->addDays(3);
            $porExpirar = RecetaModel::whereBetween('proxima_cita', [Carbon::now(), $tresDias])->count();
            
            return response()->json([
                'total' => $total,
                'activas' => $activas,
                'expiradas' => $expiradas,
                'por_expirar' => $porExpirar,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular estado de la receta basado en la fecha de próxima cita
     */
    private function calcularEstado($proximaCita): string
    {
        if (!$proximaCita) {
            return 'completada';
        }
        
        $hoy = Carbon::now();
        $proxima = Carbon::parse($proximaCita);
        
        if ($proxima->isPast()) {
            return 'expirada';
        } elseif ($proxima->diffInDays($hoy) <= 3) {
            return 'activa'; // Considerar como "por expirar" en el frontend
        } else {
            return 'activa';
        }
    }

    /**
     * Métodos auxiliares para extraer información de las indicaciones
     */
    private function extraerFrecuencia(string $indicaciones): string
    {
        if (preg_match('/Frecuencia:\s*(.+?)(?:\n|$)/', $indicaciones, $matches)) {
            return trim($matches[1]);
        }
        return 'No especificada';
    }

    private function extraerDuracion(string $indicaciones): string
    {
        if (preg_match('/Duración:\s*(.+?)(?:\n|$)/', $indicaciones, $matches)) {
            return trim($matches[1]);
        }
        return 'No especificada';
    }

    private function extraerInstrucciones(string $indicaciones): string
    {
        $lines = explode("\n", $indicaciones);
        $instrucciones = [];
        
        foreach ($lines as $line) {
            if (!str_contains($line, 'Frecuencia:') && !str_contains($line, 'Duración:')) {
                $instrucciones[] = trim($line);
            }
        }
        
        return implode(' ', $instrucciones);
    }
}