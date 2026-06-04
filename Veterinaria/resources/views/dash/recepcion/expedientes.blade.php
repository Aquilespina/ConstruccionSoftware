@extends('dash.recepcion')
@section('page-title', 'Historial Médico')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/expedientes.css') }}?v=3">
@endpush
@section('content')
<section id="mod-expedientes" class="module active">
  <div class="module-header">
    <h2 class="module-title">Historial Médico</h2>
    <div class="module-actions">
      <a href="{{ route('mascotas.index') }}" class="btn-primary" style="text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Nueva Mascota
      </a>
    </div>
  </div>

  <div class="filters-bar">
    <div class="search-filter">
      <input type="text" placeholder="Buscar mascota o dueño..." class="search-input" id="search-mascotas">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-especie">
        <option value="">Todas las especies</option>
        <option value="perro">Perro</option>
        <option value="gato">Gato</option>
        <option value="ave">Ave</option>
        <option value="roedor">Roedor</option>
        <option value="otro">Otro</option>
      </select>
      <select class="filter-select" id="filter-estado">
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>
    </div>
  </div>

  <div class="mascotas-grid" id="mascotas-container">
    @forelse($mascotas ?? [] as $mascota)
      @php
        $especie    = strtolower($mascota->especie ?? 'otro');
        $emoji      = match($especie) { 'perro'=>'🐕','gato'=>'🐈','ave'=>'🐦','roedor'=>'🐁', default=>'🐾' };
        $estado     = strtolower($mascota->estado ?? 'activo');
        $ultima     = $mascota->citas_max_fecha
                        ? \Carbon\Carbon::parse($mascota->citas_max_fecha)->format('d/m/Y')
                        : '—';
        $nConsultas = $mascota->citas_count ?? 0;
      @endphp
      <div class="mascota-card"
           data-name="{{ strtolower($mascota->nombre ?? '') }}"
           data-owner="{{ strtolower(optional($mascota->propietario)->nombre ?? '') }}"
           data-especie="{{ $especie }}"
           data-estado="{{ $estado }}">
        <div class="mascota-header">
          <div class="pet-avatar-large">{{ $emoji }}</div>
          <div class="mascota-info">
            <h3>{{ $mascota->nombre ?? '—' }}</h3>
            <p>{{ ucfirst($especie) }}{{ $mascota->raza ? ' · ' . $mascota->raza : '' }}</p>
            <span class="chip">{{ optional($mascota->propietario)->nombre ?? '—' }}</span>
          </div>
        </div>
        <div class="mascota-details">
          <div class="detail-item">
            <span class="detail-label">Última visita</span>
            <span class="detail-value">{{ $ultima }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Consultas</span>
            <span class="detail-value">{{ $nConsultas }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Estado</span>
            <span class="detail-value {{ $estado === 'activo' ? 'status-active' : 'status-inactive' }}">
              {{ ucfirst($estado) }}
            </span>
          </div>
        </div>
        <div class="mascota-actions">
          <button class="btn-primary"
                  onclick="verHistorial('{{ $mascota->id_mascota }}', '{{ addslashes($mascota->nombre ?? 'Mascota') }}')">
            Ver Historial
          </button>
        </div>
      </div>
    @empty
      <div class="text-center" style="grid-column:1/-1;padding:32px;color:#6b7280;">
        No hay mascotas registradas
      </div>
    @endforelse

    <div id="mascotas-sin-resultados" style="display:none;grid-column:1/-1;padding:32px;text-align:center;color:#6b7280;">
      No hay mascotas que coincidan con la búsqueda.
    </div>
  </div>

  <!-- Modal historial médico -->
  <div id="modal-historial" class="modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="historial-titulo">Historial Médico</h3>
        <button class="modal-close" onclick="cerrarModalHistorial()">&times;</button>
      </div>

      {{-- Ficha de la mascota (se rellena por JS) --}}
      <div id="historial-ficha" class="h-ficha" style="display:none;"></div>

      {{-- Zona con scroll (entradas del timeline) --}}
      <div id="historial-scroll" class="h-scroll">
        <div class="h-vacio">Cargando historial...</div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalHistorial()">Cerrar</button>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/recepcion/expedientes.js') }}"></script>
@endpush
