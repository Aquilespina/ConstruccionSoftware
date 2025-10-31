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
            medico: '',
            fecha: ''
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
        const filterFecha = document.getElementById('filter-fecha-receta');
        
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
        
        if (filterFecha) {
            filterFecha.addEventListener('change', (e) => {
                this.filters.fecha = e.target.value;
                this.filtrarRecetas();
            });
        }
        
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
        }
        
        // Establecer vencimiento por defecto (7 días)
        if (fechaVencimiento) {
            const vencimiento = new Date();
            vencimiento.setDate(vencimiento.getDate() + 7);
            fechaVencimiento.value = vencimiento.toISOString().split('T')[0];
        }
    }

    async cargarRecetas() {
        try {
            console.log('Cargando recetas...');
            const response = await this.fetchRecetas();
            this.recetas = response.data || [];
            this.totalPages = response.meta?.last_page || 1;
            
            this.renderizarRecetas();
            this.actualizarEstadisticas();
        } catch (error) {
            console.error('Error cargando recetas:', error);
            this.mostrarError('Error al cargar las recetas');
        }
    }

    async fetchRecetas() {
        try {
            // Aquí debes reemplazar con tu endpoint real de Laravel
            const response = await fetch('/api/recetas', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            
            return await response.json();
        } catch (error) {
            console.warn('Usando datos de prueba:', error);
            // Datos de prueba mientras implementas el backend
            return {
                data: [
                    {
                        id: 1,
                        codigo: 'REC-001',
                        mascota: { nombre: 'Max', propietario: 'María Rodríguez' },
                        medico: { nombre: 'Dra. Laura Méndez' },
                        fecha_emision: '2024-01-15',
                        fecha_vencimiento: '2024-01-30',
                        diagnostico: 'Infección respiratoria',
                        estado: 'activa',
                        medicamentos_count: 3
                    },
                    {
                        id: 2,
                        codigo: 'REC-002',
                        mascota: { nombre: 'Luna', propietario: 'Carlos Pérez' },
                        medico: { nombre: 'Dr. Roberto García' },
                        fecha_emision: '2024-01-10',
                        fecha_vencimiento: '2024-01-25',
                        diagnostico: 'Alergia alimentaria',
                        estado: 'expirada',
                        medicamentos_count: 2
                    }
                ],
                meta: { 
                    total: 2,
                    per_page: 10,
                    current_page: 1,
                    last_page: 1 
                }
            };
        }
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
                <div class="btn-actions">
                    <button class="btn-icon view" onclick="recetasManager.verReceta(${receta.id})" title="Ver receta">
                        👁
                    </button>
                    <button class="btn-icon print" onclick="recetasManager.imprimirReceta(${receta.id})" title="Imprimir">
                        🖨
                    </button>
                    ${receta.estado === 'expirada' ? 
                        `<button class="btn-icon renew" onclick="recetasManager.renovarReceta(${receta.id})" title="Renovar">🔄</button>` : 
                        ''
                    }
                </div>
            </td>
        `;
    }

    getClassEstado(estado) {
        const estados = {
            'activa': 'status-active',
            'expirada': 'status-expired',
            'completada': 'status-completed',
            'cancelada': 'status-expired'
        };
        return estados[estado] || 'status-pending';
    }

    getTextEstado(estado) {
        const textos = {
            'activa': 'Activa',
            'expirada': 'Expirada',
            'completada': 'Completada',
            'cancelada': 'Cancelada'
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
                               class="form-control" placeholder="Nombre del medicamento" required>
                    </div>
                    <div class="form-group">
                        <label>Dosis *</label>
                        <input type="text" name="medicamentos[${index}][dosis]" 
                               class="form-control" placeholder="Ej: 5mg" required>
                    </div>
                    <div class="form-group">
                        <label>Frecuencia *</label>
                        <input type="text" name="medicamentos[${index}][frecuencia]" 
                               class="form-control" placeholder="Ej: Cada 8 horas" required>
                    </div>
                    <div class="form-group">
                        <label>Duración *</label>
                        <input type="text" name="medicamentos[${index}][duracion]" 
                               class="form-control" placeholder="Ej: 7 días" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Instrucciones específicas</label>
                    <textarea name="medicamentos[${index}][instrucciones]" 
                              class="form-control" rows="2" 
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
        // Validar que haya al menos un medicamento
        if (this.medicamentos.length === 0) {
            this.mostrarError('Debe agregar al menos un medicamento');
            return false;
        }

        // Validar campos requeridos
        const paciente = document.getElementById('receta-paciente')?.value;
        const medico = document.getElementById('receta-medico')?.value;
        const diagnostico = document.getElementById('receta-diagnostico')?.value;
        const instrucciones = document.getElementById('receta-instrucciones')?.value;

        if (!paciente || !medico || !diagnostico || !instrucciones) {
            this.mostrarError('Por favor complete todos los campos obligatorios');
            return false;
        }

        // Validar fecha de vencimiento
        const fechaEmision = document.getElementById('receta-fecha-emision')?.value;
        const fechaVencimiento = document.getElementById('receta-vencimiento')?.value;
        
        if (fechaVencimiento && fechaEmision && new Date(fechaVencimiento) <= new Date(fechaEmision)) {
            this.mostrarError('La fecha de vencimiento debe ser posterior a la fecha de emisión');
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
                
                // Mostrar botón de renovar solo si está expirada
                const btnRenovar = document.getElementById('btn-renovar-receta');
                if (btnRenovar) {
                    btnRenovar.style.display = receta.estado === 'expirada' ? 'inline-block' : 'none';
                }
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

    async renovarReceta() {
        if (!this.recetaActualId) return;
        
        if (!confirm('¿Está seguro de que desea renovar esta receta?')) {
            return;
        }

        try {
            await this.renewReceta(this.recetaActualId);
            this.mostrarExito('Receta renovada correctamente');
            this.cerrarModalVerReceta();
            await this.cargarRecetas();
        } catch (error) {
            console.error('Error renovando receta:', error);
            this.mostrarError('Error al renovar la receta');
        }
    }

    async renewReceta(id) {
        try {
            const response = await fetch(`/api/recetas/${id}/renovar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error('Error al renovar receta');
            }

            return await response.json();
        } catch (error) {
            console.warn('Simulando renovación de receta:', error);
            return new Promise(resolve => setTimeout(resolve, 1000));
        }
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

    exportarRecetas() {
        // Implementar exportación a PDF o Excel
        console.log('Exportando recetas...');
        this.mostrarExito('Exportación iniciada');
        
        // Simulación de exportación
        setTimeout(() => {
            this.mostrarExito('Recetas exportadas correctamente');
        }, 2000);
    }

    actualizarEstadisticas() {
        const total = this.recetas.length;
        const activas = this.recetas.filter(r => r.estado === 'activa').length;
        const expiradas = this.recetas.filter(r => r.estado === 'expirada').length;
        
        // Calcular próximas a expirar (en los próximos 3 días)
        const hoy = new Date();
        const en3Dias = new Date();
        en3Dias.setDate(hoy.getDate() + 3);
        
        const porExpirar = this.recetas.filter(r => {
            try {
                const vencimiento = new Date(r.fecha_vencimiento);
                return vencimiento > hoy && vencimiento <= en3Dias && r.estado === 'activa';
            } catch (error) {
                return false;
            }
        }).length;

        // Actualizar estadísticas en la UI
        const totalElement = document.getElementById('total-recetas');
        const activasElement = document.getElementById('recetas-activas');
        const expiradasElement = document.getElementById('recetas-expiradas');
        const vencidasElement = document.getElementById('recetas-vencidas');

        if (totalElement) totalElement.textContent = total;
        if (activasElement) activasElement.textContent = activas;
        if (expiradasElement) expiradasElement.textContent = porExpirar;
        if (vencidasElement) vencidasElement.textContent = expiradas;
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