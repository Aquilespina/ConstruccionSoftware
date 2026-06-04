/**
 * Módulo de gestión de citas — recepción
 */
(function () {
    const config = window.CITAS_CONFIG || {};
    const baseUrl = config.baseUrl || '/recepcion/citas';
    let citasCalendario = Array.isArray(config.citasCalendario) ? config.citasCalendario : [];
    let calendarioMes = null;
    let calendarioAnio = null;
    let citaDetalleActualId = null;

    const MESES = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
    ];

    const DIAS_SEMANA = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    function opcionesFetch(opciones = {}) {
        const { headers: extraHeaders, ...rest } = opciones;
        return {
            credentials: 'include',
            ...rest,
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                ...(extraHeaders || {}),
            },
        };
    }

    async function respuestaJson(response) {
        const contentType = response.headers.get('content-type') || '';
        if (!response.ok) {
            if (contentType.includes('application/json')) {
                const err = await response.json();
                if (err.errors) {
                    const msgs = Object.values(err.errors).flat();
                    const error = new Error(msgs.join('\n') || err.message);
                    error.validationErrors = err.errors;
                    throw error;
                }
                throw new Error(err.message || `Error ${response.status}`);
            }
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }
        if (!contentType.includes('application/json')) {
            throw new Error('Respuesta no válida del servidor.');
        }
        return response.json();
    }

    function mostrarErrorCampo(fieldId, mensaje) {
        if (typeof FormValidation !== 'undefined') {
            FormValidation.mostrarErrorCampo(fieldId, mensaje);
        }
    }

    function limpiarErrorCampo(fieldId) {
        if (typeof FormValidation !== 'undefined') {
            FormValidation.limpiarErrorCampo(fieldId);
        }
    }

    function limpiarErroresCita() {
        [
            'cita-mascota', 'cita-medico', 'cita-fecha', 'cita-horario',
            'cita-tipo_cita', 'cita-estado', 'cita-tipo_servicio',
        ].forEach(limpiarErrorCampo);
    }

    function horaActualHHMM() {
        const now = new Date();
        return `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    }

    function validarFormularioCita() {
        const form = document.getElementById('form-cita');
        let valido = true;

        const campos = [
            { id: 'cita-mascota', msg: 'Seleccione una mascota.' },
            { id: 'cita-medico', msg: 'Seleccione un profesional.' },
            { id: 'cita-fecha', msg: 'La fecha es obligatoria.' },
            { id: 'cita-horario', msg: 'La hora es obligatoria.' },
            { id: 'cita-tipo_cita', msg: 'Seleccione el tipo de cita.' },
            { id: 'cita-estado', msg: 'Seleccione el estado.' },
        ];

        campos.forEach(({ id, msg }) => {
            const el = document.getElementById(id);
            if (!el || !el.value.trim()) {
                mostrarErrorCampo(id, msg);
                valido = false;
            } else {
                limpiarErrorCampo(id);
            }
        });

        const fechaInput = document.getElementById('cita-fecha');
        const horarioInput = document.getElementById('cita-horario');
        const citaId = document.getElementById('cita-id')?.value;

        if (fechaInput?.value && !citaId) {
            if (fechaInput.value < (config.hoy || '')) {
                mostrarErrorCampo('cita-fecha', 'No puede programar citas en fechas pasadas.');
                valido = false;
            } else if (fechaInput.value === config.hoy && horarioInput?.value) {
                if (horarioInput.value <= horaActualHHMM()) {
                    mostrarErrorCampo('cita-horario', 'La hora debe ser posterior a la hora actual.');
                    valido = false;
                }
            }
        }

        return valido && (form ? form.checkValidity() : false);
    }

    function actualizarMinHorario() {
        const fechaInput = document.getElementById('cita-fecha');
        const horarioInput = document.getElementById('cita-horario');
        const citaId = document.getElementById('cita-id')?.value;
        if (!fechaInput || !horarioInput || citaId) return;

        if (fechaInput.value === config.hoy) {
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            horarioInput.min = `${hh}:${mm}`;
        } else {
            horarioInput.removeAttribute('min');
        }
    }

    function initModuloCitas() {
        const tabs = document.querySelectorAll('.citas-tabs .tab-button');
        const tabsContainer = document.querySelector('.citas-tabs');

        tabs.forEach((btn) => {
            btn.addEventListener('click', function () {
                const target = this.getAttribute('data-tab');
                tabs.forEach((t) => t.classList.remove('active'));
                this.classList.add('active');
                if (tabsContainer) {
                    tabsContainer.setAttribute('data-active-tab', target);
                }
                document.querySelectorAll('.citas-list-container').forEach((c) => {
                    c.style.display = 'none';
                });
                const targetContent = document.getElementById(`citas-${target}-content`);
                if (targetContent) {
                    targetContent.style.display = 'block';
                }
                filtrarCitas();
            });
        });

        document.getElementById('btn-nueva-cita')?.addEventListener('click', abrirModalCita);

        const searchInput = document.getElementById('search-citas');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(filtrarCitas, 300));
        }
        document.getElementById('filter-estado-cita')?.addEventListener('change', filtrarCitas);
        document.getElementById('filter-medico')?.addEventListener('change', filtrarCitas);

        const fechaInput = document.getElementById('cita-fecha');
        if (fechaInput && config.hoy) {
            if (!fechaInput.value) {
                fechaInput.value = config.hoy;
            }
            fechaInput.min = config.hoy;
            fechaInput.addEventListener('change', actualizarMinHorario);
        }

        setupModalCitas();
        setupModalCalendario();
        setupModalVerCita();

        const hoy = new Date();
        calendarioMes = hoy.getMonth() + 1;
        calendarioAnio = hoy.getFullYear();
    }

    function setupModalCitas() {
        const modal = document.getElementById('modal-cita');
        if (!modal) return;

        modal.querySelector('.modal-close')?.addEventListener('click', cerrarModalCita);
        modal.querySelector('.modal-footer .btn-secondary')?.addEventListener('click', cerrarModalCita);
        modal.querySelector('.modal-content')?.addEventListener('click', (e) => e.stopPropagation());
        modal.addEventListener('click', (e) => {
            if (e.target === modal) cerrarModalCita();
        });
    }

    function setupModalCalendario() {
        const modal = document.getElementById('modal-calendario-citas');
        if (!modal) return;

        modal.querySelector('.modal-close')?.addEventListener('click', cerrarCalendario);
        modal.querySelector('#btn-cerrar-calendario')?.addEventListener('click', cerrarCalendario);
        document.getElementById('btn-calendario-anterior')?.addEventListener('click', () => cambiarMesCalendario(-1));
        document.getElementById('btn-calendario-siguiente')?.addEventListener('click', () => cambiarMesCalendario(1));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) cerrarCalendario();
        });
    }

    function setupModalVerCita() {
        const modal = document.getElementById('modal-ver-cita');
        if (!modal) return;

        modal.querySelector('.modal-close')?.addEventListener('click', cerrarModalVerCita);
        document.getElementById('btn-cerrar-ver-cita')?.addEventListener('click', cerrarModalVerCita);
        document.getElementById('btn-editar-desde-ver-cita')?.addEventListener('click', editarCitaDesdeDetalle);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) cerrarModalVerCita();
        });
    }

    function abrirModalCita() {
        const modal = document.getElementById('modal-cita');
        const titulo = document.getElementById('modal-cita-titulo');
        const form = document.getElementById('form-cita');
        if (!modal || !form) return;

        titulo.textContent = 'Nueva Cita';
        form.reset();
        document.getElementById('cita-id').value = '';
        limpiarErroresCita();

        const fechaInput = document.getElementById('cita-fecha');
        if (fechaInput && config.hoy) {
            fechaInput.value = config.hoy;
            fechaInput.min = config.hoy;
        }

        actualizarMinHorario();
        document.getElementById('cita-estado').value = 'Programada';
        document.getElementById('btn-eliminar-cita').style.display = 'none';

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
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

        if (!validarFormularioCita()) {
            showNotification('Revise los campos marcados antes de guardar.', 'error');
            return;
        }

        const citaId = document.getElementById('cita-id').value;
        const formData = new FormData(form);
        let url = config.storeUrl || form.action;

        if (citaId) {
            url = `${baseUrl}/${encodeURIComponent(citaId)}`;
            formData.append('_method', 'PUT');
            const fechaInput = document.getElementById('cita-fecha');
            if (fechaInput) fechaInput.removeAttribute('min');
        }

        try {
            const response = await fetch(url, opcionesFetch({ method: 'POST', body: formData }));
            const data = await respuestaJson(response);
            showNotification(data.message || 'Cita guardada', 'success');
            cerrarModalCita();
            setTimeout(() => location.reload(), 800);
        } catch (e) {
            if (e.validationErrors) {
                Object.entries(e.validationErrors).forEach(([campo, msgs]) => {
                    const mapa = {
                        id_mascota: 'cita-mascota',
                        rfc_profesional: 'cita-medico',
                        fecha: 'cita-fecha',
                        horario: 'cita-horario',
                        tipo_cita: 'cita-tipo_cita',
                        estado: 'cita-estado',
                    };
                    const fieldId = mapa[campo];
                    if (fieldId) {
                        mostrarErrorCampo(fieldId, Array.isArray(msgs) ? msgs[0] : msgs);
                    }
                });
            }
            showNotification('Error al guardar la cita: ' + e.message, 'error');
        }
    }

    async function editarCita(id) {
        const modal = document.getElementById('modal-cita');
        const titulo = document.getElementById('modal-cita-titulo');
        if (!modal || !titulo) return;

        titulo.textContent = 'Editar Cita';
        document.getElementById('cita-id').value = id;

        try {
            const response = await fetch(`${baseUrl}/${encodeURIComponent(id)}`, opcionesFetch());
            const result = await respuestaJson(response);
            const cita = result.data || result;

            document.getElementById('cita-mascota').value = cita.id_mascota || '';
            document.getElementById('cita-medico').value = cita.rfc_profesional || '';

            const fechaVal = cita.fecha
                ? (typeof cita.fecha === 'string' ? cita.fecha.substring(0, 10) : '')
                : '';
            const fechaInput = document.getElementById('cita-fecha');
            fechaInput.value = fechaVal;
            fechaInput.removeAttribute('min');

            document.getElementById('cita-horario').value = (cita.horario || '').substring(0, 5);
            document.getElementById('cita-tipo_cita').value = cita.tipo_cita || '';
            document.getElementById('cita-estado').value = cita.estado || 'Programada';
            document.getElementById('cita-tipo_servicio').value = cita.tipo_servicio || '';
            document.getElementById('cita-tarifa').value = cita.tarifa ?? '';
            document.getElementById('cita-peso').value = cita.peso_mascota ?? '';
            document.getElementById('cita-observaciones').value = cita.observaciones || '';

            document.getElementById('btn-eliminar-cita').style.display = 'inline-flex';
            limpiarErroresCita();

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        } catch (error) {
            showNotification('Error al cargar los datos de la cita', 'error');
        }
    }

    async function verCita(id) {
        citaDetalleActualId = id;
        const modal = document.getElementById('modal-ver-cita');
        if (modal) modal.style.display = 'flex';

        try {
            const response = await fetch(`${baseUrl}/${encodeURIComponent(id)}`, opcionesFetch());
            const result = await respuestaJson(response);
            const cita = result.data || result;
            citaDetalleActualId = cita.id_cita || id;

            document.getElementById('ver-cita-mascota').textContent = cita.mascota?.nombre || cita.nombre_mascota || '—';
            document.getElementById('ver-cita-propietario').textContent = cita.mascota?.propietario?.nombre || '—';
            document.getElementById('ver-cita-profesional').textContent = cita.profesional?.nombre || cita.nombre_medico || '—';
            document.getElementById('ver-cita-fecha').textContent = formatearFechaLegible(cita.fecha);
            document.getElementById('ver-cita-horario').textContent = (cita.horario || '').substring(0, 5);
            document.getElementById('ver-cita-tipo').textContent = cita.tipo_cita || '—';
            document.getElementById('ver-cita-servicio').textContent = cita.tipo_servicio || '—';
            document.getElementById('ver-cita-estado').textContent = cita.estado || '—';
            document.getElementById('ver-cita-tarifa').textContent = cita.tarifa != null ? `$${cita.tarifa}` : '—';
            document.getElementById('ver-cita-observaciones').textContent = cita.observaciones || 'Sin observaciones';

            const fechaLegible = formatearFechaLegible(cita.fecha);
            const hora = (cita.horario || '').substring(0, 5);
            const subtitulo = document.getElementById('ver-cita-fecha-hora');
            if (subtitulo) {
                subtitulo.textContent = `${fechaLegible} · ${hora}`;
            }
        } catch (e) {
            showNotification('Error al cargar la cita', 'error');
            cerrarModalVerCita();
        }
    }

    function cerrarModalVerCita() {
        document.getElementById('modal-ver-cita').style.display = 'none';
        citaDetalleActualId = null;
    }

    function editarCitaDesdeDetalle() {
        const id = citaDetalleActualId;
        if (!id) return;
        cerrarModalVerCita();
        editarCita(id);
    }

    async function cambiarEstadoCita(id, estado) {
        try {
            const response = await fetch(
                `${baseUrl}/${encodeURIComponent(id)}/estado`,
                opcionesFetch({
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ estado }),
                })
            );
            const data = await respuestaJson(response);
            showNotification(data.message || 'Estado actualizado', 'success');
            setTimeout(() => location.reload(), 600);
        } catch (e) {
            showNotification('Error: ' + e.message, 'error');
        }
    }

    function completarCita(id) {
        if (confirm('¿Marcar esta cita como completada?')) {
            cambiarEstadoCita(id, 'Completada');
        }
    }

    function cancelarCita(id) {
        if (confirm('¿Cancelar esta cita?')) {
            cambiarEstadoCita(id, 'Cancelada');
        }
    }

    async function eliminarCita(id) {
        const citaId = id || document.getElementById('cita-id')?.value;
        if (!citaId) return;
        if (!confirm('¿Eliminar esta cita permanentemente?')) return;

        try {
            const response = await fetch(
                `${baseUrl}/${encodeURIComponent(citaId)}`,
                opcionesFetch({ method: 'DELETE' })
            );
            const data = await respuestaJson(response);
            showNotification(data.message || 'Cita eliminada', 'success');
            cerrarModalCita();
            setTimeout(() => location.reload(), 600);
        } catch (e) {
            showNotification('Error al eliminar: ' + e.message, 'error');
        }
    }

    function abrirCalendario() {
        const modal = document.getElementById('modal-calendario-citas');
        if (!modal) return;

        const hoy = new Date();
        if (!calendarioMes) calendarioMes = hoy.getMonth() + 1;
        if (!calendarioAnio) calendarioAnio = hoy.getFullYear();

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        renderizarCalendario();
    }

    function cerrarCalendario() {
        const modal = document.getElementById('modal-calendario-citas');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
        document.getElementById('calendario-dia-detalle').style.display = 'none';
    }

    async function cambiarMesCalendario(delta) {
        calendarioMes += delta;
        if (calendarioMes > 12) {
            calendarioMes = 1;
            calendarioAnio += 1;
        } else if (calendarioMes < 1) {
            calendarioMes = 12;
            calendarioAnio -= 1;
        }

        try {
            const response = await fetch(
                `${baseUrl}/calendario?mes=${calendarioMes}&anio=${calendarioAnio}`,
                opcionesFetch()
            );
            const result = await respuestaJson(response);
            citasCalendario = result.data || [];
            if (result.hoy) config.hoy = result.hoy;
        } catch (e) {
            console.warn('Usando citas en memoria:', e.message);
        }

        renderizarCalendario();
    }

    function obtenerCitasPorFecha(fechaStr) {
        return citasCalendario.filter((c) => c.fecha === fechaStr);
    }

    function renderizarCalendario() {
        const grid = document.getElementById('calendario-grid');
        const titulo = document.getElementById('calendario-mes-titulo');
        const hoyLabel = document.getElementById('calendario-hoy-label');
        if (!grid || !titulo) return;

        titulo.textContent = `${MESES[calendarioMes - 1]} ${calendarioAnio}`;
        if (hoyLabel && config.hoy) {
            const hoyFmt = formatearFechaLegible(config.hoy);
            hoyLabel.textContent = `Hoy: ${hoyFmt}`;
        }

        grid.innerHTML = '';

        DIAS_SEMANA.forEach((dia) => {
            const header = document.createElement('div');
            header.className = 'calendario-dia-header';
            header.textContent = dia;
            grid.appendChild(header);
        });

        const primerDia = new Date(calendarioAnio, calendarioMes - 1, 1);
        const ultimoDia = new Date(calendarioAnio, calendarioMes, 0).getDate();
        const inicioSemana = primerDia.getDay();

        for (let i = 0; i < inicioSemana; i++) {
            const vacio = document.createElement('div');
            vacio.className = 'calendario-celda calendario-celda-vacia';
            grid.appendChild(vacio);
        }

        for (let dia = 1; dia <= ultimoDia; dia++) {
            const fechaStr = `${calendarioAnio}-${String(calendarioMes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
            const citasDia = obtenerCitasPorFecha(fechaStr);
            const celda = document.createElement('button');
            celda.type = 'button';
            celda.className = 'calendario-celda';
            if (fechaStr === config.hoy) {
                celda.classList.add('calendario-hoy');
            }
            if (citasDia.length > 0) {
                celda.classList.add('calendario-con-citas');
            }

            celda.innerHTML = `
                <span class="calendario-numero">${dia}</span>
                ${citasDia.length ? `<span class="calendario-badge">${citasDia.length}</span>` : ''}
            `;

            celda.addEventListener('click', () => mostrarDetalleDiaCalendario(fechaStr, citasDia));
            grid.appendChild(celda);
        }
    }

    function mostrarDetalleDiaCalendario(fechaStr, citasDia) {
        const panel = document.getElementById('calendario-dia-detalle');
        const titulo = document.getElementById('calendario-dia-titulo');
        const lista = document.getElementById('calendario-dia-lista');
        if (!panel || !titulo || !lista) return;

        titulo.textContent = formatearFechaLegible(fechaStr);
        lista.innerHTML = '';

        if (!citasDia.length) {
            lista.innerHTML = '<p class="calendario-sin-citas">No hay citas este día.</p>';
        } else {
            citasDia.forEach((cita) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'calendario-cita-item';
                item.innerHTML = `
                    <strong>${cita.horario}</strong> — ${cita.mascota}
                    <span>${cita.profesional} · ${cita.estado}</span>
                `;
                item.addEventListener('click', () => {
                    cerrarCalendario();
                    verCita(cita.id_cita);
                });
                lista.appendChild(item);
            });
        }

        panel.style.display = 'block';
    }

    function formatearFechaLegible(fecha) {
        if (!fecha) return '—';
        const str = typeof fecha === 'string' ? fecha.substring(0, 10) : fecha;
        const [y, m, d] = str.split('-');
        return `${d}/${m}/${y}`;
    }

    function filtrarCitas() {
        const searchTerm = document.getElementById('search-citas')?.value.toLowerCase().trim() || '';
        const estado = document.getElementById('filter-estado-cita')?.value || '';
        const medico = document.getElementById('filter-medico')?.value || '';

        const activeTab = document.querySelector('.citas-tabs .tab-button.active');
        const tabContent = activeTab
            ? document.getElementById(`citas-${activeTab.getAttribute('data-tab')}-content`)
            : null;
        if (!tabContent) return;

        const items = tabContent.querySelectorAll('.cita-item');
        let visibles = 0;

        items.forEach((cita) => {
            // Buscar solo en la zona de información, no en botones de acción
            const infoText = [
                cita.querySelector('.cita-paciente')?.textContent || '',
                cita.querySelector('.cita-propietario')?.textContent || '',
                cita.querySelector('.cita-medico')?.textContent || '',
                cita.querySelector('.cita-motivo')?.textContent || '',
                cita.querySelector('.cita-time')?.textContent || '',
            ].join(' ').toLowerCase();

            const estadoCita = cita.getAttribute('data-estado') || '';
            const medicoRfc = cita.getAttribute('data-medico-rfc') || '';

            const ok =
                (!searchTerm || infoText.includes(searchTerm)) &&
                (!estado || estadoCita === estado) &&
                (!medico || medicoRfc === medico);

            cita.style.display = ok ? 'flex' : 'none';
            if (ok) visibles++;
        });

        // Mensaje de sin resultados (solo cuando había items pero el filtro los ocultó todos)
        let sinResultados = tabContent.querySelector('.citas-sin-resultados');
        if (items.length > 0) {
            if (visibles === 0) {
                if (!sinResultados) {
                    sinResultados = document.createElement('div');
                    sinResultados.className = 'citas-sin-resultados empty-state';
                    sinResultados.innerHTML = '<p>No hay citas que coincidan con la búsqueda.</p>';
                    tabContent.appendChild(sinResultados);
                }
                sinResultados.style.display = 'block';
            } else if (sinResultados) {
                sinResultados.style.display = 'none';
            }
        }
    }

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function showNotification(message, type) {
        alert(message);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const modulo = document.getElementById('mod-citas');
        if (modulo?.classList.contains('active')) {
            initModuloCitas();
        }
        window.addEventListener('moduleChanged', (e) => {
            if (e.detail?.module === 'citas') {
                setTimeout(initModuloCitas, 100);
            }
        });
    });

    window.abrirModalCita = abrirModalCita;
    window.cerrarModalCita = cerrarModalCita;
    window.guardarCita = guardarCita;
    window.editarCita = editarCita;
    window.verCita = verCita;
    window.completarCita = completarCita;
    window.cancelarCita = cancelarCita;
    window.eliminarCita = eliminarCita;
    window.abrirCalendario = abrirCalendario;
})();
