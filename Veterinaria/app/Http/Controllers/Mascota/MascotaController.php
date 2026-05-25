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
            $columns = \DB::select("SHOW COLUMNS FROM mascota");
            $columnNames = array_column($columns, 'Field');
            
            // Preparar datos solo con columnas que existen
            $data = [];
            if (in_array('nombre', $columnNames)) $data['nombre'] = $validated['nombre'];
            if (in_array('especie', $columnNames)) $data['especie'] = $validated['especie'];
            if (in_array('raza', $columnNames)) $data['raza'] = $validated['raza'];
            if (in_array('edad', $columnNames)) $data['edad'] = $validated['edad'] ?? 0;
            if (in_array('peso', $columnNames)) $data['peso'] = $validated['peso'] ?? 0;
            if (in_array('sexo', $columnNames)) $data['sexo'] = $validated['sexo'] ?? '';
            if (in_array('historial_medico', $columnNames)) $data['historial_medico'] = $validated['historial_medico'] ?? '';
            if (in_array('id_propietario', $columnNames)) $data['id_propietario'] = $validated['id_propietario'];
            
            // Insertar solo los campos que existen
            $id = \DB::table('mascota')->insertGetId($data);
            
            // Obtener la mascota creada
            $mascota = \DB::table('mascota')->where('id_mascota', $id)->first();

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
          $mascota = Mascota::findOrFail($id);
    return response()->json($mascota);
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
         $mascota = Mascota::findOrFail($id);
    
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'especie' => 'required|string|max:255',
        'raza' => 'required|string|max:255',
        'edad' => 'nullable|string|max:50',
        'peso' => 'nullable|numeric',
        'sexo' => 'nullable|string|max:10',
        'estado' => 'required|string|max:20',
        'historial_medico' => 'nullable|string',
        'id_propietario' => 'required|exists:propietarios,id'
    ]);

    $mascota->update($validated);

    return response()->json([
        'message' => 'Mascota actualizada correctamente',
        'mascota' => $mascota
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
