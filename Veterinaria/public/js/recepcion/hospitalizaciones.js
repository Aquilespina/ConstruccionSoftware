document.addEventListener('DOMContentLoaded', function () {

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Fecha de ingreso: readonly + valor actual ──────────────────────
    const fechaInput = document.getElementById('hospitalizacion-fecha-ingreso');
    if (fechaInput) {
        const ahora = new Date();
        const pad   = n => String(n).padStart(2, '0');
        fechaInput.value = `${ahora.getFullYear()}-${pad(ahora.getMonth()+1)}-${pad(ahora.getDate())}T${pad(ahora.getHours())}:${pad(ahora.getMinutes())}`;
        fechaInput.readOnly = true;
    }

    // ── Abrir modal nueva hospitalización ─────────────────────────────
    const btnNueva = document.getElementById('btn-nueva-hospitalizacion');
    if (btnNueva) btnNueva.addEventListener('click', abrirModalHospitalizacion);

    // ── Filtros automáticos ───────────────────────────────────────────
    const inputBuscar = document.getElementById('search-hospitalizaciones');
    const selectEstado = document.getElementById('filter-estado-hospitalizacion');
    if (inputBuscar)   inputBuscar.addEventListener('input', filtrarTabla);
    if (selectEstado)  selectEstado.addEventListener('change', filtrarTabla);

    // Actualizar label de cambio de estado al cambiar el select
    const editarEstadoSel = document.getElementById('editar-estado');
    if (editarEstadoSel) {
        editarEstadoSel.addEventListener('change', actualizarLabelCambio);
    }
});

// ── Filtro de tabla ────────────────────────────────────────────────────
function filtrarTabla() {
    const busqueda = (document.getElementById('search-hospitalizaciones')?.value ?? '').toLowerCase().trim();
    const estado   = document.getElementById('filter-estado-hospitalizacion')?.value ?? '';
    const filas    = document.querySelectorAll('#tabla-hospitalizaciones tbody tr:not(#hosp-sin-resultados)');
    let visibles   = 0;

    filas.forEach(fila => {
        const texto    = fila.textContent.toLowerCase();
        const estadoFila = fila.dataset.estado ?? '';
        const ok = (!busqueda || texto.includes(busqueda)) && (!estado || estadoFila === estado);
        fila.style.display = ok ? '' : 'none';
        if (ok) visibles++;
    });

    const sinResultados = document.getElementById('hosp-sin-resultados');
    if (sinResultados) sinResultados.style.display = visibles === 0 ? '' : 'none';
}

// ── Modal nueva hospitalización ────────────────────────────────────────
function abrirModalHospitalizacion() {
    // Actualizar la fecha al momento de abrir el modal
    const fechaInput = document.getElementById('hospitalizacion-fecha-ingreso');
    if (fechaInput) {
        const ahora = new Date();
        const pad   = n => String(n).padStart(2, '0');
        fechaInput.value = `${ahora.getFullYear()}-${pad(ahora.getMonth()+1)}-${pad(ahora.getDate())}T${pad(ahora.getHours())}:${pad(ahora.getMinutes())}`;
    }
    document.getElementById('modal-hospitalizacion').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModalHospitalizacion() {
    document.getElementById('modal-hospitalizacion').style.display = 'none';
    document.getElementById('form-hospitalizacion').reset();
    document.body.style.overflow = 'auto';
}

async function guardarHospitalizacion() {
    const form = document.getElementById('form-hospitalizacion');

    // Validación front: observaciones obligatoria
    const obs = document.getElementById('hospitalizacion-observaciones');
    if (!obs.value.trim()) {
        obs.focus();
        mostrarAlerta('Las observaciones son obligatorias.', 'error');
        return;
    }

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const btn = document.querySelector('#modal-hospitalizacion .btn-primary');
    const txtOriginal = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const formData = new FormData(form);
        const res  = await fetch('/recepcion/hospitalizaciones', {
            method: 'POST',
            body:   formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.success) {
            cerrarModalHospitalizacion();
            mostrarAlerta(data.message, 'success');
            setTimeout(() => location.reload(), 1200);
        } else {
            const errores = data.errors ? Object.values(data.errors).flat().join(' · ') : data.message;
            mostrarAlerta(errores, 'error');
        }
    } catch (e) {
        mostrarAlerta('Error al registrar la hospitalización.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = txtOriginal;
    }
}

// ── Modal Ver ──────────────────────────────────────────────────────────
async function verHospitalizacion(id) {
    try {
        const res  = await fetch(`/recepcion/hospitalizaciones/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '' }
        });
        const data = await res.json();
        if (!data.success) { mostrarAlerta(data.message, 'error'); return; }

        const h = data.hospitalizacion;
        const fmt = dt => dt ? new Date(dt).toLocaleString('es-MX', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '—';

        document.getElementById('ver-hosp-body').innerHTML = `
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px 20px; font-size:13px;">
                <div>
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Mascota</div>
                    <div style="font-weight:600; color:#1e293b;">${esc(h.mascota_nombre)}</div>
                    <div style="font-size:11px; color:#94a3b8;">${esc(h.especie ?? '')}${h.raza ? ' · ' + esc(h.raza) : ''}</div>
                </div>
                <div>
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Propietario</div>
                    <div style="font-weight:600; color:#1e293b;">${esc(h.propietario_nombre)}</div>
                    <div style="font-size:11px; color:#94a3b8;">${esc(h.propietario_telefono ?? '—')}</div>
                </div>
                <div>
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Fecha de Ingreso</div>
                    <div style="font-weight:600;">${fmt(h.fecha_ingreso)}</div>
                </div>
                <div>
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Fecha de Egreso</div>
                    <div style="font-weight:600;">${fmt(h.fecha_egreso)}</div>
                </div>
                <div>
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Estado</div>
                    <span style="display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700;
                        background:${h.estado==='Alta'?'#dcfce7':h.estado==='Internado'?'#fee2e2':'#fef9c3'};
                        color:${h.estado==='Alta'?'#166534':h.estado==='Internado'?'#991b1b':'#854d0e'};">
                        ${esc(h.estado)}
                    </span>
                </div>
                <div>
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; margin-bottom:2px;">ID</div>
                    <div style="font-weight:600;">#${h.id_hospitalizacion}</div>
                </div>
            </div>
            <div style="margin-top:16px;">
                <div style="font-size:11px; color:#64748b; text-transform:uppercase; margin-bottom:4px;">Observaciones</div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 12px; font-size:13px; color:#334155; white-space:pre-wrap; min-height:60px;">
                    ${esc(h.observaciones ?? '—')}
                </div>
            </div>`;

        document.getElementById('modal-ver-hospitalizacion').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    } catch (e) {
        mostrarAlerta('Error al cargar los datos.', 'error');
    }
};

function cerrarModalVer() {
    document.getElementById('modal-ver-hospitalizacion').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ── Modal Editar ───────────────────────────────────────────────────────
async function editarHospitalizacion(id) {
    try {
        const res  = await fetch(`/recepcion/hospitalizaciones/${id}/editar`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '' }
        });
        const data = await res.json();
        if (!data.success) { mostrarAlerta(data.message, 'error'); return; }

        const h = data.hospitalizacion;

        document.getElementById('editar-hosp-id').value         = h.id_hospitalizacion;
        document.getElementById('editar-estado-anterior').value = h.estado;
        document.getElementById('editar-mascota-display').value = h.mascota_nombre + ' — ' + h.propietario_nombre;
        document.getElementById('editar-estado').value          = h.estado;
        document.getElementById('editar-nuevo-comentario').value = '';

        // Mostrar historial existente
        const historialDiv = document.getElementById('editar-historial');
        historialDiv.textContent = h.observaciones
            ? h.observaciones
            : '(Sin observaciones previas)';

        // La fecha de egreso se asigna automáticamente si ya tenía una previa (conservar)
        const fechaEgresoInput = document.getElementById('editar-fecha-egreso');
        if (h.fecha_egreso) {
            const d   = new Date(h.fecha_egreso);
            const pad = n => String(n).padStart(2, '0');
            fechaEgresoInput.value = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
        } else {
            fechaEgresoInput.value = '';
        }

        actualizarLabelCambio();

        document.getElementById('modal-editar-hospitalizacion').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    } catch (e) {
        mostrarAlerta('Error al cargar los datos.', 'error');
    }
}

function cerrarModalEditar() {
    document.getElementById('modal-editar-hospitalizacion').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Muestra junto al label el cambio de estado que se va a registrar
function actualizarLabelCambio() {
    const anterior = document.getElementById('editar-estado-anterior')?.value ?? '';
    const nuevo    = document.getElementById('editar-estado')?.value ?? '';
    const lbl      = document.getElementById('lbl-cambio-estado');
    if (!lbl) return;

    if (anterior && nuevo && anterior !== nuevo) {
        lbl.textContent = `(${anterior} → ${nuevo})`;
        lbl.style.color = '#2563eb';
    } else if (anterior) {
        lbl.textContent = `(sin cambio de estado)`;
        lbl.style.color = '#94a3b8';
    }
}


async function guardarEdicionHospitalizacion() {
    const id          = document.getElementById('editar-hosp-id').value;
    const estadoAntes = document.getElementById('editar-estado-anterior').value;
    const estadoNuevo = document.getElementById('editar-estado').value;
    const comentario  = document.getElementById('editar-nuevo-comentario');

    if (!comentario.value.trim()) {
        comentario.focus();
        mostrarAlerta('El comentario es obligatorio para registrar el cambio.', 'error');
        return;
    }

    // Construir la entrada del log: [fecha] EstadoAntes → EstadoNuevo: comentario
    const ahora = new Date();
    const pad   = n => String(n).padStart(2, '0');
    const fecha = `${pad(ahora.getDate())}/${pad(ahora.getMonth()+1)}/${ahora.getFullYear()} ${pad(ahora.getHours())}:${pad(ahora.getMinutes())}`;

    let encabezado = `[${fecha}]`;
    encabezado += estadoAntes !== estadoNuevo
        ? ` ${estadoAntes} → ${estadoNuevo}`
        : ` ${estadoNuevo}`;
    const entradaLog = `${encabezado}: ${comentario.value.trim()}`;

    // Si el nuevo estado es Alta, asignar fecha de egreso = ahora (con segundos para evitar
    // problemas de validación en el backend con el formato datetime-local)
    const fechaEgresoInput = document.getElementById('editar-fecha-egreso');
    if (estadoNuevo === 'Alta' && !fechaEgresoInput.value) {
        fechaEgresoInput.value = `${ahora.getFullYear()}-${pad(ahora.getMonth()+1)}-${pad(ahora.getDate())} ${pad(ahora.getHours())}:${pad(ahora.getMinutes())}:${pad(ahora.getSeconds())}`;
    }

    const btn = document.getElementById('btn-guardar-editar');
    const txtOriginal = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const body = new URLSearchParams({
            _method:          'PUT',
            _token:           document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            estado:           estadoNuevo,
            nuevo_comentario: entradaLog,
            fecha_egreso:     fechaEgresoInput.value ?? '',
        });

        const res  = await fetch(`/recepcion/hospitalizaciones/${id}`, {
            method:  'POST',
            body:    body,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.success) {
            cerrarModalEditar();
            mostrarAlerta(data.message, 'success');
            setTimeout(() => location.reload(), 1200);
        } else {
            const errores = data.errors ? Object.values(data.errors).flat().join(' · ') : data.message;
            mostrarAlerta(errores, 'error');
        }
    } catch (e) {
        mostrarAlerta('Error al guardar los cambios.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = txtOriginal;
    }
}

// ── Utilidades ─────────────────────────────────────────────────────────
function esc(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(String(str ?? '')));
    return d.innerHTML;
}

function mostrarAlerta(mensaje, tipo) {
    const el = document.createElement('div');
    el.style.cssText = `position:fixed;top:20px;right:20px;padding:14px 20px;border-radius:8px;
        color:#fff;font-weight:500;z-index:99999;max-width:420px;font-size:13px;
        background:${tipo==='success'?'#10b981':tipo==='error'?'#ef4444':'#f59e0b'};
        box-shadow:0 4px 12px rgba(0,0,0,.15);`;
    el.textContent = mensaje;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .3s'; setTimeout(() => el.remove(), 300); }, 4000);
}
