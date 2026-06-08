<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte General de Honorarios</title>
<style>
    * { margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 10.5px; color: #1e293b; }

    /* ── Encabezado ── */
    .header-table { width: 100%; border-bottom: 2px solid #1d4ed8; margin-bottom: 14px; padding-bottom: 10px; }
    .header-title  { font-size: 17px; font-weight: bold; color: #1d4ed8; }
    .header-meta   { font-size: 9px; color: #64748b; margin-top: 3px; }
    .header-periodo {
        background: #eff6ff; border: 1px solid #bfdbfe;
        padding: 4px 10px; font-size: 9.5px; color: #1e40af; font-weight: bold;
        text-align: right;
    }

    /* ── KPIs (tabla 4 celdas) ── */
    .kpi-table { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin-bottom: 14px; }
    .kpi-td {
        width: 25%; background: #f8fafc; border: 1px solid #e2e8f0;
        border-top: 3px solid #1d4ed8; padding: 9px 11px; vertical-align: top;
    }
    .kpi-td.g { border-top-color: #059669; }
    .kpi-td.r { border-top-color: #dc2626; }
    .kpi-td.p { border-top-color: #7c3aed; }
    .kpi-lbl  { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; }
    .kpi-val  { font-size: 15px; font-weight: bold; color: #1e293b; margin: 4px 0 2px; }
    .kpi-sub  { font-size: 8.5px; color: #94a3b8; }

    /* ── Barra de cobranza ── */
    .cw-box   { background: #f8fafc; border: 1px solid #e2e8f0; padding: 9px 12px; margin-bottom: 14px; }
    .cw-head  { font-size: 10px; font-weight: bold; color: #334155; margin-bottom: 6px; }
    .cw-track { width: 100%; background: #e2e8f0; height: 10px; }
    .cw-fill  { height: 10px; background: #059669; }
    .cw-row   { width: 100%; margin-top: 5px; }
    .cw-pct   { font-size: 13px; font-weight: bold; color: #059669; float: right; margin-top: -18px; }
    .cw-note  { font-size: 8.5px; color: #475569; margin-top: 5px; }

    /* ── Título de sección ── */
    .sec-title {
        font-size: 10.5px; font-weight: bold; color: #fff;
        background: #1d4ed8; padding: 5px 10px;
        margin-bottom: 0; border-bottom: none;
    }

    /* ── Tablas de datos ── */
    .data-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 9.5px; }
    .data-table th {
        background: #f1f5f9; border: 1px solid #e2e8f0; padding: 5px 7px;
        text-align: left; font-size: 8.5px; font-weight: bold; color: #475569;
        text-transform: uppercase; letter-spacing: 0.3px;
    }
    .data-table td { border: 1px solid #e2e8f0; padding: 5px 7px; color: #1e293b; }
    .data-table tr:nth-child(even) td { background: #f8fafc; }
    .tr  { text-align: right; }
    .tc  { text-align: center; }

    /* ── Badges ── */
    .badge {
        display: inline; padding: 1px 6px; font-size: 8.5px; font-weight: bold;
        border: 1px solid #ccc;
    }
    .b-pendiente { background: #fef9c3; color: #854d0e; border-color: #fde68a; }
    .b-parcial   { background: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
    .b-pagado    { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
    .b-alta  { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
    .b-media { background: #fef9c3; color: #854d0e; border-color: #fde68a; }
    .b-baja  { background: #dcfce7; color: #166534; border-color: #bbf7d0; }

    /* ── Dos columnas ── */
    .two-col  { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 14px; }
    .col-l, .col-r { width: 50%; vertical-align: top; }

    /* ── Footer ── */
    .pdf-footer {
        border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 6px;
        font-size: 8.5px; color: #94a3b8; text-align: center;
    }
</style>
</head>
<body>

{{-- ── Encabezado ──────────────────────────────────────────────────────── --}}
@php
    $totalSubtotal = $stats->total_subtotal > 0 ? $stats->total_subtotal : 1;
    $barPagado     = min(100, round($stats->total_pagado / $totalSubtotal * 100, 1));
    $periodoTexto  = ($desde || $hasta)
        ? (($desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : 'Inicio') . ' al ' . ($hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : 'Hoy'))
        : 'Todos los registros';
@endphp

<table width="100%" style="margin-bottom:14px; border-bottom:2px solid #1d4ed8; padding-bottom:10px;" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header-title">Reporte General de Honorarios</div>
            <div class="header-meta">Clinica Veterinaria &nbsp;&middot;&nbsp; Generado el {{ date('d/m/Y') }} a las {{ date('H:i') }}</div>
        </td>
        <td width="160" style="text-align:right; vertical-align:top;">
            <div class="header-periodo">{{ $periodoTexto }}</div>
        </td>
    </tr>
</table>

{{-- ── KPIs ─────────────────────────────────────────────────────────────── --}}
<table class="kpi-table">
    <tr>
        <td class="kpi-td">
            <div class="kpi-lbl">Total Honorarios</div>
            <div class="kpi-val">{{ number_format($stats->total_honorarios) }}</div>
            <div class="kpi-sub">{{ $stats->total_pendiente_count }} pend. &middot; {{ $stats->total_parcial_count }} parc. &middot; {{ $stats->total_pagado_count }} pag.</div>
        </td>
        <td class="kpi-td p">
            <div class="kpi-lbl">Monto Facturado</div>
            <div class="kpi-val">${{ number_format($stats->total_subtotal, 2) }}</div>
            <div class="kpi-sub">Suma total de subtotales</div>
        </td>
        <td class="kpi-td g">
            <div class="kpi-lbl">Total Cobrado</div>
            <div class="kpi-val">${{ number_format($stats->total_pagado, 2) }}</div>
            <div class="kpi-sub">{{ $stats->porcentaje_cobranza }}% de cobranza</div>
        </td>
        <td class="kpi-td r">
            <div class="kpi-lbl">Saldo por Cobrar</div>
            <div class="kpi-val">${{ number_format($stats->total_pendiente, 2) }}</div>
            <div class="kpi-sub">{{ 100 - $stats->porcentaje_cobranza }}% pendiente</div>
        </td>
    </tr>
</table>

{{-- ── Barra de cobranza ────────────────────────────────────────────────── --}}
<div class="cw-box">
    <div class="cw-head">Progreso de cobranza general</div>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:4px;">
        <tr>
            <td width="{{ $barPagado }}%" style="background:#059669; height:10px;"></td>
            <td style="background:#e2e8f0; height:10px;"></td>
        </tr>
    </table>
    <div class="cw-note">
        Cobrado: ${{ number_format($stats->total_pagado,2) }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Pendiente: ${{ number_format($stats->total_pendiente,2) }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        {{ $stats->total_honorarios }} honorarios &nbsp;&nbsp;
        <strong style="color:#059669;">{{ $stats->porcentaje_cobranza }}% cobrado</strong>
    </div>
</div>

{{-- ── Desglose por Estado ──────────────────────────────────────────────── --}}
<div class="sec-title">DESGLOSE POR ESTADO</div>
<table class="data-table">
    <thead>
        <tr>
            <th>Estado</th>
            <th class="tc">Cantidad</th>
            <th class="tr">Facturado</th>
            <th class="tr">Cobrado</th>
            <th class="tr">Pendiente</th>
        </tr>
    </thead>
    <tbody>
        @forelse($porEstado as $fila)
        <tr>
            <td><span class="badge b-{{ strtolower($fila->estado) }}">{{ $fila->estado }}</span></td>
            <td class="tc">{{ $fila->cantidad }}</td>
            <td class="tr">${{ number_format($fila->total_subtotal,2) }}</td>
            <td class="tr" style="color:#059669; font-weight:bold;">${{ number_format($fila->total_pagado,2) }}</td>
            <td class="tr" style="color:#dc2626; font-weight:bold;">${{ number_format($fila->total_pendiente,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="tc" style="color:#94a3b8; padding:10px;">Sin datos</td></tr>
        @endforelse
    </tbody>
</table>

{{-- ── Propietarios con mayor saldo pendiente ─────────────────────────── --}}
<div class="sec-title">PROPIETARIOS CON MAYOR SALDO PENDIENTE</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="20">#</th>
            <th>Propietario</th>
            <th>Telefono</th>
            <th class="tc">Honorarios</th>
            <th class="tr">Saldo Pendiente</th>
            <th class="tc">Prioridad</th>
        </tr>
    </thead>
    <tbody>
        @forelse($topDeudores as $i => $d)
        @php
            $prioridad = $d->total_pendiente >= 1000 ? 'Alta' : ($d->total_pendiente >= 300 ? 'Media' : 'Baja');
        @endphp
        <tr>
            <td style="color:#94a3b8;">{{ $i+1 }}</td>
            <td style="font-weight:bold;">{{ $d->propietario }}</td>
            <td style="color:#64748b;">{{ $d->telefono ?? '—' }}</td>
            <td class="tc">{{ $d->num_honorarios }}</td>
            <td class="tr" style="color:#dc2626; font-weight:bold;">${{ number_format($d->total_pendiente,2) }}</td>
            <td class="tc"><span class="badge b-{{ strtolower($prioridad) }}">{{ $prioridad }}</span></td>
        </tr>
        @empty
        <tr><td colspan="6" class="tc" style="color:#94a3b8; padding:10px;">Sin saldos pendientes</td></tr>
        @endforelse
    </tbody>
</table>

{{-- ── Dos columnas: antiguedad + conceptos ───────────────────────────── --}}
<table class="two-col">
    <tr>
        {{-- Antigüedad de deuda --}}
        <td class="col-l">
            <div class="sec-title">DEUDA MAS ANTIGUA SIN LIQUIDAR</div>
            <table class="data-table" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th>Mascota / Propietario</th>
                        <th class="tc">Dias</th>
                        <th class="tr">Pendiente</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masAntiguos as $h)
                    @php
                        $dias = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($h->fecha_ingreso)->startOfDay());
                        $diasColor = $dias > 60 ? '#dc2626' : ($dias > 30 ? '#d97706' : '#059669');
                    @endphp
                    <tr>
                        <td style="color:#94a3b8;">#{{ $h->id_honorario }}</td>
                        <td>
                            <strong>{{ $h->mascota_nombre }}</strong><br>
                            <span style="color:#64748b; font-size:8.5px;">{{ $h->propietario_nombre }}</span>
                        </td>
                        <td class="tc" style="color:{{ $diasColor }}; font-weight:bold;">{{ $dias }}d</td>
                        <td class="tr" style="color:#dc2626; font-weight:bold;">${{ number_format($h->saldo_pendiente,2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="tc" style="color:#94a3b8; padding:8px;">Sin deuda pendiente</td></tr>
                    @endforelse
                </tbody>
            </table>
        </td>

        {{-- Servicios más facturados --}}
        <td class="col-r">
            <div class="sec-title">SERVICIOS MAS FACTURADOS</div>
            <table class="data-table" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th width="20">#</th>
                        <th>Concepto</th>
                        <th class="tc">Veces</th>
                        <th class="tr">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topConceptos as $i => $c)
                    <tr>
                        <td style="color:#94a3b8; font-weight:bold;">{{ $i+1 }}</td>
                        <td>{{ $c->concepto }}</td>
                        <td class="tc">{{ $c->veces }}</td>
                        <td class="tr" style="font-weight:bold;">${{ number_format($c->total_facturado,2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="tc" style="color:#94a3b8; padding:8px;">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        </td>
    </tr>
</table>

{{-- ── Ultimos pagos recibidos ─────────────────────────────────────────── --}}
<div class="sec-title">ULTIMOS PAGOS RECIBIDOS</div>
<table class="data-table">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Mascota</th>
            <th>Propietario</th>
            <th class="tc">Metodo</th>
            <th class="tr">Monto</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pagosRecientes as $p)
        <tr>
            <td style="color:#64748b; white-space:nowrap;">{{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y H:i') }}</td>
            <td>{{ $p->mascota_nombre }}</td>
            <td style="color:#64748b;">{{ $p->propietario_nombre }}</td>
            <td class="tc">{{ ucfirst($p->metodo_pago ?? '—') }}</td>
            <td class="tr" style="color:#059669; font-weight:bold;">${{ number_format($p->monto,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="tc" style="color:#94a3b8; padding:10px;">Sin pagos registrados</td></tr>
        @endforelse
    </tbody>
</table>

<div class="pdf-footer">
    Reporte generado automaticamente el {{ date('d/m/Y') }} a las {{ date('H:i') }}
    &nbsp;&middot;&nbsp; Clinica Veterinaria — Sistema de Gestion de Honorarios
</div>

</body>
</html>
