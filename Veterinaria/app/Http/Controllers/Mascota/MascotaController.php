<?php

namespace App\Http\Controllers\Mascota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mascota\Mascota;
use Illuminate\Support\Facades\DB;

class MascotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener mascotas con su propietario para listarlas en la vista
        $mascotas = Mascota::with('propietario')->orderBy('id_mascota', 'asc')->get();
        return view('dash.recepcion.mascotas', compact('mascotas'));
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
            // Validación básica (solo campos que sabemos que existen)
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'especie' => 'required|string|max:255',
                'raza' => 'required|string|max:255',
                'edad' => 'nullable|integer|min:0',
                'peso' => 'nullable|numeric|min:0',
                'sexo' => 'nullable|string|max:10',
                'historial_medico' => 'nullable|string',
                'id_propietario' => 'required|integer|min:1',
            ]);
            
            // Verificar qué columnas existen en la tabla
            $columns = DB::select("SHOW COLUMNS FROM mascota");
            $columnNames = array_column($columns, 'Field');
            $ageColumn = in_array('edad', $columnNames)
                ? 'edad'
                : (in_array('años', $columnNames) ? 'años' : null);
            $statusColumn = in_array('estado', $columnNames)
                ? 'estado'
                : (in_array('color', $columnNames) ? 'color' : null);
            
            // Preparar datos solo con columnas que existen
            $data = [];
            if (in_array('nombre', $columnNames)) $data['nombre'] = $validated['nombre'];
            if (in_array('especie', $columnNames)) $data['especie'] = $validated['especie'];
            if (in_array('raza', $columnNames)) $data['raza'] = $validated['raza'];
            if ($ageColumn) $data[$ageColumn] = $validated['edad'] ?? 0;
            if (in_array('peso', $columnNames)) $data['peso'] = $validated['peso'] ?? 0;
            if (in_array('sexo', $columnNames)) $data['sexo'] = $validated['sexo'] ?? '';
            if ($statusColumn) $data[$statusColumn] = $validated['estado'] ?? '';
            if (in_array('id_propietario', $columnNames)) $data['id_propietario'] = $validated['id_propietario'];
            
            // Insertar solo los campos que existen
            $id = DB::table('mascota')->insertGetId($data);
            
            // Obtener la mascota creada
            $mascota = DB::table('mascota')->where('id_mascota', $id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Mascota creada correctamente',
                'mascota' => $mascota,
                'columnas_disponibles' => $columnNames
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la mascota: ' . $e->getMessage(),
                'error' => $e->getMessage()
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
        'citas'
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
        'estado' => $mascota->estado ?? ($mascota->color ?? null),
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

            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'especie' => 'required|string|max:255',
                'raza' => 'required|string|max:255',
                'edad' => 'nullable|integer|min:0',
                'peso' => 'nullable|numeric|min:0',
                'sexo' => 'nullable|string|max:10',
                'estado' => 'required|string|max:20',
                'historial_medico' => 'nullable|string',
                'id_propietario' => 'required|integer|min:1|exists:propietario,id_propietario',
            ]);

            $columns = DB::select('SHOW COLUMNS FROM mascota');
            $columnNames = array_column($columns, 'Field');
            $ageColumn = in_array('edad', $columnNames)
                ? 'edad'
                : (in_array('años', $columnNames) ? 'años' : null);
            $statusColumn = in_array('estado', $columnNames)
                ? 'estado'
                : (in_array('color', $columnNames) ? 'color' : null);

            $data = [];
            if (in_array('nombre', $columnNames)) $data['nombre'] = $validated['nombre'];
            if (in_array('especie', $columnNames)) $data['especie'] = $validated['especie'];
            if (in_array('raza', $columnNames)) $data['raza'] = $validated['raza'];
            if ($ageColumn) $data[$ageColumn] = $validated['edad'] ?? null;
            if (in_array('peso', $columnNames)) $data['peso'] = $validated['peso'] ?? null;
            if (in_array('sexo', $columnNames)) $data['sexo'] = $validated['sexo'] ?? null;
            if ($statusColumn) $data[$statusColumn] = $validated['estado'];
            if (in_array('id_propietario', $columnNames)) $data['id_propietario'] = $validated['id_propietario'];

            $mascota->update($data);

            return response()->json([
                'message' => 'Mascota actualizada correctamente',
                'mascota' => $mascota->fresh('propietario')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la mascota: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
