<?php

namespace App\Http\Controllers\Propietario;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Propietario\Propietario;
use Illuminate\Support\Facades\DB;

class PropietarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $propietarios = Propietario::orderBy('fecha_registro', 'desc')->get();

        // Si la petición solicita JSON (API), devolver un arreglo simple con id/nombre
        if ($request->wantsJson() || $request->ajax()) {
            $simple = $propietarios->map(function ($p) {
                return [
                    'id' => $p->id_propietario ?? $p->id ?? null,
                    'nombre' => $p->nombre ?? $p->nombre_completo ?? null,
                ];
            });
            return response()->json($simple);
        }

        return view('dash.recepcion.propietarios', compact('propietarios'));
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
            // Validación básica
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'direccion' => 'nullable|string|max:500',
                'correo_electronico' => 'nullable|email|max:255',
            ]);
            
            // Verificar qué columnas existen en la tabla
            $columns = \DB::select("SHOW COLUMNS FROM propietario");
            $columnNames = array_column($columns, 'Field');
            
            // Preparar datos solo con columnas que existen
            $data = [];
            if (in_array('nombre', $columnNames)) $data['nombre'] = $validated['nombre'];
            if (in_array('telefono', $columnNames)) $data['telefono'] = $validated['telefono'];
            if (in_array('direccion', $columnNames)) $data['direccion'] = $validated['direccion'] ?? '';
            if (in_array('correo_electronico', $columnNames)) $data['correo_electronico'] = $validated['correo_electronico'] ?? '';
            
            // Insertar solo los campos que existen
            $id = \DB::table('propietario')->insertGetId($data);
            
            // Obtener el propietario creado
            $propietario = \DB::table('propietario')->where('id_propietario', $id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Propietario creado correctamente',
                'propietario' => $propietario,
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
                'message' => 'Error al guardar el propietario: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
            $propietario = Propietario::findOrFail($id);
    return response()->json($propietario);
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
            $propietario = Propietario::findOrFail($id);
    $propietario->update($request->all());

    return response()->json([
        'message' => 'Propietario actualizado correctamente',
        'propietario' => $propietario
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
