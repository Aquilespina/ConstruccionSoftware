@extends('dash.recepcion')
@section('page-title', 'Mascotas')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/mascotas.css') }}">

@endpush
@section('content')
<section id="mod-mascotas" class="module active">
  <div class="module-header">
    <h2 class="module-title">Gestión de Mascotas</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nueva-mascota">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Nueva Mascota
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
      <input type="text" placeholder="Buscar mascota..." class="search-input" id="search-mascotas">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-especie">
        <option value="">Todas las especies</option>
        <option value="perro">Perro</option>
        <option value="gato">Gato</option>
        <option value="ave">Ave</option>
        <option value="roedor">Roedor</option>
        <option value="reptil">Reptil</option>
      </select>
      <select class="filter-select" id="filter-estado-mascota">
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
          <th>Mascota</th>
          <th>Especie</th>
          <th>Raza</th>
          <th>Propietario</th>
          <th>Edad</th>
          <th>Última Visita</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-mascotas">
        @forelse ($mascotas ?? [] as $mascota)
        @php
          // Seleccionar emoji según especie
          $especie = strtolower($mascota->especie ?? '');
          $avatar = str_contains($especie, 'gat') ? '🐈' : (str_contains($especie, 'ave') ? '🐦' : '🐕');
        @endphp
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div class="pet-avatar">{{ $avatar }}</div>
              <div>
                <div style="font-weight: 600;">{{ $mascota->nombre ?? '-' }}</div>
                <div style="font-size: 0.75rem; color: var(--gray-500);">ID: {{ $mascota->id_mascota ?? '' }}</div>
              </div>
            </div>
          </td>
          <td>{{ $mascota->especie ?? '-' }}</td>
          <td>{{ $mascota->raza ?? '-' }}</td>
          <td>{{ optional($mascota->propietario)->nombre ?? '-' }}</td>
          <td>{{ $mascota->edad ?? '-' }}</td>
          <td>{{ $mascota->ultima_visita ?? '-' }}</td>
          <td>
            <div style="display: flex; gap: 0.5rem;">
              <button class="btn-outline" onclick="verMascota('{{ $mascota->id_mascota }}')">Ver</button>
              <button class="btn-secondary" onclick="editarMascota('{{ $mascota->id_mascota }}')">Editar</button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center; padding:1.5rem; color:var(--gray-600);">No hay mascotas registradas.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Modal para nueva/editar mascota -->
  <div id="modal-mascota" class="modal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modal-mascota-titulo">Nueva Mascota</h3>
        <button class="modal-close" onclick="cerrarModalMascota()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-mascota">
          @csrf
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-nombre">Nombre *</label>
              <input type="text" id="mascota-nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="mascota-especie">Especie *</label>
              <select id="mascota-especie" name="especie" class="form-control" required>
                <option value="">Seleccionar especie</option>
                <option value="perro">Perro</option>
                <option value="gato">Gato</option>
                <option value="ave">Ave</option>
                <option value="roedor">Roedor</option>
                <option value="reptil">Reptil</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-raza">Raza *</label>
              <input type="text" id="mascota-raza" name="raza" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="mascota-propietario">Propietario *</label>
              <select id="mascota-propietario" name="propietario_id" class="form-control" required>
                <option value="">Seleccionar propietario</option>
                <option value="1">María Rodríguez</option>
                <option value="2">Carlos Pérez</option>
                <option value="3">Ana González</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-edad">Edad</label>
              <input type="text" id="mascota-edad" name="edad" class="form-control" placeholder="Ej: 3 años">
            </div>
            <div class="form-group">
              <label for="mascota-peso">Peso (kg)</label>
              <input type="number" id="mascota-peso" name="peso" class="form-control" step="0.1">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-sexo">Sexo</label>
              <select id="mascota-sexo" name="sexo" class="form-control">
                <option value="">Seleccionar</option>
                <option value="macho">Macho</option>
                <option value="hembra">Hembra</option>
              </select>
            </div>
            <div class="form-group">
              <label for="mascota-estado">Estado *</label>
              <select id="mascota-estado" name="estado" class="form-control" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalMascota()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="guardarMascota()">Guardar Mascota</button>
      </div>
    </div>
  </div>
</section>

<script src="{{ asset('js/recepcion/mascotas.js') }}"></script>

@endsection