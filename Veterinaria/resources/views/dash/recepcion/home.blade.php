@extends('dash.recepcion')

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
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simular carga de datos
    setTimeout(() => {
        document.getElementById('citas-hoy').textContent = '8';
        document.getElementById('pacientes-nuevos').textContent = '12';
        document.getElementById('consultas-pendientes').textContent = '3';
        document.getElementById('hospitalizaciones').textContent = '2';
        
        // Cargar próximas citas
        const citasContainer = document.getElementById('proximas-citas');
        citasContainer.innerHTML = `
            <div class="cita-item">
                <div class="cita-time">09:00 AM</div>
                <div class="cita-info">
                    <div class="cita-paciente">Max - Golden Retriever</div>
                    <div class="cita-propietario">María Rodríguez</div>
                </div>
                <span class="status-badge status-confirmed">Confirmada</span>
            </div>
            <div class="cita-item">
                <div class="cita-time">10:30 AM</div>
                <div class="cita-info">
                    <div class="cita-paciente">Luna - Siames</div>
                    <div class="cita-propietario">Carlos Pérez</div>
                </div>
                <span class="status-badge status-pending">Pendiente</span>
            </div>
        `;
        
        // Cargar pacientes en espera
        const esperaContainer = document.getElementById('pacientes-espera');
        esperaContainer.innerHTML = `
            <div class="cita-item">
                <div class="cita-time">Llegó hace 5 min</div>
                <div class="cita-info">
                    <div class="cita-paciente">Rocky - Bulldog</div>
                    <div class="cita-propietario">Luis Martínez</div>
                </div>
                <span class="status-badge status-pending">En espera</span>
            </div>
        `;
    }, 1000);
});
</script>
@endsection
