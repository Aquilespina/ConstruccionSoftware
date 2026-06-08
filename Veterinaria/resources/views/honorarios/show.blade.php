@extends('dash.recepcion')
@section('page-title', 'Detalle Honorario #' . $honorario->id_honorario)
@push('styles')
<link rel="stylesheet" href="{{ asset('css/recepcion/honorarios.css') }}">
<style>
    .show-container { max-width: 1000px; margin: 0 auto; padding: 0 0 40px; }

    /* ── Encabezado ── */
    .show-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .show-title { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0; }
    .show-subtitle { font-size: 13px; color: #64748b; margin: 4px 0 0; }
    .show-actions { display: flex; gap: 10px; flex-wrap: wrap; }

    /* ── Cards de resumen ── */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .summary-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 18px 20px;
    }
    .summary-card .sc-label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .summary-card .sc-value { font-size: 22px; font-weight: 700; margin: 4px 0 0; color: #1e293b; }
    .summary-card.sc-green .sc-value { color: #059669; }
    .summary-card.sc-red   .sc-value { color: #dc2626; }
    .summary-card.sc-blue  .sc-value { color: #2563eb; }

    /* ── Barra de progreso ── */
    .progress-wrap { margin-bottom: 24px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; }
    .progress-wrap .pw-header { display: flex; justify-content: space-between; font-size: 13px; color: #475569; margin-bottom: 8px; }
    .progress-bar-bg { background: #e2e8f0; border-radius: 999px; height: 10px; overflow: hidden; }
    .progress-bar-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg,#059669,#34d399); transition: width .4s; }

    /* ── Panel de dos columnas ── */
    .info-panels {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }
    .info-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .info-panel-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 18px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-panel-body { padding: 16px 18px; }
    .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .info-row:last-child { border-bottom: none; }
    .info-row .ir-label { color: #64748b; }
    .info-row .ir-value { font-weight: 500; color: #1e293b; text-align: right; }

    /* ── Tabla de conceptos ── */
    .section-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .section-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .conceptos-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .conceptos-table th {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .conceptos-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
    .conceptos-table tr:last-child td { border-bottom: none; }
    .conceptos-table tr:hover td { background: #f8fafc; }
    .td-right { text-align: right; }
    .td-center { text-align: center; }

    /* ── Totales ── */
    .totals-box {
        background: #f8fafc;
        border-top: 2px solid #e2e8f0;
        padding: 16px 20px;
    }
    .totals-box .tb-row { display: flex; justify-content: flex-end; gap: 40px; padding: 4px 0; font-size: 14px; color: #475569; }
    .totals-box .tb-row.tb-total { font-size: 16px; font-weight: 700; color: #1e293b; border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 4px; }
    .totals-box .tb-row.tb-pagado { color: #059669; font-weight: 600; }
    .totals-box .tb-row.tb-saldo  { color: #dc2626; font-weight: 600; }
    .totals-box .tb-row.tb-saldo.pagado { color: #059669; }

    /* ── Historial de pagos ── */
    .pagos-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .pagos-table th {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .pagos-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
    .pagos-table tr:last-child td { border-bottom: none; }
    .pagos-table tr:hover td { background: #f8fafc; }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-pendiente { background: #fef9c3; color: #854d0e; }
    .badge-parcial   { background: #dbeafe; color: #1e40af; }
    .badge-pagado    { background: #dcfce7; color: #166534; }
    .badge-method    { background: #f1f5f9; color: #475569; }

    /* ── Estado vacío ── */
    .empty-pagos { padding: 24px; text-align: center; color: #94a3b8; font-size: 14px; }

    /* ── Resumen conceptos ── */
    .resumen-conceptos {
        display: flex;
        gap: 20px;
        padding: 14px 20px;
        border-top: 1px solid #e2e8f0;
        font-size: 13px;
    }
    .rc-item { display: flex; align-items: center; gap: 6px; }
    .rc-dot { width: 10px; height: 10px; border-radius: 50%; }
    .rc-dot-pagado { background: #059669; }
    .rc-dot-parcial { background: #2563eb; }
    .rc-dot-pendiente { background: #dc2626; }

    @media (max-width: 768px) {
        .summary-grid { grid-template-columns: 1fr; }
        .info-panels  { grid-template-columns: 1fr; }
        .show-header  { flex-direction: column; }
    }
</style>
@endpush
@section('content')
<section id="mod-honorarios" class="module active">
<div class="show-container">

    {{-- Encabezado --}}
    <div class="show-header">
        <div>
            <h2 class="show-title">Honorario #{{ $honorario->id_honorario }}</h2>
            <p class="show-subtitle">
                Registrado el {{ \Carbon\Carbon::parse($honorario->fecha_ingreso)->format('d/m/Y') }}
                @if($honorario->fecha_corte)
                    &nbsp;&bull;&nbsp; Corte: {{ \Carbon\Carbon::parse($honorario->fecha_corte)->format('d/m/Y') }}
                @endif
            </p>
        </div>
        <div class="show-actions">
            <a href="{{ route('honorarios.honorarios.index') }}" class="btn-secondary btn-small">
                &larr; Volver
            </a>
            @if($honorario->estado !== 'Pagado')
                <button class="btn-outline btn-small"
                        onclick="editarHonorario({{ $honorario->id_honorario }})">
                    Editar
                </button>
                <button class="btn-primary btn-small"
                        onclick="abrirModalPagoShow({{ $honorario->id_honorario }})">
                    Registrar Pago
                </button>
            @endif
            <a href="{{ route('honorarios.honorarios.pdf', $honorario->id_honorario) }}"
               class="btn-outline btn-small" target="_blank">
                Descargar PDF
            </a>
        </div>
    </div>

    {{-- Cards de resumen --}}
    @php
        $conceptosPagados   = $detallesConEstado->where('estado_pago','Pagado')->count();
        $conceptosParciales = $detallesConEstado->where('estado_pago','Parcial')->count();
        $conceptosPendientes= $detallesConEstado->where('estado_pago','Pendiente')->count();
    @endphp
    <div class="summary-grid">
        <div class="summary-card sc-blue">
            <div class="sc-label">Subtotal</div>
            <div class="sc-value">${{ number_format($honorario->subtotal, 2) }}</div>
        </div>
        <div class="summary-card sc-green">
            <div class="sc-label">Total Pagado</div>
            <div class="sc-value">${{ number_format($totalPagadoReal, 2) }}</div>
        </div>
        <div class="summary-card {{ $saldoPendiente > 0 ? 'sc-red' : 'sc-green' }}">
            <div class="sc-label">Saldo Pendiente</div>
            <div class="sc-value">${{ number_format(max($saldoPendiente, 0), 2) }}</div>
        </div>
    </div>

    {{-- Barra de progreso --}}
    <div class="progress-wrap">
        <div class="pw-header">
            <span>Progreso de pago</span>
            <span>
                <strong>{{ number_format($porcentajePagado, 1) }}%</strong> pagado &mdash;
                {{ $pagos->count() }} pago(s) registrado(s)
            </span>
        </div>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill" style="width: {{ min($porcentajePagado, 100) }}%"></div>
        </div>
    </div>

    {{-- Paneles de información --}}
    <div class="info-panels">

        {{-- Propietario --}}
        <div class="info-panel">
            <div class="info-panel-header">👤 Propietario</div>
            <div class="info-panel-body">
                <div class="info-row">
                    <span class="ir-label">Nombre</span>
                    <span class="ir-value">{{ $honorario->propietario_nombre }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Teléfono</span>
                    <span class="ir-value">{{ $honorario->propietario_telefono ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Dirección</span>
                    <span class="ir-value">{{ $honorario->propietario_direccion ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Mascota --}}
        <div class="info-panel">
            <div class="info-panel-header">🐾 Mascota</div>
            <div class="info-panel-body">
                <div class="info-row">
                    <span class="ir-label">Nombre</span>
                    <span class="ir-value">{{ $honorario->mascota_nombre }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Especie</span>
                    <span class="ir-value">{{ $honorario->especie ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Raza</span>
                    <span class="ir-value">{{ $honorario->raza ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Edad</span>
                    <span class="ir-value">{{ $honorario->edad ? $honorario->edad . ' años' : '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Honorario --}}
        <div class="info-panel">
            <div class="info-panel-header">📋 Datos del Honorario</div>
            <div class="info-panel-body">
                <div class="info-row">
                    <span class="ir-label">Fecha de Ingreso</span>
                    <span class="ir-value">{{ \Carbon\Carbon::parse($honorario->fecha_ingreso)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Fecha de Corte</span>
                    <span class="ir-value">
                        {{ $honorario->fecha_corte
                            ? \Carbon\Carbon::parse($honorario->fecha_corte)->format('d/m/Y')
                            : '—' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Estado</span>
                    <span class="ir-value">
                        <span class="badge badge-{{ strtolower($honorario->estado) }}">
                            {{ $honorario->estado }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Hospitalización --}}
        <div class="info-panel">
            <div class="info-panel-header">🏥 Hospitalización Relacionada</div>
            <div class="info-panel-body">
                @if($honorario->hospitalizacion_fecha)
                    <div class="info-row">
                        <span class="ir-label">Fecha de Ingreso</span>
                        <span class="ir-value">{{ \Carbon\Carbon::parse($honorario->hospitalizacion_fecha)->format('d/m/Y H:i') }}</span>
                    </div>
                @else
                    <p style="color:#94a3b8; font-size:14px; margin:0; text-align:center; padding: 10px 0;">
                        Sin hospitalización asociada
                    </p>
                @endif
            </div>
        </div>

    </div>

    {{-- Tabla de conceptos --}}
    <div class="section-card">
        <div class="section-card-header">📝 Conceptos del Honorario</div>
        <table class="conceptos-table">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th class="td-center">Cantidad</th>
                    <th class="td-right">Precio Unit.</th>
                    <th class="td-right">Importe</th>
                    <th class="td-right">Pagado</th>
                    <th class="td-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detallesConEstado as $detalle)
                <tr>
                    <td>{{ $detalle->concepto }}</td>
                    <td class="td-center">{{ $detalle->cantidad }}</td>
                    <td class="td-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="td-right">${{ number_format($detalle->importe, 2) }}</td>
                    <td class="td-right">
                        ${{ number_format($detalle->monto_pagado ?? 0, 2) }}
                        @if($detalle->saldo_concepto > 0)
                            <br><small style="color:#94a3b8;">resta ${{ number_format($detalle->saldo_concepto, 2) }}</small>
                        @endif
                    </td>
                    <td class="td-center">
                        <span class="badge badge-{{ strtolower($detalle->estado_pago) }}">
                            {{ $detalle->estado_pago }}
                        </span>
                        @if($detalle->fecha_pago)
                            <br><small style="color:#94a3b8;">{{ \Carbon\Carbon::parse($detalle->fecha_pago)->format('d/m/Y') }}</small>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Resumen de conceptos --}}
        <div class="resumen-conceptos">
            @if($conceptosPagados > 0)
                <span class="rc-item"><span class="rc-dot rc-dot-pagado"></span>{{ $conceptosPagados }} pagado(s)</span>
            @endif
            @if($conceptosParciales > 0)
                <span class="rc-item"><span class="rc-dot rc-dot-parcial"></span>{{ $conceptosParciales }} parcial(es)</span>
            @endif
            @if($conceptosPendientes > 0)
                <span class="rc-item"><span class="rc-dot rc-dot-pendiente"></span>{{ $conceptosPendientes }} pendiente(s)</span>
            @endif
        </div>

        {{-- Totales --}}
        <div class="totals-box">
            <div class="tb-row">
                <span>Subtotal</span>
                <span>${{ number_format($honorario->subtotal, 2) }}</span>
            </div>
            <div class="tb-row tb-pagado">
                <span>Total Pagado</span>
                <span>${{ number_format($totalPagadoReal, 2) }}</span>
            </div>
            <div class="tb-row tb-saldo {{ $saldoPendiente <= 0 ? 'pagado' : '' }}">
                <span>Saldo Pendiente</span>
                <span>${{ number_format(max($saldoPendiente, 0), 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Historial de pagos --}}
    <div class="section-card">
        <div class="section-card-header">💳 Historial de Pagos</div>
        @if($pagos->count() > 0)
            <table class="pagos-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th class="td-right">Monto</th>
                        <th class="td-center">Método</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagos as $i => $pago)
                    <tr>
                        <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}</td>
                        <td class="td-right" style="color:#059669; font-weight:600;">
                            ${{ number_format($pago->monto, 2) }}
                        </td>
                        <td class="td-center">
                            <span class="badge badge-method">
                                {{ ucfirst($pago->metodo_pago ?? 'N/A') }}
                            </span>
                        </td>
                        <td style="color:#64748b;">{{ $pago->observaciones ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-pagos">Sin pagos registrados aún</div>
        @endif
    </div>

</div>
</section>

<!-- Modal de edición de honorario -->
<div id="modal-honorario" class="modal">
    <div class="modal-overlay" onclick="cerrarModalHonorario()"></div>
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>Editar Honorario</h3>
            <button class="modal-close" onclick="cerrarModalHonorario()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-honorario" action="{{ route('honorarios.honorarios.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="honorario-mascota">Mascota *</label>
                        <select id="honorario-mascota" name="id_mascota" class="form-control" required>
                            <option value="">Seleccionar mascota</option>
                            @foreach($mascotas as $mascota)
                                <option value="{{ $mascota['id'] }}" data-especie="{{ $mascota['especie'] }}">
                                    {{ $mascota['display_name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="honorario-hospitalizacion">Hospitalización (Opcional)</label>
                        <select id="honorario-hospitalizacion" name="id_hospitalizacion" class="form-control">
                            <option value="">Sin hospitalización relacionada</option>
                            @foreach($hospitalizaciones as $hosp)
                                <option value="{{ $hosp->id_hospitalizacion }}">
                                    {{ $hosp->mascota_nombre }} - {{ \Carbon\Carbon::parse($hosp->fecha_ingreso)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="honorario-fecha-ingreso">Fecha de Ingreso *</label>
                        <input type="date" id="honorario-fecha-ingreso" name="fecha_ingreso"
                               class="form-control" value="{{ date('Y-m-d') }}"
                               min="{{ date('Y-m-d') }}" readonly tabindex="-1"
                               onkeydown="return false" onpaste="return false"
                               style="background-color:#f1f5f9; cursor:not-allowed;"
                               title="La fecha de ingreso se registra automáticamente con la fecha actual" required>
                        <small class="form-text text-muted">Se registra automáticamente con la fecha actual</small>
                    </div>
                    <div class="form-group">
                        <label for="honorario-fecha-corte">Fecha de Corte</label>
                        <input type="date" id="honorario-fecha-corte" name="fecha_corte"
                               class="form-control" min="{{ date('Y-m-d') }}"
                               title="La fecha de corte no puede ser anterior a la fecha de ingreso">
                        <small class="form-text text-muted">Opcional. No puede ser anterior a la fecha de ingreso</small>
                    </div>
                </div>

                <div id="honorario-form-alert" style="display:none; margin-bottom:10px;" class="alert alert-warning">
                    <span id="honorario-form-alert-msg"></span>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <h4>Detalles del Honorario</h4>
                        <button type="button" class="btn-secondary btn-small" onclick="agregarDetalle()">
                            + Agregar Concepto
                        </button>
                    </div>
                    <div id="detalles-container">
                        <div class="detalle-item" data-index="0">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Concepto *</label>
                                    <input type="text" name="detalles[0][concepto]"
                                           class="form-control concepto-input"
                                           placeholder="Descripción del servicio"
                                           maxlength="200" pattern=".*\S.*"
                                           title="El concepto es obligatorio y no puede contener solo espacios" required>
                                    <div class="concepto-suggestions"></div>
                                </div>
                                <div class="form-group form-group-small">
                                    <label>Cantidad *</label>
                                    <input type="number" name="detalles[0][cantidad]"
                                           class="form-control cantidad-input"
                                           min="1" max="9999" step="1" value="1" required>
                                </div>
                                <div class="form-group">
                                    <label>Precio Unitario *</label>
                                    <input type="number" name="detalles[0][precio_unitario]"
                                           class="form-control precio-input"
                                           step="0.01" min="0.01" max="999999.99" required>
                                </div>
                                <div class="form-group">
                                    <label>Importe</label>
                                    <input type="text" class="form-control importe-display" readonly>
                                </div>
                                <div class="form-group form-group-actions">
                                    <button type="button" class="btn-danger btn-small"
                                            onclick="eliminarDetalle(this)" style="margin-top:25px;">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="total-section">
                        <div class="total-row">
                            <strong>Subtotal: $<span id="subtotal-display">0.00</span></strong>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="cerrarModalHonorario()">Cancelar</button>
            <button type="button" class="btn-primary" onclick="guardarHonorario()">Actualizar Honorario</button>
        </div>
    </div>
</div>

<!-- Modal de pago (autocontenido, carga datos vía AJAX) -->
<div id="modal-pago-show" class="modal" style="display:none; position:fixed; inset:0; z-index:1000;
     background:rgba(0,0,0,.45); align-items:center; justify-content:center;">
    <div class="modal-content" style="position:relative; z-index:1001; width:100%; max-width:520px;
         background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div class="modal-header">
            <h3>Registrar Pago</h3>
            <button class="modal-close" onclick="cerrarModalPagoShow()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="info-pago-show" class="info-card" style="margin-bottom:20px;"></div>
            <form id="form-pago-show">
                @csrf
                <input type="hidden" id="show-pago-id" name="id_honorario">
                <div class="form-row">
                    <div class="form-group">
                        <label for="show-pago-monto">Monto a Pagar *</label>
                        <input type="number" id="show-pago-monto" name="monto"
                               class="form-control" step="0.01" min="0.01" required placeholder="0.00">
                        <small class="form-text text-muted">Máximo: el saldo pendiente</small>
                    </div>
                    <div class="form-group">
                        <label for="show-pago-tipo">Tipo de Pago *</label>
                        <select id="show-pago-tipo" name="tipo_pago" class="form-control" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="show-pago-notas">Notas (Opcional)</label>
                    <textarea id="show-pago-notas" name="notas" class="form-control" rows="2"
                              maxlength="500" placeholder="Notas adicionales..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="cerrarModalPagoShow()">Cancelar</button>
            <button type="button" class="btn-primary" id="btn-procesar-pago-show">Registrar Pago</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/recepcion/honorarios.js') }}"></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    window.abrirModalPagoShow = function(idHonorario) {
        const modal = document.getElementById('modal-pago-show');
        document.getElementById('form-pago-show').reset();
        document.getElementById('show-pago-id').value = idHonorario;
        document.getElementById('info-pago-show').innerHTML = '<p style="color:#64748b;font-size:13px;">Cargando información...</p>';

        fetch(`/recepcion/honorarios/${idHonorario}/info-pago`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { alert(data.message || 'Error al cargar el honorario'); return; }
            const h = data.honorario;
            const inputMonto = document.getElementById('show-pago-monto');
            inputMonto.max = h.saldo_pendiente;
            inputMonto.placeholder = `Máx: $${parseFloat(h.saldo_pendiente).toFixed(2)}`;
            document.getElementById('info-pago-show').innerHTML = `
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px;">
                    <div><strong>Mascota:</strong> ${h.mascota_nombre}<br>
                         <strong>Propietario:</strong> ${h.propietario_nombre}</div>
                    <div><strong>Subtotal:</strong> $${parseFloat(h.subtotal).toFixed(2)}<br>
                         <strong style="color:#dc2626;">Saldo pendiente:</strong>
                         $${parseFloat(h.saldo_pendiente).toFixed(2)}</div>
                </div>`;
        })
        .catch(() => alert('Error al cargar información del honorario'));

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.cerrarModalPagoShow = function() {
        document.getElementById('modal-pago-show').style.display = 'none';
        document.body.style.overflow = 'auto';
    };

    document.getElementById('btn-procesar-pago-show').addEventListener('click', function() {
        const form = document.getElementById('form-pago-show');
        if (!form.checkValidity()) { form.reportValidity(); return; }

        const monto  = parseFloat(document.getElementById('show-pago-monto').value);
        const maxVal = parseFloat(document.getElementById('show-pago-monto').max);
        if (!isNaN(maxVal) && monto > maxVal) {
            alert(`El monto no puede superar el saldo pendiente ($${maxVal.toFixed(2)})`);
            return;
        }

        const idHonorario = document.getElementById('show-pago-id').value;
        const formData = new FormData(form);
        const btn = this;
        btn.textContent = 'Procesando...';
        btn.disabled = true;

        fetch(`/recepcion/honorarios/${idHonorario}/pago`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                cerrarModalPagoShow();
                setTimeout(() => window.location.reload(), 800);
            } else {
                alert(data.message || 'Error al registrar el pago');
            }
        })
        .catch(() => alert('Error al procesar el pago'))
        .finally(() => { btn.textContent = 'Registrar Pago'; btn.disabled = false; });
    });

    // Cerrar al click fuera del modal
    document.getElementById('modal-pago-show').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalPagoShow();
    });
</script>
@endsection
