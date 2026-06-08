@extends('dash.recepcion')
@section('page-title', 'Reporte General de Honorarios')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/recepcion/honorarios.css') }}">
<style>
    .rpt-container { max-width: 1100px; margin: 0 auto; padding: 0 0 48px; }

    /* ── Encabezado ── */
    .rpt-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        flex-wrap: wrap; gap: 16px; margin-bottom: 28px;
    }
    .rpt-title { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0; }
    .rpt-subtitle { font-size: 13px; color: #64748b; margin: 4px 0 0; }
    .rpt-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

    /* ── Filtro de fechas ── */
    .rpt-filter-bar {
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 14px 20px; margin-bottom: 24px;
    }
    .rpt-filter-bar label { font-size: 13px; color: #475569; font-weight: 500; white-space: nowrap; }
    .rpt-filter-bar input[type="date"] {
        border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px;
        font-size: 13px; color: #1e293b;
    }
    .rpt-filter-bar .btn-apply {
        background: #2563eb; color: #fff; border: none; border-radius: 6px;
        padding: 7px 16px; font-size: 13px; font-weight: 600; cursor: pointer;
    }
    .rpt-filter-bar .btn-apply:hover { background: #1d4ed8; }
    .rpt-filter-bar .btn-clear {
        background: #f1f5f9; color: #475569; border: none; border-radius: 6px;
        padding: 7px 14px; font-size: 13px; cursor: pointer;
    }
    .rpt-periodo {
        margin-left: auto; font-size: 12px; color: #94a3b8; white-space: nowrap;
    }

    /* ── Cards KPI ── */
    .kpi-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px;
    }
    .kpi-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 18px 20px; border-top: 3px solid transparent;
    }
    .kpi-card.kpi-blue   { border-top-color: #2563eb; }
    .kpi-card.kpi-green  { border-top-color: #059669; }
    .kpi-card.kpi-red    { border-top-color: #dc2626; }
    .kpi-card.kpi-purple { border-top-color: #7c3aed; }
    .kpi-label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .kpi-value { font-size: 24px; font-weight: 700; color: #1e293b; margin: 6px 0 2px; }
    .kpi-sub   { font-size: 12px; color: #94a3b8; }

    /* ── Barra de cobranza ── */
    .cobranza-bar {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 18px 24px; margin-bottom: 24px;
    }
    .cobranza-bar .cb-header { display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 10px; }
    .cb-track { background: #e2e8f0; border-radius: 999px; height: 14px; overflow: hidden; display: flex; }
    .cb-pagado  { background: #059669; height: 100%; transition: width .5s; }
    .cb-parcial { background: #f59e0b; height: 100%; transition: width .5s; }
    .cb-pending { background: #dc2626; height: 100%; transition: width .5s; }
    .cb-legend  { display: flex; gap: 20px; margin-top: 10px; font-size: 12px; color: #475569; }
    .cb-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }

    /* ── Sección genérica ── */
    .rpt-section {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        overflow: hidden; margin-bottom: 24px;
    }
    .rpt-section-header {
        background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px; font-size: 14px; font-weight: 600; color: #334155;
        display: flex; align-items: center; gap: 8px;
    }
    .rpt-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .rpt-table th {
        background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 10px 16px;
        text-align: left; font-size: 11px; font-weight: 600; color: #64748b;
        text-transform: uppercase; letter-spacing: .4px;
    }
    .rpt-table td { padding: 11px 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
    .rpt-table tr:last-child td { border-bottom: none; }
    .rpt-table tr:hover td { background: #f8fafc; }
    .td-right  { text-align: right; }
    .td-center { text-align: center; }
    .empty-rpt { padding: 24px; text-align: center; color: #94a3b8; font-size: 13px; }

    /* ── Badges ── */
    .badge { display: inline-flex; align-items: center; padding: 3px 10px;
             border-radius: 999px; font-size: 11px; font-weight: 600; }
    .badge-pendiente { background: #fef9c3; color: #854d0e; }
    .badge-parcial   { background: #dbeafe; color: #1e40af; }
    .badge-pagado    { background: #dcfce7; color: #166534; }
    .badge-method    { background: #f1f5f9; color: #475569; }

    /* ── Grid 2 columnas ── */
    .rpt-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }

    /* ── Días vencido ── */
    .dias-badge {
        display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;
    }
    .dias-ok     { background: #dcfce7; color: #166534; }
    .dias-warn   { background: #fef9c3; color: #854d0e; }
    .dias-danger { background: #fee2e2; color: #991b1b; }

    /* ── Scroll en tablas ── */
    .rpt-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .rpt-table-wrap table { min-width: 520px; }

    @media (max-width: 900px) {
        .kpi-grid    { grid-template-columns: repeat(2,1fr); }
        .rpt-two-col { grid-template-columns: 1fr; }
        .rpt-header  { flex-direction: column; }
    }
    @media (max-width: 560px) {
        .kpi-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush
@section('content')
<section id="mod-honorarios" class="module active">
<div class="rpt-container">

    {{-- Encabezado ─────────────────────────────────────────────────────── --}}
    <div class="rpt-header">
        <div>
            <h2 class="rpt-title">Reporte General de Honorarios</h2>
            <p class="rpt-subtitle">
                Análisis financiero y de cobranza de la clínica veterinaria
            </p>
        </div>
        <div class="rpt-actions">
            <a href="{{ route('honorarios.honorarios.index') }}" class="btn-secondary btn-small">
                &larr; Volver
            </a>
            <a href="{{ route('honorarios.honorarios.reporte-pdf', array_filter(['desde'=>$desde,'hasta'=>$hasta])) }}"
               target="_blank" class="btn-primary btn-small">
                Exportar PDF
            </a>
        </div>
    </div>

    {{-- Filtro de período ──────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('honorarios.honorarios.reporte') }}" class="rpt-filter-bar">
        <label>Período:</label>
        <label>Desde</label>
        <input type="date" name="desde" value="{{ $desde }}" max="{{ date('Y-m-d') }}">
        <label>Hasta</label>
        <input type="date" name="hasta" value="{{ $hasta }}" max="{{ date('Y-m-d') }}">
        <button type="submit" class="btn-apply">Aplicar</button>
        @if($desde || $hasta)
            <a href="{{ route('honorarios.honorarios.reporte') }}" class="btn-clear">Limpiar</a>
        @endif
        <span class="rpt-periodo">
            @if($desde || $hasta)
                {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : '—' }}
                al
                {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : 'hoy' }}
            @else
                Todos los registros
            @endif
        </span>
    </form>

    {{-- KPIs ───────────────────────────────────────────────────────────── --}}
    @php
        $totalEstados = $stats->total_pendiente_count + $stats->total_parcial_count + $stats->total_pagado_count;
        $pctPagado  = $totalEstados > 0 ? round($stats->total_pagado_count  / $totalEstados * 100) : 0;
        $pctParcial = $totalEstados > 0 ? round($stats->total_parcial_count / $totalEstados * 100) : 0;
        $pctPending = 100 - $pctPagado - $pctParcial;

        $totalSubtotal = $stats->total_subtotal > 0 ? $stats->total_subtotal : 1;
        $barPagado  = round($stats->total_pagado    / $totalSubtotal * 100, 1);
        $barParcial = round(($stats->total_subtotal - $stats->total_pagado - $stats->total_pendiente) / $totalSubtotal * 100, 1);
        $barPendiente = max(0, 100 - $barPagado - $barParcial);
    @endphp

    <div class="kpi-grid">
        <div class="kpi-card kpi-blue">
            <div class="kpi-label">Total Honorarios</div>
            <div class="kpi-value">{{ number_format($stats->total_honorarios) }}</div>
            <div class="kpi-sub">{{ $stats->total_pendiente_count }} pendientes · {{ $stats->total_parcial_count }} parciales · {{ $stats->total_pagado_count }} pagados</div>
        </div>
        <div class="kpi-card kpi-purple">
            <div class="kpi-label">Monto Total Facturado</div>
            <div class="kpi-value">${{ number_format($stats->total_subtotal, 2) }}</div>
            <div class="kpi-sub">Suma de todos los subtotales</div>
        </div>
        <div class="kpi-card kpi-green">
            <div class="kpi-label">Total Cobrado</div>
            <div class="kpi-value">${{ number_format($stats->total_pagado, 2) }}</div>
            <div class="kpi-sub">{{ $stats->porcentaje_cobranza }}% de cobranza</div>
        </div>
        <div class="kpi-card kpi-red">
            <div class="kpi-label">Saldo por Cobrar</div>
            <div class="kpi-value">${{ number_format($stats->total_pendiente, 2) }}</div>
            <div class="kpi-sub">{{ 100 - $stats->porcentaje_cobranza }}% pendiente de cobro</div>
        </div>
    </div>

    {{-- Barra de cobranza ──────────────────────────────────────────────── --}}
    <div class="cobranza-bar">
        <div class="cb-header">
            <span>Progreso de cobranza general</span>
            <span style="color:#059669; font-size:20px; font-weight:700;">{{ $stats->porcentaje_cobranza }}%</span>
        </div>
        <div class="cb-track">
            <div class="cb-pagado"  style="width:{{ $barPagado }}%"   title="Pagado: ${{ number_format($stats->total_pagado,2) }}"></div>
            <div class="cb-parcial" style="width:{{ max(0,$barParcial) }}%" title="Parcialmente pagado"></div>
            <div class="cb-pending" style="width:{{ $barPendiente }}%" title="Pendiente: ${{ number_format($stats->total_pendiente,2) }}"></div>
        </div>
        <div class="cb-legend">
            <span><span class="cb-dot" style="background:#059669"></span>Cobrado ${{ number_format($stats->total_pagado,2) }}</span>
            <span><span class="cb-dot" style="background:#dc2626"></span>Pendiente ${{ number_format($stats->total_pendiente,2) }}</span>
            <span><span class="cb-dot" style="background:#94a3b8"></span>{{ $stats->total_honorarios }} honorarios totales</span>
        </div>
    </div>

    {{-- Desglose por estado + Conceptos más facturados ────────────────── --}}
    <div class="rpt-two-col">

        {{-- Por estado --}}
        <div class="rpt-section">
            <div class="rpt-section-header">📊 Desglose por Estado</div>
            @if($porEstado->count())
            <div class="rpt-table-wrap">
            <table class="rpt-table">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th class="td-center">Cantidad</th>
                        <th class="td-right">Facturado</th>
                        <th class="td-right">Cobrado</th>
                        <th class="td-right">Pendiente</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($porEstado as $fila)
                    <tr>
                        <td><span class="badge badge-{{ strtolower($fila->estado) }}">{{ $fila->estado }}</span></td>
                        <td class="td-center">{{ $fila->cantidad }}</td>
                        <td class="td-right">${{ number_format($fila->total_subtotal,2) }}</td>
                        <td class="td-right" style="color:#059669">${{ number_format($fila->total_pagado,2) }}</td>
                        <td class="td-right" style="color:#dc2626">${{ number_format($fila->total_pendiente,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @else
                <div class="empty-rpt">Sin datos para el período seleccionado</div>
            @endif
        </div>

        {{-- Top conceptos --}}
        <div class="rpt-section">
            <div class="rpt-section-header">🏷️ Servicios Más Facturados</div>
            @if($topConceptos->count())
            <div class="rpt-table-wrap">
            <table class="rpt-table">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th class="td-center">Veces</th>
                        <th class="td-right">Total</th>
                        <th class="td-right">Precio Prom.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topConceptos as $i => $c)
                    <tr>
                        <td>
                            @if($i === 0)<span style="color:#f59e0b;font-weight:700;">🥇</span>
                            @elseif($i === 1)<span style="color:#94a3b8;font-weight:700;">🥈</span>
                            @elseif($i === 2)<span style="color:#cd7f32;font-weight:700;">🥉</span>
                            @else <span style="color:#cbd5e1;font-size:11px;">{{ $i+1 }}.</span>
                            @endif
                            {{ $c->concepto }}
                        </td>
                        <td class="td-center">{{ $c->veces }}</td>
                        <td class="td-right" style="font-weight:600">${{ number_format($c->total_facturado,2) }}</td>
                        <td class="td-right" style="color:#64748b">${{ number_format($c->precio_promedio,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @else
                <div class="empty-rpt">Sin datos para el período seleccionado</div>
            @endif
        </div>

    </div>

    {{-- Propietarios con mayor saldo pendiente ─────────────────────────── --}}
    <div class="rpt-section">
        <div class="rpt-section-header">⚠️ Propietarios con Mayor Saldo Pendiente</div>
        @if($topDeudores->count())
        <div class="rpt-table-wrap">
        <table class="rpt-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Propietario</th>
                    <th>Teléfono</th>
                    <th class="td-center">Honorarios</th>
                    <th class="td-right">Saldo Pendiente</th>
                    <th class="td-right">Prioridad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topDeudores as $i => $d)
                @php
                    $prioridad = $d->total_pendiente >= 1000 ? 'Alta' : ($d->total_pendiente >= 300 ? 'Media' : 'Baja');
                    $priClase  = $prioridad === 'Alta' ? 'dias-danger' : ($prioridad === 'Media' ? 'dias-warn' : 'dias-ok');
                @endphp
                <tr>
                    <td style="color:#94a3b8; font-weight:600;">{{ $i+1 }}</td>
                    <td style="font-weight:600;">{{ $d->propietario }}</td>
                    <td style="color:#64748b;">{{ $d->telefono ?? '—' }}</td>
                    <td class="td-center">{{ $d->num_honorarios }}</td>
                    <td class="td-right" style="color:#dc2626; font-weight:700; font-size:15px;">${{ number_format($d->total_pendiente,2) }}</td>
                    <td class="td-center"><span class="dias-badge {{ $priClase }}">{{ $prioridad }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
            <div class="empty-rpt">No hay saldos pendientes en el período seleccionado</div>
        @endif
    </div>

    {{-- Honorarios más antiguos sin pagar + Pagos recientes ───────────── --}}
    <div class="rpt-two-col">

        {{-- Antigüedad de deuda --}}
        <div class="rpt-section">
            <div class="rpt-section-header">⏳ Deuda Más Antigua sin Liquidar</div>
            @if($masAntiguos->count())
            <div class="rpt-table-wrap">
            <table class="rpt-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mascota / Propietario</th>
                        <th class="td-center">Días</th>
                        <th class="td-right">Pendiente</th>
                        <th class="td-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($masAntiguos as $h)
                    @php
                        $dias = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($h->fecha_ingreso)->startOfDay());
                        $diasClase = $dias > 60 ? 'dias-danger' : ($dias > 30 ? 'dias-warn' : 'dias-ok');
                    @endphp
                    <tr>
                        <td style="color:#94a3b8; font-size:12px;">
                            <a href="{{ route('honorarios.honorarios.show', $h->id_honorario) }}"
                               style="color:#2563eb; text-decoration:none;">#{{ $h->id_honorario }}</a>
                        </td>
                        <td>
                            <strong>{{ $h->mascota_nombre }}</strong><br>
                            <small style="color:#64748b;">{{ $h->propietario_nombre }}</small>
                        </td>
                        <td class="td-center">
                            <span class="dias-badge {{ $diasClase }}">{{ $dias }}d</span>
                        </td>
                        <td class="td-right" style="color:#dc2626; font-weight:600;">${{ number_format($h->saldo_pendiente,2) }}</td>
                        <td class="td-center">
                            <span class="badge badge-{{ strtolower($h->estado) }}">{{ $h->estado }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @else
                <div class="empty-rpt">No hay deuda pendiente</div>
            @endif
        </div>

        {{-- Pagos recientes --}}
        <div class="rpt-section">
            <div class="rpt-section-header">💳 Últimos Pagos Recibidos</div>
            @if($pagosRecientes->count())
            <div class="rpt-table-wrap">
            <table class="rpt-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Mascota / Propietario</th>
                        <th class="td-center">Método</th>
                        <th class="td-right">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagosRecientes as $p)
                    <tr>
                        <td style="color:#64748b; font-size:12px; white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y') }}<br>
                            <span style="color:#94a3b8;">{{ \Carbon\Carbon::parse($p->fecha_pago)->format('H:i') }}</span>
                        </td>
                        <td>
                            <strong>{{ $p->mascota_nombre }}</strong><br>
                            <small style="color:#64748b;">{{ $p->propietario_nombre }}</small>
                        </td>
                        <td class="td-center">
                            <span class="badge badge-method">{{ ucfirst($p->metodo_pago ?? '—') }}</span>
                        </td>
                        <td class="td-right" style="color:#059669; font-weight:700; font-size:15px;">
                            ${{ number_format($p->monto,2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @else
                <div class="empty-rpt">Sin pagos registrados en el período</div>
            @endif
        </div>

    </div>

</div>
</section>
@endsection
