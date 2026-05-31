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
      <button class="btn-primary" id="btn-nueva-mascota">Nueva Mascota</button>
      <button class="btn-secondary" id="btn-buscar-mascotas">Buscar</button>
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
    @forelse($expedientes ?? [] as $cita)
      @php
        $especie = strtolower($cita->mascota->especie ?? 'otro');
        $emoji = match($especie) {
          'perro' => '🐕',
          'gato' => '🐈',
          'ave' => '🐦',
          'roedor' => '🐁',
          default => '🐾'
        };
        $estado = strtolower($cita->estado ?? 'programada');
        $estadoClass = $estado === 'completada' ? 'status-active' : ($estado === 'cancelada' ? 'status-inactive' : 'status-pending');
      @endphp
      <div class="mascota-card"
           data-name="{{ strtolower($cita->mascota->nombre ?? '') }}"
           data-owner="{{ strtolower(optional(optional($cita->mascota)->propietario)->nombre_completo ?? '') }}"
           data-especie="{{ $especie }}">
        <div class="mascota-header">
          <div class="pet-avatar-large">{{ $emoji }}</div>
          <div class="mascota-info">
            <h3>{{ $cita->mascota->nombre ?? '—' }}</h3>
            <p>{{ ucfirst($especie) }}</p>
            <span class="chip">{{ optional(optional($cita->mascota)->propietario)->nombre_completo ?? '—' }}</span>
          </div>
        </div>
        <div class="mascota-details">
          <div class="detail-item">
            <span class="detail-label">Fecha</span>
            <span class="detail-value">{{ optional($cita->fecha)->format('Y-m-d') }} {{ $cita->horario }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Médico</span>
            <span class="detail-value">{{ optional($cita->profesional)->nombre ?? '—' }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Tipo</span>
            <span class="detail-value">{{ ucfirst($cita->tipo_cita ?? '-') }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Estado</span>
            <span class="detail-value {{ $estadoClass }}">{{ ucfirst($estado) }}</span>
          </div>
        </div>
        <div class="mascota-actions">
          <button class="btn-outline" onclick="verHistorial({{ $cita->mascota->id_mascota ?? 'null' }})">Ver Historial</button>
          <button class="btn-primary" onclick="abrirExpediente({{ $cita->mascota->id_mascota ?? 'null' }})">Abrir Expediente</button>
        </div>
      </div>
    @empty
      <div class="text-center" style="grid-column: 1/-1; padding: 16px; color:#6b7280;">No hay expedientes para mostrar</div>
    @endforelse
  </div>

  <!-- Modal para nueva mascota -->
  <div id="modal-mascota" class="modal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Nueva Mascota</h3>
        <button class="modal-close" onclick="cerrarModalMascota()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-mascota">
          @csrf
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-nombre">Nombre de la Mascota *</label>
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
                <option value="otro">Otro</option>
              </select>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-raza">Raza</label>
              <input type="text" id="mascota-raza" name="raza" class="form-control">
            </div>
            <div class="form-group">
              <label for="mascota-edad">Edad</label>
              <input type="number" id="mascota-edad" name="edad" class="form-control" min="0" max="50">
            </div>
          </div>

          <div class="form-group">
            <label for="mascota-propietario">Propietario *</label>
            <select id="mascota-propietario" name="propietario_id" class="form-control" required>
              <option value="">Seleccionar propietario</option>
              <!-- Opciones de propietarios se cargarán dinámicamente -->
            </select>
          </div>

          <div class="form-group">
            <label for="mascota-alergias">Alergias conocidas</label>
            <textarea id="mascota-alergias" name="alergias" class="form-control" rows="3" placeholder="Lista de alergias conocidas..."></textarea>
          </div>

          <div class="form-group">
            <label for="mascota-notas">Notas adicionales</label>
            <textarea id="mascota-notas" name="notas" class="form-control" rows="3" placeholder="Observaciones adicionales..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalMascota()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="guardarMascota()">Guardar Mascota</button>
      </div>
    </div>
  </div>

  <!-- Modal para ver historial completo -->
  <div id="modal-historial" class="modal modal-lg" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="historial-titulo">Historial Médico</h3>
        <button class="modal-close" onclick="cerrarModalHistorial()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="historial-content">
          <!-- Contenido del historial se cargará dinámicamente -->
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/recepcion/expedientes.js') }}"></script>
@endpush