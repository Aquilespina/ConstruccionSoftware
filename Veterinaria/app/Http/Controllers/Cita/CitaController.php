<?php

namespace App\Http\Controllers\Cita;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CitaController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $cita= Event::create(
            tipo_servicio:$request->tipo_servicio,
            tipo_cita:$request->tipo_cita,
            tarifa:$request->tarifa,
            peso_mascota: $request->peso_mascota,
            fecha: Carbon::parse($request->fecha),
            diagnostico: $request->diagnostico,
            observaciones:$request->observaciones,
            estado:$request->estado,
        )

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
