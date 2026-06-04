<?php

namespace App\Http\Controllers\Cita;

use App\Http\Controllers\Controller;
use App\Models\Cita\Cita;
use App\Models\Mascota\Mascota;
use App\Models\Profesional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CitaController extends Controller
{
    public const TIPOS_CITA = ['Consulta', 'Urgencia', 'Cirugía', 'Estética'];

    public const ESTADOS = ['Programada', 'Completada', 'Cancelada'];

    public function index()
    {
        $mascotas = Mascota::orderBy('nombre')->get(['id_mascota', 'nombre']);
        $profesionales = Profesional::orderBy('nombre')->get(['rfc', 'nombre', 'activo']);

        $hoy = Carbon::today();

        $citasQuery = Cita::with(['mascota.propietario', 'profesional'])
            ->orderBy('fecha')
            ->orderBy('horario');

        $citasTodas = (clone $citasQuery)->get();
        $citasHoy = (clone $citasQuery)->whereDate('fecha', $hoy)->get();
        $citasProximas = (clone $citasQuery)->whereDate('fecha', '>', $hoy)->get();
        $citasPasadas = (clone $citasQuery)->whereDate('fecha', '<', $hoy)->get();

        $citasCalendario = $citasTodas->map(function (Cita $cita) {
            return $this->formatearCitaParaCalendario($cita);
        })->values();

        return view('dash.recepcion.citas', compact(
            'mascotas',
            'profesionales',
            'citasHoy',
            'citasProximas',
            'citasPasadas',
            'citasTodas',
            'citasCalendario'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                $this->reglasCita(),
                $this->mensajesValidacionCita()
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $this->normalizarDatosCita($validator->validated());
            $this->validarConflictoHorario($data);

            $cita = Cita::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Cita creada correctamente',
                'data' => $cita->load(['mascota.propietario', 'profesional']),
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la cita: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $cita = Cita::with(['mascota.propietario', 'profesional'])->find($id);

            if (!$cita) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cita no encontrada',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cita,
                'message' => 'Cita obtenida correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la cita: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $cita = Cita::find($id);

            if (!$cita) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cita no encontrada',
                ], 404);
            }

            $validator = Validator::make(
                $request->all(),
                $this->reglasCita($id),
                $this->mensajesValidacionCita()
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $this->normalizarDatosCita($validator->validated());
            $this->validarConflictoHorario($data, (int) $id);

            $cita->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Cita actualizada correctamente',
                'data' => $cita->fresh(['mascota.propietario', 'profesional']),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la cita: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $cita = Cita::find($id);

            if (!$cita) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cita no encontrada',
                ], 404);
            }

            $cita->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cita eliminada correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la cita: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cambiarEstado(Request $request, string $id)
    {
        try {
            $cita = Cita::find($id);

            if (!$cita) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cita no encontrada',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'estado' => ['required', Rule::in(self::ESTADOS)],
            ], [
                'estado.required' => 'El estado es obligatorio.',
                'estado.in' => 'Estado no válido.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $cita->update(['estado' => $validator->validated()['estado']]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de la cita actualizado',
                'data' => $cita->fresh(['mascota.propietario', 'profesional']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function calendario(Request $request)
    {
        $mes = (int) $request->query('mes', now()->month);
        $anio = (int) $request->query('anio', now()->year);

        $inicio = Carbon::create($anio, $mes, 1)->startOfMonth();
        $fin = (clone $inicio)->endOfMonth();

        $citas = Cita::with(['mascota', 'profesional'])
            ->whereBetween('fecha', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->orderBy('fecha')
            ->orderBy('horario')
            ->get()
            ->map(fn (Cita $cita) => $this->formatearCitaParaCalendario($cita));

        return response()->json([
            'success' => true,
            'mes' => $mes,
            'anio' => $anio,
            'hoy' => Carbon::today()->format('Y-m-d'),
            'data' => $citas,
        ]);
    }

    private function reglasCita(?string $idCita = null): array
    {
        $fechaRegla = $idCita ? 'required|date' : 'required|date|after_or_equal:today';

        return [
            'id_mascota' => 'required|integer|exists:mascota,id_mascota',
            'rfc_profesional' => 'required|string|exists:profesional,rfc',
            'tipo_servicio' => 'nullable|string|max:100',
            'tipo_cita' => ['required', Rule::in(self::TIPOS_CITA)],
            'tarifa' => 'nullable|numeric|min:0|max:999999.99',
            'peso_mascota' => 'nullable|numeric|min:0|max:500',
            'fecha' => $fechaRegla,
            'horario' => 'required|date_format:H:i',
            'diagnostico' => 'nullable|string|max:2000',
            'observaciones' => 'nullable|string|max:2000',
            'estado' => ['required', Rule::in(self::ESTADOS)],
        ];
    }

    private function mensajesValidacionCita(): array
    {
        return [
            'id_mascota.required' => 'Seleccione una mascota.',
            'id_mascota.exists' => 'La mascota seleccionada no existe.',
            'rfc_profesional.required' => 'Seleccione un profesional.',
            'rfc_profesional.exists' => 'El profesional seleccionado no existe.',
            'tipo_cita.required' => 'Seleccione el tipo de cita.',
            'tipo_cita.in' => 'Tipo de cita no válido.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.after_or_equal' => 'No puede programar citas en fechas pasadas.',
            'horario.required' => 'La hora es obligatoria.',
            'horario.date_format' => 'Formato de hora inválido (HH:MM).',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'Estado no válido.',
            'tarifa.numeric' => 'La tarifa debe ser un número válido.',
            'peso_mascota.numeric' => 'El peso debe ser un número válido.',
        ];
    }

    private function normalizarDatosCita(array $data): array
    {
        $data['fecha'] = Carbon::parse($data['fecha'])->format('Y-m-d');
        $data['horario'] = Carbon::parse($data['horario'])->format('H:i:s');
        $data['tipo_servicio'] = isset($data['tipo_servicio']) ? trim((string) $data['tipo_servicio']) : null;

        return $data;
    }

    private function validarConflictoHorario(array $data, ?int $excluirId = null): void
    {
        if (($data['estado'] ?? '') === 'Cancelada') {
            return;
        }

        $query = Cita::where('rfc_profesional', $data['rfc_profesional'])
            ->whereDate('fecha', $data['fecha'])
            ->where('horario', $data['horario'])
            ->where('estado', '!=', 'Cancelada');

        if ($excluirId) {
            $query->where('id_cita', '!=', $excluirId);
        }

        if ($query->exists()) {
            throw new \InvalidArgumentException(
                'El profesional ya tiene una cita programada en esa fecha y hora.'
            );
        }
    }

    private function formatearCitaParaCalendario(Cita $cita): array
    {
        $fecha = $cita->fecha instanceof Carbon
            ? $cita->fecha->format('Y-m-d')
            : Carbon::parse($cita->fecha)->format('Y-m-d');

        return [
            'id_cita' => $cita->id_cita,
            'fecha' => $fecha,
            'horario' => substr((string) $cita->horario, 0, 5),
            'estado' => $cita->estado,
            'tipo_cita' => $cita->tipo_cita,
            'tipo_servicio' => $cita->tipo_servicio,
            'id_mascota' => $cita->id_mascota,
            'rfc_profesional' => $cita->rfc_profesional,
            'mascota' => $cita->mascota->nombre ?? $cita->nombre_mascota ?? 'Mascota',
            'profesional' => $cita->profesional->nombre ?? $cita->nombre_medico ?? 'Profesional',
            'propietario' => $cita->mascota->propietario->nombre ?? '—',
            'tarifa' => $cita->tarifa,
            'observaciones' => $cita->observaciones,
        ];
    }
}
