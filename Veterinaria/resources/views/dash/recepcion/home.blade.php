@extends('dash.recepcion')

@push('styles')
<style>
  .dashboard-panel-actions {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .dashboard-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 9px 14px;
    border-radius: 999px;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    border: 1px solid transparent;
    white-space: nowrap;
  }

  .dashboard-action-btn:hover {
    transform: translateY(-1px);
  }

  .dashboard-action-btn-primary {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
  }

  .dashboard-action-btn-secondary {
    background: #ffffff;
    color: #0f172a;
    border-color: #cbd5e1;
  }

  .panel-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 14px;
    box-shadow: 0 8px 20px rgba(15,23,42,0.05);
    border: 1px solid rgba(2,6,23,0.04);
    margin-top: 18px;
  }

  .panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .panel-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
  }

  /* TABLE FLUJO CLÍNICO */
  .flujo-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
  }

  .flujo-table thead {
    background: #f1f5f9;
  }

  .flujo-table th {
    text-align: left;
    padding: 12px;
    font-weight: 700;
    border-bottom: 1px solid #e2e8f0;
  }

  .flujo-table td {
    padding: 12px;
    border-bottom: 1px solid #f1f5f9;
  }

  .flujo-row:hover {
    background: #f8fafc;
  }

  .status-badge {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
  }

  .status-completed  { background:#d1fae5; color:#065f46; }
  .status-cancelled  { background:#fee2e2; color:#991b1b; }
  .status-pending    { background:#fff7ed; color:#92400e; }

  .empty-state {
    text-align: center;
    color: #94a3b8;
    padding: 14px;
  }
</style>
@endpush

@section('content')

<section class="module active">

  <div class="welcome-section">
    <h1 class="welcome-title">
      Bienvenido/a, {{ Auth::user()->nombre_usuario }}
    </h1>
    <p class="welcome-subtitle">
      Panel de recepción - Flujo clínico del día
    </p>
  </div>

  {{-- STATS --}}
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Citas de Hoy</h3>
        <div class="stat-icon">📅</div>
      </div>
      <div class="stat-value" id="citas-hoy">—</div>
    </div>

    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Pacientes Nuevos</h3>
        <div class="stat-icon">🐾</div>
      </div>
      <div class="stat-value" id="pacientes-nuevos">—</div>
    </div>

    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Consultas Pendientes</h3>
        <div class="stat-icon">🩺</div>
      </div>
      <div class="stat-value" id="consultas-pendientes">—</div>
    </div>

    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Hospitalizaciones</h3>
        <div class="stat-icon">🏥</div>
      </div>
      <div class="stat-value" id="hospitalizaciones">—</div>
    </div>
  </div>

  {{-- FLUJO CLÍNICO UNIFICADO --}}
  <div class="panel-card">

    <div class="panel-header">
      <h3 class="panel-title">Flujo clínico del día</h3>

      <div class="dashboard-panel-actions">
        <a href="{{ route('citas.index') }}"
           class="dashboard-action-btn dashboard-action-btn-primary">
          Ver Agenda
        </a>
      </div>
    </div>

    <div style="overflow-x:auto;">
      <table class="flujo-table">
        <thead>
          <tr>
            <th>Hora</th>
            <th>Paciente</th>
            <th>Propietario</th>
            <th>Profesional</th>
            <th>Estado</th>
          </tr>
        </thead>

        <tbody id="flujo-diario">
          <tr>
            <td colspan="5" class="empty-state">
              Cargando flujo del día...
            </td>
          </tr>
        </tbody>

      </table>
    </div>

  </div>

</section>

<script>
async function cargarDashboard() {
  try {
    const resp = await fetch('/api/dashboard/recepcion?_=' + Date.now(), {
      headers: {
        'Accept': 'application/json',
        'Cache-Control': 'no-cache, no-store',
        'Pragma': 'no-cache'
      },
      cache: 'no-store'
    });

    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);

    const data = await resp.json();

    // STATS
    document.getElementById('citas-hoy').textContent = data.citas_hoy ?? 0;
    document.getElementById('pacientes-nuevos').textContent = data.pacientes_nuevos ?? 0;
    document.getElementById('consultas-pendientes').textContent = data.consultas_pendientes ?? 0;
    document.getElementById('hospitalizaciones').textContent = data.hospitalizaciones ?? 0;

    // FLUJO CLÍNICO
    const tbody = document.getElementById('flujo-diario');

    if (Array.isArray(data.flujo_diario) && data.flujo_diario.length) {

      const estadoClase = e => {
        const v = (e || '').toLowerCase();
        if (v === 'completada') return 'status-completed';
        if (v === 'cancelada')  return 'status-cancelled';
        return 'status-pending';
      };

      const estadoLabel = e => {
        const v = (e || '').toLowerCase();
        if (v === 'completada') return 'Completada';
        if (v === 'cancelada')  return 'Cancelada';
        return 'En espera';
      };

      tbody.innerHTML = data.flujo_diario.map(item => `
        <tr class="flujo-row">
          <td>${item.hora || '-'}</td>
          <td><strong>${item.paciente || '-'}</strong></td>
          <td>${item.propietario || '-'}</td>
          <td>${item.profesional || '-'}</td>
          <td>
            <span class="status-badge ${estadoClase(item.estado)}">
              ${estadoLabel(item.estado)}
            </span>
          </td>
        </tr>
      `).join('');

    } else {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" class="empty-state">
            No hay actividad registrada hoy
          </td>
        </tr>
      `;
    }

  } catch (e) {
    console.error('Error dashboard:', e);
  }
}

document.addEventListener('DOMContentLoaded', function () {
  cargarDashboard();
  setInterval(cargarDashboard, 10000);
});
</script>

@endsection