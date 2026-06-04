/**
 * Módulo de Historial Médico
 */
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function fetchHeaders(extra = {}) {
        return {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF,
            ...extra,
        };
    }

    // ─── Init ─────────────────────────────────────────────────────────────────
    // El script se carga al final del body: el DOM ya está listo
    init();

    function init() {
        document.getElementById('search-mascotas')?.addEventListener('input', aplicarFiltros);
        document.getElementById('filter-especie')?.addEventListener('change', aplicarFiltros);
        document.getElementById('filter-estado')?.addEventListener('change', aplicarFiltros);

        document.getElementById('modal-historial')?.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) cerrarModalHistorial();
        });
    }

    // ─── Filtros ─────────────────────────────────────────────────────────────
    function aplicarFiltros() {
        const termino = (document.getElementById('search-mascotas')?.value || '').toLowerCase().trim();
        const especie = (document.getElementById('filter-especie')?.value || '').toLowerCase();
        const estado  = (document.getElementById('filter-estado')?.value || '').toLowerCase();

        const cards = document.querySelectorAll('#mascotas-container .mascota-card');
        let visibles = 0;

        cards.forEach((card) => {
            const name       = (card.dataset.name   || '').toLowerCase();
            const owner      = (card.dataset.owner  || '').toLowerCase();
            const cardEsp    = (card.dataset.especie || '').toLowerCase();
            const cardEstado = (card.dataset.estado  || '').toLowerCase();

            const ok =
                (!termino || name.includes(termino) || owner.includes(termino)) &&
                (!especie || cardEsp    === especie) &&
                (!estado  || cardEstado === estado);

            card.style.display = ok ? '' : 'none';
            if (ok) visibles++;
        });

        const sinRes = document.getElementById('mascotas-sin-resultados');
        if (sinRes) {
            sinRes.style.display = (cards.length > 0 && visibles === 0) ? 'block' : 'none';
        }
    }

    // ─── Modal historial ─────────────────────────────────────────────────────
    function verHistorial(mascotaId, nombre) {
        const modal = document.getElementById('modal-historial');
        const titulo = document.getElementById('historial-titulo');
        const body   = document.getElementById('historial-body');
        if (!modal) return;

        titulo.textContent = `Historial — ${nombre || 'Mascota'}`;

        const ficha  = document.getElementById('historial-ficha');
        const scroll = document.getElementById('historial-scroll');
        if (ficha)  { ficha.style.display = 'none'; ficha.innerHTML = ''; }
        if (scroll) { scroll.innerHTML = '<div class="h-vacio">Cargando historial...</div>'; }

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        cargarHistorial(mascotaId);
    }

    function cerrarModalHistorial() {
        document.getElementById('modal-historial').style.display = 'none';
        document.body.style.overflow = '';
    }

    async function cargarHistorial(mascotaId) {
        const ficha  = document.getElementById('historial-ficha');
        const scroll = document.getElementById('historial-scroll');
        const titulo = document.getElementById('historial-titulo');

        try {
            const resp = await fetch(
                `/api/expedientes/${encodeURIComponent(mascotaId)}/historial`,
                { headers: fetchHeaders() }
            );
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            const { mascota, timeline } = await resp.json();

            titulo.textContent = `Historial — ${mascota.nombre}`;

            // ── Ficha fija de la mascota ─────────────────────────────────────
            const emoji = { gato: '🐈', perro: '🐕', ave: '🐦', roedor: '🐁' }[
                (mascota.especie || '').toLowerCase()
            ] || '🐾';

            const chips = [
                mascota.especie  ? `<span class="h-chip">${mascota.especie}</span>` : '',
                mascota.raza     ? `<span class="h-chip">${mascota.raza}</span>` : '',
                mascota.propietario ? `<span class="h-chip">👤 ${mascota.propietario}</span>` : '',
                `<span class="h-chip">${timeline.length} registro${timeline.length !== 1 ? 's' : ''}</span>`,
                mascota.alergias ? `<span class="h-chip h-chip-alerta">⚠️ ${mascota.alergias}</span>` : '',
            ].filter(Boolean).join('');

            ficha.innerHTML = `
                <div class="h-ficha-avatar">${emoji}</div>
                <div class="h-ficha-datos">
                    <p class="h-ficha-nombre">${mascota.nombre}</p>
                    <p class="h-ficha-sub">Historial médico completo</p>
                    <div class="h-ficha-chips">${chips}</div>
                </div>`;
            ficha.style.display = 'flex';

            // ── Entradas ─────────────────────────────────────────────────────
            if (!timeline.length) {
                scroll.innerHTML = '<div class="h-vacio">Esta mascota no tiene registros médicos aún.</div>';
                return;
            }

            scroll.innerHTML = timeline.map((item) => {
                if (item.tipo === 'consulta')       return renderConsulta(item);
                if (item.tipo === 'hospitalizacion') return renderHospitalizacion(item);
                return '';
            }).join('');

        } catch (e) {
            ficha.style.display = 'none';
            scroll.innerHTML = `<div class="h-vacio" style="color:#ef4444;">
                No se pudo cargar el historial: ${e.message}
            </div>`;
        }
    }

    function renderConsulta(c) {
        const estadoKey = (c.estado || 'programada').toLowerCase();
        const tipoKey   = { urgencia: 'urgencia', 'cirugía': 'cirugia', cirugia: 'cirugia',
                            'estética': 'estetica', estetica: 'estetica' }[
                           (c.tipo_cita || '').toLowerCase()] || 'consulta';
        const servicio  = c.tipo_servicio ? ` · ${c.tipo_servicio}` : '';

        const pesoHtml = c.peso_mascota
            ? `<span class="h-peso">⚖️ ${c.peso_mascota} kg registrado</span>` : '';

        const diagHtml = c.diagnostico
            ? `<div class="h-campo">
                 <label>Diagnóstico</label>
                 <p>${c.diagnostico}</p>
               </div>` : '';

        const obsHtml = c.observaciones
            ? `<div class="h-campo">
                 <label>Observaciones</label>
                 <p>${c.observaciones}</p>
               </div>` : '';

        const recetasHtml = (c.recetas && c.recetas.length)
            ? `<p class="h-recetas-titulo">💊 Tratamiento recetado</p>
               ${c.recetas.map((r) => `
                 <div class="h-receta">
                   <span class="h-receta-med">${r.medicamento || '—'}</span>
                   ${r.dosis ? `<span class="h-receta-dosis">· ${r.dosis}</span>` : ''}
                   ${r.indicaciones ? `<p class="h-receta-ind">${r.indicaciones}</p>` : ''}
                 </div>`).join('')}`
            : `<p class="h-sin-recetas">Sin medicamentos recetados</p>`;

        return `
            <div class="h-card">
                <div class="h-card-top">
                    <span class="h-card-fecha">📅 ${fmt(c.fecha)}${c.hora ? ` · ${c.hora}` : ''}</span>
                    <span class="h-badge h-badge-${tipoKey}">${c.tipo_cita || 'Consulta'}${servicio}</span>
                    <span class="h-badge h-badge-${estadoKey}">${c.estado || ''}</span>
                </div>
                <div class="h-card-body">
                    <p class="h-profesional">🩺 Dr/a. ${c.profesional}</p>
                    ${pesoHtml}${diagHtml}${obsHtml}${recetasHtml}
                </div>
            </div>`;
    }

    function renderHospitalizacion(h) {
        const estadoBadge = (h.estado || 'internado').toLowerCase().replace('í', 'i');
        const egresoTxt   = h.fecha_egreso
            ? `Egreso: ${fmt(h.fecha_egreso)}`
            : 'Actualmente internado/a';

        return `
            <div class="h-card">
                <div class="h-card-top">
                    <span class="h-card-fecha">🏥 Hospitalización · Ingreso: ${fmt(h.fecha_ingreso)}</span>
                    <span class="h-badge h-badge-${estadoBadge}">${h.estado || 'Internado'}</span>
                </div>
                <div class="h-card-body">
                    <p class="h-profesional">📅 ${egresoTxt}</p>
                    ${h.observaciones
                        ? `<div class="h-campo">
                             <label>Observaciones</label>
                             <p>${h.observaciones}</p>
                           </div>`
                        : ''}
                </div>
            </div>`;
    }

    function fmt(fechaStr) {
        if (!fechaStr) return '—';
        const [y, m, d] = String(fechaStr).substring(0, 10).split('-');
        return `${d}/${m}/${y}`;
    }

    // ─── Exports globales ────────────────────────────────────────────────────
    window.verHistorial         = verHistorial;
    window.cerrarModalHistorial = cerrarModalHistorial;
})();
