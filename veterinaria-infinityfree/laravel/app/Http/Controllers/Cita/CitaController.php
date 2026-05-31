<?php

namespace App\Http\Controllers\Cita;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Cita\Cita;
use App\Models\Mascota\Mascota;
use App\Models\Profesional;

class CitaController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cargar listas para selects del formulario
        $mascotas = Mascota::orderBy('nombre')->get(['id_mascota','nombre']);
        $profesionales = Profesional::orderBy('nombre')->get(['rfc','nombre']);

        // Cargar citas desde la BD categorizadas
        $hoy = Carbon::today();

        $citasQuery = Cita::with(['mascota.propietario', 'profesional'])
            ->orderBy('fecha')
            ->orderBy('horario');

        $citasTodas = (clone $citasQuery)->get();
        $citasHoy = (clone $citasQuery)->whereDate('fecha', $hoy)->get();
        $citasProximas = (clone $citasQuery)->whereDate('fecha', '>', $hoy)->get();
        $citasPasadas = (clone $citasQuery)->whereDate('fecha', '<', $hoy)->get();

        return view('dash.recepcion.citas', compact(
            'mascotas', 'profesionales',
            'citasHoy', 'citasProximas', 'citasPasadas', 'citasTodas'
        ));
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
        // Validación básica según esquema
        $data = $request->validate([
            'id_mascota' => 'required|exists:mascota,id_mascota',
            'rfc_profesional' => 'required|exists:profesional,rfc',
            'tipo_servicio' => 'nullable|string|max:100',
            'tipo_cita' => 'required|in:Consulta,Urgencia,Cirugía,Estética',
            'tarifa' => 'nullable|numeric|min:0',
            'peso_mascota' => 'nullable|numeric|min:0',
            'fecha' => 'required|date',
            'horario' => 'required|date_format:H:i',
            'diagnostico' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:Programada,Completada,Cancelada',
        ]);

        $data['fecha'] = Carbon::parse($data['fecha'])->format('Y-m-d');

        $cita = Cita::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Cita creada correctamente',
            'data' => $cita
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $cita = Cita::with(['mascota.propietario', 'profesional'])->find($id);

            if (!$cita) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cita no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cita,
                'message' => 'Cita obtenida correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la cita: ' . $e->getMessage()
            ], 500);
        }
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
