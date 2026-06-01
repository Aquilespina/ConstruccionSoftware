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
      <input type="text" placeholder="Buscar mascota..." class="search-input" id="search-mascotas" value="{{ $search ?? '' }}">
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
        <tr
          data-especie="{{ strtolower($mascota->especie ?? '') }}"
          data-estado="{{ strtolower((string) ($mascota->estado ?? ($mascota->color ?? ''))) }}"
          data-nombre="{{ strtolower($mascota->nombre ?? '') }}">
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
<input
    type="text"
    id="mascota-raza"
    name="raza"
    class="form-control"
    minlength="2"
    maxlength="50"
    pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\-]+$"
    required>
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
<input
    type="number"
    id="mascota-edad"
    name="edad"
    class="form-control"
    min="0"
    max="15"
    oninput="if(this.value > 15) this.value = 15";
    placeholder="Ej: 3">            </div>
            <div class="form-group">
              <label for="mascota-peso">Peso (kg)</label>
              <input type="number" id="mascota-peso" name="peso" class="form-control" step="0.1"
                  min="0"
                 max="100"
                 oninput="if(this.value > 100) this.value = 100;"
                 >
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
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-danger" id="btn-eliminar-mascota" style="display: none;" onclick="eliminarMascota(document.getElementById('form-mascota').dataset.mascotaId)">Eliminar</button>
        <button type="button" class="btn-secondary" onclick="cerrarModalMascota()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="guardarMascota()">Guardar Mascota</button>
      </div>
    </div>
  </div>

  <!-- Modal de detalle de mascota -->
  <div id="modal-ver-mascota" class="modal" style="display: none;">
    <div class="modal-content modal-detail-content">
      <div class="modal-header">
        <div>
          <h3 id="ver-mascota-nombre">Detalle de Mascota</h3>
          <p id="ver-mascota-id" class="detail-subtitle">ID: -</p>
        </div>
        <button class="modal-close" onclick="cerrarModalVerMascota()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="detail-hero">
          <div class="detail-avatar" id="ver-mascota-avatar">🐾</div>
          <div class="detail-summary">
            <div class="detail-badges">
              <span class="detail-badge" id="ver-mascota-especie">Especie: -</span>
              <span class="detail-badge" id="ver-mascota-estado">Estado: -</span>
            </div>
            <p class="detail-text" id="ver-mascota-raza">Raza: -</p>
            <p class="detail-text" id="ver-mascota-propietario">Propietario: -</p>
          </div>
        </div>

        <div class="detail-grid">
          <div class="detail-card">
            <span class="detail-label">Sexo</span>
            <strong class="detail-value" id="ver-mascota-sexo">-</strong>
          </div>
          <div class="detail-card">
            <span class="detail-label">Edad</span>
            <strong class="detail-value" id="ver-mascota-edad">-</strong>
          </div>
          <div class="detail-card">
            <span class="detail-label">Peso</span>
            <strong class="detail-value" id="ver-mascota-peso">-</strong>
          </div>
          <div class="detail-card">
            <span class="detail-label">Última visita</span>
            <strong class="detail-value" id="ver-mascota-ultima-visita">-</strong>
          </div>
          <div class="detail-card">
            <span class="detail-label">Creada</span>
            <strong class="detail-value" id="ver-mascota-created-at">-</strong>
          </div>
          <div class="detail-card">
            <span class="detail-label">Actualizada</span>
            <strong class="detail-value" id="ver-mascota-updated-at">-</strong>
          </div>
        </div>

        <div class="detail-section">
          <h4>Resumen clínico</h4>
          <p id="ver-mascota-historial" class="detail-text detail-panel">Sin historial médico registrado.</p>
        </div>

        <div class="detail-section detail-stats">
          <div>
            <span class="detail-label">Total de citas</span>
            <strong class="detail-value" id="ver-mascota-total-citas">0</strong>
          </div>
        </div>
      </div>
      <div class="modal-footer modal-footer-detail">
        <button type="button" class="btn-secondary" onclick="cerrarModalVerMascota()">Cerrar</button>
        <button type="button" class="btn-primary" onclick="editarMascotaDesdeDetalle()">Editar Mascota</button>
      </div>
    </div>
  </div>
</section>

<script src="{{ asset('js/recepcion/mascotas.js') }}"></script>

@endsection