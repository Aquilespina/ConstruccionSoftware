<?php

namespace App\Http\Controllers\Propietario;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Propietario\Propietario;

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
        $propietario = Propietario::create([
            'nombre' => $request->input('nombre'),
            'telefono' => $request->input('telefono'),
            'direccion' => $request->input('direccion'),
            'correo_electronico' => $request->input('correo_electronico'),
        ]);


        return response()->json([
            'message' => 'Propietario creado correctamente',
            'propietario' => $propietario
        ], 201);
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
