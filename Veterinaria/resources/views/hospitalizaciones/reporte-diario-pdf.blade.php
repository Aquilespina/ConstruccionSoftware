<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte Diario Hospitalizaciones</title>
<style>
    * { margin:0; padding:0; }
    body { font-family: Arial, sans-serif; font-size:10.5px; color:#1e293b; padding:20px; }

    /* Encabezado */
    .hdr-table { width:100%; border-bottom:2px solid #1d4ed8; margin-bottom:14px; padding-bottom:10px; }
    .hdr-title  { font-size:16px; font-weight:bold; color:#1d4ed8; }
    .hdr-fecha  { font-size:9px; color:#64748b; margin-top:3px; }
    .hdr-gen    { font-size:9px; color:#94a3b8; text-align:right; }

    /* KPIs */
    .kpi-tbl { width:100%; border-collapse:separate; border-spacing:6px 0; margin-bottom:14px; }
    .kpi-td {
        width:33%; background:#f8fafc; border:1px solid #e2e8f0;
        padding:8px 12px; vertical-align:top;
    }
    .kpi-td.r { border-top:3px solid #ef4444; }
    .kpi-td.o { border-top:3px solid #f59e0b; }
    .kpi-td.g { border-top:3px solid #10b981; }
    .kpi-lbl { font-size:8px; color:#64748b; text-transform:uppercase; letter-spacing:.4px; }
    .kpi-num { font-size:20px; font-weight:bold; color:#1e293b; margin:3px 0 1px; }
    .kpi-sub { font-size:8.5px; color:#94a3b8; }

    /* Sección */
    .sec-title {
        font-size:10px; font-weight:bold; color:#fff;
        background:#1d4ed8; padding:5px 10px; margin-bottom:0;
    }

    /* Tablas */
    table.dt { width:100%; border-collapse:collapse; margin-bottom:14px; font-size:9.5px; }
    table.dt th {
        background:#f1f5f9; border:1px solid #e2e8f0;
        padding:5px 7px; text-align:left;
        font-size:8.5px; font-weight:bold; color:#475569;
        text-transform:uppercase; letter-spacing:.3px;
    }
    table.dt td { border:1px solid #e2e8f0; padding:5px 7px; }
    table.dt tr:nth-child(even) td { background:#f8fafc; }
    .tr { text-align:right; }
    .empty-row td { text-align:center; color:#94a3b8; padding:10px; }

    /* Badges */
    .badge { display:inline; padding:1px 6px; font-size:8.5px; font-weight:bold; border:1px solid; }
    .b-internado   { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }
    .b-tratamiento { background:#dbeafe; color:#1e40af; border-color:#bfdbfe; }
    .b-alta        { background:#dcfce7; color:#166534; border-color:#bbf7d0; }

    /* Dias color */
    .d-ok   { color:#059669; font-weight:bold; }
    .d-warn { color:#d97706; font-weight:bold; }
    .d-bad  { color:#dc2626; font-weight:bold; }

    /* Footer */
    .pdf-footer { border-top:1px solid #e2e8f0; padding-top:8px; margin-top:6px; font-size:8.5px; color:#94a3b8; text-align:center; }
</style>
</head>
<body>

{{-- Encabezado --}}
<table class="hdr-table" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="hdr-title">Reporte Diario de Hospitalizaciones</div>
            <div class="hdr-fecha">
                {{ \Carbon\Carbon::parse($hoy)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </div>
        </td>
        <td style="text-align:right; vertical-align:top;">
            <div class="hdr-gen">Generado: {{ date('d/m/Y H:i') }}</div>
            <div class="hdr-gen">Clinica Veterinaria</div>
        </td>
    </tr>
</table>

{{-- KPIs --}}
<table class="kpi-tbl">
    <tr>
        <td class="kpi-td r">
            <div class="kpi-lbl">Pacientes Activos</div>
            <div class="kpi-num">{{ $activos->count() }}</div>
            <div class="kpi-sub">Internados + Tratamiento</div>
        </td>
        <td class="kpi-td o">
            <div class="kpi-lbl">Ingresos Hoy</div>
            <div class="kpi-num">{{ $ingresosHoy->count() }}</div>
            <div class="kpi-sub">Nuevas hospitalizaciones</div>
        </td>
        <td class="kpi-td g">
            <div class="kpi-lbl">Altas Hoy</div>
            <div class="kpi-num">{{ $altasHoy->count() }}</div>
            <div class="kpi-sub">Pacientes dados de alta</div>
        </td>
    </tr>
</table>

{{-- Pacientes activos --}}
<div class="sec-title">PACIENTES INTERNADOS Y EN TRATAMIENTO</div>
<table class="dt">
    <thead>
        <tr>
            <th width="25">#</th>
            <th>Mascota / Especie</th>
            <th>Propietario</th>
            <th>Telefono</th>
            <th>Ingreso</th>
            <th class="tr">Dias</th>
            <th>Estado</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($activos as $p)
        @php $dc = $p->dias > 7 ? 'd-bad' : ($p->dias >= 3 ? 'd-warn' : 'd-ok'); @endphp
        <tr>
            <td style="color:#94a3b8;">#{{ $p->id_hospitalizacion }}</td>
            <td><strong>{{ $p->mascota }}</strong><br><span style="color:#94a3b8; font-size:8.5px;">{{ $p->especie }}{{ $p->raza ? ' · '.$p->raza : '' }}</span></td>
            <td>{{ $p->propietario }}</td>
            <td style="color:#64748b;">{{ $p->telefono ?? '—' }}</td>
            <td style="white-space:nowrap; color:#64748b;">{{ \Carbon\Carbon::parse($p->fecha_ingreso)->format('d/m/Y H:i') }}</td>
            <td class="tr {{ $dc }}">{{ $p->dias }}d</td>
            <td><span class="badge b-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
            <td style="font-size:8.5px; color:#475569;">{{ \Str::limit($p->observaciones, 60) }}</td>
        </tr>
        @empty
        <tr class="empty-row"><td colspan="8">Sin pacientes activos.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Ingresos hoy --}}
<div class="sec-title">INGRESOS DE HOY</div>
<table class="dt">
    <thead>
        <tr>
            <th width="25">#</th>
            <th>Mascota</th>
            <th>Propietario</th>
            <th>Telefono</th>
            <th>Hora Ingreso</th>
            <th>Estado</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ingresosHoy as $p)
        <tr>
            <td style="color:#94a3b8;">#{{ $p->id_hospitalizacion }}</td>
            <td><strong>{{ $p->mascota }}</strong><br><span style="color:#94a3b8; font-size:8.5px;">{{ $p->especie }}</span></td>
            <td>{{ $p->propietario }}</td>
            <td style="color:#64748b;">{{ $p->telefono ?? '—' }}</td>
            <td style="font-weight:bold; color:#d97706;">{{ \Carbon\Carbon::parse($p->fecha_ingreso)->format('H:i') }}</td>
            <td><span class="badge b-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
            <td style="font-size:8.5px; color:#475569;">{{ \Str::limit($p->observaciones, 60) }}</td>
        </tr>
        @empty
        <tr class="empty-row"><td colspan="7">Sin ingresos hoy.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Altas hoy --}}
<div class="sec-title">ALTAS DE HOY</div>
<table class="dt">
    <thead>
        <tr>
            <th width="25">#</th>
            <th>Mascota</th>
            <th>Propietario</th>
            <th>Ingreso</th>
            <th>Alta</th>
            <th class="tr">Dias internado</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($altasHoy as $p)
        <tr>
            <td style="color:#94a3b8;">#{{ $p->id_hospitalizacion }}</td>
            <td><strong>{{ $p->mascota }}</strong><br><span style="color:#94a3b8; font-size:8.5px;">{{ $p->especie }}</span></td>
            <td>{{ $p->propietario }}</td>
            <td style="color:#64748b; white-space:nowrap;">{{ \Carbon\Carbon::parse($p->fecha_ingreso)->format('d/m/Y') }}</td>
            <td style="font-weight:bold; color:#059669; white-space:nowrap;">{{ \Carbon\Carbon::parse($p->fecha_egreso)->format('H:i') }}</td>
            <td class="tr d-ok">{{ $p->dias_internado }}d</td>
            <td style="font-size:8.5px; color:#475569;">{{ \Str::limit($p->observaciones, 60) }}</td>
        </tr>
        @empty
        <tr class="empty-row"><td colspan="7">Sin altas hoy.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="pdf-footer">
    Reporte generado el {{ date('d/m/Y') }} a las {{ date('H:i') }} &nbsp;&middot;&nbsp; Clinica Veterinaria &mdash; Sistema de Gestion Hospitalaria
</div>
</body>
</html>
