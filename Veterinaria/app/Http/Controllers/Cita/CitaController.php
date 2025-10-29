<?php

namespace App\Http\Controllers\Cita;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Cita\Cita;

class CitaController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mostrar la vista de gestión de citas
        return view('dash.recepcion.citas');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cita= Cita::create(
            tipo_servicio:$request->tipo_servicio,
            tipo_cita:$request->tipo_cita,
            tarifa:$request->tarifa,
            peso_mascota: $request->peso_mascota,
            fecha: Carbon::parse($request->fecha),
            diagnostico: $request->diagnostico,
            observaciones:$request->observaciones,
            estado:$request->estado,
        );
        // devolver respuesta JSON para peticiones AJAX
        return response()->json([
            'message' => 'Cita creada correctamente',
            'code' => 201,
            'data' => $cita
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
