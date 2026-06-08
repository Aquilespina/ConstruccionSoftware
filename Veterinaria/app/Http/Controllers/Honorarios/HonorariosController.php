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
                    'h.id_mascota',
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
                'fecha_ingreso' => 'required|date|before_or_equal:today',
                'fecha_corte' => 'nullable|date|after_or_equal:fecha_ingreso',
                'detalles' => 'required|array|min:1',
                'detalles.*.concepto' => ['required', 'string', 'max:200', 'regex:/\S/'],
                'detalles.*.cantidad' => 'required|integer|min:1|max:9999',
                'detalles.*.precio_unitario' => 'required|numeric|min:0.01|max:999999.99|decimal:0,2'
            ], $this->mensajesValidacionHonorario());

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
                ->orderBy('id_detalle', 'asc')
                ->get();

            $pagos = DB::table('pago_honorario')
                ->where('id_honorario', $id)
                ->orderBy('fecha_pago', 'asc')
                ->get();

            $totalPagadoReal  = $pagos->sum('monto');
            $saldoPendiente   = $honorario->subtotal - $totalPagadoReal;
            $porcentajePagado = $honorario->subtotal > 0
                ? ($totalPagadoReal / $honorario->subtotal * 100)
                : 0;

            $detallesConEstado = $this->calcularEstadoPagosDetalles($detalles, $pagos);

            $mascotas          = Mascota::getMascotasConPropietarios();
            $hospitalizaciones = $this->getHospitalizacionesActivas();

            return view('honorarios.show', compact(
                'honorario',
                'detallesConEstado',
                'pagos',
                'totalPagadoReal',
                'saldoPendiente',
                'porcentajePagado',
                'mascotas',
                'hospitalizaciones'
            ));
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
                'fecha_ingreso' => 'required|date|before_or_equal:today',
                'fecha_corte' => 'nullable|date|after_or_equal:fecha_ingreso',
                'detalles' => 'required|array|min:1',
                'detalles.*.concepto' => ['required', 'string', 'max:200', 'regex:/\S/'],
                'detalles.*.cantidad' => 'required|integer|min:1|max:9999',
                'detalles.*.precio_unitario' => 'required|numeric|min:0.01|max:999999.99|decimal:0,2'
            ], $this->mensajesValidacionHonorario());

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

            // Eliminar todos los detalles existentes
            DB::table('detalle_honorario')
                ->where('id_honorario', $id)
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

            // Recalcular estado del honorario basado en el historial de pagos
            $totalPagadoReal = DB::table('pago_honorario')
                ->where('id_honorario', $id)
                ->sum('monto') ?? 0;

            $saldoPendiente = $subtotal - $totalPagadoReal;
            $estado = 'Pendiente';
            if ($saldoPendiente <= 0) {
                $estado = 'Pagado';
            } elseif ($totalPagadoReal > 0) {
                $estado = 'Parcial';
            }

            DB::table('honorario')
                ->where('id_honorario', $id)
                ->update([
                    'total_pagado' => $totalPagadoReal,
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
                'monto' => 'required|numeric|min:0.01|max:99999999.99|decimal:0,2',
                'tipo_pago' => 'required|in:Efectivo,Tarjeta,Transferencia',
                'notas' => 'nullable|string|max:500'
            ], [
                'monto.required' => 'El monto es obligatorio.',
                'monto.numeric' => 'El monto debe ser un valor numérico.',
                'monto.min' => 'El monto debe ser mayor a 0.',
                'monto.max' => 'El monto excede el valor máximo permitido.',
                'monto.decimal' => 'El monto admite máximo 2 decimales.',
                'tipo_pago.required' => 'Debe seleccionar un tipo de pago.',
                'tipo_pago.in' => 'El tipo de pago seleccionado no es válido.',
                'notas.max' => 'Las notas no pueden exceder 500 caracteres.'
            ]);

            // Convertir tipo_pago a formato de base de datos (minúscula)
            $metodoPagoDB = strtolower($validatedData['tipo_pago']);

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

            // Obtener detalles del honorario ordenados por ID (primeros conceptos primero)
            $detalles = DB::table('detalle_honorario')
                ->where('id_honorario', $id)
                ->orderBy('id_detalle', 'asc')
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
                'metodo_pago' => $metodoPagoDB, // Usar metodo_pago en lugar de tipo_pago
                'observaciones' => $validatedData['notas'] ?? null, // Usar observaciones en lugar de notas
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

            // Obtener los detalles del honorario con columnas específicas
            $detalles = DB::table('detalle_honorario')
                ->where('id_honorario', $id)
                ->select(
                    'id_detalle',
                    'id_honorario', 
                    'concepto',
                    'cantidad',
                    'precio_unitario',
                    'importe',
                    'fecha_pago',
                    'monto_pagado',
                    'tipo_pago'
                )
                ->get();

            if ($detalles->isEmpty()) {
                return response()->json(['error' => 'No se encontraron detalles válidos para este honorario'], 404);
            }

            // Recalcular el subtotal basado en los detalles actuales
            $subtotalActual = $detalles->sum('importe');
            
            // Actualizar el subtotal en la tabla honorario si es diferente
            if ($subtotalActual != $honorario->subtotal) {
                DB::table('honorario')
                    ->where('id_honorario', $id)
                    ->update(['subtotal' => $subtotalActual]);
                $honorario->subtotal = $subtotalActual;
            }

            // Obtener historial de pagos realizados
            $pagos = DB::table('pago_honorario')
                ->where('id_honorario', $id)
                ->orderBy('fecha_pago', 'asc')
                ->get();

            // Calcular suma real de pagos desde el historial
            $totalPagadoReal = $pagos->sum('monto');
            
            // Calcular información de pagos
            $saldoPendiente = $honorario->subtotal - $totalPagadoReal;
            $porcentajePagado = $honorario->subtotal > 0 ? ($totalPagadoReal / $honorario->subtotal * 100) : 0;

            // Recalcular la distribución de pagos en tiempo real
            $detallesConEstado = $this->calcularEstadoPagosDetalles($detalles, $pagos);

            $pdf = Pdf::loadView('honorarios.pdf', compact('honorario', 'detallesConEstado', 'pagos', 'totalPagadoReal', 'saldoPendiente', 'porcentajePagado'));
            
            $filename = "honorario_{$id}_" . date('Y-m-d') . ".pdf";
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reporte general de honorarios (vista web)
     */
    public function reporte(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $query = DB::table('honorario');
        if ($desde) $query->whereDate('fecha_ingreso', '>=', $desde);
        if ($hasta) $query->whereDate('fecha_ingreso', '<=', $hasta);

        // Totales generales
        $stats = (object) [
            'total_honorarios'   => (clone $query)->count(),
            'total_subtotal'     => (clone $query)->sum('subtotal'),
            'total_pagado'       => (clone $query)->sum('total_pagado'),
            'total_pendiente'    => (clone $query)->sum('saldo_pendiente'),
            'total_pendiente_count' => (clone $query)->where('estado', 'Pendiente')->count(),
            'total_parcial_count'   => (clone $query)->where('estado', 'Parcial')->count(),
            'total_pagado_count'    => (clone $query)->where('estado', 'Pagado')->count(),
        ];
        $stats->porcentaje_cobranza = $stats->total_subtotal > 0
            ? round($stats->total_pagado / $stats->total_subtotal * 100, 1)
            : 0;

        // Desglose por estado
        $porEstado = (clone $query)
            ->select('estado',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(subtotal) as total_subtotal'),
                DB::raw('SUM(total_pagado) as total_pagado'),
                DB::raw('SUM(saldo_pendiente) as total_pendiente'))
            ->groupBy('estado')
            ->get();

        // Propietarios con mayor saldo pendiente
        $topDeudores = DB::table('honorario as h')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->where('h.saldo_pendiente', '>', 0)
            ->when($desde, fn($q) => $q->whereDate('h.fecha_ingreso', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('h.fecha_ingreso', '<=', $hasta))
            ->select(
                'p.nombre as propietario',
                'p.telefono',
                DB::raw('SUM(h.saldo_pendiente) as total_pendiente'),
                DB::raw('COUNT(*) as num_honorarios'))
            ->groupBy('p.id_propietario', 'p.nombre', 'p.telefono')
            ->orderByDesc('total_pendiente')
            ->limit(10)
            ->get();

        // Honorarios pendientes más antiguos (antigüedad de deuda)
        $masAntiguos = DB::table('honorario as h')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->whereIn('h.estado', ['Pendiente', 'Parcial'])
            ->when($desde, fn($q) => $q->whereDate('h.fecha_ingreso', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('h.fecha_ingreso', '<=', $hasta))
            ->select(
                'h.id_honorario', 'h.fecha_ingreso', 'h.subtotal',
                'h.total_pagado', 'h.saldo_pendiente', 'h.estado',
                'm.nombre as mascota_nombre',
                'p.nombre as propietario_nombre', 'p.telefono')
            ->orderBy('h.fecha_ingreso', 'asc')
            ->limit(10)
            ->get();

        // Conceptos más facturados
        $topConceptos = DB::table('detalle_honorario as dh')
            ->join('honorario as h', 'dh.id_honorario', '=', 'h.id_honorario')
            ->when($desde, fn($q) => $q->whereDate('h.fecha_ingreso', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('h.fecha_ingreso', '<=', $hasta))
            ->select(
                'dh.concepto',
                DB::raw('COUNT(*) as veces'),
                DB::raw('SUM(dh.importe) as total_facturado'),
                DB::raw('AVG(dh.precio_unitario) as precio_promedio'))
            ->groupBy('dh.concepto')
            ->orderByDesc('total_facturado')
            ->limit(10)
            ->get();

        // Pagos recientes
        $pagosRecientes = DB::table('pago_honorario as ph')
            ->join('honorario as h', 'ph.id_honorario', '=', 'h.id_honorario')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->when($desde, fn($q) => $q->whereDate('ph.fecha_pago', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('ph.fecha_pago', '<=', $hasta))
            ->select(
                'ph.id_pago', 'ph.monto', 'ph.metodo_pago', 'ph.fecha_pago',
                'h.id_honorario',
                'm.nombre as mascota_nombre',
                'p.nombre as propietario_nombre')
            ->orderByDesc('ph.fecha_pago')
            ->limit(10)
            ->get();

        return view('honorarios.reporte', compact(
            'stats', 'porEstado', 'topDeudores', 'masAntiguos',
            'topConceptos', 'pagosRecientes', 'desde', 'hasta'
        ));
    }

    /**
     * Exportar reporte general a PDF
     */
    public function reportePDF(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        // Reutiliza exactamente la misma lógica del método reporte()
        $reporteData = $this->buildReporteData($desde, $hasta);

        $pdf = Pdf::loadView('honorarios.reporte-pdf', array_merge(
            $reporteData,
            ['desde' => $desde, 'hasta' => $hasta]
        ))->setPaper('a4', 'portrait');

        $filename = 'reporte_honorarios_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Construye los datos del reporte (usado por web y PDF)
     */
    private function buildReporteData(?string $desde, ?string $hasta): array
    {
        $query = DB::table('honorario');
        if ($desde) $query->whereDate('fecha_ingreso', '>=', $desde);
        if ($hasta) $query->whereDate('fecha_ingreso', '<=', $hasta);

        $stats = (object) [
            'total_honorarios'      => (clone $query)->count(),
            'total_subtotal'        => (clone $query)->sum('subtotal'),
            'total_pagado'          => (clone $query)->sum('total_pagado'),
            'total_pendiente'       => (clone $query)->sum('saldo_pendiente'),
            'total_pendiente_count' => (clone $query)->where('estado', 'Pendiente')->count(),
            'total_parcial_count'   => (clone $query)->where('estado', 'Parcial')->count(),
            'total_pagado_count'    => (clone $query)->where('estado', 'Pagado')->count(),
        ];
        $stats->porcentaje_cobranza = $stats->total_subtotal > 0
            ? round($stats->total_pagado / $stats->total_subtotal * 100, 1) : 0;

        $porEstado = (clone $query)
            ->select('estado',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(subtotal) as total_subtotal'),
                DB::raw('SUM(total_pagado) as total_pagado'),
                DB::raw('SUM(saldo_pendiente) as total_pendiente'))
            ->groupBy('estado')->get();

        $topDeudores = DB::table('honorario as h')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->where('h.saldo_pendiente', '>', 0)
            ->when($desde, fn($q) => $q->whereDate('h.fecha_ingreso', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('h.fecha_ingreso', '<=', $hasta))
            ->select('p.nombre as propietario', 'p.telefono',
                DB::raw('SUM(h.saldo_pendiente) as total_pendiente'),
                DB::raw('COUNT(*) as num_honorarios'))
            ->groupBy('p.id_propietario', 'p.nombre', 'p.telefono')
            ->orderByDesc('total_pendiente')->limit(10)->get();

        $masAntiguos = DB::table('honorario as h')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->whereIn('h.estado', ['Pendiente', 'Parcial'])
            ->when($desde, fn($q) => $q->whereDate('h.fecha_ingreso', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('h.fecha_ingreso', '<=', $hasta))
            ->select('h.id_honorario', 'h.fecha_ingreso', 'h.subtotal',
                'h.total_pagado', 'h.saldo_pendiente', 'h.estado',
                'm.nombre as mascota_nombre',
                'p.nombre as propietario_nombre', 'p.telefono')
            ->orderBy('h.fecha_ingreso', 'asc')->limit(10)->get();

        $topConceptos = DB::table('detalle_honorario as dh')
            ->join('honorario as h', 'dh.id_honorario', '=', 'h.id_honorario')
            ->when($desde, fn($q) => $q->whereDate('h.fecha_ingreso', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('h.fecha_ingreso', '<=', $hasta))
            ->select('dh.concepto',
                DB::raw('COUNT(*) as veces'),
                DB::raw('SUM(dh.importe) as total_facturado'),
                DB::raw('AVG(dh.precio_unitario) as precio_promedio'))
            ->groupBy('dh.concepto')->orderByDesc('total_facturado')->limit(10)->get();

        $pagosRecientes = DB::table('pago_honorario as ph')
            ->join('honorario as h', 'ph.id_honorario', '=', 'h.id_honorario')
            ->join('mascota as m', 'h.id_mascota', '=', 'm.id_mascota')
            ->join('propietario as p', 'm.id_propietario', '=', 'p.id_propietario')
            ->when($desde, fn($q) => $q->whereDate('ph.fecha_pago', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('ph.fecha_pago', '<=', $hasta))
            ->select('ph.id_pago', 'ph.monto', 'ph.metodo_pago', 'ph.fecha_pago',
                'h.id_honorario', 'm.nombre as mascota_nombre', 'p.nombre as propietario_nombre')
            ->orderByDesc('ph.fecha_pago')->limit(10)->get();

        return compact('stats', 'porEstado', 'topDeudores', 'masAntiguos', 'topConceptos', 'pagosRecientes');
    }

    /**
     * Mensajes de validación personalizados para el alta/edición de honorarios
     */
    private function mensajesValidacionHonorario()
    {
        return [
            'id_mascota.required' => 'Debe seleccionar una mascota.',
            'id_mascota.exists' => 'La mascota seleccionada no existe.',
            'id_hospitalizacion.exists' => 'La hospitalización seleccionada no existe.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'fecha_ingreso.date' => 'La fecha de ingreso no es válida.',
            'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser futura.',
            'fecha_corte.date' => 'La fecha de corte no es válida.',
            'fecha_corte.after_or_equal' => 'La fecha de corte no puede ser anterior a la fecha de ingreso.',
            'detalles.required' => 'Debe agregar al menos un concepto.',
            'detalles.min' => 'Debe agregar al menos un concepto.',
            'detalles.*.concepto.required' => 'El concepto es obligatorio.',
            'detalles.*.concepto.max' => 'El concepto no puede exceder 200 caracteres.',
            'detalles.*.concepto.regex' => 'El concepto no puede estar vacío.',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria.',
            'detalles.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'detalles.*.cantidad.max' => 'La cantidad no puede exceder 9999.',
            'detalles.*.precio_unitario.required' => 'El precio unitario es obligatorio.',
            'detalles.*.precio_unitario.numeric' => 'El precio unitario debe ser numérico.',
            'detalles.*.precio_unitario.min' => 'El precio unitario debe ser mayor a 0.',
            'detalles.*.precio_unitario.max' => 'El precio unitario excede el valor máximo permitido.',
            'detalles.*.precio_unitario.decimal' => 'El precio unitario admite máximo 2 decimales.',
        ];
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

    /**
     * Calcular el estado de pago de cada detalle basado en los datos reales de la tabla
     * Usa los valores monto_pagado, fecha_pago, tipo_pago guardados en detalle_honorario
     */
    private function calcularEstadoPagosDetalles($detalles, $pagos)
    {
        // Asegurar que tenemos colecciones
        $detalles = collect($detalles);

        // Procesar cada detalle usando los datos reales de la base de datos
        $detallesConEstado = $detalles->map(function($detalle) {
            $montoConcepto = (float) $detalle->importe;
            
            // Usar los valores reales de la tabla detalle_honorario
            $montoPagadoReal = (float) ($detalle->monto_pagado ?? 0);
            
            // Mantener los valores originales de la tabla
            $detalle->monto_pagado_calculado = $montoPagadoReal;
            
            // Asegurar que TODAS las propiedades necesarias estén definidas
            $detalle->fecha_pago = $detalle->fecha_pago ?? null;
            $detalle->tipo_pago = $detalle->tipo_pago ?? null;
            $detalle->monto_pagado = $detalle->monto_pagado ?? 0;

            // Calcular estado de pago basado en el monto real pagado
            if ($montoPagadoReal >= $montoConcepto) {
                $detalle->estado_pago = 'Pagado';
            } elseif ($montoPagadoReal > 0) {
                $detalle->estado_pago = 'Parcial';
            } else {
                $detalle->estado_pago = 'Pendiente';
            }

            // Calcular saldo restante
            $detalle->saldo_concepto = $montoConcepto - $montoPagadoReal;
            
            return $detalle;
        });

        return $detallesConEstado;
    }
}