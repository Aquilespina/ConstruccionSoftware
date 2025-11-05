<?php

namespace App\Http\Controllers\Honorarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mascota\Mascota;
use App\Models\Profesional\Profesional;
use Barryvdh\DomPDF\Facade\Pdf;

class HonorariosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $honorarios = DB::table('honorario as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->leftJoin('hospitalizacion as hosp', 'h.id_hospitalizacion', '=', 'hosp.id_hospitalizacion')
                ->select(
                    'h.id_honorario',
                    'h.fecha_ingreso',
                    'h.fecha_corte',
                    'h.subtotal',
                    'h.total_pagado',
                    'h.saldo_pendiente',
                    'h.estado',
                    'm.nombre as mascota_nombre',
                    'm.especie',
                    'p.nombre as propietario_nombre',
                    'p.telefono as propietario_telefono',
                    'hosp.fecha_ingreso as hospitalizacion_fecha'
                )
                ->orderBy('h.fecha_ingreso', 'desc')
                ->get();

            // Obtener datos para el formulario
            $mascotas = Mascota::getMascotasConPropietarios();
            $hospitalizaciones = $this->getHospitalizacionesActivas();

            return view('dash.recepcion.honorarios', compact('honorarios', 'mascotas', 'hospitalizaciones'));
        } catch (\Exception $e) {
            return view('dash.recepcion.honorarios', [
                'honorarios' => collect(),
                'mascotas' => collect(),
                'hospitalizaciones' => collect()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_mascota' => 'required|exists:mascota,id_mascota',
                'id_hospitalizacion' => 'nullable|exists:hospitalizacion,id_hospitalizacion',
                'fecha_ingreso' => 'required|date',
                'fecha_corte' => 'nullable|date|after_or_equal:fecha_ingreso',
                'detalles' => 'required|array|min:1',
                'detalles.*.concepto' => 'required|string|max:200',
                'detalles.*.cantidad' => 'required|integer|min:1',
                'detalles.*.precio_unitario' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            // Calcular subtotal
            $subtotal = 0;
            foreach ($validatedData['detalles'] as $detalle) {
                $subtotal += $detalle['cantidad'] * $detalle['precio_unitario'];
            }

            // Crear honorario
            $honorarioId = DB::table('honorario')->insertGetId([
                'id_mascota' => $validatedData['id_mascota'],
                'id_hospitalizacion' => $validatedData['id_hospitalizacion'] ?? null,
                'fecha_ingreso' => $validatedData['fecha_ingreso'],
                'fecha_corte' => $validatedData['fecha_corte'] ?? null,
                'subtotal' => $subtotal,
                'total_pagado' => 0.00,
                'saldo_pendiente' => $subtotal,
                'estado' => 'Pendiente'
            ]);

            // Crear detalles
            foreach ($validatedData['detalles'] as $detalle) {
                $importe = $detalle['cantidad'] * $detalle['precio_unitario'];
                
                DB::table('detalle_honorario')->insert([
                    'id_honorario' => $honorarioId,
                    'concepto' => $detalle['concepto'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'importe' => $importe,
                    'fecha_pago' => null,
                    'monto_pagado' => null,
                    'tipo_pago' => null
                ]);
            }

            DB::commit();

            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Honorario registrado exitosamente',
                    'id_honorario' => $honorarioId
                ]);
            }
            
            return redirect()->route('honorarios.honorarios.index')
                ->with('success', 'Honorario registrado exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar el honorario: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al registrar el honorario: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $honorario = DB::table('honorario as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->leftJoin('hospitalizacion as hosp', 'h.id_hospitalizacion', '=', 'hosp.id_hospitalizacion')
                ->where('h.id_honorario', $id)
                ->select(
                    'h.*',
                    'm.nombre as mascota_nombre',
                    'm.especie',
                    'm.raza',
                    'm.años as edad',
                    'p.nombre as propietario_nombre',
                    'p.telefono as propietario_telefono',
                    'p.direccion as propietario_direccion',
                    'hosp.fecha_ingreso as hospitalizacion_fecha'
                )
                ->first();

            if (!$honorario) {
                return redirect()->route('honorarios.honorarios.index')
                    ->with('error', 'Honorario no encontrado');
            }

            $detalles = DB::table('detalle_honorario')
                ->where('id_honorario', $id)
                ->get();

            return view('honorarios.show', compact('honorario', 'detalles'));
        } catch (\Exception $e) {
            return redirect()->route('honorarios.honorarios.index')
                ->with('error', 'Error al cargar el honorario');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            // Obtener honorario con información relacionada
            $honorario = DB::table('honorario as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->leftJoin('hospitalizacion as hosp', 'h.id_hospitalizacion', '=', 'hosp.id_hospitalizacion')
                ->where('h.id_honorario', $id)
                ->select(
                    'h.*',
                    'm.nombre as mascota_nombre',
                    'm.especie',
                    'm.raza',
                    'm.años',
                    'p.nombre as propietario_nombre',
                    'p.telefono as propietario_telefono',
                    'p.direccion as propietario_direccion',
                    'hosp.fecha_ingreso as hospitalizacion_fecha'
                )
                ->first();

            if (!$honorario) {
                return redirect()->route('honorarios.honorarios.index')
                    ->with('error', 'Honorario no encontrado');
            }

            // Obtener detalles del honorario
            $detalles = DB::table('detalle_honorario')
                ->where('id_honorario', $id)
                ->get();

            // Obtener datos para el formulario
            $mascotas = Mascota::getMascotasConPropietarios();
            $hospitalizaciones = $this->getHospitalizacionesActivas();

            // Si es una petición AJAX, devolver JSON para modal
            if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'honorario' => $honorario,
                    'detalles' => $detalles,
                    'mascotas' => $mascotas,
                    'hospitalizaciones' => $hospitalizaciones
                ]);
            }

            return view('honorarios.edit', compact('honorario', 'detalles', 'mascotas', 'hospitalizaciones'));
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar el honorario: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('honorarios.honorarios.index')
                ->with('error', 'Error al cargar el honorario para edición');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate([
                'id_mascota' => 'required|exists:mascota,id_mascota',
                'id_hospitalizacion' => 'nullable|exists:hospitalizacion,id_hospitalizacion',
                'fecha_ingreso' => 'required|date',
                'fecha_corte' => 'nullable|date|after_or_equal:fecha_ingreso',
                'detalles' => 'required|array|min:1',
                'detalles.*.concepto' => 'required|string|max:200',
                'detalles.*.cantidad' => 'required|integer|min:1',
                'detalles.*.precio_unitario' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            // Verificar que el honorario existe
            $honorario = DB::table('honorario')->where('id_honorario', $id)->first();
            if (!$honorario) {
                throw new \Exception('Honorario no encontrado');
            }

            // Calcular nuevo subtotal
            $subtotal = 0;
            foreach ($validatedData['detalles'] as $detalle) {
                $subtotal += $detalle['cantidad'] * $detalle['precio_unitario'];
            }

            // Actualizar honorario principal
            DB::table('honorario')
                ->where('id_honorario', $id)
                ->update([
                    'id_mascota' => $validatedData['id_mascota'],
                    'id_hospitalizacion' => $validatedData['id_hospitalizacion'] ?? null,
                    'fecha_ingreso' => $validatedData['fecha_ingreso'],
                    'fecha_corte' => $validatedData['fecha_corte'] ?? null,
                    'subtotal' => $subtotal,
                    'saldo_pendiente' => $subtotal - $honorario->total_pagado
                ]);

            // Eliminar detalles existentes que no han sido pagados
            DB::table('detalle_honorario')
                ->where('id_honorario', $id)
                ->whereNull('fecha_pago')
                ->delete();

            // Crear nuevos detalles
            foreach ($validatedData['detalles'] as $detalle) {
                $importe = $detalle['cantidad'] * $detalle['precio_unitario'];
                
                DB::table('detalle_honorario')->insert([
                    'id_honorario' => $id,
                    'concepto' => $detalle['concepto'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'importe' => $importe,
                    'fecha_pago' => null,
                    'monto_pagado' => null,
                    'tipo_pago' => null
                ]);
            }

            // Recalcular estado del honorario
            $totalPagado = DB::table('detalle_honorario')
                ->where('id_honorario', $id)
                ->sum('monto_pagado') ?? 0;

            $saldoPendiente = $subtotal - $totalPagado;
            $estado = 'Pendiente';
            if ($saldoPendiente <= 0) {
                $estado = 'Pagado';
            } elseif ($totalPagado > 0) {
                $estado = 'Parcial';
            }

            DB::table('honorario')
                ->where('id_honorario', $id)
                ->update([
                    'total_pagado' => $totalPagado,
                    'saldo_pendiente' => $saldoPendiente,
                    'estado' => $estado
                ]);

            DB::commit();

            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Honorario actualizado exitosamente'
                ]);
            }
            
            return redirect()->route('honorarios.honorarios.index')
                ->with('success', 'Honorario actualizado exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el honorario: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el honorario: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Registrar pago de honorario
     */
    public function registrarPago(Request $request, string $id)
    {
        try {
            // Validar datos de entrada
            $validatedData = $request->validate([
                'monto' => 'required|numeric|min:0.01',
                'tipo_pago' => 'required|in:Efectivo,Tarjeta,Transferencia',
                'notas' => 'nullable|string|max:500'
            ]);

            DB::beginTransaction();

            // Obtener el honorario actual
            $honorario = DB::table('honorario')->where('id_honorario', $id)->first();
            if (!$honorario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Honorario no encontrado'
                ], 404);
            }

            // Verificar que el monto no exceda el saldo pendiente
            if ($validatedData['monto'] > $honorario->saldo_pendiente) {
                return response()->json([
                    'success' => false,
                    'message' => "El monto ({$validatedData['monto']}) no puede ser mayor al saldo pendiente ({$honorario->saldo_pendiente})"
                ], 400);
            }

            // Obtener detalles del honorario ordenados por importe (primero los más caros)
            $detalles = DB::table('detalle_honorario')
                ->where('id_honorario', $id)
                ->orderBy('importe', 'desc')
                ->get();

            // Distribuir el pago entre los conceptos
            $montoPendienteDistribuir = $validatedData['monto'];
            $conceptosActualizados = [];
            
            foreach ($detalles as $detalle) {
                if ($montoPendienteDistribuir <= 0) break;
                
                // Calcular cuánto falta por pagar de este concepto
                $montoPagadoAnterior = $detalle->monto_pagado ?? 0;
                $saldoConcepto = $detalle->importe - $montoPagadoAnterior;
                
                if ($saldoConcepto > 0) {
                    // Determinar cuánto pagar a este concepto
                    $montoPagarConcepto = min($montoPendienteDistribuir, $saldoConcepto);
                    $nuevoMontoPagadoConcepto = $montoPagadoAnterior + $montoPagarConcepto;
                    
                    // Determinar estado del concepto (calculado dinámicamente)
                    $estadoConcepto = 'Pendiente';
                    if ($nuevoMontoPagadoConcepto >= $detalle->importe) {
                        $estadoConcepto = 'Pagado';
                    } elseif ($nuevoMontoPagadoConcepto > 0) {
                        $estadoConcepto = 'Parcial';
                    }
                    
                    // Actualizar el detalle del concepto
                    DB::table('detalle_honorario')
                        ->where('id_detalle', $detalle->id_detalle)
                        ->update([
                            'monto_pagado' => $nuevoMontoPagadoConcepto,
                            'fecha_pago' => $estadoConcepto !== 'Pendiente' ? now() : null,
                            'tipo_pago' => $estadoConcepto !== 'Pendiente' ? $validatedData['tipo_pago'] : null
                        ]);
                    
                    $conceptosActualizados[] = [
                        'concepto' => $detalle->concepto,
                        'monto_pagado' => $montoPagarConcepto,
                        'estado' => $estadoConcepto,
                        'saldo_restante' => $detalle->importe - $nuevoMontoPagadoConcepto
                    ];
                    
                    $montoPendienteDistribuir -= $montoPagarConcepto;
                }
            }

            // Calcular nuevos totales del honorario
            $nuevoTotalPagado = $honorario->total_pagado + $validatedData['monto'];
            $nuevoSaldoPendiente = $honorario->subtotal - $nuevoTotalPagado;
            
            // Determinar el estado general del honorario
            $estado = 'Pendiente';
            if ($nuevoSaldoPendiente <= 0) {
                $estado = 'Pagado';
                $nuevoSaldoPendiente = 0; // Evitar valores negativos
            } elseif ($nuevoTotalPagado > 0) {
                $estado = 'Parcial';
            }

            // Actualizar honorario
            $updated = DB::table('honorario')
                ->where('id_honorario', $id)
                ->update([
                    'total_pagado' => $nuevoTotalPagado,
                    'saldo_pendiente' => $nuevoSaldoPendiente,
                    'estado' => $estado
                ]);

            // Registrar el pago en la tabla de pagos
            $pagoId = DB::table('pago_honorario')->insertGetId([
                'id_honorario' => $id,
                'monto' => $validatedData['monto'],
                'tipo_pago' => $validatedData['tipo_pago'],
                'notas' => $validatedData['notas'] ?? null,
                'fecha_pago' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado exitosamente',
                'data' => [
                    'nuevo_saldo' => $nuevoSaldoPendiente,
                    'nuevo_estado' => $estado,
                    'total_pagado' => $nuevoTotalPagado,
                    'monto_pago' => $validatedData['monto'],
                    'pago_id' => $pagoId,
                    'updated_rows' => $updated,
                    'conceptos_actualizados' => $conceptosActualizados,
                    'resumen_pago' => [
                        'conceptos_pagados' => count(array_filter($conceptosActualizados, function($c) { return $c['estado'] === 'Pagado'; })),
                        'conceptos_parciales' => count(array_filter($conceptosActualizados, function($c) { return $c['estado'] === 'Parcial'; })),
                        'monto_distribuido' => $validatedData['monto'] - $montoPendienteDistribuir
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar el pago: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Obtener información de un honorario para el modal de pago
     */
    public function getInfoPago(string $id)
    {
        try {
            $honorario = DB::table('honorario as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->where('h.id_honorario', $id)
                ->select(
                    'h.*',
                    'm.nombre as mascota_nombre',
                    'm.especie',
                    'p.nombre as propietario_nombre'
                )
                ->first();

            if (!$honorario) {
                return response()->json(['error' => 'Honorario no encontrado'], 404);
            }

            $detalles = DB::table('detalle_honorario')
                ->where('id_honorario', $id)
                ->get();

            // Calcular el estado de cada concepto dinámicamente
            $detallesConEstado = $detalles->map(function ($detalle) {
                $montoPagado = $detalle->monto_pagado ?? 0;
                $estado = 'Pendiente';
                
                if ($montoPagado >= $detalle->importe) {
                    $estado = 'Pagado';
                } elseif ($montoPagado > 0) {
                    $estado = 'Parcial';
                }
                
                return (object) array_merge((array) $detalle, [
                    'estado_calculado' => $estado,
                    'porcentaje_pagado' => $detalle->importe > 0 ? ($montoPagado / $detalle->importe * 100) : 0,
                    'saldo_pendiente_concepto' => $detalle->importe - $montoPagado
                ]);
            });

            return response()->json([
                'success' => true,
                'honorario' => $honorario,
                'detalles' => $detallesConEstado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información del honorario'
            ], 500);
        }
    }

    /**
     * Generar PDF del honorario
     */
    public function generarPDF(string $id)
    {
        try {
            $honorario = DB::table('honorario as h')
                ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
                ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
                ->leftJoin('hospitalizacion as hosp', 'h.id_hospitalizacion', '=', 'hosp.id_hospitalizacion')
                ->where('h.id_honorario', $id)
                ->select(
                    'h.*',
                    'm.nombre as mascota_nombre',
                    'm.especie',
                    'm.raza',
                    'm.años as edad',
                    'p.nombre as propietario_nombre',
                    'p.telefono as propietario_telefono',
                    'p.direccion as propietario_direccion',
                    'hosp.fecha_ingreso as hospitalizacion_fecha'
                )
                ->first();

            if (!$honorario) {
                return response()->json(['error' => 'Honorario no encontrado'], 404);
            }

            $detalles = DB::table('detalle_honorario')
                ->where('id_honorario', $id)
                ->get();

            $pdf = Pdf::loadView('honorarios.pdf', compact('honorario', 'detalles'));
            
            $filename = "honorario_{$id}_" . date('Y-m-d') . ".pdf";
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener hospitalizaciones activas para el formulario
     */
    private function getHospitalizacionesActivas()
    {
        return DB::table('hospitalizacion as h')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->where('h.estado', 'Internado')
            ->select(
                'h.id_hospitalizacion',
                'h.id_mascota',
                'h.fecha_ingreso',
                'm.nombre as mascota_nombre',
                'p.nombre as propietario_nombre'
            )
            ->get();
    }

    /**
     * Obtener conceptos de la tabla receta para autocompletar
     */
    public function getConceptosReceta()
    {
        try {
            // Primero intentamos obtener de recetas existentes
            $conceptos = collect();
            
            try {
                $conceptosReceta = DB::table('receta as r')
                    ->join('detalle_receta as dr', 'r.id_receta', '=', 'dr.id_receta')
                    ->select('dr.medicamento as concepto', DB::raw('COALESCE(dr.precio_unitario, 0) as precio'))
                    ->distinct()
                    ->orderBy('dr.medicamento')
                    ->get();
                    
                $conceptos = $conceptos->merge($conceptosReceta);
            } catch (\Exception $e) {
                // Si no hay tabla receta o detalle_receta, usar conceptos predeterminados
            }
            
            // Agregar conceptos comunes predeterminados
            $conceptosPredeterminados = collect([
                ['concepto' => 'Consulta General', 'precio' => 150.00],
                ['concepto' => 'Vacunación', 'precio' => 120.00],
                ['concepto' => 'Desparasitación', 'precio' => 80.00],
                ['concepto' => 'Cirugía Menor', 'precio' => 500.00],
                ['concepto' => 'Radiografía', 'precio' => 200.00],
                ['concepto' => 'Análisis de Sangre', 'precio' => 180.00],
                ['concepto' => 'Hospitalización (día)', 'precio' => 300.00],
                ['concepto' => 'Limpieza Dental', 'precio' => 250.00],
                ['concepto' => 'Esterilización', 'precio' => 800.00],
                ['concepto' => 'Medicamentos', 'precio' => 100.00],
            ]);
            
            // Combinar conceptos de recetas con predeterminados
            $todosConceptos = $conceptos->merge($conceptosPredeterminados)->unique('concepto');

            return response()->json([
                'success' => true,
                'conceptos' => $todosConceptos->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener conceptos: ' . $e->getMessage()
            ], 500);
        }
    }
}