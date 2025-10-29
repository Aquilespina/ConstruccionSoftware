@extends('dash.recepcion')
@section('page-title', 'Expedientes')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/expedientes.css') }}">
@endpush
@section('content')
<section id="mod-expedientes" class="module active">
  <div class="module-header">
    <h2 class="module-title">Expedientes Médicos</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nuevo-expediente">Nuevo Expediente</button>
      <button class="btn-secondary">Buscar</button>
    </div>
  </div>
  
  <div class="filters-bar">
    <div class="search-filter">
      <input type="text" placeholder="Buscar expediente..." class="search-input" id="search-expedientes">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-especie-expediente">
        <option value="">Todas las especies</option>
        <option value="perro">Perro</option>
        <option value="gato">Gato</option>
      </select>
      <select class="filter-select" id="filter-estado-expediente">
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>
    </div>
  </div>
  
  <div class="expedientes-grid">
    <div class="expediente-card">
      <div class="expediente-header">
        <div class="pet-avatar-large">🐕</div>
        <div class="expediente-info">
          <h3>Max</h3>
          <p>Golden Retriever - 4 años</p>
          <span class="chip">María Rodríguez</span>
        </div>
      </div>
      <div class="expediente-details">
        <div class="detail-item">
          <span class="detail-label">Última visita:</span>
          <span class="detail-value">15 Nov 2023</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Próxima cita:</span>
          <span class="detail-value">30 Nov 2023</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Vacunas:</span>
          <span class="detail-value status-active">Al día</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Enfermedades:</span>
          <span class="detail-value">Ninguna</span>
        </div>
      </div>
      <div class="expediente-actions">
        <button class="btn-outline" onclick="verHistorial(1)">Ver Historial</button>
        <button class="btn-primary" onclick="abrirExpediente(1)">Abrir Expediente</button>
      </div>
    </div>
    
    <div class="expediente-card">
      <div class="expediente-header">
        <div class="pet-avatar-large">🐈</div>
        <div class="expediente-info">
          <h3>Luna</h3>
          <p>Gato Siames - 2 años</p>
          <span class="chip">Carlos Pérez</span>
        </div>
      </div>
      <div class="expediente-details">
        <div class="detail-item">
          <span class="detail-label">Última visita:</span>
          <span class="detail-value">12 Nov 2023</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Próxima cita:</span>
          <span class="detail-value">-</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Vacunas:</span>
          <span class="detail-value status-inactive">Pendiente</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Enfermedades:</span>
          <span class="detail-value">Alergia alimentaria</span>
        </div>
      </div>
      <div class="expediente-actions">
        <button class="btn-outline" onclick="verHistorial(2)">Ver Historial</button>
        <button class="btn-primary" onclick="abrirExpediente(2)">Abrir Expediente</button>
      </div>
    </div>

    <div class="expediente-card">
      <div class="expediente-header">
        <div class="pet-avatar-large">🐕</div>
        <div class="expediente-info">
          <h3>Toby</h3>
          <p>Border Collie - 3 años</p>
          <span class="chip">Ana González</span>
        </div>
      </div>
      <div class="expediente-details">
        <div class="detail-item">
          <span class="detail-label">Última visita:</span>
          <span class="detail-value">10 Nov 2023</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Próxima cita:</span>
          <span class="detail-value">25 Nov 2023</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Vacunas:</span>
          <span class="detail-value status-active">Al día</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Enfermedades:</span>
          <span class="detail-value">Displasia de cadera</span>
        </div>
      </div>
      <div class="expediente-actions">
        <button class="btn-outline" onclick="verHistorial(3)">Ver Historial</button>
        <button class="btn-primary" onclick="abrirExpediente(3)">Abrir Expediente</button>
      </div>
    </div>
  </div>

  <!-- Modal para nuevo expediente -->
  <div id="modal-expediente" class="modal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Nuevo Expediente</h3>
        <button class="modal-close" onclick="cerrarModalExpediente()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-expediente">
          @csrf
          <div class="form-group">
            <label for="expediente-paciente">Seleccionar Paciente *</label>
            <select id="expediente-paciente" name="paciente_id" class="form-control" required>
              <option value="">Seleccionar paciente</option>
              <option value="1">Max - María Rodríguez</option>
              <option value="2">Luna - Carlos Pérez</option>
              <option value="3">Toby - Ana González</option>
            </select>
          </div>
          <div class="form-group">
            <label for="expediente-notas">Notas iniciales</label>
            <textarea id="expediente-notas" name="notas" class="form-control" rows="4" placeholder="Observaciones iniciales del paciente..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalExpediente()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="crearExpediente()">Crear Expediente</button>
      </div>
    </div>
  </div>
</section>
@endsection