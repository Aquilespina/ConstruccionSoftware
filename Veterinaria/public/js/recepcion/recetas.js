// Funciones para el módulo de Recetas
class RecetasManager {
    constructor() {
        this.recetas = [];
        this.medicamentos = [];
        this.currentPage = 1;
        this.totalPages = 1;
        this.filters = {
            search: '',
            estado: '',
            medico: ''
        };
        this.recetaActualId = null;
    }

    init() {
        this.cargarRecetas();
        this.setupEventListeners();
        this.setFechaHoy();
    }

    setupEventListeners() {
        // Botones principales
        const btnNuevaReceta = document.getElementById('btn-nueva-receta');
        const btnExportar = document.getElementById('btn-exportar-recetas');
        
        if (btnNuevaReceta) {
            btnNuevaReceta.addEventListener('click', () => this.abrirModalReceta());
        }
        
        if (btnExportar) {
            btnExportar.addEventListener('click', () => this.exportarRecetas());
        }
        
        // Filtros
        const searchInput = document.getElementById('search-recetas');
        const filterEstado = document.getElementById('filter-estado-receta');
        const filterMedico = document.getElementById('filter-medico-receta');
        
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.filters.search = e.target.value;
                this.filtrarRecetas();
            });
        }
        
        if (filterEstado) {
            filterEstado.addEventListener('change', (e) => {
                this.filters.estado = e.target.value;
                this.filtrarRecetas();
            });
        }
        
        if (filterMedico) {
            filterMedico.addEventListener('change', (e) => {
                this.filters.medico = e.target.value;
                this.filtrarRecetas();
            });
        }
        
        // Validación en tiempo real para campos de texto del formulario
        document.addEventListener('input', (e) => {
            const input = e.target;
            if (!input.classList.contains('receta-input-validado')) return;
            const tipo  = input.dataset.tipo || 'texto';
            const error = this.validarSinCaracteresEspeciales(input.value, input.name || 'campo', tipo);
            if (error) {
                input.style.borderColor = '#ef4444';
                input.title = error;
            } else {
                input.style.borderColor = '';
                input.title = '';
            }
        });

        // Validación en tiempo real para diagnóstico e instrucciones
        ['receta-diagnostico', 'receta-instrucciones', 'receta-observaciones'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            const tipo = id === 'receta-diagnostico' ? 'nombre' : 'instrucciones';
            el.addEventListener('input', () => {
                const error = this.validarSinCaracteresEspeciales(el.value, el.id, tipo);
                if (error) {
                    el.style.borderColor = '#ef4444';
                    el.title = error;
                } else {
                    el.style.borderColor = '';
                    el.title = '';
                }
            });
        });

        // Cerrar modales al hacer click fuera
        document.addEventListener('click', (e) => {
            const modalReceta = document.getElementById('modal-receta');
            const modalVerReceta = document.getElementById('modal-ver-receta');
            
            if (modalReceta && e.target === modalReceta) {
                this.cerrarModalReceta();
            }
            
            if (modalVerReceta && e.target === modalVerReceta) {
                this.cerrarModalVerReceta();
            }
        });
    }

    setFechaHoy() {
        const hoy = new Date().toISOString().split('T')[0];
        const fechaEmision = document.getElementById('receta-fecha-emision');
        const fechaVencimiento = document.getElementById('receta-vencimiento');

        if (fechaEmision) {
            fechaEmision.value = hoy;
            fechaEmision.setAttribute('readonly', true);
        }

        if (fechaVencimiento) {
            this.actualizarLimitesFechaVencimiento(hoy);
            const vencimientoDefault = new Date();
            vencimientoDefault.setDate(vencimientoDefault.getDate() + 7);
            fechaVencimiento.value = vencimientoDefault.toISOString().split('T')[0];
        }
    }

    actualizarLimitesFechaVencimiento(fechaEmisionStr) {
        const fechaVencimiento = document.getElementById('receta-vencimiento');
        if (!fechaVencimiento || !fechaEmisionStr) return;

        const emision = new Date(fechaEmisionStr + 'T00:00:00');

        const minVenc = new Date(emision);
        minVenc.setDate(minVenc.getDate() + 1);

        const maxVenc = new Date(emision);
        maxVenc.setDate(maxVenc.getDate() + 14);

        fechaVencimiento.setAttribute('min', minVenc.toISOString().split('T')[0]);
        fechaVencimiento.setAttribute('max', maxVenc.toISOString().split('T')[0]);
    }

    validarSinCaracteresEspeciales(valor, nombreCampo, tipo = 'texto') {
        if (!valor || valor.trim() === '') return null;

        const patrones = {
            nombre:        /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s.,\-()']+$/,
            dosis:         /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s.\/\-]+$/,
            frecuencia:    /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s.,\-()]+$/,
            instrucciones: /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s.,;:\-()'"/]+$/,
            texto:         /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ0-9\s.,;:\-()']+$/,
        };

        const patron = patrones[tipo] || patrones.texto;
        if (!patron.test(valor.trim())) {
            return `"${nombreCampo}" contiene caracteres no permitidos. Solo se aceptan letras, números y puntuación básica (. , - ( ) ')`;
        }
        return null;
    }

    async cargarRecetas() {
        try {
            const response = await this.fetchRecetas();
            this.recetas = response.data || [];
            this.totalPages = response.meta?.last_page || 1;
            this.renderizarRecetas();
            this.actualizarEstadisticas();
        } catch (error) {
            console.error('Error cargando recetas:', error);
            this.mostrarError('Error al cargar las recetas: ' + error.message);
        }
    }

    async fetchRecetas() {
        const params = new URLSearchParams({ page: this.currentPage });
        if (this.filters.search) params.append('search', this.filters.search);
        if (this.filters.estado)  params.append('estado',  this.filters.estado);
        if (this.filters.medico)  params.append('medico',  this.filters.medico);

        const response = await fetch(`/api/recetas?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        if (!response.ok) {
            throw new Error(`Error ${response.status} al cargar las recetas`);
        }

        return await response.json();
    }

    renderizarRecetas() {
        const tbody = document.getElementById('tabla-recetas');
        if (!tbody) return;

        if (this.recetas.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center" style="padding: 40px; color: #6b7280;">
                        <div style="font-size: 1.1rem; margin-bottom: 8px;">No hay recetas registradas</div>
                        <div style="font-size: 0.9rem;">Haga clic en "Nueva Receta" para comenzar</div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = '';

        this.recetas.forEach(receta => {
            const tr = document.createElement('tr');
            tr.innerHTML = this.generarFilaReceta(receta);
            tbody.appendChild(tr);
        });

        this.renderizarPaginacion();
    }

    generarFilaReceta(receta) {
        const estadoClass = this.getClassEstado(receta.estado);
        const estadoText = this.getTextEstado(receta.estado);
        
        return `
            <td>
                <div class="receta-item">
                    <div class="receta-icon">💊</div>
                    <div class="receta-info">
                        <h4>${receta.codigo || `REC-${receta.id.toString().padStart(3, '0')}`}</h4>
                        <div class="receta-subinfo">${receta.medicamentos_count || 0} medicamento(s)</div>
                    </div>
                </div>
            </td>
            <td>
                <strong>${receta.mascota?.nombre || 'N/A'}</strong><br>
                <small class="text-muted">${receta.mascota?.propietario || 'Propietario no disponible'}</small>
            </td>
            <td>${receta.medico?.nombre || 'Médico no asignado'}</td>
            <td>${this.formatearFecha(receta.fecha_emision)}</td>
            <td>${this.formatearFecha(receta.fecha_vencimiento)}</td>
            <td><span class="status-badge ${estadoClass}">${estadoText}</span></td>
            <td>
                <div style="display:flex;gap:0.5rem;align-items:center;">
                    <button class="receta-btn-ver" onclick="recetasManager.verReceta(${receta.id})">Ver</button>
                </div>
            </td>
        `;
    }

    getClassEstado(estado) {
        const estados = {
            'activa':      'status-active',
            'por_vencer':  'status-warning',
            'expirada':    'status-expired',
            'completada':  'status-completed',
            'cancelada':   'status-cancelled',
        };
        return estados[estado] || 'status-pending';
    }

    getTextEstado(estado) {
        const textos = {
            'activa':     'Activa',
            'por_vencer': 'Por vencer',
            'expirada':   'Expirada',
            'completada': 'Completada',
            'cancelada':  'Cancelada',
        };
        return textos[estado] || estado;
    }

    formatearFecha(fecha) {
        if (!fecha) return '-';
        try {
            return new Date(fecha).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (error) {
            return '-';
        }
    }

    async cargarDatosMascota() {
        const select = document.getElementById('receta-paciente');
        const mascotaId = select?.value;
        const infoPaciente = document.getElementById('info-paciente');

        if (!mascotaId || !infoPaciente) {
            if (infoPaciente) infoPaciente.style.display = 'none';
            return;
        }

        try {
            const mascota = await this.fetchMascota(mascotaId);
            
            document.getElementById('info-propietario').textContent = mascota.propietario || 'No disponible';
            document.getElementById('info-especie').textContent = mascota.especie || 'No disponible';
            document.getElementById('info-edad').textContent = mascota.edad ? `${mascota.edad} años` : 'No disponible';
            document.getElementById('info-peso').textContent = mascota.peso ? `${mascota.peso} kg` : 'No disponible';
            
            infoPaciente.style.display = 'block';
        } catch (error) {
            console.error('Error cargando datos de mascota:', error);
            if (infoPaciente) infoPaciente.style.display = 'none';
        }
    }

    async fetchMascota(id) {
        try {
            // Reemplazar con tu endpoint real
            const response = await fetch(`/api/mascotas/${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (response.ok) {
                return await response.json();
            }
            throw new Error('Error al cargar mascota');
        } catch (error) {
            console.warn('Usando datos de prueba para mascota:', error);
            // Datos de prueba
            return {
                id: id,
                nombre: 'Mascota ' + id,
                especie: 'Perro',
                edad: 3,
                peso: 15,
                propietario: 'Propietario ' + id
            };
        }
    }

    agregarMedicamento() {
        const container = document.getElementById('lista-medicamentos');
        if (!container) return;

        const index = this.medicamentos.length;
        
        const medicamentoHTML = `
            <div class="medicamento-item" data-index="${index}">
                <div class="medicamento-header">
                    <div class="medicamento-title">Medicamento ${index + 1}</div>
                    <button type="button" class="btn-remove-med" onclick="recetasManager.removerMedicamento(${index})">
                        ×
                    </button>
                </div>
                <div class="medicamento-form">
                    <div class="form-group">
                        <label>Medicamento *</label>
                        <input type="text" name="medicamentos[${index}][nombre]"
                               class="form-control receta-input-validado" placeholder="Nombre del medicamento"
                               data-tipo="nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Dosis *</label>
                        <input type="text" name="medicamentos[${index}][dosis]"
                               class="form-control receta-input-validado" placeholder="Ej: 5mg"
                               data-tipo="dosis" required>
                    </div>
                    <div class="form-group">
                        <label>Frecuencia *</label>
                        <input type="text" name="medicamentos[${index}][frecuencia]"
                               class="form-control receta-input-validado" placeholder="Ej: Cada 8 horas"
                               data-tipo="frecuencia" required>
                    </div>
                    <div class="form-group">
                        <label>Duración *</label>
                        <input type="text" name="medicamentos[${index}][duracion]"
                               class="form-control receta-input-validado" placeholder="Ej: 7 días"
                               data-tipo="frecuencia" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Instrucciones específicas</label>
                    <textarea name="medicamentos[${index}][instrucciones]"
                              class="form-control receta-input-validado" rows="2"
                              data-tipo="instrucciones"
                              placeholder="Instrucciones específicas para este medicamento..."></textarea>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', medicamentoHTML);
        this.medicamentos.push({
            nombre: '',
            dosis: '',
            frecuencia: '',
            duracion: '',
            instrucciones: ''
        });
    }

    removerMedicamento(index) {
        const elemento = document.querySelector(`.medicamento-item[data-index="${index}"]`);
        if (elemento) {
            elemento.remove();
            this.medicamentos.splice(index, 1);
            this.reindexarMedicamentos();
        }
    }

    reindexarMedicamentos() {
        const elementos = document.querySelectorAll('.medicamento-item');
        this.medicamentos = [];
        
        elementos.forEach((elemento, newIndex) => {
            elemento.setAttribute('data-index', newIndex);
            elemento.querySelector('.medicamento-title').textContent = `Medicamento ${newIndex + 1}`;
            
            // Actualizar los names de los inputs
            const inputs = elemento.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                const name = input.getAttribute('name').replace(/medicamentos\[\d+\]/, `medicamentos[${newIndex}]`);
                input.setAttribute('name', name);
            });
            
            // Reconstruir array de medicamentos
            this.medicamentos.push({
                nombre: inputs[0]?.value || '',
                dosis: inputs[1]?.value || '',
                frecuencia: inputs[2]?.value || '',
                duracion: inputs[3]?.value || '',
                instrucciones: inputs[4]?.value || ''
            });
        });
    }

    async guardarReceta() {
        const form = document.getElementById('form-receta');
        if (!form) {
            this.mostrarError('No se pudo encontrar el formulario');
            return;
        }

        // Asegura que los valores escritos en inputs se reflejen en this.medicamentos
        this.reindexarMedicamentos();

        // Validación básica
        if (!this.validarReceta()) {
            return;
        }

        try {
            const formData = new FormData(form);
            const data = this.prepararDatosReceta(formData);
            
            await this.saveReceta(data);
            this.mostrarExito('Receta guardada correctamente');
            this.cerrarModalReceta();
            await this.cargarRecetas();
        } catch (error) {
            console.error('Error guardando receta:', error);
            // Intentar mostrar detalles de validación si existen
            if (error.responseJson && error.responseJson.errors) {
                const detalles = Object.entries(error.responseJson.errors)
                  .map(([campo, msgs]) => `- ${campo}: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                  .join('\n');
                this.mostrarError('Error de validación:\n' + detalles);
            } else {
                this.mostrarError('Error al guardar la receta: ' + (error.message || 'Error desconocido'));
            }
        }
    }

    validarReceta() {
        const errores = [];

        // Validar que haya al menos un medicamento
        if (this.medicamentos.length === 0) {
            this.mostrarError('Debe agregar al menos un medicamento');
            return false;
        }

        // Validar campos requeridos
        const paciente    = document.getElementById('receta-paciente')?.value;
        const medico      = document.getElementById('receta-medico')?.value;
        const diagnostico = document.getElementById('receta-diagnostico')?.value?.trim();
        const instrucciones = document.getElementById('receta-instrucciones')?.value?.trim();

        if (!paciente || !medico || !diagnostico || !instrucciones) {
            this.mostrarError('Por favor complete todos los campos obligatorios');
            return false;
        }

        // Validar caracteres en campos de texto
        const errDiag = this.validarSinCaracteresEspeciales(diagnostico, 'Diagnóstico', 'nombre');
        if (errDiag) errores.push(errDiag);

        const errInstr = this.validarSinCaracteresEspeciales(instrucciones, 'Instrucciones de uso', 'instrucciones');
        if (errInstr) errores.push(errInstr);

        const observaciones = document.getElementById('receta-observaciones')?.value?.trim();
        if (observaciones) {
            const errObs = this.validarSinCaracteresEspeciales(observaciones, 'Observaciones', 'instrucciones');
            if (errObs) errores.push(errObs);
        }

        // Validar fechas
        const fechaEmision     = document.getElementById('receta-fecha-emision')?.value;
        const fechaVencimiento = document.getElementById('receta-vencimiento')?.value;

        if (fechaEmision && fechaVencimiento) {
            const emision     = new Date(fechaEmision + 'T00:00:00');
            const vencimiento = new Date(fechaVencimiento + 'T00:00:00');

            if (vencimiento <= emision) {
                errores.push('La fecha de vencimiento debe ser posterior a la fecha de emisión');
            } else {
                const maxVencimiento = new Date(emision);
                maxVencimiento.setDate(maxVencimiento.getDate() + 14);
                if (vencimiento > maxVencimiento) {
                    errores.push('La fecha de vencimiento no puede superar los 14 días desde la emisión (máximo para tratamientos veterinarios)');
                }
            }
        }

        // Validar campos de medicamentos
        const elementosMeds = document.querySelectorAll('.medicamento-item');
        elementosMeds.forEach((elem, idx) => {
            const num     = idx + 1;
            const inputs  = elem.querySelectorAll('input[type="text"], input:not([type])');
            const nombre  = inputs[0]?.value?.trim();
            const dosis   = inputs[1]?.value?.trim();
            const frec    = inputs[2]?.value?.trim();
            const dur     = inputs[3]?.value?.trim();

            [
                [nombre, `Medicamento ${num} - Nombre`,    'nombre'],
                [dosis,  `Medicamento ${num} - Dosis`,     'dosis'],
                [frec,   `Medicamento ${num} - Frecuencia`,'frecuencia'],
                [dur,    `Medicamento ${num} - Duración`,  'frecuencia'],
            ].forEach(([val, campo, tipo]) => {
                if (val) {
                    const err = this.validarSinCaracteresEspeciales(val, campo, tipo);
                    if (err) errores.push(err);
                }
            });

            const textarea = elem.querySelector('textarea');
            const instrMed = textarea?.value?.trim();
            if (instrMed) {
                const err = this.validarSinCaracteresEspeciales(instrMed, `Medicamento ${num} - Instrucciones`, 'instrucciones');
                if (err) errores.push(err);
            }
        });

        if (errores.length > 0) {
            this.mostrarError(errores.join('\n'));
            return false;
        }

        return true;
    }

    prepararDatosReceta(formData) {
        const data = {
            mascota_id: formData.get('mascota_id'),
            medico_id: formData.get('medico_id'),
            diagnostico: formData.get('diagnostico'),
            fecha_emision: formData.get('fecha_emision'),
            fecha_vencimiento: formData.get('fecha_vencimiento'),
            instrucciones: formData.get('instrucciones'),
            observaciones: formData.get('observaciones'),
            medicamentos: this.medicamentos
        };

        if (this.recetaActualId) {
            data.id = this.recetaActualId;
        }

        return data;
    }

    async saveReceta(data) {
        const url = this.recetaActualId ? `/api/recetas/${this.recetaActualId}` : '/api/recetas';
        const method = this.recetaActualId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            let errorMessage = `HTTP ${response.status}`;
            let responseJson = null;
            try {
                responseJson = await response.json();
                errorMessage = responseJson.error || responseJson.message || JSON.stringify(responseJson);
            } catch (_) {
                // ignore json parse errors
            }
            const err = new Error(errorMessage);
            err.responseJson = responseJson;
            throw err;
        }

        return await response.json();
    }

    abrirModalReceta() {
        this.recetaActualId = null;
        this.limpiarFormulario();
        const modal = document.getElementById('modal-receta');
        if (modal) {
            modal.style.display = 'flex';
            document.getElementById('modal-receta-titulo').textContent = 'Nueva Receta Médica';
        }
    }

    cerrarModalReceta() {
        const modal = document.getElementById('modal-receta');
        if (modal) {
            modal.style.display = 'none';
        }
        this.limpiarFormulario();
    }

    limpiarFormulario() {
        const form = document.getElementById('form-receta');
        if (form) {
            form.reset();
            // Limpiar marcas de error de validación
            form.querySelectorAll('input, textarea').forEach(el => {
                el.style.borderColor = '';
                el.title = '';
            });
        }

        const listaMedicamentos = document.getElementById('lista-medicamentos');
        if (listaMedicamentos) {
            listaMedicamentos.innerHTML = '';
        }

        const infoPaciente = document.getElementById('info-paciente');
        if (infoPaciente) {
            infoPaciente.style.display = 'none';
        }

        this.medicamentos = [];
        this.recetaActualId = null;
        this.setFechaHoy();
    }

    async verReceta(id) {
        try {
            this.recetaActualId = id;
            const receta = await this.fetchReceta(id);
            this.mostrarDetalleReceta(receta);
            const modal = document.getElementById('modal-ver-receta');
            if (modal) {
                modal.style.display = 'flex';
                

            }
        } catch (error) {
            console.error('Error cargando receta:', error);
            this.mostrarError('Error al cargar la receta');
        }
    }

    async fetchReceta(id) {
        try {
            const response = await fetch(`/api/recetas/${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (response.ok) {
                return await response.json();
            }
            throw new Error('Error al cargar receta');
        } catch (error) {
            console.warn('Usando datos de prueba para receta:', error);
            // Datos de prueba
            return {
                id: id,
                codigo: `REC-${id.toString().padStart(3, '0')}`,
                mascota: { nombre: 'Max', especie: 'Perro', edad: 4, peso: 25 },
                propietario: { nombre: 'María Rodríguez', telefono: '+1234567890' },
                medico: { nombre: 'Dra. Laura Méndez', especialidad: 'Veterinaria General' },
                fecha_emision: '2024-01-15',
                fecha_vencimiento: '2024-01-30',
                diagnostico: 'Infección respiratoria superior',
                instrucciones: 'Administrar los medicamentos con alimento. Mantener al paciente en reposo.',
                observaciones: 'Seguimiento en 7 días.',
                estado: 'activa',
                medicamentos: [
                    {
                        nombre: 'Amoxicilina',
                        dosis: '250mg',
                        frecuencia: 'Cada 12 horas',
                        duracion: '7 días',
                        instrucciones: 'Administrar con alimento'
                    },
                    {
                        nombre: 'Antihistamínico',
                        dosis: '10mg',
                        frecuencia: 'Cada 24 horas',
                        duracion: '5 días',
                        instrucciones: 'Administrar en la noche'
                    }
                ]
            };
        }
    }

    mostrarDetalleReceta(receta) {
        const container = document.getElementById('receta-detalle');
        if (!container) return;

        container.innerHTML = `
            <div class="receta-print">
                <div class="print-header">
                    <h1>RECETA MÉDICA VETERINARIA</h1>
                    <div class="clinic-info">Clínica Veterinaria - Tel: (123) 456-7890</div>
                </div>
                
                <div class="print-section">
                    <h3>Información del Paciente</h3>
                    <div class="print-grid">
                        <div class="print-item">
                            <label>Paciente:</label> ${receta.mascota?.nombre || 'N/A'}
                        </div>
                        <div class="print-item">
                            <label>Propietario:</label> ${receta.propietario?.nombre || 'N/A'}
                        </div>
                        <div class="print-item">
                            <label>Especie:</label> ${receta.mascota?.especie || 'N/A'}
                        </div>
                        <div class="print-item">
                            <label>Edad/Peso:</label> ${receta.mascota?.edad || 'N/A'} años / ${receta.mascota?.peso || 'N/A'} kg
                        </div>
                    </div>
                </div>
                
                <div class="print-section">
                    <h3>Diagnóstico y Tratamiento</h3>
                    <div class="print-item">
                        <label>Diagnóstico:</label> ${receta.diagnostico || 'N/A'}
                    </div>
                    <div class="print-item">
                        <label>Fecha Emisión:</label> ${this.formatearFecha(receta.fecha_emision)}
                    </div>
                    <div class="print-item">
                        <label>Válida hasta:</label> ${this.formatearFecha(receta.fecha_vencimiento)}
                    </div>
                    <div class="print-item">
                        <label>Estado:</label> <span class="status-badge ${this.getClassEstado(receta.estado)}">${this.getTextEstado(receta.estado)}</span>
                    </div>
                </div>
                
                <div class="print-section">
                    <h3>Medicamentos Recetados</h3>
                    ${receta.medicamentos?.map(med => `
                        <div class="medicamento-print">
                            <h4>${med.nombre}</h4>
                            <div class="medicamento-details">
                                <div><strong>Dosis:</strong> ${med.dosis}</div>
                                <div><strong>Frecuencia:</strong> ${med.frecuencia}</div>
                                <div><strong>Duración:</strong> ${med.duracion}</div>
                                ${med.instrucciones ? `<div><strong>Instrucciones:</strong> ${med.instrucciones}</div>` : ''}
                            </div>
                        </div>
                    `).join('') || '<p>No hay medicamentos registrados</p>'}
                </div>
                
                <div class="print-section">
                    <h3>Instrucciones Generales</h3>
                    <p>${receta.instrucciones || 'No hay instrucciones especificadas'}</p>
                    ${receta.observaciones ? `
                        <h4>Observaciones:</h4>
                        <p>${receta.observaciones}</p>
                    ` : ''}
                </div>
                
                <div class="print-footer">
                    <div class="firma-medico">
                        <div class="firma-line"></div>
                        <div>${receta.medico?.nombre || 'Médico no asignado'}</div>
                        <div>${receta.medico?.especialidad || 'Especialidad no especificada'}</div>
                        <div>CVME: ${Math.random().toString(36).substr(2, 8).toUpperCase()}</div>
                    </div>
                </div>
            </div>
        `;
    }

    cerrarModalVerReceta() {
        const modal = document.getElementById('modal-ver-receta');
        if (modal) {
            modal.style.display = 'none';
        }
        this.recetaActualId = null;
    }

    imprimirReceta() {
        window.print();
    }

    filtrarRecetas() {
        console.log('Aplicando filtros:', this.filters);
        this.currentPage = 1;
        this.cargarRecetas();
    }

    renderizarPaginacion() {
        const container = document.getElementById('pagination-recetas');
        if (!container || this.totalPages <= 1) {
            if (container) container.innerHTML = '';
            return;
        }

        container.innerHTML = `
            <div class="pagination-info">
                Mostrando ${this.recetas.length} de ${this.recetas.length} registros
            </div>
            <div class="pagination">
                ${this.generarBotonesPaginacion()}
            </div>
        `;
    }

    generarBotonesPaginacion() {
        let botones = '';
        
        if (this.currentPage > 1) {
            botones += `<button onclick="recetasManager.cambiarPagina(${this.currentPage - 1})">‹</button>`;
        }
        
        for (let i = 1; i <= this.totalPages; i++) {
            if (i === this.currentPage) {
                botones += `<button class="active">${i}</button>`;
            } else {
                botones += `<button onclick="recetasManager.cambiarPagina(${i})">${i}</button>`;
            }
        }
        
        if (this.currentPage < this.totalPages) {
            botones += `<button onclick="recetasManager.cambiarPagina(${this.currentPage + 1})">›</button>`;
        }
        
        return botones;
    }

    cambiarPagina(pagina) {
        this.currentPage = pagina;
        this.cargarRecetas();
    }

    async exportarRecetas() {
        if (typeof XLSX === 'undefined') {
            this.mostrarError('La librería de exportación no está disponible. Recargue la página.');
            return;
        }

        const btnExportar = document.getElementById('btn-exportar-recetas');
        const textoOriginal = btnExportar?.innerHTML;
        if (btnExportar) {
            btnExportar.disabled = true;
            btnExportar.innerHTML = '⏳ Exportando...';
        }

        try {
            // Traer todos los registros respetando los filtros activos
            const params = new URLSearchParams({ per_page: 1000, page: 1 });
            if (this.filters.search) params.append('search', this.filters.search);
            if (this.filters.estado)  params.append('estado',  this.filters.estado);
            if (this.filters.medico)  params.append('medico',  this.filters.medico);

            const response = await fetch(`/api/recetas?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) throw new Error(`Error ${response.status}`);

            const json    = await response.json();
            const recetas = json.data || [];

            if (recetas.length === 0) {
                this.mostrarError('No hay recetas para exportar con los filtros actuales');
                return;
            }

            const filas = recetas.map(r => ({
                'Código':          r.codigo || '',
                'Paciente':        r.mascota?.nombre || '',
                'Propietario':     r.mascota?.propietario || '',
                'Médico':          r.medico?.nombre || '',
                'Especialidad':    r.medico?.especialidad || '',
                'Fecha Emisión':   r.fecha_emision  || '',
                'Vencimiento':     r.fecha_vencimiento || '',
                'Estado':          this.getTextEstado(r.estado),
                'Diagnóstico':     r.diagnostico || '',
                'Medicamento':     r.medicamento || '',
                'Dosis':           r.dosis || '',
                'Indicaciones':    r.indicaciones || '',
            }));

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.json_to_sheet(filas);

            // Anchos de columna
            ws['!cols'] = [
                { wch: 10 }, { wch: 20 }, { wch: 26 }, { wch: 26 }, { wch: 22 },
                { wch: 14 }, { wch: 14 }, { wch: 12 },
                { wch: 36 }, { wch: 26 }, { wch: 10 }, { wch: 40 },
            ];

            XLSX.utils.book_append_sheet(wb, ws, 'Recetas');

            const fecha = new Date().toISOString().split('T')[0];
            XLSX.writeFile(wb, `Recetas_VeteClini_${fecha}.xlsx`);

        } catch (error) {
            console.error('Error exportando:', error);
            this.mostrarError('Error al exportar las recetas: ' + error.message);
        } finally {
            if (btnExportar) {
                btnExportar.disabled = false;
                btnExportar.innerHTML = textoOriginal;
            }
        }
    }

    actualizarEstadisticas() {
        const total     = this.recetas.length;
        const activas   = this.recetas.filter(r => r.estado === 'activa').length;
        const porVencer = this.recetas.filter(r => r.estado === 'por_vencer').length;
        const expiradas = this.recetas.filter(r => r.estado === 'expirada').length;

        const totalElement     = document.getElementById('total-recetas');
        const activasElement   = document.getElementById('recetas-activas');
        const porVencerElement = document.getElementById('recetas-expiradas');
        const expiradasElement = document.getElementById('recetas-vencidas');

        if (totalElement)     totalElement.textContent     = total;
        if (activasElement)   activasElement.textContent   = activas;
        if (porVencerElement) porVencerElement.textContent = porVencer;
        if (expiradasElement) expiradasElement.textContent = expiradas;
    }

    mostrarExito(mensaje) {
        // Puedes reemplazar esto con tu sistema de notificaciones
        console.log('✅ ' + mensaje);
        alert('✅ ' + mensaje);
    }

    mostrarError(mensaje) {
        // Puedes reemplazar esto con tu sistema de notificaciones
        console.error('❌ ' + mensaje);
        alert('❌ ' + mensaje);
    }
}

// Inicializar el manager
const recetasManager = new RecetasManager();

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        recetasManager.init();
    });
} else {
    recetasManager.init();
}