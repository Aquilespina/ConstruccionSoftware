document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const btnNuevoHonorario = document.getElementById('btn-nuevo-honorario');
    const modalHonorario = document.getElementById('modal-honorario');
    const formHonorario = document.getElementById('form-honorario');
    let detalleIndex = 1;
    let conceptosCache = [];

    // Cargar conceptos de receta al inicializar
    cargarConceptosReceta();

    // Abrir modal para nuevo honorario
    if (btnNuevoHonorario) {
        btnNuevoHonorario.addEventListener('click', function() {
            limpiarFormulario();
            modalHonorario.style.display = 'flex';
            modalHonorario.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevenir scroll del body
        });
    }

    // Cerrar modal
    window.cerrarModalHonorario = function() {
        modalHonorario.style.display = 'none';
        modalHonorario.classList.remove('show');
        document.body.style.overflow = 'auto'; // Restaurar scroll del body
        limpiarFormulario();
    };

    // Cerrar modal al hacer clic fuera de él
    window.addEventListener('click', function(event) {
        if (event.target === modalHonorario) {
            cerrarModalHonorario();
        }
    });

    // Guardar honorario
    window.guardarHonorario = function() {
        limpiarErrores();

        // Validación nativa del formulario (required, min, max, step)
        // Necesaria porque el envío es por fetch y no dispara la validación HTML5 automática
        if (!formHonorario.checkValidity()) {
            formHonorario.reportValidity();
            mostrarMensaje('warning', 'Revise los campos marcados: hay datos faltantes o inválidos');
            return;
        }

        // Validación adicional de los detalles
        const detalles = document.querySelectorAll('.detalle-item');
        if (detalles.length === 0) {
            mostrarMensaje('warning', 'Debe agregar al menos un concepto');
            return;
        }
        for (const detalle of detalles) {
            const concepto = detalle.querySelector('.concepto-input').value.trim();
            const cantidad = parseInt(detalle.querySelector('.cantidad-input').value, 10);
            const precio = parseFloat(detalle.querySelector('.precio-input').value);

            if (!concepto) {
                mostrarMensaje('warning', 'El concepto no puede estar vacío');
                return;
            }
            if (!Number.isInteger(cantidad) || cantidad < 1 || cantidad > 9999) {
                mostrarMensaje('warning', 'La cantidad debe ser un entero entre 1 y 9999');
                return;
            }
            if (isNaN(precio) || precio < 0.01 || precio > 999999.99) {
                mostrarMensaje('warning', 'El precio unitario debe estar entre 0.01 y 999999.99');
                return;
            }
        }

        const formData = new FormData(formHonorario);

        // Determinar si es creación o edición
        const isEdit = formHonorario.dataset.mode === 'edit';
        const honorarioId = formHonorario.dataset.honorarioId;
        
        // Configurar URL y método
        let url = '/recepcion/honorarios';
        let method = 'POST';
        
        if (isEdit) {
            url = `/recepcion/honorarios/${honorarioId}`;
            method = 'POST'; // Laravel usa POST con _method=PUT
            formData.append('_method', 'PUT');
        }
        
        // Mostrar indicador de carga
        const btnGuardar = document.querySelector('.btn-primary[onclick="guardarHonorario()"]');
        const textoOriginal = btnGuardar.textContent;
        btnGuardar.textContent = isEdit ? 'Actualizando...' : 'Guardando...';
        btnGuardar.disabled = true;

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensaje('success', data.message);
                cerrarModalHonorario();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                mostrarMensaje('error', data.message);
                if (data.errors) {
                    mostrarErroresValidacion(data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('error', 'Error al procesar la solicitud');
        })
        .finally(() => {
            btnGuardar.textContent = textoOriginal;
            btnGuardar.disabled = false;
        });
    };

    // Agregar nuevo detalle
    window.agregarDetalle = function() {
        const container = document.getElementById('detalles-container');
        const detalleHtml = `
            <div class="detalle-item" data-index="${detalleIndex}">
                <div class="form-row">
                    <div class="form-group">
                        <label>Concepto *</label>
                        <input type="text" name="detalles[${detalleIndex}][concepto]" 
                               class="form-control concepto-input" 
                               placeholder="Descripción del servicio" required>
                        <div class="concepto-suggestions"></div>
                    </div>
                    <div class="form-group form-group-small">
                        <label>Cantidad *</label>
                        <input type="number" name="detalles[${detalleIndex}][cantidad]"
                               class="form-control cantidad-input"
                               min="1" max="9999" step="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Unitario *</label>
                        <input type="number" name="detalles[${detalleIndex}][precio_unitario]"
                               class="form-control precio-input"
                               step="0.01" min="0.01" max="999999.99" required>
                    </div>
                    <div class="form-group">
                        <label>Importe</label>
                        <input type="text" class="form-control importe-display" readonly>
                    </div>
                    <div class="form-group form-group-actions">
                        <button type="button" class="btn-danger btn-small" 
                                onclick="eliminarDetalle(this)" style="margin-top: 25px;">
                            ×
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', detalleHtml);
        detalleIndex++;
        
        // Agregar eventos a los nuevos campos
        const nuevoDetalle = container.lastElementChild;
        configurarEventosDetalle(nuevoDetalle);
    };

    // Eliminar detalle
    window.eliminarDetalle = function(button) {
        const detalleItem = button.closest('.detalle-item');
        const container = document.getElementById('detalles-container');
        
        if (container.children.length > 1) {
            detalleItem.remove();
            calcularSubtotal();
            ocultarAlertaFormulario();
        } else {
            mostrarAlertaFormulario('Debe mantener al menos un concepto');
        }
    };

    // Configurar eventos para cálculos automáticos
    function configurarEventosDetalle(detalleElement) {
        const cantidadInput = detalleElement.querySelector('.cantidad-input');
        const precioInput = detalleElement.querySelector('.precio-input');
        const importeDisplay = detalleElement.querySelector('.importe-display');
        const conceptoInput = detalleElement.querySelector('.concepto-input');

        // Calcular importe automáticamente
        function calcularImporte() {
            const cantidad = parseInt(cantidadInput.value, 10) || 0;
            const precio = parseFloat(precioInput.value) || 0;
            const importe = cantidad * precio;
            importeDisplay.value = '$' + importe.toFixed(2);
            calcularSubtotal();
        }

        cantidadInput.addEventListener('input', calcularImporte);
        precioInput.addEventListener('input', calcularImporte);

        // Autocompletar conceptos
        configurarAutocompletarConcepto(conceptoInput);
    }

    // Configurar autocompletar para conceptos
    function configurarAutocompletarConcepto(input) {
        const suggestionsDiv = input.nextElementSibling;

        input.addEventListener('input', function() {
            const valor = this.value.toLowerCase();
            if (valor.length < 2) {
                suggestionsDiv.innerHTML = '';
                return;
            }

            const sugerencias = conceptosCache.filter(concepto => 
                concepto.concepto.toLowerCase().includes(valor)
            );

            if (sugerencias.length > 0) {
                const suggestionsHtml = sugerencias.slice(0, 5).map(concepto => 
                    `<div class="suggestion-item" data-concepto="${concepto.concepto}" data-precio="${concepto.precio}">
                        ${concepto.concepto} - $${parseFloat(concepto.precio).toFixed(2)}
                    </div>`
                ).join('');

                suggestionsDiv.innerHTML = suggestionsHtml;
                suggestionsDiv.style.display = 'block';

                // Agregar eventos a las sugerencias
                suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
                    item.addEventListener('click', function() {
                        input.value = this.dataset.concepto;
                        const precioInput = input.closest('.detalle-item').querySelector('.precio-input');
                        precioInput.value = this.dataset.precio;
                        suggestionsDiv.innerHTML = '';
                        suggestionsDiv.style.display = 'none';
                        
                        // Recalcular importe
                        const cantidadInput = input.closest('.detalle-item').querySelector('.cantidad-input');
                        const importeDisplay = input.closest('.detalle-item').querySelector('.importe-display');
                        const cantidad = parseFloat(cantidadInput.value) || 1;
                        const precio = parseFloat(precioInput.value) || 0;
                        importeDisplay.value = '$' + (cantidad * precio).toFixed(2);
                        calcularSubtotal();
                    });
                });
            } else {
                suggestionsDiv.innerHTML = '';
                suggestionsDiv.style.display = 'none';
            }
        });

        // Ocultar sugerencias cuando se hace clic fuera
        document.addEventListener('click', function(event) {
            if (!input.contains(event.target) && !suggestionsDiv.contains(event.target)) {
                suggestionsDiv.style.display = 'none';
            }
        });
    }

    // Calcular subtotal
    function calcularSubtotal() {
        let subtotal = 0;
        document.querySelectorAll('.detalle-item').forEach(detalle => {
            const cantidad = parseInt(detalle.querySelector('.cantidad-input').value, 10) || 0;
            const precio = parseFloat(detalle.querySelector('.precio-input').value) || 0;
            subtotal += cantidad * precio;
        });

        document.getElementById('subtotal-display').textContent = subtotal.toFixed(2);
    }

    // Cargar conceptos de receta
    function cargarConceptosReceta() {
        fetch('/recepcion/honorarios/conceptos/receta', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                conceptosCache = data.conceptos;
            }
        })
        .catch(error => {
            console.error('Error al cargar conceptos:', error);
        });
    }

    // Configurar eventos iniciales
    document.querySelectorAll('.detalle-item').forEach(configurarEventosDetalle);

    // Funciones auxiliares
    function limpiarFormulario() {
        formHonorario.reset();
        
        // Resetear modo de edición
        formHonorario.removeAttribute('data-mode');
        formHonorario.removeAttribute('data-honorario-id');
        
        // Restaurar título y botón originales
        document.querySelector('#modal-honorario .modal-header h3').textContent = 'Nuevo Honorario';
        document.querySelector('.btn-primary[onclick="guardarHonorario()"]').textContent = 'Registrar Honorario';
        
        document.getElementById('detalles-container').innerHTML = `
            <div class="detalle-item" data-index="0">
                <div class="form-row">
                    <div class="form-group">
                        <label>Concepto *</label>
                        <input type="text" name="detalles[0][concepto]" 
                               class="form-control concepto-input" 
                               placeholder="Descripción del servicio" required>
                        <div class="concepto-suggestions"></div>
                    </div>
                    <div class="form-group form-group-small">
                        <label>Cantidad *</label>
                        <input type="number" name="detalles[0][cantidad]"
                               class="form-control cantidad-input"
                               min="1" max="9999" step="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Unitario *</label>
                        <input type="number" name="detalles[0][precio_unitario]"
                               class="form-control precio-input"
                               step="0.01" min="0.01" max="999999.99" required>
                    </div>
                    <div class="form-group">
                        <label>Importe</label>
                        <input type="text" class="form-control importe-display" readonly>
                    </div>
                    <div class="form-group form-group-actions">
                        <button type="button" class="btn-danger btn-small" 
                                onclick="eliminarDetalle(this)" style="margin-top: 25px;">
                            ×
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('subtotal-display').textContent = '0.00';
        detalleIndex = 1;
        limpiarErrores();
        ocultarAlertaFormulario();
        
        // Reconfigurar eventos
        document.querySelectorAll('.detalle-item').forEach(configurarEventosDetalle);
    }

    function mostrarMensaje(tipo, mensaje) {
        const mensajeDiv = document.createElement('div');
        mensajeDiv.className = `alert alert-${tipo === 'success' ? 'success' : (tipo === 'warning' ? 'warning' : 'danger')}`;
        mensajeDiv.innerHTML = `
            <span>${mensaje}</span>
            <button type="button" class="close" onclick="this.parentElement.remove()">×</button>
        `;

        const contenido = document.querySelector('.module-header');
        if (contenido) {
            contenido.insertAdjacentElement('afterend', mensajeDiv);
        }

        setTimeout(() => {
            if (mensajeDiv.parentNode) {
                mensajeDiv.remove();
            }
        }, 5000);
    }

    function mostrarErroresValidacion(errores) {
        Object.keys(errores).forEach(campo => {
            const input = document.querySelector(`[name="${campo}"]`);
            if (input) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = errores[campo][0];
                
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
                input.classList.add('error');
            }
        });
    }

    function limpiarErrores() {
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    }

    // Función global para editar honorario
    window.editarHonorario = function(id) {
        fetch(`/recepcion/honorarios/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Llenar el formulario con los datos existentes
                cargarDatosParaEdicion(data.honorario, data.detalles);
                // Cambiar el modo del formulario a edición
                formHonorario.dataset.mode = 'edit';
                formHonorario.dataset.honorarioId = id;
                // Cambiar título y botón
                document.querySelector('#modal-honorario .modal-header h3').textContent = 'Editar Honorario';
                document.querySelector('.btn-primary[onclick="guardarHonorario()"]').textContent = 'Actualizar Honorario';
                // Abrir modal
                modalHonorario.style.display = 'flex';
                modalHonorario.classList.add('show');
                document.body.style.overflow = 'hidden';
            } else {
                mostrarMensaje('error', data.message || 'Error al cargar el honorario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('error', 'Error al cargar el honorario para edición');
        });
    };

    // Función para cargar datos en el formulario para edición
    function cargarDatosParaEdicion(honorario, detalles) {
        // Llenar campos básicos
        document.getElementById('honorario-mascota').value = honorario.id_mascota;
        document.getElementById('honorario-hospitalizacion').value = honorario.id_hospitalizacion || '';
        document.getElementById('honorario-fecha-ingreso').value = honorario.fecha_ingreso;
        document.getElementById('honorario-fecha-corte').value = honorario.fecha_corte || '';

        // Limpiar detalles existentes
        const container = document.getElementById('detalles-container');
        container.innerHTML = '';

        // Agregar detalles existentes
        detalles.forEach((detalle, index) => {
            const detalleHtml = `
                <div class="detalle-item" data-index="${index}">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Concepto *</label>
                            <input type="text" name="detalles[${index}][concepto]" 
                                   class="form-control concepto-input" 
                                   value="${detalle.concepto}" 
                                   placeholder="Descripción del servicio" required>
                            <div class="concepto-suggestions"></div>
                        </div>
                        <div class="form-group form-group-small">
                            <label>Cantidad *</label>
                            <input type="number" name="detalles[${index}][cantidad]"
                                   class="form-control cantidad-input"
                                   value="${detalle.cantidad}"
                                   min="1" max="9999" step="1" required>
                        </div>
                        <div class="form-group">
                            <label>Precio Unitario *</label>
                            <input type="number" name="detalles[${index}][precio_unitario]"
                                   class="form-control precio-input"
                                   value="${detalle.precio_unitario}"
                                   step="0.01" min="0.01" max="999999.99" required>
                        </div>
                        <div class="form-group">
                            <label>Importe</label>
                            <input type="text" class="form-control importe-display" 
                                   value="$${(detalle.cantidad * detalle.precio_unitario).toFixed(2)}" readonly>
                        </div>
                        <div class="form-group form-group-actions">
                            <button type="button" class="btn-danger btn-small" 
                                    onclick="eliminarDetalle(this)" style="margin-top: 25px;">
                                ×
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', detalleHtml);
        });

        // Actualizar índice global
        detalleIndex = detalles.length;

        // Reconfigurar eventos para los nuevos elementos
        document.querySelectorAll('.detalle-item').forEach(configurarEventosDetalle);

        // Calcular subtotal inicial
        calcularSubtotal();
    }

    // Variables para el modal de pago
    const modalPago = document.getElementById('modal-pago');
    const formPago = document.getElementById('form-pago');

    // Función global para abrir modal de pago
    window.abrirModalPago = function(idHonorario) {
        // Limpiar formulario
        formPago.reset();
        document.getElementById('pago-id-honorario').value = idHonorario;
        
        // Cargar información del honorario
        fetch(`/recepcion/honorarios/${idHonorario}/info-pago`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarInfoHonorarioPago(data.honorario, data.detalles);
                modalPago.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            } else {
                mostrarMensaje('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('error', 'Error al cargar información del honorario');
        });
    };

    // Cerrar modal de pago
    window.cerrarModalPago = function() {
        modalPago.style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('pago-preview').style.display = 'none';
    };

    // Mostrar información del honorario en el modal de pago
    function mostrarInfoHonorarioPago(honorario, detalles) {
        const infoDiv = document.getElementById('info-honorario-pago');
        
        // Calcular porcentaje pagado
        const porcentajePagado = honorario.subtotal > 0 ? (honorario.total_pagado / honorario.subtotal * 100) : 0;
        
        // Generar lista de conceptos con sus estados
        let conceptosHtml = '';
        if (detalles && detalles.length > 0) {
            conceptosHtml = `
                <div style="margin-top: 15px;">
                    <h5 style="margin-bottom: 10px;">Conceptos:</h5>
                    <div style="max-height: 120px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px;">
                        ${detalles.map(detalle => {
                            const porcentajeConcepto = detalle.porcentaje_pagado || 0;
                            const estadoColor = detalle.estado_calculado === 'Pagado' ? '#10b981' : 
                                              detalle.estado_calculado === 'Parcial' ? '#f59e0b' : '#ef4444';
                            return `
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: 1px solid #f3f4f6;">
                                    <span style="font-size: 12px;">${detalle.concepto}</span>
                                    <div style="text-align: right;">
                                        <span style="font-size: 11px; color: ${estadoColor}; font-weight: bold;">
                                            ${detalle.estado_calculado} (${porcentajeConcepto.toFixed(0)}%)
                                        </span><br>
                                        <span style="font-size: 10px; color: #6b7280;">
                                            $${parseFloat(detalle.monto_pagado || 0).toFixed(2)} / $${parseFloat(detalle.importe).toFixed(2)}
                                        </span>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;
        }
        
        infoDiv.innerHTML = `
            <h4>Honorario #${honorario.id_honorario}</h4>
            <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 10px;">
                <div>
                    <strong>Mascota:</strong> ${honorario.mascota_nombre}<br>
                    <strong>Propietario:</strong> ${honorario.propietario_nombre}<br>
                    <strong>Estado:</strong> <span class="status-badge status-${honorario.estado.toLowerCase()}">${honorario.estado}</span>
                </div>
                <div>
                    <strong>Subtotal:</strong> $${parseFloat(honorario.subtotal).toFixed(2)}<br>
                    <strong>Total Pagado:</strong> $${parseFloat(honorario.total_pagado).toFixed(2)}<br>
                    <strong>Saldo Pendiente:</strong> <span style="color: #dc2626; font-weight: bold;">$${parseFloat(honorario.saldo_pendiente).toFixed(2)}</span>
                </div>
            </div>
            <div class="progress-bar" style="margin-top: 15px;">
                <div class="progress-fill" style="width: ${porcentajePagado}%; background-color: #10b981; height: 8px; border-radius: 4px; background: linear-gradient(to right, #e5e7eb ${porcentajePagado}%, #10b981 ${porcentajePagado}%);"></div>
                <small style="color: #6b7280;">${porcentajePagado.toFixed(1)}% pagado</small>
            </div>
            ${conceptosHtml}
        `;

        // Configurar límite máximo del input
        const inputMonto = document.getElementById('pago-monto');
        inputMonto.max = honorario.saldo_pendiente;
        inputMonto.placeholder = `Máximo: $${parseFloat(honorario.saldo_pendiente).toFixed(2)}`;
    }

    // Procesar pago
    window.procesarPago = function() {
        limpiarErrores();

        // Validación nativa (el envío por fetch no dispara la validación HTML5)
        if (!formPago.checkValidity()) {
            formPago.reportValidity();
            mostrarMensaje('warning', 'Revise los campos del pago: hay datos faltantes o inválidos');
            return;
        }

        const inputMonto = document.getElementById('pago-monto');
        const monto = parseFloat(inputMonto.value);
        const saldoMax = parseFloat(inputMonto.max);

        if (isNaN(monto) || monto < 0.01) {
            mostrarMensaje('warning', 'El monto debe ser mayor a 0');
            return;
        }
        if (!isNaN(saldoMax) && monto > saldoMax) {
            mostrarMensaje('warning', `El monto no puede ser mayor al saldo pendiente ($${saldoMax.toFixed(2)})`);
            return;
        }

        const idHonorario = document.getElementById('pago-id-honorario').value;
        const formData = new FormData(formPago);

        const btnProcesar = document.querySelector('.btn-primary[onclick="procesarPago()"]');
        const textoOriginal = btnProcesar.textContent;
        btnProcesar.textContent = 'Procesando...';
        btnProcesar.disabled = true;

        fetch(`/recepcion/honorarios/${idHonorario}/pago`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data); // Debug
            if (data.success) {
                // Crear mensaje más detallado sobre el pago
                let mensajeDetallado = data.message;
                if (data.data && data.data.conceptos_actualizados && data.data.conceptos_actualizados.length > 0) {
                    const conceptosPagados = data.data.conceptos_actualizados.filter(c => c.estado === 'Pagado').length;
                    const conceptosParciales = data.data.conceptos_actualizados.filter(c => c.estado === 'Parcial').length;
                    
                    if (conceptosPagados > 0 || conceptosParciales > 0) {
                        mensajeDetallado += ` - ${conceptosPagados} concepto(s) pagado(s) completamente`;
                        if (conceptosParciales > 0) {
                            mensajeDetallado += `, ${conceptosParciales} parcialmente`;
                        }
                    }
                }
                
                mostrarMensaje('success', mensajeDetallado);
                
                // Actualizar la fila del honorario en la tabla si existe
                const filaHonorario = document.querySelector(`tr[data-id="${idHonorario}"]`);
                if (filaHonorario && data.data) {
                    // Actualizar columnas específicas
                    const columnaEstado = filaHonorario.querySelector('.estado-badge');
                    const columnaTotalPagado = filaHonorario.querySelector('.total-pagado');
                    const columnaSaldoPendiente = filaHonorario.querySelector('.saldo-pendiente');
                    
                    if (columnaEstado) {
                        columnaEstado.textContent = data.data.nuevo_estado;
                        columnaEstado.className = `status-badge estado-badge status-${data.data.nuevo_estado.toLowerCase()}`;
                    }
                    if (columnaTotalPagado) {
                        columnaTotalPagado.textContent = `$${parseFloat(data.data.total_pagado).toFixed(2)}`;
                    }
                    if (columnaSaldoPendiente) {
                        columnaSaldoPendiente.textContent = `$${parseFloat(data.data.nuevo_saldo).toFixed(2)}`;
                    }
                }
                
                cerrarModalPago();
                
                // Recargar después de 2.5 segundos para mostrar cambios
                setTimeout(() => {
                    window.location.reload();
                }, 2500);
            } else {
                mostrarMensaje('error', data.message || 'Error al procesar el pago');
                if (data.errors) {
                    mostrarErroresValidacion(data.errors);
                }
                console.error('Errores de validación:', data.errors); // Debug
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('error', 'Error al procesar el pago');
        })
        .finally(() => {
            btnProcesar.textContent = textoOriginal;
            btnProcesar.disabled = false;
        });
    };

    // Preview del pago cuando cambia el monto
    if (document.getElementById('pago-monto')) {
        document.getElementById('pago-monto').addEventListener('input', function() {
            const monto = parseFloat(this.value) || 0;
            if (monto > 0) {
                mostrarPreviewPago(monto);
            } else {
                document.getElementById('pago-preview').style.display = 'none';
            }
        });
    }

    function mostrarAlertaFormulario(mensaje) {
        const alerta = document.getElementById('honorario-form-alert');
        if (alerta) {
            document.getElementById('honorario-form-alert-msg').textContent = mensaje;
            alerta.style.display = 'block';
        }
    }

    function ocultarAlertaFormulario() {
        const alerta = document.getElementById('honorario-form-alert');
        if (alerta) alerta.style.display = 'none';
    }

    function mostrarPreviewPago(monto) {
        const previewDiv = document.getElementById('pago-preview');
        const idHonorario = document.getElementById('pago-id-honorario').value;
        
        if (monto > 0) {
            previewDiv.innerHTML = `
                <h4>Vista Previa del Pago</h4>
                <div class="preview-content">
                    <div style="background: #f0f9ff; padding: 15px; border-radius: 8px; border-left: 4px solid #0ea5e9;">
                        <strong>Monto a pagar:</strong> $${monto.toFixed(2)}<br>
                        <small style="color: #6b7280;">
                            El pago se distribuirá automáticamente entre los conceptos pendientes, 
                            comenzando por los conceptos más antiguos (primeros agregados).
                        </small>
                    </div>
                </div>
            `;
            previewDiv.style.display = 'block';
        } else {
            previewDiv.style.display = 'none';
        }
    }

    // ── Filtros automáticos ──────────────────────────────────────────────────

    function filtrarTabla() {
        const busqueda    = (document.getElementById('search-honorarios')?.value ?? '').toLowerCase().trim();
        const estadoFiltro  = document.getElementById('filter-estado')?.value  ?? '';
        const mascotaFiltro = document.getElementById('filter-mascota')?.value ?? '';

        const filas = document.querySelectorAll('.data-table tbody tr:not(#fila-sin-resultados)');
        let visibles = 0;

        filas.forEach(fila => {
            const texto   = fila.dataset.texto   ?? fila.textContent.toLowerCase();
            const estado  = fila.dataset.estado  ?? '';
            const mascota = fila.dataset.mascota ?? '';

            const okBusqueda = !busqueda       || texto.includes(busqueda);
            const okEstado   = !estadoFiltro   || estado === estadoFiltro;
            const okMascota  = !mascotaFiltro  || mascota === mascotaFiltro;

            const visible = okBusqueda && okEstado && okMascota;
            fila.style.display = visible ? '' : 'none';
            if (visible) visibles++;
        });

        const filaSinResultados = document.getElementById('fila-sin-resultados');
        if (filaSinResultados) {
            filaSinResultados.style.display = visibles === 0 ? '' : 'none';
        }
    }

    const inputBusqueda  = document.getElementById('search-honorarios');
    const selectEstado   = document.getElementById('filter-estado');
    const selectMascota  = document.getElementById('filter-mascota');

    if (inputBusqueda) inputBusqueda.addEventListener('input',  filtrarTabla);
    if (selectEstado)  selectEstado.addEventListener('change',  filtrarTabla);
    if (selectMascota) selectMascota.addEventListener('change', filtrarTabla);

});