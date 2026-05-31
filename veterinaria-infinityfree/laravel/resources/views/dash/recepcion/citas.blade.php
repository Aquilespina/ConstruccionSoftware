@extends('dash.recepcion')
@section('page-title', 'Citas')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/citas.css') }}">
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
                    <button class="btn-action btn-edit" onclick="editarCita({{ $cita->id_cita }})">Editar</button>
                    <button class="btn-action btn-complete" onclick="completarCita({{ $cita->id_cita }})">Completar</button>
                    <button class="btn-action btn-cancel" onclick="cancelarCita({{ $cita->id_cita }})">Cancelar</button>
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
                <form id="form-cita" method="POST" action="{{ route('citas.store') }}">
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
                            </div>
                            <div class="form-group">
                                                                <label for="cita-medico">Médico *</label>
                                                                <select id="cita-medico" name="rfc_profesional" class="form-control" required>
                                                                        <option value="">Seleccionar médico</option>
                                                                        @isset($profesionales)
                                                                            @foreach($profesionales as $p)
                                                                                <option value="{{ $p->rfc }}">{{ $p->nombre }}</option>
                                                                            @endforeach
                                                                        @endisset
                                                                </select>
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
                            </div>
                            <div class="form-group">
                                <label for="cita-horario">Hora *</label>
                                <input type="time" id="cita-horario" name="horario" class="form-control" required>
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
                            </div>
                            <div class="form-group">
                                <label for="cita-estado">Estado *</label>
                                <select id="cita-estado" name="estado" class="form-control" required>
                                    <option value="Programada">Programada</option>
                                    <option value="Completada">Completada</option>
                                    <option value="Cancelada">Cancelada</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cita-tipo_servicio">Servicio</label>
                                <input type="text" id="cita-tipo_servicio" name="tipo_servicio" class="form-control" placeholder="Consulta general, vacunación, etc.">
                            </div>
                            <div class="form-group">
                                <label for="cita-tarifa">Tarifa</label>
                                <input type="number" step="0.01" id="cita-tarifa" name="tarifa" class="form-control" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="cita-peso">Peso de la mascota (kg)</label>
                                <input type="number" step="0.01" id="cita-peso" name="peso_mascota" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cita-observaciones">Observaciones</label>
                            <textarea id="cita-observaciones" name="observaciones" class="form-control" rows="3" placeholder="Agregue observaciones adicionales..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalCita()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="guardarCita()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Guardar Cita
                </button>
            </div>
        </div>
    </div>
</section>

<script>
// Inicialización del módulo de citas
function initModuloCitas() {
    console.log('📅 Inicializando módulo de citas...');
    
    // Tab switching
    const tabs = document.querySelectorAll('.citas-tabs .tab-button');
    const tabsContainer = document.querySelector('.citas-tabs');
    
    tabs.forEach(btn => {
        btn.addEventListener('click', function() {
            const target = this.getAttribute('data-tab');
            
            // Update active tab
            tabs.forEach(tab => tab.classList.remove('active'));
            this.classList.add('active');
            
            // Update tabs container data attribute for the indicator
            tabsContainer.setAttribute('data-active-tab', target);
            
            // Show corresponding content
            document.querySelectorAll('.citas-list-container').forEach(container => {
                container.style.display = 'none';
            });
            
            const targetContent = document.getElementById(`citas-${target}-content`);
            if (targetContent) {
                targetContent.style.display = 'block';
            }
        });
    });

    // Nueva cita modal
    const btnNuevaCita = document.getElementById('btn-nueva-cita');
    if (btnNuevaCita) {
        btnNuevaCita.addEventListener('click', abrirModalCita);
    }

    // Filtros
    const searchInput = document.getElementById('search-citas');
    const filterEstado = document.getElementById('filter-estado-cita');
    const filterMedico = document.getElementById('filter-medico');

    if (searchInput) {
        searchInput.addEventListener('input', debounce(filtrarCitas, 300));
    }
    if (filterEstado) {
        filterEstado.addEventListener('change', filtrarCitas);
    }
    if (filterMedico) {
        filterMedico.addEventListener('change', filtrarCitas);
    }

    // Set today's date as default
    const fechaInput = document.getElementById('cita-fecha');
    if (fechaInput) {
        const today = new Date().toISOString().split('T')[0];
        fechaInput.value = today;
        fechaInput.min = today; // Prevent past dates
    }

    // Setup modal events
    setupModalCitas();
}

function setupModalCitas() {
    const modal = document.getElementById('modal-cita');
    if (!modal) {
        console.warn('Modal de cita no encontrado');
        return;
    }

    const modalClose = modal.querySelector('.modal-close');
    const modalCancel = modal.querySelector('.btn-secondary');
    
    if (modalClose) {
        modalClose.addEventListener('click', cerrarModalCita);
    }
    if (modalCancel) {
        modalCancel.addEventListener('click', cerrarModalCita);
    }

    // Prevent modal close when clicking inside modal content
    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalCita();
        }
    });
}

function abrirModalCita() {
    const modal = document.getElementById('modal-cita');
    const titulo = document.getElementById('modal-cita-titulo');
    const form = document.getElementById('form-cita');
    
    if (modal && titulo && form) {
        titulo.textContent = 'Nueva Cita';
        form.reset();
        document.getElementById('cita-id').value = '';
        
        // Set today's date
        const fechaInput = document.getElementById('cita-fecha');
        if (fechaInput) {
            const today = new Date().toISOString().split('T')[0];
            fechaInput.value = today;
        }
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function cerrarModalCita() {
    const modal = document.getElementById('modal-cita');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

async function guardarCita() {
    const form = document.getElementById('form-cita');
    if (!form) return;
    
    const citaId = document.getElementById('cita-id').value;
    const formData = new FormData(form);
    
    // Basic validation
    const requiredFields = ['id_mascota', 'rfc_profesional', 'fecha', 'horario', 'tipo_cita', 'estado'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input && !input.value.trim()) {
            isValid = false;
            highlightInvalidField(input);
        }
    });
    
    if (!isValid) {
        showNotification('Por favor, complete todos los campos obligatorios', 'error');
        return;
    }
    
    // Determine URL and method based on whether we're creating or updating (web routes)
    let url = form.action;
    const method = 'POST';
    if (citaId) {
        url = url.replace(/\/$/, '') + `/${citaId}`;
        formData.append('_method', 'PUT');
    }

    try {
        const response = await fetch(url, {
            method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        });
        const contentType = response.headers.get('content-type');
        if (!response.ok) {
            let msg = `Error ${response.status}`;
            if (contentType && contentType.includes('application/json')) {
                const err = await response.json();
                msg = err.message || JSON.stringify(err.errors || {});
            }
            throw new Error(msg);
        }
        const data = contentType && contentType.includes('application/json') ? await response.json() : {};
        showNotification(data.message || (citaId ? 'Cita actualizada' : 'Cita creada'), 'success');
        cerrarModalCita();
    } catch (e) {
        console.error(e);
        showNotification('Error al guardar la cita: ' + e.message, 'error');
    }
}

async function editarCita(id) {
    console.log(`Editando cita ID: ${id}`);
    const modal = document.getElementById('modal-cita');
    const titulo = document.getElementById('modal-cita-titulo');
    const form = document.getElementById('form-cita');
    
    if (!modal || !titulo || !form) return;
    
    titulo.textContent = 'Editar Cita';
    document.getElementById('cita-id').value = id;

    try {
        const baseUrl = '{{ url("recepcion/citas") }}';
        const response = await fetch(`${baseUrl}/${id}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!response.ok) {
            let message = `Error ${response.status}`;
            try {
                const err = await response.json();
                message = err.message || message;
            } catch {}
            throw new Error(message);
        }
        const result = await response.json();
        const cita = result.data || result;

        // Populate form with cita data
        document.getElementById('cita-mascota').value = cita.id_mascota || '';
        document.getElementById('cita-medico').value = cita.rfc_profesional || '';
        // fecha puede venir como YYYY-MM-DDTHH:MM:SS o carbon date; normalizamos
        const fechaVal = (cita.fecha && typeof cita.fecha === 'string') ? cita.fecha.substring(0,10) : (cita.fecha?.date ? cita.fecha.date.substring(0,10) : '');
        document.getElementById('cita-fecha').value = fechaVal;
        document.getElementById('cita-horario').value = (cita.horario || '').substring(0,5);
        document.getElementById('cita-tipo_cita').value = cita.tipo_cita || '';
        document.getElementById('cita-estado').value = cita.estado || 'Programada';
        document.getElementById('cita-tipo_servicio').value = cita.tipo_servicio || '';
        document.getElementById('cita-tarifa').value = cita.tarifa ?? '';
        document.getElementById('cita-peso').value = cita.peso_mascota ?? '';
        document.getElementById('cita-observaciones').value = cita.observaciones || '';

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    } catch (error) {
        showNotification('Error al cargar los datos de la cita', 'error');
        console.error('Error:', error);
    }
}

function completarCita(id) {
    if (confirm('¿Está seguro de que desea marcar esta cita como completada?')) {
        simulateAPICall(`/api/citas/${id}/completar`, { method: 'POST' })
            .then(data => {
                showNotification('Cita marcada como completada', 'success');
                // In a real app, you would update the UI here
            })
            .catch(error => {
                showNotification('Error al completar la cita', 'error');
                console.error('Error:', error);
            });
    }
}

function cancelarCita(id) {
    if (confirm('¿Está seguro de que desea cancelar esta cita?')) {
        simulateAPICall(`/api/citas/${id}/cancelar`, { method: 'POST' })
            .then(data => {
                showNotification('Cita cancelada correctamente', 'success');
                // In a real app, you would update the UI here
            })
            .catch(error => {
                showNotification('Error al cancelar la cita', 'error');
                console.error('Error:', error);
            });
    }
}

function abrirCalendario() {
    showNotification('Funcionalidad de calendario en desarrollo', 'info');
}

function filtrarCitas() {
    const searchTerm = document.getElementById('search-citas')?.value.toLowerCase() || '';
    const estado = document.getElementById('filter-estado-cita')?.value || '';
    const medico = document.getElementById('filter-medico')?.value || '';
    
    const activeTab = document.querySelector('.citas-tabs .tab-button.active');
    const tabContent = activeTab ? document.getElementById(`citas-${activeTab.getAttribute('data-tab')}-content`) : null;
    
    if (!tabContent) return;
    
    const citas = tabContent.querySelectorAll('.cita-item');
    
    citas.forEach(cita => {
        const textoCita = cita.textContent.toLowerCase();
        const estadoCita = cita.getAttribute('data-estado') || '';
        const medicoRfc = cita.getAttribute('data-medico-rfc') || '';
        
        const coincideBusqueda = !searchTerm || textoCita.includes(searchTerm);
        const coincideEstado = !estado || estadoCita === estado;
        const coincideMedico = !medico || medicoRfc === medico;
        
        if (coincideBusqueda && coincideEstado && coincideMedico) {
            cita.style.display = 'flex';
        } else {
            cita.style.display = 'none';
        }
    });
}

// Utilidades
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function highlightInvalidField(field) {
    field.style.borderColor = '#ef4444';
    field.addEventListener('input', function() {
        this.style.borderColor = '';
    }, { once: true });
}

function showNotification(message, type = 'info') {
    // Implementar sistema de notificaciones si es necesario
    alert(message);
}

function simulateAPICall(url, options = {}) {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            if (Math.random() > 0.1) {
                resolve({ 
                    success: true, 
                    message: 'Operación exitosa',
                    paciente_id: '1',
                    medico_id: '1',
                    fecha: '2024-01-15',
                    hora: '09:00',
                    motivo: 'consulta',
                    estado: 'pendiente',
                    observaciones: 'Primera consulta del paciente'
                });
            } else {
                reject(new Error('Error de servidor simulado'));
            }
        }, 1000);
    });
}

// Inicializar cuando el módulo esté activo
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el módulo de citas está activo
    const moduloCitas = document.getElementById('mod-citas');
    if (moduloCitas && moduloCitas.classList.contains('active')) {
        initModuloCitas();
    }
    
    // También escuchar cambios de módulo si estás usando el sistema de navegación SPA
    window.addEventListener('moduleChanged', function(e) {
        if (e.detail.module === 'citas') {
            setTimeout(initModuloCitas, 100);
        }
    });
});

// Exportar funciones para uso global
window.abrirModalCita = abrirModalCita;
window.cerrarModalCita = cerrarModalCita;
window.guardarCita = guardarCita;
window.editarCita = editarCita;
window.completarCita = completarCita;
window.cancelarCita = cancelarCita;
window.abrirCalendario = abrirCalendario;
</script>
@endsection