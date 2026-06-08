@extends('dash.recepcion')
@section('page-title', 'Reporte Diario — Hospitalizaciones')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/recepcion/hospitalizaciones.css') }}">
<style>
    .rd-container { max-width: 1000px; margin: 0 auto; padding: 0 0 40px; }

    .rd-header { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:22px; }
    .rd-title   { font-size:20px; font-weight:700; color:#1e293b; margin:0; }
    .rd-fecha   { font-size:13px; color:#64748b; margin:4px 0 0; }
    .rd-actions { display:flex; gap:10px; flex-wrap:wrap; }

    /* KPIs */
    .rd-kpis { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:22px; }
    .rd-kpi  { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px 16px; border-top:3px solid #e2e8f0; }
    .rd-kpi.internado { border-top-color:#ef4444; }
    .rd-kpi.ingreso   { border-top-color:#f59e0b; }
    .rd-kpi.alta      { border-top-color:#10b981; }
    .rd-kpi-num  { font-size:26px; font-weight:700; color:#1e293b; line-height:1; margin:4px 0 2px; }
    .rd-kpi-lbl  { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:.4px; font-weight:600; }
    .rd-kpi-sub  { font-size:11px; color:#94a3b8; }

    /* Secciones */
    .rd-section { background:#fff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:20px; }
    .rd-section-hdr {
        background:#f8fafc; border-bottom:1px solid #e2e8f0;
        padding:12px 18px; font-size:13px; font-weight:600; color:#334155;
        display:flex; align-items:center; gap:8px;
    }
    .rd-section-hdr .hdr-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }

    /* Tabla */
    .rd-table { width:100%; border-collapse:collapse; font-size:13px; }
    .rd-table th {
        background:#f8fafc; border-bottom:1px solid #e2e8f0;
        padding:8px 14px; text-align:left;
        font-size:11px; font-weight:600; color:#64748b;
        text-transform:uppercase; letter-spacing:.4px;
        white-space:nowrap;
    }
    .rd-table td { padding:10px 14px; border-bottom:1px solid #f1f5f9; color:#1e293b; }
    .rd-table tr:last-child td { border-bottom:none; }
    .rd-table tr:hover td { background:#f8fafc; }
    .td-r { text-align:right; }
    .empty-rd { padding:24px; text-align:center; color:#94a3b8; font-size:13px; }

    /* Badges */
    .badge { display:inline-block; padding:2px 9px; border-radius:999px; font-size:11px; font-weight:600; }
    .badge-internado { background:#fee2e2; color:#991b1b; }
    .badge-tratamiento { background:#dbeafe; color:#1e40af; }
    .badge-alta { background:#dcfce7; color:#166534; }

    /* Dias badge */
    .dias-badge { display:inline-block; padding:2px 7px; border-radius:4px; font-size:11px; font-weight:600; }
    .dias-ok     { background:#dcfce7; color:#166534; }
    .dias-warn   { background:#fef9c3; color:#854d0e; }
    .dias-danger { background:#fee2e2; color:#991b1b; }

    @media(max-width:700px){ .rd-kpis{ grid-template-columns:1fr; } }
</style>
@endpush
@section('content')
<section class="module active">
<div class="rd-container">

    {{-- Encabezado --}}
    <div class="rd-header">
        <div>
            <h2 class="rd-title">Reporte Diario de Hospitalizaciones</h2>
            <p class="rd-fecha">{{ \Carbon\Carbon::parse($hoy)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        </div>
        <div class="rd-actions">
            <a href="{{ route('hospitalizaciones.index') }}" class="btn-secondary btn-small">&larr; Volver</a>
            <a href="{{ route('hospitalizaciones.reporte-diario-pdf') }}" target="_blank" class="btn-primary btn-small">
                Exportar PDF
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="rd-kpis">
        <div class="rd-kpi internado">
            <div class="rd-kpi-lbl">Pacientes Activos</div>
            <div class="rd-kpi-num">{{ $activos->count() }}</div>
            <div class="rd-kpi-sub">Internados + En tratamiento</div>
        </div>
        <div class="rd-kpi ingreso">
            <div class="rd-kpi-lbl">Ingresos Hoy</div>
            <div class="rd-kpi-num">{{ $ingresosHoy->count() }}</div>
            <div class="rd-kpi-sub">Nuevas hospitalizaciones</div>
        </div>
        <div class="rd-kpi alta">
            <div class="rd-kpi-lbl">Altas Hoy</div>
            <div class="rd-kpi-num">{{ $altasHoy->count() }}</div>
            <div class="rd-kpi-sub">Pacientes dados de alta</div>
        </div>
    </div>

    {{-- Pacientes activos --}}
    <div class="rd-section">
        <div class="rd-section-hdr">
            <span class="hdr-dot" style="background:#ef4444"></span>
            Pacientes Internados y En Tratamiento
            <span style="margin-left:auto; font-size:11px; color:#94a3b8; font-weight:400;">{{ $activos->count() }} paciente(s)</span>
        </div>
        @if($activos->count())
        <div style="overflow-x:auto;">
        <table class="rd-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mascota</th>
                    <th>Propietario</th>
                    <th>Tel.</th>
                    <th>Ingreso</th>
                    <th class="td-r">Días</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activos as $p)
                @php
                    $diasClase = $p->dias > 7 ? 'dias-danger' : ($p->dias >= 3 ? 'dias-warn' : 'dias-ok');
                @endphp
                <tr>
                    <td style="color:#94a3b8;">#{{ $p->id_hospitalizacion }}</td>
                    <td>
                        <strong>{{ $p->mascota }}</strong><br>
                        <small style="color:#94a3b8;">{{ $p->especie }}{{ $p->raza ? ' · '.$p->raza : '' }}</small>
                    </td>
                    <td>{{ $p->propietario }}</td>
                    <td style="color:#64748b; white-space:nowrap;">{{ $p->telefono ?? '—' }}</td>
                    <td style="white-space:nowrap; color:#64748b;">
                        {{ \Carbon\Carbon::parse($p->fecha_ingreso)->format('d/m/Y') }}<br>
                        <small>{{ \Carbon\Carbon::parse($p->fecha_ingreso)->format('H:i') }}</small>
                    </td>
                    <td class="td-r">
                        <span class="dias-badge {{ $diasClase }}">{{ $p->dias }}d</span>
                    </td>
                    <td><span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
                    <td style="max-width:220px; color:#475569; font-size:12px;">
                        {{ \Str::limit($p->observaciones, 80) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
            <div class="empty-rd">No hay pacientes activos en este momento.</div>
        @endif
    </div>

    {{-- Ingresos de hoy --}}
    <div class="rd-section">
        <div class="rd-section-hdr">
            <span class="hdr-dot" style="background:#f59e0b"></span>
            Ingresos de Hoy
            <span style="margin-left:auto; font-size:11px; color:#94a3b8; font-weight:400;">{{ $ingresosHoy->count() }} ingreso(s)</span>
        </div>
        @if($ingresosHoy->count())
        <div style="overflow-x:auto;">
        <table class="rd-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mascota</th>
                    <th>Propietario</th>
                    <th>Tel.</th>
                    <th>Hora Ingreso</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ingresosHoy as $p)
                <tr>
                    <td style="color:#94a3b8;">#{{ $p->id_hospitalizacion }}</td>
                    <td><strong>{{ $p->mascota }}</strong><br><small style="color:#94a3b8;">{{ $p->especie }}</small></td>
                    <td>{{ $p->propietario }}</td>
                    <td style="color:#64748b;">{{ $p->telefono ?? '—' }}</td>
                    <td style="font-weight:600; color:#f59e0b;">{{ \Carbon\Carbon::parse($p->fecha_ingreso)->format('H:i') }}</td>
                    <td><span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
                    <td style="max-width:220px; color:#475569; font-size:12px;">{{ \Str::limit($p->observaciones, 80) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
            <div class="empty-rd">Sin ingresos registrados hoy.</div>
        @endif
    </div>

    {{-- Altas de hoy --}}
    <div class="rd-section">
        <div class="rd-section-hdr">
            <span class="hdr-dot" style="background:#10b981"></span>
            Altas de Hoy
            <span style="margin-left:auto; font-size:11px; color:#94a3b8; font-weight:400;">{{ $altasHoy->count() }} alta(s)</span>
        </div>
        @if($altasHoy->count())
        <div style="overflow-x:auto;">
        <table class="rd-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mascota</th>
                    <th>Propietario</th>
                    <th>Tel.</th>
                    <th>Ingresó</th>
                    <th>Alta</th>
                    <th class="td-r">Días internado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($altasHoy as $p)
                <tr>
                    <td style="color:#94a3b8;">#{{ $p->id_hospitalizacion }}</td>
                    <td><strong>{{ $p->mascota }}</strong><br><small style="color:#94a3b8;">{{ $p->especie }}</small></td>
                    <td>{{ $p->propietario }}</td>
                    <td style="color:#64748b;">{{ $p->telefono ?? '—' }}</td>
                    <td style="color:#64748b; white-space:nowrap;">{{ \Carbon\Carbon::parse($p->fecha_ingreso)->format('d/m/Y') }}</td>
                    <td style="font-weight:600; color:#10b981; white-space:nowrap;">{{ \Carbon\Carbon::parse($p->fecha_egreso)->format('H:i') }}</td>
                    <td class="td-r"><span class="dias-badge dias-ok">{{ $p->dias_internado }}d</span></td>
                    <td style="max-width:200px; color:#475569; font-size:12px;">{{ \Str::limit($p->observaciones, 80) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
            <div class="empty-rd">Sin altas registradas hoy.</div>
        @endif
    </div>

</div>
</section>
@endsection
