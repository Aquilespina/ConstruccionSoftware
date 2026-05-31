@extends('dash.recepcion')
@section('page-title', 'Recetas')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/recetas.css') }}">
@endpush
@section('content')
<section id="mod-recetas" class="module active">
  <div class="module-header">
    <h2 class="module-title">Gestión de Recetas</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nueva-receta">
        <i class="icon-plus"></i> Nueva Receta
      </button>
      <button class="btn-secondary" id="btn-exportar-recetas">
        <i class="icon-download"></i> Exportar
      </button>
    </div>
  </div>
  
  <div class="filters-bar">
    <div class="search-filter">
      <i class="icon-search">🔍</i>
      <input type="text" placeholder="Buscar receta, paciente o médico..." class="search-input" id="search-recetas">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-estado-receta">
        <option value="">Todos los estados</option>
        <option value="activa">Activa</option>
        <option value="expirada">Expirada</option>
        <option value="completada">Completada</option>
        <option value="cancelada">Cancelada</option>
      </select>
      <select class="filter-select" id="filter-medico-receta">
        <option value="">Todos los médicos</option>
        @foreach($medicos as $medico)
          <option value="{{ $medico->id }}">{{ $medico->nombre }}</option>
        @endforeach
      </select>
      <input type="date" class="filter-select" id="filter-fecha-receta" placeholder="Fecha">
    </div>
  </div>

  <!-- Estadísticas rápidas -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon primary">
        <i class="icon-prescription">💊</i>
      </div>
      <div class="stat-info">
        <span class="stat-value" id="total-recetas">0</span>
        <span class="stat-label">Total Recetas</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon success">
        <i class="icon-check">✅</i>
      </div>
      <div class="stat-info">
        <span class="stat-value" id="recetas-activas">0</span>
        <span class="stat-label">Activas</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon warning">
        <i class="icon-clock">⏰</i>
      </div>
      <div class="stat-info">
        <span class="stat-value" id="recetas-expiradas">0</span>
        <span class="stat-label">Por Expirar</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon danger">
        <i class="icon-expired">❌</i>
      </div>
      <div class="stat-info">
        <span class="stat-value" id="recetas-vencidas">0</span>
        <span class="stat-label">Expiradas</span>
      </div>
    </div>
  </div>
  
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th width="180">Receta</th>
          <th>Paciente</th>
          <th>Médico</th>
          <th width="120">Fecha Emisión</th>
          <th width="120">Vencimiento</th>
          <th width="100">Estado</th>
          <th width="150" class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-recetas">
        <!-- Las recetas se cargarán dinámicamente -->
        <tr>
          <td colspan="7" class="text-center" style="padding: 40px; color: #6b7280;">
            <div style="font-size: 1.1rem; margin-bottom: 8px;">No hay recetas registradas</div>
            <div style="font-size: 0.9rem;">Haga clic en "Nueva Receta" para comenzar</div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Paginación -->
  <div class="pagination-container" id="pagination-recetas">
    <div class="pagination-info">Mostrando 0 de 0 registros</div>
    <div class="pagination">
      <button disabled>‹</button>
      <button class="active">1</button>
      <button disabled>›</button>
    </div>
  </div>

  <!-- Modal para nueva receta -->
  <div id="modal-receta" class="modal">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <h3 id="modal-receta-titulo">Nueva Receta Médica</h3>
        <button class="modal-close" onclick="recetasManager.cerrarModalReceta()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-receta">
          @csrf
          <input type="hidden" id="receta-id" name="id">
          
          <div class="form-section">
            <h4 class="section-title">
              <i class="icon-info">📋</i> Información General
            </h4>
            <div class="form-row">
              <div class="form-group">
                <label for="receta-paciente">Paciente *</label>
                <select id="receta-paciente" name="mascota_id" class="form-control" required onchange="recetasManager.cargarDatosMascota()">
                  <option value="">Seleccionar paciente</option>
                  @foreach($mascotas as $mascota)
                    <option value="{{ $mascota->id }}" data-propietario="{{ $mascota->propietario->nombre }}" data-especie="{{ $mascota->especie }}">
                      {{ $mascota->nombre }} - {{ $mascota->propietario->nombre }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="receta-medico">Médico *</label>
                <select id="receta-medico" name="medico_id" class="form-control" required>
                  <option value="">Seleccionar médico</option>
                  @foreach($medicos as $medico)
                    <option value="{{ $medico->id }}">{{ $medico->nombre }} - {{ $medico->especialidad }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            
            <div class="info-paciente" id="info-paciente" style="display: none;">
              <div class="info-grid">
                <div class="info-item">
                  <label>Propietario:</label>
                  <span id="info-propietario"></span>
                </div>
                <div class="info-item">
                  <label>Especie:</label>
                  <span id="info-especie"></span>
                </div>
                <div class="info-item">
                  <label>Edad:</label>
                  <span id="info-edad"></span>
                </div>
                <div class="info-item">
                  <label>Peso:</label>
                  <span id="info-peso"></span>
                </div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h4 class="section-title">
              <i class="icon-diagnosis">🩺</i> Diagnóstico y Tratamiento
            </h4>
            <div class="form-group">
              <label for="receta-diagnostico">Diagnóstico Principal *</label>
              <input type="text" id="receta-diagnostico" name="diagnostico" class="form-control" placeholder="Ej: Infección respiratoria superior" required>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="receta-fecha-emision">Fecha Emisión *</label>
                <input type="date" id="receta-fecha-emision" name="fecha_emision" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="receta-vencimiento">Vencimiento *</label>
                <input type="date" id="receta-vencimiento" name="fecha_vencimiento" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="section-header">
              <h4 class="section-title">
                <i class="icon-meds">💊</i> Medicamentos
              </h4>
              <button type="button" class="btn-outline btn-sm" onclick="recetasManager.agregarMedicamento()">
                <i class="icon-plus">+</i> Agregar Medicamento
              </button>
            </div>
            <div id="lista-medicamentos" class="medicamentos-container">
              <!-- Los medicamentos se agregarán dinámicamente aquí -->
            </div>
          </div>

          <div class="form-section">
            <h4 class="section-title">
              <i class="icon-instructions">📝</i> Instrucciones y Observaciones
            </h4>
            <div class="form-group">
              <label for="receta-instrucciones">Instrucciones de uso *</label>
              <textarea id="receta-instrucciones" name="instrucciones" class="form-control" rows="3" placeholder="Instrucciones generales para el propietario..." required></textarea>
            </div>
            <div class="form-group">
              <label for="receta-observaciones">Observaciones adicionales</label>
              <textarea id="receta-observaciones" name="observaciones" class="form-control" rows="2" placeholder="Observaciones o recomendaciones adicionales..."></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="recetasManager.cerrarModalReceta()">Cancelar</button>
        <button type="button" class="btn-primary" id="btn-guardar-receta" onclick="recetasManager.guardarReceta()">
          <i class="icon-save">💾</i> Guardar Receta
        </button>
      </div>
    </div>
  </div>

  <!-- Modal para ver receta -->
  <div id="modal-ver-receta" class="modal">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <h3>Receta Médica</h3>
        <button class="modal-close" onclick="recetasManager.cerrarModalVerReceta()">&times;</button>
      </div>
      <div class="modal-body">
        <div id="receta-detalle">
          <!-- Detalle de la receta se cargará aquí -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="recetasManager.cerrarModalVerReceta()">Cerrar</button>
        <button type="button" class="btn-primary" onclick="recetasManager.imprimirReceta()">
          <i class="icon-print">🖨</i> Imprimir
        </button>
        <button type="button" class="btn-outline" id="btn-renovar-receta" onclick="recetasManager.renovarReceta()" style="display: none;">
          <i class="icon-renew">🔄</i> Renovar
        </button>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/recepcion/recetas.js') }}"></script>
@endpush