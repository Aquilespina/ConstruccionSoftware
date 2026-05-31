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
      <button class="btn-secondary"
              type="button"
              onclick="window.location.href='{{ route('propietarios.exportar') }}'">
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
      <input type="text" placeholder="Buscar propietario o mascota " class="search-input" id="search-propietarios">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-estado-propietarios">
        <option value="">Todos los estados</option>
        <option value="1">Activo</option>
        <option value="0">Inactivo</option>
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
          <th>Estado</th>
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
          <td>
            <span class="estado-badge {{ $propietario->estado ? 'estado-activo' : 'estado-inactivo' }}">
              {{ $propietario->estado ? 'Activo' : 'Inactivo' }}
            </span>
          </td>
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
          <td colspan="7" style="text-align: center; padding: 20px;">
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
              <input type="text" id="propietario-nombre" name="nombre" class="form-control" required  maxlength="30"
    oninput="this.value=this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g,'')">
            </div>
            <div class="form-group">
              <label for="propietario-telefono">Teléfono</label>
              <input type="tel" id="propietario-telefono" name="telefono" class="form-control" required 
    maxlength="10"
    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="propietario-email">Email</label>
              <input type="email" id="propietario-email" name="correo_electronico" class="form-control"  required maxlength="255"
    placeholder="ejemplo@correo.com">

            </div>
            <div class="form-group">
              <label for="propietario-direccion">Dirección</label>
              <textarea id="propietario-direccion" name="direccion" class="form-control" required rows="2"
    maxlength="255"></textarea>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="propietario-fecha">Fecha Registro</label>
                <input
        type="text"
        id="propietario-fecha"
        class="form-control"
        readonly>
            </div>
            <div class="form-group">
              <label for="propietario-estado">Estado</label>
              <select id="propietario-estado" name="estado" class="form-control" disabled>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
          <button type="button"
              class="btn-danger"
                  id="btn-eliminar-propietario"
                  style="display: none;"
                  onclick="eliminarPropietario(document.getElementById('propietario-id').value)">
            Eliminar
          </button>
          <button type="button" class="btn-secondary" onclick="cerrarModalPropietario()">Cancelar</button>
          <button type="button" class="btn-primary" onclick="guardarPropietario()">Guardar Propietario</button>
      </div>
    </div>
  </div>


  
  <!-- Modal para ver  propietario -->
  <div id="modal-ver-propietario" class="modal" style="display:none;">
    <div class="modal-content">

        <div class="modal-header">
            <h3>Detalle del Propietario</h3>
            <button class="modal-close" onclick="cerrarModalVerPropietario()">
                &times;
            </button>
        </div>

        <div class="modal-body">

<div class="form-row">
    <div class="form-group">
        <label>Nombre</label>
        <input type="text" id="ver-nombre" class="form-control campo-solo-lectura" readonly>
    </div>

    <div class="form-group">
        <label>Teléfono</label>
        <input type="text" id="ver-telefono" class="form-control campo-solo-lectura" readonly>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Correo</label>
        <input type="text" id="ver-correo" class="form-control campo-solo-lectura" readonly>
    </div>

    <div class="form-group">
        <label>Fecha Registro</label>
        <input type="text" id="ver-fecha" class="form-control campo-solo-lectura" readonly>
    </div>
</div>

<div class="form-group">
    <label>Dirección</label>
    <textarea
        id="ver-direccion"
        class="form-control campo-solo-lectura"
        rows="2"
        readonly></textarea>
</div>

            <hr>

            <h4>Mascotas Registradas</h4>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Especie</th>
                        <th>Citas</th>
                    </tr>
                </thead>

                <tbody id="tabla-mascotas-propietario">
                </tbody>
            </table>

        </div>

        <div class="modal-footer">
            <button class="btn-secondary"
                    onclick="cerrarModalVerPropietario()">
                Cerrar
            </button>
        </div>

    </div>
</div>
</section>

<script src="{{ asset('js/recepcion/propietarios.js') }}"></script>
@endsection