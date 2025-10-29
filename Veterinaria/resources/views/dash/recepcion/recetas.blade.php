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
      <button class="btn-primary" id="btn-nueva-receta">Nueva Receta</button>
      <button class="btn-secondary">Exportar</button>
    </div>
  </div>
  
  <div class="filters-bar">
    <div class="search-filter">
      <input type="text" placeholder="Buscar receta..." class="search-input" id="search-recetas">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-estado-receta">
        <option value="">Todos los estados</option>
        <option value="activa">Activa</option>
        <option value="expirada">Expirada</option>
        <option value="completada">Completada</option>
      </select>
      <select class="filter-select" id="filter-medico-receta">
        <option value="">Todos los médicos</option>
        <option value="1">Dra. Laura Méndez</option>
        <option value="2">Dr. Roberto García</option>
      </select>
    </div>
  </div>
  
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>Receta</th>
          <th>Paciente</th>
          <th>Médico</th>
          <th>Fecha Emisión</th>
          <th>Vencimiento</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-recetas">
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 8px;">
              <div class="receta-icon">💊</div>
              <div>
                <div style="font-weight: 600;">REC-001</div>
                <div style="font-size: 12px; color: #64748b;">3 medicamentos</div>
              </div>
            </div>
          </td>
          <td>Max - María Rodríguez</td>
          <td>Dra. Laura Méndez</td>
          <td>15 Nov 2023</td>
          <td>30 Nov 2023</td>
          <td><span class="status-badge status-active">Activa</span></td>
          <td>
            <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;" onclick="verReceta(1)">Ver</button>
            <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;" onclick="imprimirReceta(1)">Imprimir</button>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 8px;">
              <div class="receta-icon">💊</div>
              <div>
                <div style="font-weight: 600;">REC-002</div>
                <div style="font-size: 12px; color: #64748b;">2 medicamentos</div>
              </div>
            </div>
          </td>
          <td>Luna - Carlos Pérez</td>
          <td>Dr. Roberto García</td>
          <td>12 Nov 2023</td>
          <td>27 Nov 2023</td>
          <td><span class="status-badge status-active">Activa</span></td>
          <td>
            <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;" onclick="verReceta(2)">Ver</button>
            <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;" onclick="imprimirReceta(2)">Imprimir</button>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 8px;">
              <div class="receta-icon">💊</div>
              <div>
                <div style="font-weight: 600;">REC-003</div>
                <div style="font-size: 12px; color: #64748b;">1 medicamento</div>
              </div>
            </div>
          </td>
          <td>Toby - Ana González</td>
          <td>Dra. Laura Méndez</td>
          <td>10 Nov 2023</td>
          <td>25 Nov 2023</td>
          <td><span class="status-badge status-inactive">Expirada</span></td>
          <td>
            <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;" onclick="verReceta(3)">Ver</button>
            <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;" onclick="renovarReceta(3)">Renovar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Modal para nueva receta -->
  <div id="modal-receta" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 800px;">
      <div class="modal-header">
        <h3>Nueva Receta</h3>
        <button class="modal-close" onclick="cerrarModalReceta()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-receta">
          @csrf
          <div class="form-row">
            <div class="form-group">
              <label for="receta-paciente">Paciente *</label>
              <select id="receta-paciente" name="paciente_id" class="form-control" required>
                <option value="">Seleccionar paciente</option>
                <option value="1">Max - María Rodríguez</option>
                <option value="2">Luna - Carlos Pérez</option>
                <option value="3">Toby - Ana González</option>
              </select>
            </div>
            <div class="form-group">
              <label for="receta-medico">Médico *</label>
              <select id="receta-medico" name="medico_id" class="form-control" required>
                <option value="">Seleccionar médico</option>
                <option value="1">Dra. Laura Méndez</option>
                <option value="2">Dr. Roberto García</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="receta-diagnostico">Diagnóstico *</label>
              <input type="text" id="receta-diagnostico" name="diagnostico" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="receta-vencimiento">Vencimiento *</label>
              <input type="date" id="receta-vencimiento" name="vencimiento" class="form-control" required>
            </div>
          </div>
          
          <div class="medicamentos-section">
            <h4>Medicamentos</h4>
            <div id="lista-medicamentos">
              <!-- Los medicamentos se agregarán dinámicamente aquí -->
            </div>
            <button type="button" class="btn-outline" onclick="agregarMedicamento()" style="margin-top: 10px;">
              + Agregar Medicamento
            </button>
          </div>
          
          <div class="form-group">
            <label for="receta-instrucciones">Instrucciones generales</label>
            <textarea id="receta-instrucciones" name="instrucciones" class="form-control" rows="3"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalReceta()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="guardarReceta()">Guardar Receta</button>
      </div>
    </div>
  </div>
</section>
@endsection