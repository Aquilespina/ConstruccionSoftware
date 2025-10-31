// Funciones para el módulo de Expedientes
document.addEventListener('DOMContentLoaded', function() {
    inicializarExpedientes();
});

function inicializarExpedientes() {
    // Event listeners
    document.getElementById('btn-nueva-mascota').addEventListener('click', abrirModalMascota);
    document.getElementById('btn-buscar-mascotas').addEventListener('click', aplicarFiltros);
    document.getElementById('search-mascotas').addEventListener('input', aplicarFiltros);
    document.getElementById('filter-especie').addEventListener('change', aplicarFiltros);
    document.getElementById('filter-estado').addEventListener('change', aplicarFiltros);
}

function getStatusClass(status) {
    const statusClasses = {
        'al-dia': 'status-active',
        'pendiente': 'status-pending',
        'vencida': 'status-inactive'
    };
    return statusClasses[status] || '';
}

function getVacunaText(status) {
    const statusTexts = {
        'al-dia': 'Al día',
        'pendiente': 'Pendiente',
        'vencida': 'Vencida'
    };
    return statusTexts[status] || status;
}

// Funciones de modales
function abrirModalMascota() {
    document.getElementById('modal-mascota').style.display = 'flex';
    cargarPropietarios();
}

function cerrarModalMascota() {
    document.getElementById('modal-mascota').style.display = 'none';
    document.getElementById('form-mascota').reset();
}

function abrirModalHistorial() {
    document.getElementById('modal-historial').style.display = 'flex';
}

function cerrarModalHistorial() {
    document.getElementById('modal-historial').style.display = 'none';
}

// Funciones de negocio
function guardarMascota() {
    const form = document.getElementById('form-mascota');
    const formData = new FormData(form);
    
    // Validación básica
    if (!formData.get('nombre') || !formData.get('especie') || !formData.get('propietario_id')) {
        alert('Por favor complete todos los campos obligatorios');
        return;
    }
    
    // Aquí iría la petición AJAX para guardar la mascota
    console.log('Guardando mascota:', Object.fromEntries(formData));
    
    // Cerrar modal (la lista de expedientes es server-rendered)
    cerrarModalMascota();
}

function verHistorial(mascotaId) {
    // Cargar historial de la mascota
    cargarHistorialMascota(mascotaId);
    abrirModalHistorial();
}

function abrirExpediente(mascotaId) {
    // Por ahora redirige al módulo de mascotas; se puede ajustar a una ruta específica si existe
    window.location.href = '/recepcion/mascotas';
}

function aplicarFiltros() {
    const termino = (document.getElementById('search-mascotas').value || '').toLowerCase();
    const especie = (document.getElementById('filter-especie').value || '').toLowerCase();
    // El filtro de estado se ignora por ahora (no hay un estado "activo/inactivo" en las filas)

    // Filtrar tarjetas (diseño actual)
    const cards = document.querySelectorAll('#mascotas-container .mascota-card');
    cards.forEach(card => {
        const name = (card.dataset.name || '');
        const owner = (card.dataset.owner || '');
        const rowEspecie = (card.dataset.especie || '');

        const matchTermino = !termino || name.includes(termino) || owner.includes(termino);
        const matchEspecie = !especie || rowEspecie === especie;

        card.style.display = (matchTermino && matchEspecie) ? '' : 'none';
    });

    // Compatibilidad: si existe una tabla, filtrarla también
    const filas = document.querySelectorAll('#expedientes-body tr');
    if (filas.length) {
        filas.forEach(fila => {
            const name = (fila.dataset.name || '');
            const owner = (fila.dataset.owner || '');
            const rowEspecie = (fila.dataset.especie || '');

            const matchTermino = !termino || name.includes(termino) || owner.includes(termino);
            const matchEspecie = !especie || rowEspecie === especie;

            fila.style.display = (matchTermino && matchEspecie) ? '' : 'none';
        });
    }
}

function cargarPropietarios() {
    // Simulación de carga de propietarios
    const select = document.getElementById('mascota-propietario');
    select.innerHTML = '<option value="">Seleccionar propietario</option>';
    
    const propietarios = [
        { id: 1, nombre: 'María Rodríguez' },
        { id: 2, nombre: 'Carlos Pérez' },
        { id: 3, nombre: 'Ana González' }
    ];
    
    propietarios.forEach(prop => {
        const option = document.createElement('option');
        option.value = prop.id;
        option.textContent = prop.nombre;
        select.appendChild(option);
    });
}

async function cargarHistorialMascota(mascotaId) {
        const titulo = document.getElementById('historial-titulo');
        const contenido = document.querySelector('.historial-content');
        titulo.textContent = `Historial Médico - Mascota #${mascotaId}`;
        contenido.innerHTML = `<div style="text-align:center;padding:16px;color:#6b7280">Cargando historial...</div>`;

        try {
                const resp = await fetch(`/api/recetas?mascota_id=${encodeURIComponent(mascotaId)}&per_page=100`, {
                        headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                const data = await resp.json();
                const recetas = Array.isArray(data.data) ? data.data : [];

                // Si hay al menos una receta, usa sus datos para encabezar
                let mascotaNombre = `Mascota #${mascotaId}`;
                let propietarioNombre = '';
                if (recetas.length > 0) {
                        mascotaNombre = recetas[0]?.mascota?.nombre || mascotaNombre;
                        propietarioNombre = recetas[0]?.mascota?.propietario || '';
                }
                titulo.textContent = `Historial Médico – ${mascotaNombre}`;

                // Render
                contenido.innerHTML = `
                    <div class="historial-header" style="text-align:center;margin-bottom:16px;">
                        <div style="font-size:1.1rem;font-weight:700;color:#111827;">${mascotaNombre}</div>
                        ${propietarioNombre ? `<div style="color:#6b7280;">Propietario: ${propietarioNombre}</div>` : ''}
                    </div>

                    <div class="historial-section">
                        <h4 style="text-align:center;">Recetas</h4>
                        ${recetas.length === 0 ? `
                            <div class="historial-item" style="text-align:center;color:#6b7280;">No hay recetas registradas</div>
                        ` : recetas.map(r => {
                                const estadoClass = (r.estado === 'expirada') ? 'status-expired' : (r.estado === 'completada' ? 'status-completed' : 'status-active');
                                return `
                                <div class="historial-item receta-card" style="max-width:800px;margin:12px auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                                    <div class="historial-item-header" style="display:flex;justify-content:space-between;align-items:center;">
                                        <span class="historial-item-title" style="font-weight:700;">${r.codigo || `REC-${String(r.id).padStart(3,'0')}`}</span>
                                        <span class="status-badge ${estadoClass}">${(r.estado || '').toUpperCase()}</span>
                                    </div>
                                    <div class="historial-item-content">
                                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px 16px;margin-bottom:8px;">
                                            <div><strong>Médico:</strong> ${r.medico?.nombre || 'N/A'}</div>
                                            <div><strong>Emisión:</strong> ${r.fecha_emision || '-'}</div>
                                            <div><strong>Vence:</strong> ${r.fecha_vencimiento || '-'}</div>
                                            <div><strong>Diagnóstico:</strong> ${r.diagnostico || '-'}</div>
                                        </div>
                                        <div>
                                            <strong>Medicamentos:</strong>
                                            <ul style="margin:6px 0 0 18px;">
                                                ${(r.medicamentos && r.medicamentos.length) ? r.medicamentos.map(m => {
                                                        const partes = [
                                                            m.dosis ? `Dosis: ${m.dosis}` : '',
                                                            m.frecuencia ? `Frecuencia: ${m.frecuencia}` : '',
                                                            m.duracion ? `Duración: ${m.duracion}` : ''
                                                        ].filter(Boolean).join(' • ');
                                                        return `<li><strong>${m.nombre}</strong>${partes ? ` – ${partes}` : ''}${m.instrucciones ? `.<br/><em>${m.instrucciones}</em>` : ''}</li>`;
                                                }).join('') : '<li>No hay medicamentos registrados</li>'}
                                            </ul>
                                        </div>
                                    </div>
                                </div>`;
                        }).join('')}
                    </div>
                `;
        } catch (e) {
                contenido.innerHTML = `<div style="text-align:center;padding:16px;color:#ef4444;">No se pudo cargar el historial: ${e.message}</div>`;
        }
}

// Cerrar modales al hacer click fuera
window.addEventListener('click', function(event) {
    const modalMascota = document.getElementById('modal-mascota');
    const modalHistorial = document.getElementById('modal-historial');
    
    if (event.target === modalMascota) {
        cerrarModalMascota();
    }
    if (event.target === modalHistorial) {
        cerrarModalHistorial();
    }
});