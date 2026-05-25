<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Honorario #{{ $honorario->id_honorario }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #007bff;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .info-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #007bff;
            font-size: 14px;
        }
        
        .info-row {
            margin-bottom: 5px;
        }
        
        .info-row strong {
            display: inline-block;
            width: 120px;
            color: #333;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .details-table th,
        .details-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .details-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .details-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-section {
            margin-top: 20px;
            border-top: 2px solid #007bff;
            padding-top: 15px;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .total-label {
            display: table-cell;
            width: 70%;
            text-align: right;
            padding-right: 20px;
            font-weight: bold;
        }
        
        .total-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pendiente {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-parcial {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .status-pagado {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .payment-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .payment-info h4 {
            margin: 0 0 10px 0;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HONORARIO VETERINARIO</h1>
        <p><strong>Clínica Veterinaria</strong></p>
        <p>Honorario #{{ $honorario->id_honorario }}</p>
        <p>Fecha de emisión: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="info-section">
        <div class="info-column">
            <div class="info-box">
                <h3>Información del Propietario</h3>
                <div class="info-row">
                    <strong>Nombre:</strong> {{ $honorario->propietario_nombre }}
                </div>
                <div class="info-row">
                    <strong>Teléfono:</strong> {{ $honorario->propietario_telefono }}
                </div>
                @if($honorario->propietario_direccion)
                <div class="info-row">
                    <strong>Dirección:</strong> {{ $honorario->propietario_direccion }}
                </div>
                @endif
            </div>
        </div>
        
        <div class="info-column">
            <div class="info-box">
                <h3>Información de la Mascota</h3>
                <div class="info-row">
                    <strong>Nombre:</strong> {{ $honorario->mascota_nombre }}
                </div>
                <div class="info-row">
                    <strong>Especie:</strong> {{ $honorario->especie }}
                </div>
                @if($honorario->raza)
                <div class="info-row">
                    <strong>Raza:</strong> {{ $honorario->raza }}
                </div>
                @endif
                @if($honorario->edad)
                <div class="info-row">
                    <strong>Edad:</strong> {{ $honorario->edad }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-column">
            <div class="info-box">
                <h3>Detalles del Honorario</h3>
                <div class="info-row">
                    <strong>Fecha Ingreso:</strong> {{ $honorario->fecha_ingreso ? \Carbon\Carbon::parse($honorario->fecha_ingreso)->format('d/m/Y') : 'No especificada' }}
                </div>
                @if($honorario->fecha_corte)
                <div class="info-row">
                    <strong>Fecha Corte:</strong> {{ \Carbon\Carbon::parse($honorario->fecha_corte)->format('d/m/Y') }}
                </div>
                @endif
                @if($honorario->hospitalizacion_fecha)
                <div class="info-row">
                    <strong>Hospitalización:</strong> {{ \Carbon\Carbon::parse($honorario->hospitalizacion_fecha)->format('d/m/Y H:i') }}
                </div>
                @endif
                <div class="info-row">
                    <strong>Estado:</strong> 
                    <span class="status-badge status-{{ strtolower($honorario->estado) }}">
                        {{ $honorario->estado }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unitario</th>
                <th class="text-right">Importe</th>
                <th class="text-center">Estado de Pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detallesConEstado as $detalle)
            <tr>
                <td>{{ $detalle->concepto }}</td>
                <td class="text-center">{{ $detalle->cantidad }}</td>
                <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">${{ number_format($detalle->importe, 2) }}</td>
                <td class="text-center">
                    @if($detalle->estado_pago === 'Pagado')
                        <span style="color: green; font-weight: bold;">✅ Pagado</span>
                        @if($detalle->fecha_pago)
                            <br><small style="color: #666;">{{ \Carbon\Carbon::parse($detalle->fecha_pago)->format('d/m/Y') }}</small>
                        @endif
                        @if(isset($detalle->tipo_pago) && $detalle->tipo_pago)
                            <br><small style="color: #666;">{{ $detalle->tipo_pago }}</small>
                        @endif
                    @elseif($detalle->estado_pago === 'Parcial')
                        <span style="color: orange; font-weight: bold;">🔄 Parcial</span>
                        <br><small style="color: #666;">Pagado: ${{ number_format($detalle->monto_pagado_calculado ?? 0, 2) }}</small>
                        <br><small style="color: #666;">Resta: ${{ number_format($detalle->saldo_concepto, 2) }}</small>
                    @else
                        <span style="color: red; font-weight: bold;">⏳ Pendiente</span>
                        <br><small style="color: #666;">Monto completo</small>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-row">
            <div class="total-label">Subtotal:</div>
            <div class="total-value">${{ number_format($honorario->subtotal, 2) }}</div>
        </div>
        <div class="total-row">
            <div class="total-label">Total Pagado:</div>
            <div class="total-value" style="color: green; font-weight: bold;">
                ${{ number_format($totalPagadoReal, 2) }}
            </div>
        </div>
        <div class="total-row" style="border-top: 2px solid #007bff; margin-top: 10px; padding-top: 10px;">
            <div class="total-label">Saldo Pendiente:</div>
            <div class="total-value" style="color: {{ $saldoPendiente > 0 ? 'red' : 'green' }}; font-size: 16px;">
                ${{ number_format($saldoPendiente, 2) }}
            </div>
        </div>
    </div>

    @php
        $conceptosPagados = $detallesConEstado->where('estado_pago', 'Pagado')->count();
        $conceptosParciales = $detallesConEstado->where('estado_pago', 'Parcial')->count();
        $conceptosPendientes = $detallesConEstado->where('estado_pago', 'Pendiente')->count();
        $totalConceptos = $detallesConEstado->count();
    @endphp

    @if($totalConceptos > 0)
    <div class="payment-summary" style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px; border-left: 4px solid #007bff;">
        <h4 style="margin-top: 0; color: #007bff;">Resumen de Estado de Conceptos</h4>
        <div style="display: table; width: 100%;">
            <div style="display: table-row;">
                <div style="display: table-cell; padding: 5px 15px 5px 0;">
                    <span style="color: green; font-weight: bold;">Conceptos Pagados:</span> {{ $conceptosPagados }}/{{ $totalConceptos }}
                </div>
                @if($conceptosParciales > 0)
                <div style="display: table-cell; padding: 5px 15px 5px 0;">
                    <span style="color: orange; font-weight: bold;">Conceptos Parciales:</span> {{ $conceptosParciales }}
                </div>
                @endif
                @if($conceptosPendientes > 0)
                <div style="display: table-cell; padding: 5px;">
                    <span style="color: red; font-weight: bold;">⏳ Conceptos Pendientes:</span> {{ $conceptosPendientes }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if(count($pagos) > 0)
    <div class="payment-history">
        <h4 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Historial de Pagos</h4>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Fecha</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Monto</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Tipo</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Notas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $pago)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 6px;">
                        {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}
                    </td>
                    <td style="border: 1px solid #ddd; padding: 6px; text-align: right; color: green;">
                        ${{ number_format($pago->monto, 2) }}
                    </td>
                    <td style="border: 1px solid #ddd; padding: 6px; text-align: center;">
                        {{ $pago->metodo_pago ?? 'N/A' }}
                    </td>
                    <td style="border: 1px solid #ddd; padding: 6px;">
                        {{ $pago->observaciones ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($honorario->saldo_pendiente > 0)
    <div class="payment-info">
        <h4>Información para Pagos</h4>
        <p><strong>Métodos de pago aceptados:</strong> Efectivo, Tarjeta de crédito/débito, Transferencia bancaria</p>
        <p><strong>Nota:</strong> Este documento es un resumen de los servicios prestados y montos adeudados.</p>
    </div>
    @endif

    <div style="background-color: #e8f4fd; border: 1px solid #bee5eb; border-radius: 5px; padding: 10px; margin: 20px 0; font-size: 10px;">
        <h4 style="margin: 0 0 10px 0; color: #0c5460;">📋 Información del Documento</h4>
        <p style="margin: 5px 0;"><strong>✅ Datos Actualizados:</strong> Este PDF refleja únicamente los conceptos vigentes del honorario.</p>
        <p style="margin: 5px 0;"><strong>🔄 Cálculo en Tiempo Real:</strong> Los estados de pago se calculan automáticamente basados en el historial de pagos.</p>
        <p style="margin: 5px 0;"><strong>📊 Distribución de Pagos:</strong> Los pagos se aplican automáticamente a los conceptos más antiguos primero.</p>
        @if(count($detallesConEstado) > 0)
        <p style="margin: 5px 0;"><strong>📝 Total de Conceptos:</strong> {{ count($detallesConEstado) }} concepto(s) activo(s) en este honorario.</p>
        @endif
    </div>

    <div class="footer">
        <p>Este documento fue generado automáticamente el {{ date('d/m/Y') }} a las {{ date('H:i') }}</p>
        <p><strong>Nota:</strong> Si los conceptos fueron modificados recientemente, este PDF ya refleja los cambios actuales</p>
        <p>Clínica Veterinaria - Sistema de Gestión de Honorarios v1.0</p>
    </div>
</body>
</html>