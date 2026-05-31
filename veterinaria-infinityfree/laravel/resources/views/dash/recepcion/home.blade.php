@extends('dash.recepcion')

@push('styles')
<style>
  .flujo-table { width: 100%; border-collapse: collapse; }
  .flujo-table th, .flujo-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
  .flujo-table thead { background: #f8fafc; }
  .tipo-cita { color: #2563eb; font-weight: 600; }
  .tipo-espera { color: #b45309; font-weight: 600; }
</style>
@endpush

@section('content')
<section class="module active">
  <div class="welcome-section">
    <h1 class="welcome-title">Bienvenido/a, {{ Auth::user()->nombre_usuario }}</h1>
    <p class="welcome-subtitle">Panel de control de recepción - Resumen de actividades del día</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Citas de Hoy</h3>
        <div class="stat-icon icon-blue">📅</div>
      </div>
      <div class="stat-value" id="citas-hoy">0</div>
      <div class="stat-change">+2 desde ayer</div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Pacientes Nuevos</h3>
        <div class="stat-icon icon-green">🐾</div>
      </div>
      <div class="stat-value" id="pacientes-nuevos">0</div>
      <div class="stat-change">Este mes</div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Consultas Pendientes</h3>
        <div class="stat-icon icon-orange">🩺</div>
      </div>
      <div class="stat-value" id="consultas-pendientes">0</div>
      <div class="stat-change">En espera</div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Hospitalizaciones</h3>
        <div class="stat-icon icon-red">🏥</div>
      </div>
      <div class="stat-value" id="hospitalizaciones">0</div>
      <div class="stat-change">Activas</div>
    </div>
  </div>

  <div class="panels-grid">
    <div class="panel-card">
      <div class="panel-header">
        <h3 class="panel-title">Próximas Citas</h3>
        <a href="{{ route('citas.index') }}" class="btn-primary">Ver Todas</a>
      </div>
      <div class="citas-list" id="proximas-citas">
        <div class="empty-state">
          <p>No hay citas próximas</p>
        </div>
      </div>
    </div>
    
    <div class="panel-card">
      <div class="panel-header">
        <h3 class="panel-title">Pacientes en Espera</h3>
        <a href="{{ route('recepcion.expedientes') }}" class="btn-secondary">Ver Expedientes</a>
      </div>
      <div class="espera-list" id="pacientes-espera">
        <div class="empty-state">
          <p>No hay pacientes en espera</p>
        </div>
      </div>
    </div>
    
    <div class="panel-card">
      <div class="panel-header">
        <h3 class="panel-title">Recordatorios</h3>
        <button class="btn-outline">Agregar</button>
      </div>
      <div class="recordatorios-list">
        <div class="recordatorio-item">
          <div class="recordatorio-icon">💊</div>
          <div class="recordatorio-content">
            <div class="recordatorio-title">Recordar medicación a Max</div>
            <div class="recordatorio-time">Hoy, 14:00</div>
          </div>
        </div>
        <div class="recordatorio-item">
          <div class="recordatorio-icon">📞</div>
          <div class="recordatorio-content">
            <div class="recordatorio-title">Llamar a María Rodríguez</div>
            <div class="recordatorio-time">Hoy, 16:30</div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel-card">
      <div class="panel-header">
        <h3 class="panel-title">Flujo Clínico del Día</h3>
      </div>
      <div style="overflow-x:auto;">
        <table class="flujo-table">
          <thead>
            <tr>
              <th>Hora</th>
              <th>Paciente</th>
              <th>Propietario</th>
              <th>Tipo</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody id="flujo-diario">
            <tr><td colspan="5" class="empty-state"><p>Cargando flujo del día...</p></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script>
async function cargarDashboard() {
  try {
    const resp = await fetch('/api/dashboard/recepcion', { headers: { 'Accept': 'application/json' } });
    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
    const data = await resp.json();

    // Stats
    document.getElementById('citas-hoy').textContent = data.citas_hoy ?? 0;
    document.getElementById('pacientes-nuevos').textContent = data.pacientes_nuevos ?? 0;
    document.getElementById('consultas-pendientes').textContent = data.consultas_pendientes ?? 0;
    document.getElementById('hospitalizaciones').textContent = data.hospitalizaciones ?? 0;

    // Próximas citas
    const citasContainer = document.getElementById('proximas-citas');
    if (Array.isArray(data.proximas_citas) && data.proximas_citas.length) {
      citasContainer.innerHTML = data.proximas_citas.map(c => `
        <div class="cita-item">
          <div class="cita-time">${(c.hora || '').slice(0,5)}</div>
          <div class="cita-info">
            <div class="cita-paciente">${c.paciente || '-'}</div>
            <div class="cita-propietario">${c.propietario || '-'}</div>
          </div>
          <span class="status-badge ${c.estado === 'Completada' ? 'status-completed' : (c.estado === 'Cancelada' ? 'status-expired' : 'status-pending')}">${c.estado || ''}</span>
        </div>
      `).join('');
    } else {
      citasContainer.innerHTML = `<div class="empty-state"><p>No hay citas próximas</p></div>`;
    }

    // Pacientes en espera
    const esperaContainer = document.getElementById('pacientes-espera');
    if (Array.isArray(data.pacientes_espera) && data.pacientes_espera.length) {
      esperaContainer.innerHTML = data.pacientes_espera.map(e => `
        <div class="cita-item">
          <div class="cita-time">${(e.llego || '').slice(0,5)}</div>
          <div class="cita-info">
            <div class="cita-paciente">${e.paciente || '-'}</div>
            <div class="cita-propietario">${e.propietario || '-'}</div>
          </div>
          <span class="status-badge status-pending">${e.estado || 'En espera'}</span>
        </div>
      `).join('');
    } else {
      esperaContainer.innerHTML = `<div class="empty-state"><p>No hay pacientes en espera</p></div>`;
    }

    // Flujo clínico del día
    const flujoContainer = document.getElementById('flujo-diario');
    if (flujoContainer) {
      if (Array.isArray(data.flujo_diario) && data.flujo_diario.length) {
        flujoContainer.innerHTML = data.flujo_diario.map(item => `
          <tr>
            <td>${(item.hora || '').slice(0,5) || '-'}</td>
            <td>${item.paciente || '-'}</td>
            <td>${item.propietario || '-'}</td>
            <td class="${item.tipo === 'ESPERA' ? 'tipo-espera' : 'tipo-cita'}">${item.tipo || '-'}</td>
            <td>${item.estado || '-'}</td>
          </tr>
        `).join('');
      } else {
        flujoContainer.innerHTML = `<tr><td colspan="5" class="empty-state"><p>No hay actividad registrada hoy</p></td></tr>`;
      }
    }
  } catch (e) {
    console.error('Error cargando dashboard:', e);
  }
}

document.addEventListener('DOMContentLoaded', function() {
  cargarDashboard();
  setInterval(cargarDashboard, 10000);
});
</script>
@endsection
