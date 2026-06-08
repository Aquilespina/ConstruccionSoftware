@extends('dash.recepcion')
@section('page-title', 'Hospitalizaciones')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/hospitalizaciones.css') }}">
@endpush
@section('content')
<section id="mod-hospitalizaciones" class="module active">

    <div class="module-header">
        <h2 class="module-title">Gestión de Hospitalizaciones</h2>
        <div class="module-actions">
            <button class="btn-primary" id="btn-nueva-hospitalizacion">
                <span class="btn-icon">+</span>
                Nueva Hospitalización
            </button>
            <a href="{{ route('hospitalizaciones.reporte-diario') }}" class="btn-secondary">
                <span class="btn-icon">&#128203;</span>
                Reporte Diario
            </a>
        </div>
    </div>

    {{-- Stats dinámicos --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Internados</h3>
                <div class="stat-icon icon-red">H</div>
            </div>
            <div class="stat-value">{{ $stats['internados'] }}</div>
            <div class="stat-change">Pacientes activos</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">En Tratamiento</h3>
                <div class="stat-icon icon-orange">T</div>
            </div>
            <div class="stat-value">{{ $stats['tratamiento'] }}</div>
            <div class="stat-change">En proceso</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Altas este Mes</h3>
                <div class="stat-icon icon-green">A</div>
            </div>
            <div class="stat-value">{{ $stats['alta_mes'] }}</div>
            <div class="stat-change">{{ now()->format('F Y') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Total Registradas</h3>
                <div class="stat-icon icon-blue">R</div>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-change">Histórico</div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="filters-bar">
        <div class="search-filter">
            <div class="search-wrapper">
                <span class="search-icon">&#128269;</span>
                <input type="text" placeholder="Buscar mascota, propietario..." class="search-input" id="search-hospitalizaciones">
            </div>
        </div>
        <div class="filter-actions">
            <select class="filter-select" id="filter-estado-hospitalizacion">
                <option value="">Todos los estados</option>
                <option value="Internado">Internado</option>
                <option value="Tratamiento">Tratamiento</option>
                <option value="Alta">Alta</option>
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="table-container">
        @if($hospitalizaciones->count() > 0)
            <table class="data-table" id="tabla-hospitalizaciones">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mascota</th>
                        <th>Especie</th>
                        <th>Propietario</th>
                        <th>Fecha Ingreso</th>
                        <th>Fecha Egreso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hospitalizaciones as $hosp)
                        <tr data-estado="{{ $hosp->estado }}">
                            <td>{{ $hosp->id_hospitalizacion }}</td>
                            <td>
                                <div class="pet-info">
                                    <div class="pet-avatar">&#128062;</div>
                                    <div class="pet-details">
                                        <strong>{{ $hosp->mascota_nombre }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $hosp->especie }}</td>
                            <td>{{ $hosp->propietario_nombre }}</td>
                            <td>{{ $hosp->fecha_ingreso ? \Carbon\Carbon::parse($hosp->fecha_ingreso)->format('d/m/Y H:i') : '—' }}</td>
                            <td>{{ $hosp->fecha_egreso  ? \Carbon\Carbon::parse($hosp->fecha_egreso)->format('d/m/Y H:i')  : '—' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($hosp->estado) }}">
                                    {{ $hosp->estado }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px; align-items:center;">
                                    <button class="btn-outline btn-small"
                                            onclick="verHospitalizacion({{ $hosp->id_hospitalizacion }})">
                                        Ver
                                    </button>
                                    @if($hosp->estado !== 'Alta')
                                        <button class="btn-secondary btn-small"
                                                onclick="editarHospitalizacion({{ $hosp->id_hospitalizacion }})">
                                            Editar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="hosp-sin-resultados" style="display:none;">
                        <td colspan="8" style="text-align:center; padding:24px; color:#94a3b8;">
                            No se encontraron hospitalizaciones con los filtros seleccionados.
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">H</div>
                <h3>No hay hospitalizaciones registradas</h3>
                <p>Registra la primera haciendo clic en "Nueva Hospitalización".</p>
            </div>
        @endif
    </div>

    {{-- ── Modal: Nueva Hospitalización ─────────────────────────────── --}}
    <div id="modal-hospitalizacion" class="modal">
        <div class="modal-overlay" onclick="cerrarModalHospitalizacion()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nueva Hospitalización</h3>
                <button class="modal-close" onclick="cerrarModalHospitalizacion()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-hospitalizacion" action="{{ route('hospitalizaciones.store') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hospitalizacion-mascota">Mascota *</label>
                            <select id="hospitalizacion-mascota" name="id_mascota" class="form-control" required>
                                <option value="">Seleccionar mascota</option>
                                @forelse($mascotas as $mascota)
                                    <option value="{{ $mascota['id'] }}"
                                            data-especie="{{ $mascota['especie'] }}">
                                        {{ $mascota['display_name'] }}
                                    </option>
                                @empty
                                    <option value="" disabled>No hay mascotas registradas</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hospitalizacion-estado">Estado *</label>
                            <select id="hospitalizacion-estado" name="estado" class="form-control" required>
                                <option value="Internado" selected>Internado</option>
                                <option value="Tratamiento">Tratamiento</option>
                                <option value="Alta">Alta</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hospitalizacion-fecha-ingreso">Fecha de Ingreso *</label>
                        <input type="datetime-local" id="hospitalizacion-fecha-ingreso"
                               name="fecha_ingreso" class="form-control"
                               style="background:#f1f5f9; cursor:not-allowed;"
                               title="La fecha se registra automáticamente con el momento actual"
                               readonly required>
                        <small class="form-text text-muted">Se registra automáticamente con la fecha y hora actuales.</small>
                    </div>
                    <div class="form-group">
                        <label for="hospitalizacion-observaciones">Observaciones *</label>
                        <textarea id="hospitalizacion-observaciones" name="observaciones"
                                  class="form-control" rows="4"
                                  maxlength="1000"
                                  placeholder="Motivo de la hospitalización, tratamiento requerido, observaciones médicas..."
                                  required></textarea>
                        <small class="form-text text-muted">Campo obligatorio. Máximo 1000 caracteres.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalHospitalizacion()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="guardarHospitalizacion()">Registrar Hospitalización</button>
            </div>
        </div>
    </div>

    {{-- ── Modal: Ver Hospitalización ────────────────────────────────── --}}
    <div id="modal-ver-hospitalizacion" class="modal">
        <div class="modal-overlay" onclick="cerrarModalVer()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalle de Hospitalización</h3>
                <button class="modal-close" onclick="cerrarModalVer()">&times;</button>
            </div>
            <div class="modal-body" id="ver-hosp-body">
                <!-- Se llena dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalVer()">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- ── Modal: Editar Hospitalización ─────────────────────────────── --}}
    <div id="modal-editar-hospitalizacion" class="modal">
        <div class="modal-overlay" onclick="cerrarModalEditar()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Hospitalización</h3>
                <button class="modal-close" onclick="cerrarModalEditar()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-editar-hospitalizacion">
                    @csrf
                    <input type="hidden" id="editar-hosp-id">
                    <input type="hidden" id="editar-estado-anterior">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Mascota</label>
                            <input type="text" id="editar-mascota-display" class="form-control" readonly
                                   style="background:#f1f5f9; cursor:not-allowed;">
                        </div>
                        <div class="form-group">
                            <label for="editar-estado">Nuevo Estado *</label>
                            <select id="editar-estado" name="estado" class="form-control" required>
                                <option value="Internado">Internado</option>
                                <option value="Tratamiento">Tratamiento</option>
                                <option value="Alta">Alta</option>
                            </select>
                        </div>
                    </div>

                    {{-- fecha_egreso se genera automáticamente en el JS al seleccionar Alta --}}
                    <input type="hidden" id="editar-fecha-egreso">

                    {{-- Historial existente (solo lectura) --}}
                    <div class="form-group">
                        <label>Historial de Observaciones</label>
                        <div id="editar-historial"
                             style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px;
                                    padding:10px 12px; font-size:12px; color:#475569;
                                    max-height:120px; overflow-y:auto; white-space:pre-wrap; line-height:1.6;">
                        </div>
                    </div>

                    {{-- Nuevo comentario --}}
                    <div class="form-group">
                        <label for="editar-nuevo-comentario">
                            Nuevo Comentario *
                            <span id="lbl-cambio-estado" style="font-size:11px; color:#64748b; font-weight:400; margin-left:6px;"></span>
                        </label>
                        <textarea id="editar-nuevo-comentario"
                                  class="form-control" rows="3" maxlength="800" required
                                  placeholder="Describe la evolución, tratamiento o motivo del cambio de estado..."></textarea>
                        <small class="form-text text-muted">Se agregará al historial con fecha, hora y cambio de estado.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalEditar()">Cancelar</button>
                <button type="button" class="btn-primary" id="btn-guardar-editar" onclick="guardarEdicionHospitalizacion()">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>

</section>
<script src="{{ asset('js/recepcion/hospitalizaciones.js') }}?v=2"></script>
@endsection
