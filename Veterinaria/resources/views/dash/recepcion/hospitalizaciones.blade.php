@extends('dash.recepcion')
@section('page-title', 'Hospitalizaciones')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/hospitalizaciones.css') }}">
@endpush
@section('content')
<section id="mod-hospitalizaciones" class="module active">
  <div class="module-header">
    <h2 class="module-title">Gestión de Hospitalizaciones</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nueva-hospitalizacion">Nueva Hospitalización</button>
      <button class="btn-secondary">Reporte Diario</button>
    </div>
  </div>
  
  <div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Pacientes Hospitalizados</h3>
        <div class="stat-icon icon-red">🏥</div>
      </div>
      <div class="stat-value">3</div>
      <div class="stat-change">Activos</div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Altas del Mes</h3>
        <div class="stat-icon icon-green">✅</div>
      </div>
      <div class="stat-value">12</div>
      <div class="stat-change">+2 esta semana</div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Camas Disponibles</h3>
        <div class="stat-icon icon-blue">🛏️</div>
      </div>
      <div class="stat-value">7/10</div>
      <div class="stat-change">70% ocupación</div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Promedio Estancia</h3>
        <div class="stat-icon icon-purple">📅</div>
      </div>
      <div class="stat-value">3.2 días</div>
      <div class="stat-change">-0.5 vs mes anterior</div>
    </div>
  </div>
  
  <div class="filters-bar">
    <div class="search-filter">
      <input type="text" placeholder="Buscar hospitalización..." class="search-input" id="search-hospitalizaciones">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-estado-hospitalizacion">
        <option value="">Todos los estados</option>
        <option value="activa">Activa</option>
        <option value="alta">Con alta</option>
        <option value="observacion">En observación</option>
      </select>
      <select class="filter-select" id="filter-area-hospitalizacion">
        <option value="">Todas las áreas</option>
        <option value="uci">UCI</option>
        <option value="general">Área General</option>
        <option value="aislamiento">Aislamiento</option>
      </select>
    </div>
  </div>
  
  <div class="hospitalizaciones-grid">
    <div class="hospitalizacion-card">
      <div class="hospitalizacion-header">
        <div class="pet-avatar-large">🐕</div>
        <div class="hospitalizacion-info">
          <h3>Rocky</h3>
          <p>Bulldog Francés - 5 años</p>
          <span class="chip">Luis Martínez</span>
        </div>
        <span class="status-badge status-active">Activo</span>
      </div>
      <div class="hospitalizacion-details">
        <div class="detail-item">
          <span class="detail-label">Ingreso:</span>
          <span class="detail-value">14 Nov 2023, 14:30</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Médico:</span>
          <span class="detail-value">Dra. Laura Méndez</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Diagnóstico:</span>
          <span class="detail-value">Gastroenteritis aguda</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Cama:</span>
          <span class="detail-value">UCI-02</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Próximo control:</span>
          <span class="detail-value">Hoy, 16:00</span>
        </div>
      </div>
      <div class="hospitalizacion-actions">
        <button class="btn-outline" onclick="verHistorialHospitalizacion(1)">Historial</button>
        <button class="btn-secondary" onclick="darAlta(1)">Dar Alta</button>
        <button class="btn-primary" onclick="actualizarEstado(1)">Actualizar</button>
      </div>
    </div>
    
    <div class="hospitalizacion-card">
      <div class="hospitalizacion-header">
        <div class="pet-avatar-large">🐈</div>
        <div class="hospitalizacion-info">
          <h3>Mimi</h3>
          <p>Gato Persa - 3 años</p>
          <span class="chip">Sofía Hernández</span>
        </div>
        <span class="status-badge status-pending">Observación</span>
      </div>
      <div class="hospitalizacion-details">
        <div class="detail-item">
          <span class="detail-label">Ingreso:</span>
          <span class="detail-value">15 Nov 2023, 09:15</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Médico:</span>
          <span class="detail-value">Dr. Roberto García</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Diagnóstico:</span>
          <span class="detail-value">Intoxicación alimentaria</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Cama:</span>
          <span class="detail-value">GEN-05</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Próximo control:</span>
          <span class="detail-value">Hoy, 18:00</span>
        </div>
      </div>
      <div class="hospitalizacion-actions">
        <button class="btn-outline" onclick="verHistorialHospitalizacion(2)">Historial</button>
        <button class="btn-secondary" onclick="darAlta(2)">Dar Alta</button>
        <button class="btn-primary" onclick="actualizarEstado(2)">Actualizar</button>
      </div>
    </div>

    <div class="hospitalizacion-card">
      <div class="hospitalizacion-header">
        <div class="pet-avatar-large">🐕</div>
        <div class="hospitalizacion-info">
          <h3>Thor</h3>
          <p>Pastor Alemán - 2 años</p>
          <span class="chip">Javier López</span>
        </div>
        <span class="status-badge status-active">Activo</span>
      </div>
      <div class="hospitalizacion-details">
        <div class="detail-item">
          <span class="detail-label">Ingreso:</span>
          <span class="detail-value">13 Nov 2023, 16:45</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Médico:</span>
          <span class="detail-value">Dra. Laura Méndez</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Diagnóstico:</span>
          <span class="detail-value">Fractura pata posterior</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Cama:</span>
          <span class="detail-value">GEN-03</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Próximo control:</span>
          <span class="detail-value">Mañana, 10:00</span>
        </div>
      </div>
      <div class="hospitalizacion-actions">
        <button class="btn-outline" onclick="verHistorialHospitalizacion(3)">Historial</button>
        <button class="btn-secondary" onclick="darAlta(3)">Dar Alta</button>
        <button class="btn-primary" onclick="actualizarEstado(3)">Actualizar</button>
      </div>
    </div>
  </div>

  <!-- Modal para nueva hospitalización -->
  <div id="modal-hospitalizacion" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 700px;">
      <div class="modal-header">
        <h3>Nueva Hospitalización</h3>
        <button class="modal-close" onclick="cerrarModalHospitalizacion()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-hospitalizacion">
          @csrf
          <div class="form-row">
            <div class="form-group">
              <label for="hospitalizacion-paciente">Paciente *</label>
              <select id="hospitalizacion-paciente" name="paciente_id" class="form-control" required>
                <option value="">Seleccionar paciente</option>
                <option value="1">Max - María Rodríguez</option>
                <option value="2">Luna - Carlos Pérez</option>
                <option value="3">Toby - Ana González</option>
              </select>
            </div>
            <div class="form-group">
              <label for="hospitalizacion-medico">Médico Responsable *</label>
              <select id="hospitalizacion-medico" name="medico_id" class="form-control" required>
                <option value="">Seleccionar médico</option>
                <option value="1">Dra. Laura Méndez</option>
                <option value="2">Dr. Roberto García</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="hospitalizacion-diagnostico">Diagnóstico *</label>
              <input type="text" id="hospitalizacion-diagnostico" name="diagnostico" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="hospitalizacion-area">Área *</label>
              <select id="hospitalizacion-area" name="area" class="form-control" required>
                <option value="">Seleccionar área</option>
                <option value="uci">UCI</option>
                <option value="general">Área General</option>
                <option value="aislamiento">Aislamiento</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="hospitalizacion-cama">Número de Cama *</label>
              <input type="text" id="hospitalizacion-cama" name="cama" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="hospitalizacion-estado">Estado *</label>
              <select id="hospitalizacion-estado" name="estado" class="form-control" required>
                <option value="activa">Activa</option>
                <option value="observacion">En observación</option>
                <option value="critico">Crítico</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="hospitalizacion-observaciones">Observaciones Iniciales</label>
            <textarea id="hospitalizacion-observaciones" name="observaciones" class="form-control" rows="4"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalHospitalizacion()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="guardarHospitalizacion()">Registrar Hospitalización</button>
      </div>
    </div>
  </div>
</section>
@endsection