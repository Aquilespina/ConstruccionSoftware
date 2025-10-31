<?php

namespace App\Http\Controllers\Mascota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mascota\Mascota;

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
          $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'especie' => 'required|string|max:255',
        'raza' => 'required|string|max:255',
        'edad' => 'required|integer',
        'peso' => 'required|numeric',
        'sexo' => 'required|string|max:10',
        'historial_medico' => 'nullable|string',
        'id_propietario' => 'required|exists:propietario,id_propietario',
    ]);
    dd( $validated);

    $mascota = Mascota::create($validated);

    return response()->json([
        'message' => 'Mascota creada correctamente',
        'mascota' => $mascota->load('propietario:id,nombre') // Carga la relación
    ], 201);
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
