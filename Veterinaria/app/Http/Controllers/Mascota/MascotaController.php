<?php

namespace App\Http\Controllers\Mascota;

use App\Http\Controllers\Controller;
use App\Models\Mascota\Mascota;
use App\Support\SimpleXlsxExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MascotaController extends Controller
{
    public const ESPECIES = ['perro', 'gato', 'ave', 'roedor', 'reptil'];

    private const NOMBRE_MASCOTA_REGEX = '/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9][A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s\.\-]{1,49}$/u';

    private const RAZA_REGEX = '/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü][A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\-]{1,49}$/u';

    private function normalizarEstadoMascotaParaVista($value): string
    {
        if (is_bool($value)) {
            return $value ? 'activo' : 'inactivo';
        }

        $valor = strtolower(trim((string) $value));

        if (in_array($valor, ['1', 'true', 'activo', 'active'], true)) {
            return 'activo';
        }

        if (in_array($valor, ['0', 'false', 'inactivo', 'inactive'], true)) {
            return 'inactivo';
        }

        return $valor !== '' ? $valor : 'inactivo';
    }

    private function normalizarEstadoMascotaParaBaseDeDatos($value, ?string $columnType = null)
    {
        $valor = strtolower(trim((string) $value));
        $esBooleano = $columnType !== null && preg_match('/\b(tinyint|bit|bool|boolean)\b/i', $columnType) === 1;

        if ($esBooleano) {
            return in_array($valor, ['1', 'true', 'activo', 'active'], true) ? 1 : 0;
        }

        if (in_array($valor, ['1', 'true', 'activo', 'active'], true)) {
            return 'activo';
        }

        if (in_array($valor, ['0', 'false', 'inactivo', 'inactive'], true)) {
            return 'inactivo';
        }

        return $valor;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mascota::with('propietario');

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where('nombre', 'like', '%' . $search . '%');
        }

        $mascotas = $query->orderBy('id_mascota', 'asc')->get();

        $especies = self::ESPECIES;

        return view('dash.recepcion.mascotas', compact('mascotas', 'search', 'especies'));
    }

    public function buscar(Request $request)
    {
        $columns = DB::select('SHOW COLUMNS FROM mascota');
        $columnNames = array_column($columns, 'Field');

        $query = Mascota::with('propietario');

        $texto = trim((string) $request->query('q', ''));
        $especie = trim((string) $request->query('especie', ''));
        $estado = trim((string) $request->query('estado', ''));

        if ($texto !== '') {
            $query->where('nombre', 'like', '%' . $texto . '%');
        }

        if ($especie !== '') {
            $query->where('especie', 'like', '%' . $especie . '%');
        }

        if ($estado !== '') {
            $statusColumn = in_array('estado', $columnNames)
                ? 'estado'
                : (in_array('color', $columnNames) ? 'color' : null);

            if ($statusColumn === 'estado') {
                $query->where(function ($subQuery) use ($estado) {
                    if ($estado === 'activo') {
                        $subQuery->where('estado', 1)->orWhere('estado', 'activo')->orWhere('estado', 'true');
                    } elseif ($estado === 'inactivo') {
                        $subQuery->where('estado', 0)->orWhere('estado', 'inactivo')->orWhere('estado', 'false');
                    }
                });
            } elseif ($statusColumn === 'color') {
                $query->where($statusColumn, $estado);
            }
        }

        $mascotas = $query->orderBy('id_mascota', 'asc')->get();

        $rows = $mascotas->map(function ($mascota) {
            $especieNormalizada = strtolower((string) ($mascota->especie ?? ''));
            $avatar = str_contains($especieNormalizada, 'gat') ? '🐈' : (str_contains($especieNormalizada, 'ave') ? '🐦' : '🐕');
            $estado = $this->normalizarEstadoMascotaParaVista($mascota->estado ?? ($mascota->color ?? null));

            return [
                'id_mascota' => $mascota->id_mascota,
                'nombre' => $mascota->nombre ?? '-',
                'especie' => $mascota->especie ?? '-',
                'raza' => $mascota->raza ?? '-',
                'propietario' => optional($mascota->propietario)->nombre ?? '-',
                'edad' => $mascota->edad ?? '-',
                'ultima_visita' => $mascota->ultima_visita ?? '-',
                'avatar' => $avatar,
                'estado' => $estado,
            ];
        });

        return response()->json($rows);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = Validator::make(
                $this->datosParaValidacionMascota($request),
                $this->reglasMascota(),
                $this->mensajesValidacionMascota()
            )->validate();

            $columns = DB::select('SHOW COLUMNS FROM mascota');
            $columnNames = array_column($columns, 'Field');
            $columnTypes = [];
            foreach ($columns as $column) {
                $columnTypes[$column->Field] = $column->Type ?? null;
            }

            $ageColumn = in_array('edad', $columnNames)
                ? 'edad'
                : (in_array('años', $columnNames) ? 'años' : null);

            $statusColumn = in_array('estado', $columnNames)
                ? 'estado'
                : (in_array('color', $columnNames) ? 'color' : null);

            $data = [];
            if (in_array('nombre', $columnNames)) {
                $data['nombre'] = $validated['nombre'];
            }
            if (in_array('especie', $columnNames)) {
                $data['especie'] = $validated['especie'];
            }
            if (in_array('raza', $columnNames)) {
                $data['raza'] = $validated['raza'];
            }
            if ($ageColumn) {
                $data[$ageColumn] = $validated['edad'] ?? 0;
            }
            if (in_array('peso', $columnNames)) {
                $data['peso'] = $validated['peso'] ?? 0;
            }
            if (in_array('sexo', $columnNames)) {
                $data['sexo'] = $validated['sexo'] ?? '';
            }
            if ($statusColumn) {
                $data[$statusColumn] = $this->normalizarEstadoMascotaParaBaseDeDatos(
                    $validated['estado'],
                    $columnTypes[$statusColumn] ?? null
                );
            }
            if (in_array('id_propietario', $columnNames)) {
                $data['id_propietario'] = $validated['id_propietario'];
            }

            $id = DB::table('mascota')->insertGetId($data);
            $mascota = DB::table('mascota')->where('id_mascota', $id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Mascota creada correctamente',
                'mascota' => $mascota,
                'columnas_disponibles' => $columnNames,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la mascota: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mascota = Mascota::with([
            'propietario',
            'citas',
        ])->findOrFail($id);

        $ultimaVisita = $mascota->ultima_visita ?? null;

        return response()->json([
            'id_mascota' => $mascota->id_mascota,
            'nombre' => $mascota->nombre,
            'especie' => $mascota->especie,
            'raza' => $mascota->raza,
            'sexo' => $mascota->sexo,
            'edad' => $mascota->edad ?? ($mascota->años ?? null),
            'peso' => $mascota->peso,
            'estado' => $this->normalizarEstadoMascotaParaVista($mascota->estado ?? ($mascota->color ?? null)),
            'historial_medico' => $mascota->historial_medico,
            'id_propietario' => $mascota->id_propietario,
            'ultima_visita' => $ultimaVisita,
            'created_at' => $mascota->created_at,
            'updated_at' => $mascota->updated_at,
            'propietario' => $mascota->propietario ? [
                'id' => $mascota->propietario->id ?? $mascota->propietario->id_propietario ?? null,
                'nombre' => $mascota->propietario->nombre ?? null,
                'telefono' => $mascota->propietario->telefono ?? null,
                'correo' => $mascota->propietario->correo ?? null,
            ] : null,
            'total_citas' => $mascota->citas->count(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $mascota = Mascota::findOrFail($id);
           

            $validated = Validator::make(
                $this->datosParaValidacionMascota($request),
                $this->reglasMascota(),
                $this->mensajesValidacionMascota()
            )->validate();

            $columns = DB::select('SHOW COLUMNS FROM mascota');
            $columnNames = array_column($columns, 'Field');
            $columnTypes = [];
            foreach ($columns as $column) {
                $columnTypes[$column->Field] = $column->Type ?? null;
            }

            $ageColumn = in_array('edad', $columnNames)
                ? 'edad'
                : (in_array('años', $columnNames) ? 'años' : null);
            $statusColumn = in_array('estado', $columnNames)
                ? 'estado'
                : (in_array('color', $columnNames) ? 'color' : null);

            $data = [];
            if (in_array('nombre', $columnNames)) {
                $data['nombre'] = $validated['nombre'];
            }
            if (in_array('especie', $columnNames)) {
                $data['especie'] = $validated['especie'];
            }
            if (in_array('raza', $columnNames)) {
                $data['raza'] = $validated['raza'];
            }
            if ($ageColumn) {
                $data[$ageColumn] = $validated['edad'] ?? null;
            }
            if (in_array('peso', $columnNames)) {
                $data['peso'] = $validated['peso'] ?? null;
            }
            if (in_array('sexo', $columnNames)) {
                $data['sexo'] = $validated['sexo'] ?? null;
            }
            if ($statusColumn) {
                $data[$statusColumn] = $this->normalizarEstadoMascotaParaBaseDeDatos(
                    $validated['estado'],
                    $columnTypes[$statusColumn] ?? null
                );
            }
            if (in_array('id_propietario', $columnNames)) {
                $data['id_propietario'] = $validated['id_propietario'];
            }

            $mascota->update($data);

            return response()->json([
                'message' => 'Mascota actualizada correctamente',
                'mascota' => $mascota->fresh('propietario'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la mascota: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $mascota = Mascota::findOrFail($id);

            DB::transaction(function () use ($mascota) {
                $mascota->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Mascota eliminada correctamente',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'La mascota no existe',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la mascota',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export mascotas with related information to Excel
     */
    public function export(Request $request)
    {
        try {
            $query = Mascota::with(['propietario', 'citas']);

            $texto = trim((string) $request->query('q', ''));
            $especie = trim((string) $request->query('especie', ''));
            $estado = trim((string) $request->query('estado', ''));

            if ($texto !== '') {
                $query->where('nombre', 'like', '%' . $texto . '%');
            }

            if ($especie !== '') {
                $query->where('especie', 'like', '%' . $especie . '%');
            }

            if ($estado !== '') {
                $columns = DB::select('SHOW COLUMNS FROM mascota');
                $columnNames = array_column($columns, 'Field');
                $statusColumn = in_array('estado', $columnNames)
                    ? 'estado'
                    : (in_array('color', $columnNames) ? 'color' : null);

                if ($statusColumn === 'estado') {
                    $query->where(function ($subQuery) use ($estado) {
                        if ($estado === 'activo') {
                            $subQuery->where('estado', 1)->orWhere('estado', 'activo')->orWhere('estado', 'true');
                        } elseif ($estado === 'inactivo') {
                            $subQuery->where('estado', 0)->orWhere('estado', 'inactivo')->orWhere('estado', 'false');
                        }
                    });
                } elseif ($statusColumn === 'color') {
                    $query->where($statusColumn, $estado);
                }
            }

            $mascotas = $query->orderBy('id_mascota', 'asc')->get();

            $headers = [
                'ID Mascota',
                'Nombre',
                'Especie',
                'Raza',
                'Sexo',
                'Edad',
                'Peso (kg)',
                'Estado',
                'Propietario',
                'Teléfono Propietario',
                'Correo Propietario',
                'Última Visita',
                'Total Citas',
                'Historial Médico',
                'Fecha Creación',
                'Fecha Actualización',
            ];

            $rows = [];

            foreach ($mascotas as $mascota) {
                $rows[] = [
                    $mascota->id_mascota,
                    $mascota->nombre,
                    $mascota->especie,
                    $mascota->raza,
                    $mascota->sexo ?? '-',
                    $mascota->edad ?? ($mascota->años ?? '-'),
                    $mascota->peso ?? '-',
                    $this->normalizarEstadoMascotaParaVista($mascota->estado ?? ($mascota->color ?? null)),
                    optional($mascota->propietario)->nombre ?? '-',
                    optional($mascota->propietario)->telefono ?? '-',
                    optional($mascota->propietario)->correo ?? '-',
                    $mascota->ultima_visita ?? '-',
                    $mascota->citas->count(),
                    $mascota->historial_medico ?? '-',
                    $mascota->created_at,
                    $mascota->updated_at,
                ];
            }

            $filename = 'mascotas_' . date('Y-m-d_H-i-s') . '.xlsx';

            return SimpleXlsxExporter::download($filename, $headers, $rows, 'Mascotas');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar mascotas: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function reglasMascota(): array
    {
        return [
            'nombre' => ['required', 'string', 'min:2', 'max:50', 'regex:' . self::NOMBRE_MASCOTA_REGEX],
            'especie' => ['required', 'string', Rule::in(self::ESPECIES)],
            'raza' => ['required', 'string', 'min:2', 'max:50', 'regex:' . self::RAZA_REGEX],
            'edad' => ['nullable', 'integer', 'min:0', 'max:15'],
            'peso' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sexo' => ['nullable', 'string', Rule::in(['macho', 'hembra', ''])],
            'estado' => ['required', Rule::in(['0', '1', 0, 1, 'activo', 'inactivo'])],
            'historial_medico' => ['nullable', 'string', 'max:2000'],
            'id_propietario' => ['required', 'integer', 'min:1', 'exists:propietario,id_propietario'],
        ];
    }

    private function datosParaValidacionMascota(Request $request): array
    {
        $datos = $request->all();

        if (!isset($datos['id_propietario']) && isset($datos['propietario_id'])) {
            $datos['id_propietario'] = $datos['propietario_id'];
        }

        if (isset($datos['nombre']) && is_string($datos['nombre'])) {
            $datos['nombre'] = preg_replace('/\s+/', ' ', trim($datos['nombre']));
        }

        if (isset($datos['raza']) && is_string($datos['raza'])) {
            $datos['raza'] = preg_replace('/\s+/', ' ', trim($datos['raza']));
        }

        if (isset($datos['especie']) && is_string($datos['especie'])) {
            $datos['especie'] = strtolower(trim($datos['especie']));
        }

        return $datos;
    }

    private function mensajesValidacionMascota(): array
    {
        return [
            'nombre.required' => 'El nombre de la mascota es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max' => 'El nombre no puede superar 50 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios, puntos y guiones.',
            'especie.required' => 'La especie es obligatoria.',
            'especie.in' => 'Seleccione una especie válida.',
            'raza.required' => 'La raza es obligatoria.',
            'raza.min' => 'La raza debe tener al menos 2 caracteres.',
            'raza.max' => 'La raza no puede superar 50 caracteres.',
            'raza.regex' => 'La raza solo puede contener letras, espacios y guiones.',
            'edad.integer' => 'La edad debe ser un número entero.',
            'edad.min' => 'La edad no puede ser negativa.',
            'edad.max' => 'La edad no puede ser mayor a 15 años.',
            'peso.numeric' => 'El peso debe ser un número válido.',
            'peso.min' => 'El peso no puede ser negativo.',
            'peso.max' => 'El peso no puede ser mayor a 100 kg.',
            'sexo.in' => 'Seleccione un sexo válido (macho o hembra).',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'id_propietario.required' => 'Debe seleccionar un propietario.',
            'id_propietario.exists' => 'El propietario seleccionado no existe.',
        ];
    }
}