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
            @foreach($detalles as $detalle)
            <tr>
                <td>{{ $detalle->concepto }}</td>
                <td class="text-center">{{ $detalle->cantidad }}</td>
                <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">${{ number_format($detalle->importe, 2) }}</td>
                <td class="text-center">
                    @if($detalle->fecha_pago)
                        <span style="color: green;">✓ Pagado</span>
                        <br><small>{{ \Carbon\Carbon::parse($detalle->fecha_pago)->format('d/m/Y') }}</small>
                        @if($detalle->tipo_pago)
                            <br><small>{{ $detalle->tipo_pago }}</small>
                        @endif
                    @else
                        <span style="color: orange;">⏳ Pendiente</span>
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
            <div class="total-value" style="color: green;">${{ number_format($honorario->total_pagado, 2) }}</div>
        </div>
        <div class="total-row" style="border-top: 2px solid #007bff; margin-top: 10px; padding-top: 10px;">
            <div class="total-label">Saldo Pendiente:</div>
            <div class="total-value" style="color: {{ $honorario->saldo_pendiente > 0 ? 'red' : 'green' }}; font-size: 16px;">
                ${{ number_format($honorario->saldo_pendiente, 2) }}
            </div>
        </div>
    </div>

    @if($honorario->saldo_pendiente > 0)
    <div class="payment-info">
        <h4>Información para Pagos</h4>
        <p><strong>Métodos de pago aceptados:</strong> Efectivo, Tarjeta de crédito/débito, Transferencia bancaria</p>
        <p><strong>Nota:</strong> Este documento es un resumen de los servicios prestados y montos adeudados.</p>
    </div>
    @endif

    <div class="footer">
        <p>Este documento fue generado automáticamente el {{ date('d/m/Y') }} a las {{ date('H:i') }}</p>
        <p>Clínica Veterinaria - Sistema de Gestión de Honorarios</p>
    </div>
</body>
</html>