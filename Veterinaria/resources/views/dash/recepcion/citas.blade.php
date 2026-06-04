@extends('dash.recepcion')
@section('page-title', 'Citas')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/citas.css') }}">
  <link rel="stylesheet" href="{{ asset('css/recepcion/form-validation.css') }}">
  <link rel="stylesheet" href="{{ asset('css/recepcion/entity-detail.css') }}">
@endpush
@section('content')
<section id="mod-citas" class="module active">
    <div class="module-header">
        <h2 class="module-title">Gestión de Citas</h2>
        <div class="module-actions">
            <button class="btn-primary" id="btn-nueva-cita">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Nueva Cita
            </button>
            <button class="btn-secondary" onclick="abrirCalendario()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Calendario
            </button>
        </div>
    </div>
    
    <div class="filters-bar">
        <div class="search-filter">
            <input type="text" placeholder="Buscar citas..." class="search-input" id="search-citas">
        </div>
        <div class="filter-actions">
                        <select class="filter-select" id="filter-estado-cita">
                                <option value="">Todos los estados</option>
                                <option value="Programada">Programada</option>
                                <option value="Completada">Completada</option>
                                <option value="Cancelada">Cancelada</option>
                        </select>
                        <select class="filter-select" id="filter-medico">
                                <option value="">Todos los médicos</option>
                                @isset($profesionales)
                                    @foreach($profesionales as $pro)
                                        <option value="{{ $pro->rfc }}">{{ $pro->nombre }}</option>
                                    @endforeach
                                @endisset
                        </select>
        </div>
    </div>
    
    <div class="citas-tabs" data-active-tab="hoy">
        <button class="tab-button active" data-tab="hoy">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            Hoy
        </button>
        <button class="tab-button" data-tab="proximas">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Próximas
        </button>
        <button class="tab-button" data-tab="pasadas">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"></path>
                <path d="M16 2v4"></path>
                <path d="M8 2v4"></path>
                <path d="M3 10h5"></path>
                <path d="M17.5 17.5 16 16.25V14"></path>
                <path d="M22 16a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z"></path>
            </svg>
            Pasadas
        </button>
        <button class="tab-button" data-tab="todas">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Todas
        </button>
    </div>
    
    <div class="citas-content">
        <div class="citas-list-container" id="citas-hoy-content">
            @forelse(($citasHoy ?? []) as $cita)
            <div class="cita-item" data-estado="{{ $cita->estado }}" data-medico-rfc="{{ $cita->rfc_profesional }}">
                <div class="cita-time">{{ substr($cita->horario,0,5) }}</div>
                <div class="cita-info">
                    <div class="cita-paciente">{{ $cita->nombre_mascota ?? ($cita->mascota->nombre ?? 'Mascota') }}</div>
                    <div class="cita-propietario">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        {{ $cita->mascota->propietario->nombre ?? '—' }}
                    </div>
                    <div class="cita-motivo">{{ $cita->tipo_servicio ?? $cita->tipo_cita }}</div>
                    <div class="cita-medico">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        {{ $cita->nombre_medico ?? ($cita->profesional->nombre ?? '—') }}
                    </div>
                </div>
                <div class="cita-actions">
                    @php
                        $statusClass = [
                            'Programada' => 'status-pending',
                            'Completada' => 'status-confirmed',
                            'Cancelada' => 'status-cancelled'
                        ][$cita->estado] ?? 'status-pending';
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $cita->estado }}</span>
                    <button type="button" class="btn-action btn-view" onclick="verCita({{ $cita->id_cita }})">Ver</button>
                    <button type="button" class="btn-action btn-edit" onclick="editarCita({{ $cita->id_cita }})">Editar</button>
                    @if($cita->estado === 'Programada')
                    <button type="button" class="btn-action btn-complete" onclick="completarCita({{ $cita->id_cita }})">Completar</button>
                    <button type="button" class="btn-action btn-cancel" onclick="cancelarCita({{ $cita->id_cita }})">Cancelar</button>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <p>No hay citas para hoy</p>
            </div>
            @endforelse
        </div>

        <div class="citas-list-container" id="citas-proximas-content" style="display: none;">
            @forelse(($citasProximas ?? []) as $cita)
            <div class="cita-item" data-estado="{{ $cita->estado }}" data-medico-rfc="{{ $cita->rfc_profesional }}">
                <div class="cita-time">{{ $cita->fecha->format('Y-m-d') }} {{ substr($cita->horario,0,5) }}</div>
                <div class="cita-info">
                    <div class="cita-paciente">{{ $cita->nombre_mascota ?? ($cita->mascota->nombre ?? 'Mascota') }}</div>
                    <div class="cita-medico">{{ $cita->nombre_medico ?? ($cita->profesional->nombre ?? '—') }}</div>
                </div>
                <div class="cita-actions">
                    @php
                        $statusClass = [
                            'Programada' => 'status-pending',
                            'Completada' => 'status-confirmed',
                            'Cancelada' => 'status-cancelled'
                        ][$cita->estado] ?? 'status-pending';
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $cita->estado }}</span>
                    <button type="button" class="btn-action btn-view" onclick="verCita({{ $cita->id_cita }})">Ver</button>
                    <button type="button" class="btn-action btn-edit" onclick="editarCita({{ $cita->id_cita }})">Editar</button>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No hay citas próximas programadas</p>
            </div>
            @endforelse
        </div>

        <div class="citas-list-container" id="citas-pasadas-content" style="display: none;">
            @forelse(($citasPasadas ?? []) as $cita)
            <div class="cita-item" data-estado="{{ $cita->estado }}" data-medico-rfc="{{ $cita->rfc_profesional }}">
                <div class="cita-time">{{ $cita->fecha->format('Y-m-d') }} {{ substr($cita->horario,0,5) }}</div>
                <div class="cita-info">
                    <div class="cita-paciente">{{ $cita->nombre_mascota ?? ($cita->mascota->nombre ?? 'Mascota') }}</div>
                    <div class="cita-medico">{{ $cita->nombre_medico ?? ($cita->profesional->nombre ?? '—') }}</div>
                </div>
                <div class="cita-actions">
                    <span class="status-badge">{{ $cita->estado }}</span>
                    <button type="button" class="btn-action btn-view" onclick="verCita({{ $cita->id_cita }})">Ver</button>
                    <button type="button" class="btn-action btn-edit" onclick="editarCita({{ $cita->id_cita }})">Editar</button>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No hay citas pasadas para mostrar</p>
            </div>
            @endforelse
        </div>

        <div class="citas-list-container" id="citas-todas-content" style="display: none;">
            @forelse(($citasTodas ?? []) as $cita)
            <div class="cita-item" data-estado="{{ $cita->estado }}" data-medico-rfc="{{ $cita->rfc_profesional }}">
                <div class="cita-time">{{ $cita->fecha->format('Y-m-d') }} {{ substr($cita->horario,0,5) }}</div>
                <div class="cita-info">
                    <div class="cita-paciente">{{ $cita->nombre_mascota ?? ($cita->mascota->nombre ?? 'Mascota') }}</div>
                    <div class="cita-medico">{{ $cita->nombre_medico ?? ($cita->profesional->nombre ?? '—') }}</div>
                </div>
                <div class="cita-actions">
                    <span class="status-badge">{{ $cita->estado }}</span>
                    <button type="button" class="btn-action btn-view" onclick="verCita({{ $cita->id_cita }})">Ver</button>
                    <button type="button" class="btn-action btn-edit" onclick="editarCita({{ $cita->id_cita }})">Editar</button>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No hay citas para mostrar</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal para nueva/editar cita -->
    <div id="modal-cita" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-cita-titulo">Nueva Cita</h3>
                <button class="modal-close" onclick="cerrarModalCita()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-cita" method="POST" action="{{ route('citas.store') }}" novalidate>
                    @csrf
                    <input type="hidden" id="cita-id" name="id">
                    
                    <div class="form-section">
                        <h4 class="form-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Información del Paciente
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                                                <label for="cita-mascota">Mascota *</label>
                                                                <select id="cita-mascota" name="id_mascota" class="form-control" required>
                                                                        <option value="">Seleccionar mascota</option>
                                                                        @isset($mascotas)
                                                                            @foreach($mascotas as $m)
                                                                                <option value="{{ $m->id_mascota }}">{{ $m->nombre }}</option>
                                                                            @endforeach
                                                                        @endisset
                                                                </select>
                                                                <small class="field-error" id="error-cita-mascota"></small>
                            </div>
                            <div class="form-group">
                                                                <label for="cita-medico">Profesional *</label>
                                                                <select id="cita-medico" name="rfc_profesional" class="form-control" required>
                                                                        <option value="">Seleccionar profesional</option>
                                                                        @isset($profesionales)
                                                                            @foreach($profesionales as $p)
                                                                                <option value="{{ $p->rfc }}">{{ $p->nombre }}{{ isset($p->activo) && !$p->activo ? ' (inactivo)' : '' }}</option>
                                                                            @endforeach
                                                                        @endisset
                                                                </select>
                                                                <small class="field-error" id="error-cita-medico"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4 class="form-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Fecha y Hora
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cita-fecha">Fecha *</label>
                                <input type="date" id="cita-fecha" name="fecha" class="form-control" required>
                                <small class="field-hint">Puedes agendar citas para hoy o fechas futuras.</small>
                                <small class="field-error" id="error-cita-fecha"></small>
                            </div>
                            <div class="form-group">
                                <label for="cita-horario">Hora *</label>
                                <input type="time" id="cita-horario" name="horario" class="form-control">
                                <small class="field-error" id="error-cita-horario"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4 class="form-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Detalles de la Cita
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cita-tipo_cita">Tipo de cita *</label>
                                <select id="cita-tipo_cita" name="tipo_cita" class="form-control" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="Consulta">Consulta</option>
                                    <option value="Urgencia">Urgencia</option>
                                    <option value="Cirugía">Cirugía</option>
                                    <option value="Estética">Estética</option>
                                </select>
                                <small class="field-error" id="error-cita-tipo_cita"></small>
                            </div>
                            <div class="form-group">
                                <label for="cita-estado">Estado *</label>
                                <select id="cita-estado" name="estado" class="form-control" required>
                                    <option value="Programada">Programada</option>
                                    <option value="Completada">Completada</option>
                                    <option value="Cancelada">Cancelada</option>
                                </select>
                                <small class="field-error" id="error-cita-estado"></small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cita-tipo_servicio">Servicio</label>
                                <input type="text" id="cita-tipo_servicio" name="tipo_servicio" class="form-control" placeholder="Consulta general, vacunación, etc.">
                            </div>
                            <div class="form-group">
                                <label for="cita-tarifa">Tarifa</label>
                                <input type="number" step="0.01" min="0" id="cita-tarifa" name="tarifa" class="form-control" placeholder="0.00">
                                <small class="field-error" id="error-cita-tarifa"></small>
                            </div>
                            <div class="form-group">
                                <label for="cita-peso">Peso de la mascota (kg)</label>
                                <input type="number" step="0.01" min="0" id="cita-peso" name="peso_mascota" class="form-control" placeholder="0.00">
                                <small class="field-error" id="error-cita-peso"></small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cita-observaciones">Observaciones</label>
                            <textarea id="cita-observaciones" name="observaciones" class="form-control" rows="3" placeholder="Agregue observaciones adicionales..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer modal-footer-cita">
                <button type="button" id="btn-eliminar-cita" class="btn-danger-outline" style="display: none;" onclick="eliminarCita()">Eliminar</button>
                <div class="modal-footer-actions">
                    <button type="button" class="btn-secondary" onclick="cerrarModalCita()">Cancelar</button>
                    <button type="button" class="btn-primary" onclick="guardarCita()">Guardar Cita</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal ver cita -->
    <div id="modal-ver-cita" class="modal" style="display: none;">
        <div class="modal-content modal-detail-content">
            <div class="modal-header">
                <div>
                    <h3>Detalle de la Cita</h3>
                    <p class="detail-subtitle" id="ver-cita-fecha-hora">—</p>
                </div>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="detail-grid">
                    <div class="detail-card">
                        <span class="detail-label">Mascota</span>
                        <strong class="detail-value" id="ver-cita-mascota">—</strong>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">Propietario</span>
                        <strong class="detail-value" id="ver-cita-propietario">—</strong>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">Profesional</span>
                        <strong class="detail-value" id="ver-cita-profesional">—</strong>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">Fecha</span>
                        <strong class="detail-value" id="ver-cita-fecha">—</strong>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">Hora</span>
                        <strong class="detail-value" id="ver-cita-horario">—</strong>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">Tipo</span>
                        <strong class="detail-value" id="ver-cita-tipo">—</strong>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">Servicio</span>
                        <strong class="detail-value" id="ver-cita-servicio">—</strong>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">Estado</span>
                        <strong class="detail-value" id="ver-cita-estado">—</strong>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">Tarifa</span>
                        <strong class="detail-value" id="ver-cita-tarifa">—</strong>
                    </div>
                </div>
                <div class="detail-section">
                    <h4>Observaciones</h4>
                    <p class="detail-text" id="ver-cita-observaciones">—</p>
                </div>
            </div>
            <div class="modal-footer modal-footer-detail">
                <button type="button" class="btn-secondary" id="btn-cerrar-ver-cita">Cerrar</button>
                <button type="button" class="btn-primary" id="btn-editar-desde-ver-cita">Editar Cita</button>
            </div>
        </div>
    </div>

    <!-- Modal calendario mensual -->
    <div id="modal-calendario-citas" class="modal" style="display: none;">
        <div class="modal-content modal-calendario-content">
            <div class="modal-header">
                <div>
                    <h3>Calendario de Citas</h3>
                    <p class="detail-subtitle" id="calendario-hoy-label">Hoy: —</p>
                </div>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="calendario-toolbar">
                    <button type="button" class="btn-icon" id="btn-calendario-anterior" aria-label="Mes anterior">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <h4 id="calendario-mes-titulo" class="calendario-mes-titulo">—</h4>
                    <button type="button" class="btn-icon" id="btn-calendario-siguiente" aria-label="Mes siguiente">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
                <div id="calendario-grid" class="calendario-grid"></div>
                <div id="calendario-dia-detalle" class="calendario-dia-detalle" style="display: none;">
                    <h4 id="calendario-dia-titulo" class="calendario-dia-titulo">—</h4>
                    <div id="calendario-dia-lista" class="calendario-dia-lista"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="btn-cerrar-calendario">Cerrar</button>
            </div>
        </div>
    </div>
</section>

<script>
window.CITAS_CONFIG = {
    baseUrl: @json(url('recepcion/citas')),
    storeUrl: @json(route('citas.store')),
    calendarioUrl: @json(route('citas.calendario')),
    hoy: @json(now()->format('Y-m-d')),
    citasCalendario: @json($citasCalendario ?? [])
};
</script>
<script src="{{ asset('js/recepcion/form-validation.js') }}"></script>
<script src="{{ asset('js/recepcion/citas.js') }}"></script>
@endsection