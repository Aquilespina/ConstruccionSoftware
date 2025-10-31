@extends('dash.recepcion')
@section('page-title', 'Gestión de Propietarios')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/propietarios.css') }}">
@endpush
@section('content')
<section id="mod-propietarios" class="module active">
  <div class="module-header">
    <h2 class="module-title">Gestión de Propietarios</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nuevo-propietario">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Nuevo Propietario
      </button>
      <button class="btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="7 10 12 15 17 10"></polyline>
          <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Exportar
      </button>
    </div>
  </div>
  
  <div class="filters-bar">
    <div class="search-filter">
      <input type="text" placeholder="Buscar propietario..." class="search-input" id="search-propietarios">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-estado-propietarios">
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>
    </div>
  </div>
  
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>Propietario</th>
          <th>Teléfono</th>
          <th>Email</th>
          <th>Dirección</th>
          <th>Fecha Registro</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-propietarios">
        @forelse($propietarios as $propietario)
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div class="user-avatar">
                {{ substr($propietario->nombre, 0, 2) }}
              </div>
              <div>
                <div style="font-weight: 600;">{{ $propietario->nombre }}</div>
                <div style="font-size: 12px; color: #64748b;">ID: PRO{{ str_pad($propietario->id_propietario, 3, '0', STR_PAD_LEFT) }}</div>
              </div>
            </div>
          </td>
          <td>{{ $propietario->telefono ?? 'N/A' }}</td>
          <td>{{ $propietario->correo_electronico ?? 'N/A' }}</td>
          <td>{{ $propietario->direccion ?? 'N/A' }}</td>
          <td>{{ $propietario->fecha_registro ? \Carbon\Carbon::parse($propietario->fecha_registro)->format('d M Y') : 'N/A' }}</td>
          <td>
            <div style="display: flex; gap: 8px;">
              <button class="btn-outline" onclick="verPropietario({{ $propietario->id_propietario }})">Ver</button>
              <button class="btn-secondary" onclick="editarPropietario({{ $propietario->id_propietario }})">Editar</button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align: center; padding: 20px;">
            No hay propietarios registrados
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Modal para nuevo/editar propietario -->
  <div id="modal-propietario" class="modal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modal-propietario-titulo">Nuevo Propietario</h3>
        <button class="modal-close" onclick="cerrarModalPropietario()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-propietario" method="POST" action="{{ route('propietarios.store') }}">
          @csrf
          <input type="hidden" id="propietario-id" name="id_propietario">
          <div class="form-row">
            <div class="form-group">
              <label for="propietario-nombre">Nombre completo *</label>
              <input type="text" id="propietario-nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="propietario-telefono">Teléfono</label>
              <input type="tel" id="propietario-telefono" name="telefono" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="propietario-email">Email</label>
              <input type="email" id="propietario-email" name="correo_electronico" class="form-control">
            </div>
            <div class="form-group">
              <label for="propietario-direccion">Dirección</label>
              <textarea id="propietario-direccion" name="direccion" class="form-control" rows="2"></textarea>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="propietario-fecha">Fecha Registro</label>
              <input type="date" id="propietario-fecha" name="fecha_registro" class="form-control">
            </div>
            <div class="form-group" style="visibility: hidden;">
              <!-- Espacio reservado para mantener el layout -->
              <label>&nbsp;</label>
              <input type="text" class="form-control" style="visibility: hidden;">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="cerrarModalPropietario()">Cancelar</button>
          <button type="button" class="btn-primary" onclick="guardarPropietario()">Guardar Propietario</button>
      </div>
    </div>
  </div>
</section>

<script src="{{ asset('js/recepcion/propietarios.js') }}"></script>
@endsection