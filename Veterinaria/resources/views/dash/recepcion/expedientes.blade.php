@extends('dash.recepcion')
@section('page-title', 'Historial Médico')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/expedientes.css') }}">
  <style>
    /* ── Modal historial: layout sin doble scroll ─────────────────────── */
    #modal-historial .modal-content {
      display: flex;
      flex-direction: column;
      max-width: 680px;
      width: 92%;
      max-height: 88vh;
      overflow: hidden; /* el scroll va solo en la zona de entradas */
      border-radius: 14px;
    }
    #modal-historial .modal-header {
      flex-shrink: 0;
      padding: 18px 24px;
      border-bottom: 1px solid #e2e8f0;
    }
    #modal-historial .modal-header h3 {
      font-size: 1.05rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
    }

    /* ── Ficha de la mascota (sin scroll) ───────────────────────────────── */
    .h-ficha {
      flex-shrink: 0;
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 24px;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
    }
    .h-ficha-avatar {
      font-size: 2rem;
      width: 52px; height: 52px;
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .h-ficha-datos { flex: 1; }
    .h-ficha-nombre {
      font-size: 1rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0 0 2px;
    }
    .h-ficha-sub {
      font-size: 0.8rem;
      color: #64748b;
      margin: 0;
    }
    .h-ficha-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 6px;
    }
    .h-chip {
      font-size: 0.72rem;
      font-weight: 600;
      padding: 2px 10px;
      border-radius: 999px;
      background: #e0e7ff;
      color: #3730a3;
    }
    .h-chip-alerta {
      background: #fef3c7;
      color: #92400e;
    }

    /* ── Zona scrollable de entradas ─────────────────────────────────────── */
    .h-scroll {
      flex: 1;
      overflow-y: auto;
      padding: 16px 24px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    /* ── Tarjeta de entrada ──────────────────────────────────────────────── */
    .h-card {
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      background: #fff;
      overflow: hidden;
      box-shadow: 0 1px 4px rgba(15,23,42,.05);
    }
    .h-card-top {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 6px;
      padding: 10px 14px;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
    }
    .h-card-fecha {
      font-size: 0.78rem;
      font-weight: 700;
      color: #334155;
    }
    .h-badge {
      font-size: 0.68rem;
      font-weight: 700;
      padding: 2px 9px;
      border-radius: 999px;
    }
    .h-badge-consulta   { background: #dbeafe; color: #1e40af; }
    .h-badge-urgencia   { background: #fee2e2; color: #991b1b; }
    .h-badge-cirugia    { background: #f3e8ff; color: #6b21a8; }
    .h-badge-estetica   { background: #dcfce7; color: #166534; }
    .h-badge-hosp       { background: #f3e8ff; color: #6b21a8; }
    .h-badge-completada { background: #d1fae5; color: #065f46; }
    .h-badge-programada { background: #fff7ed; color: #92400e; }
    .h-badge-cancelada  { background: #fee2e2; color: #991b1b; }
    .h-badge-internado  { background: #fef3c7; color: #92400e; }
    .h-badge-alta       { background: #d1fae5; color: #065f46; }
    .h-badge-tratamiento{ background: #dbeafe; color: #1e40af; }

    .h-card-body { padding: 12px 14px; }
    .h-profesional {
      font-size: 0.78rem;
      color: #64748b;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .h-campo { margin-bottom: 8px; }
    .h-campo label {
      display: block;
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #94a3b8;
      margin-bottom: 2px;
    }
    .h-campo p {
      margin: 0;
      font-size: 0.85rem;
      color: #1e293b;
      line-height: 1.4;
    }
    .h-peso {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 0.75rem;
      color: #475569;
      background: #f1f5f9;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 2px 9px;
      margin-bottom: 8px;
    }

    /* ── Recetas dentro de la tarjeta ───────────────────────────────────── */
    .h-recetas-titulo {
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #94a3b8;
      margin: 8px 0 6px;
    }
    .h-receta {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-radius: 7px;
      padding: 8px 11px;
      margin-bottom: 5px;
    }
    .h-receta-med {
      font-size: 0.83rem;
      font-weight: 700;
      color: #1e40af;
    }
    .h-receta-dosis {
      font-size: 0.78rem;
      color: #3b82f6;
      margin-left: 6px;
    }
    .h-receta-ind {
      margin: 3px 0 0;
      font-size: 0.78rem;
      color: #475569;
    }
    .h-sin-recetas {
      font-size: 0.78rem;
      color: #94a3b8;
      margin-top: 6px;
    }

    /* ── Estado vacío ───────────────────────────────────────────────────── */
    .h-vacio {
      text-align: center;
      padding: 40px 16px;
      color: #94a3b8;
      font-size: 0.9rem;
    }

    /* ── Footer ─────────────────────────────────────────────────────────── */
    #modal-historial .modal-footer {
      flex-shrink: 0;
      padding: 14px 24px;
      border-top: 1px solid #e2e8f0;
      display: flex;
      justify-content: flex-end;
    }
  </style>
@endpush
@section('content')
<section id="mod-expedientes" class="module active">
  <div class="module-header">
    <h2 class="module-title">Historial Médico</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nueva-mascota">Nueva Mascota</button>
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

  <!-- Modal nueva mascota -->
  <div id="modal-mascota" class="modal" style="display:none;">
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
              <label for="mascota-nombre">Nombre *</label>
              <input type="text" id="mascota-nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="mascota-especie">Especie *</label>
              <select id="mascota-especie" name="especie" class="form-control" required>
                <option value="">Seleccionar</option>
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
              <label for="mascota-edad">Edad (años)</label>
              <input type="number" id="mascota-edad" name="edad" class="form-control" min="0" max="50">
            </div>
          </div>
          <div class="form-group">
            <label for="mascota-propietario">Propietario *</label>
            <select id="mascota-propietario" name="id_propietario" class="form-control" required>
              <option value="">Cargando propietarios...</option>
            </select>
          </div>
          <div class="form-group">
            <label for="mascota-alergias">Alergias conocidas</label>
            <textarea id="mascota-alergias" name="alergias" class="form-control" rows="2"
                      placeholder="Ej: Penicilina, picadura de abeja..."></textarea>
          </div>
          <div class="form-group">
            <label for="mascota-notas">Notas adicionales</label>
            <textarea id="mascota-notas" name="notas" class="form-control" rows="2"
                      placeholder="Observaciones adicionales..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalMascota()">Cancelar</button>
        <button type="button" class="btn-primary" id="btn-guardar-mascota" onclick="guardarMascota()">
          Guardar Mascota
        </button>
      </div>
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
