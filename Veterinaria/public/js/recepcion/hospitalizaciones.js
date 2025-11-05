// Variables globales
let csrfToken = '';

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Obtener token CSRF
    csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                document.querySelector('input[name="_token"]')?.value || '';
    
    // Event listeners
    const btnNuevaHospitalizacion = document.getElementById('btn-nueva-hospitalizacion');
    if (btnNuevaHospitalizacion) {
        btnNuevaHospitalizacion.addEventListener('click', abrirModalHospitalizacion);
    }
    
    const searchInput = document.getElementById('search-hospitalizaciones');
    if (searchInput) {
        searchInput.addEventListener('input', filtrarTabla);
    }
    
    const estadoFilter = document.getElementById('filter-estado-hospitalizacion');
    if (estadoFilter) {
        estadoFilter.addEventListener('change', filtrarTabla);
    }

    // Event listener para el formulario
    const formHospitalizacion = document.getElementById('form-hospitalizacion');
    if (formHospitalizacion) {
        formHospitalizacion.addEventListener('submit', function(e) {
            e.preventDefault();
            guardarHospitalizacion();
        });
    }

    // Establecer fecha y hora actual por defecto
    const fechaIngresoInput = document.getElementById('hospitalizacion-fecha-ingreso');
    if (fechaIngresoInput) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        fechaIngresoInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
});

// Filtrar tabla de hospitalizaciones
function filtrarTabla() {
    const searchInput = document.getElementById('search-hospitalizaciones');
    const estadoFilter = document.getElementById('filter-estado-hospitalizacion');
    
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const estado = estadoFilter ? estadoFilter.value.toLowerCase() : '';
    
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        // Índices corregidos según la nueva estructura de la tabla:
        // 0: ID, 1: Mascota, 2: Especie, 3: Propietario, 4: Fecha Ingreso, 5: Fecha Egreso, 6: Estado, 7: Acciones
        const mascotaCell = row.cells[1]?.textContent.toLowerCase() || '';
        const especieCell = row.cells[2]?.textContent.toLowerCase() || '';
        const propietarioCell = row.cells[3]?.textContent.toLowerCase() || '';
        const estadoCell = row.cells[6]?.textContent.toLowerCase() || '';
        
        const matchesSearch = 
            mascotaCell.includes(searchTerm) ||
            especieCell.includes(searchTerm) ||
            propietarioCell.includes(searchTerm);
        
        const matchesEstado = !estado || estadoCell.includes(estado);
        
        if (matchesSearch && matchesEstado) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Filtrar hospitalizaciones
function filtrarHospitalizaciones() {
    const searchTerm = searchInput.value.toLowerCase();
    const estado = estadoFilter.value;
    const area = areaFilter.value;
    
    const hospitalizacionesFiltradas = hospitalizaciones.filter(hosp => {
        const matchesSearch = 
            hosp.nombre.toLowerCase().includes(searchTerm) ||
            hosp.propietario.toLowerCase().includes(searchTerm) ||
            hosp.diagnostico.toLowerCase().includes(searchTerm);
        
        const matchesEstado = !estado || hosp.estado === estado;
        
        // En este ejemplo, el área se determina por la cama
        let hospArea = '';
        if (hosp.cama.startsWith('UCI')) hospArea = 'uci';
        else if (hosp.cama.startsWith('GEN')) hospArea = 'general';
        else if (hosp.cama.startsWith('AIS')) hospArea = 'aislamiento';
        
        const matchesArea = !area || hospArea === area;
        
        return matchesSearch && matchesEstado && matchesArea;
    });
    
    renderHospitalizaciones(hospitalizacionesFiltradas);
}

// Funciones del modal
function abrirModalHospitalizacion() {
    const modal = document.getElementById('modal-hospitalizacion');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function cerrarModalHospitalizacion() {
    const modal = document.getElementById('modal-hospitalizacion');
    const form = document.getElementById('form-hospitalizacion');
    
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    if (form) {
        form.reset();
    }
}

async function guardarHospitalizacion() {
    // Obtener elementos del formulario corregidos
    const mascotaSelect = document.getElementById('hospitalizacion-mascota');
    const fechaIngresoInput = document.getElementById('hospitalizacion-fecha-ingreso');
    const estadoSelect = document.getElementById('hospitalizacion-estado');
    const observacionesTextarea = document.getElementById('hospitalizacion-observaciones');
    const citaSelect = document.getElementById('hospitalizacion-cita');
    
    // Validación básica
    const mascotaId = mascotaSelect?.value;
    const fechaIngreso = fechaIngresoInput?.value;
    const estado = estadoSelect?.value;
    
    if (!mascotaId || !fechaIngreso || !estado) {
        mostrarAlerta('Por favor, complete todos los campos obligatorios (Mascota, Fecha de Ingreso y Estado).', 'error');
        return;
    }
    
    try {
        // Deshabilitar botón para evitar doble envío
        const btnGuardar = document.querySelector('.btn-primary[onclick="guardarHospitalizacion()"]');
        if (btnGuardar) {
            btnGuardar.disabled = true;
            btnGuardar.textContent = 'Guardando...';
        }
        
        // Preparar datos para envío usando FormData
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('id_mascota', mascotaId);
        formData.append('fecha_ingreso', fechaIngreso);
        formData.append('estado', estado);
        
        // Campos opcionales
        if (observacionesTextarea?.value) {
            formData.append('observaciones', observacionesTextarea.value);
        }
        
        if (citaSelect?.value) {
            formData.append('id_cita', citaSelect.value);
        }
        
        console.log('Enviando datos:', {
            id_mascota: mascotaId,
            fecha_ingreso: fechaIngreso,
            estado: estado,
            observaciones: observacionesTextarea?.value,
            id_cita: citaSelect?.value
        });
        
        // Enviar datos al servidor
        const response = await fetch('/recepcion/hospitalizaciones', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            cerrarModalHospitalizacion();
            mostrarAlerta('Hospitalización registrada correctamente.', 'success');
            setTimeout(() => {
                location.reload(); // Recargar para mostrar los nuevos datos
            }, 1500);
        } else {
            // Intentar obtener el mensaje de error del servidor
            let errorMessage = 'Error al guardar la hospitalización';
            try {
                const errorData = await response.json();
                if (errorData.message) {
                    errorMessage = errorData.message;
                } else if (errorData.errors) {
                    const errors = Object.values(errorData.errors).flat();
                    errorMessage = errors.join(', ');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }
            throw new Error(errorMessage);
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarAlerta(`Error al registrar la hospitalización: ${error.message}`, 'error');
    } finally {
        // Rehabilitar botón
        const btnGuardar = document.querySelector('.btn-primary[onclick="guardarHospitalizacion()"]');
        if (btnGuardar) {
            btnGuardar.disabled = false;
            btnGuardar.textContent = 'Registrar Hospitalización';
        }
    }
}

// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo = 'info') {
    // Crear el elemento de alerta
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo}`;
    alerta.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        max-width: 400px;
        animation: slideInRight 0.3s ease;
    `;
    
    // Colores según el tipo
    switch(tipo) {
        case 'success':
            alerta.style.backgroundColor = '#10b981';
            break;
        case 'error':
            alerta.style.backgroundColor = '#ef4444';
            break;
        case 'warning':
            alerta.style.backgroundColor = '#f59e0b';
            break;
        default:
            alerta.style.backgroundColor = '#3b82f6';
    }
    
    alerta.textContent = mensaje;
    
    // Agregar al DOM
    document.body.appendChild(alerta);
    
    // Remover después de 5 segundos
    setTimeout(() => {
        alerta.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (alerta.parentNode) {
                alerta.parentNode.removeChild(alerta);
            }
        }, 300);
    }, 5000);
}

// Agregar CSS para animaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
`;
document.head.appendChild(style);

// Cerrar modal al hacer clic fuera del contenido
window.addEventListener('click', function(event) {
    const modal = document.getElementById('modal-hospitalizacion');
    if (event.target === modal) {
        cerrarModalHospitalizacion();
    }
});

// Manejar tecla Escape para cerrar modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        cerrarModalHospitalizacion();
    }
});