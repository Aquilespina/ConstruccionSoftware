document.addEventListener('DOMContentLoaded', function() {
    // Inicializar módulo de mascotas si está activo
    const mascotasModule = document.getElementById('mod-mascotas');
    if (mascotasModule && mascotasModule.classList.contains('active')) {
        initModuloMascotas();
    }
    
    // Observar cambios en caso de que el módulo se active después
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (mutation.target.id === 'mod-mascotas' && mutation.target.classList.contains('active')) {
                    console.log('Módulo de mascotas activado, inicializando...');
                    initModuloMascotas();
                }
            }
        });
    });
    
    if (mascotasModule) {
        observer.observe(mascotasModule, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
});

// Funcionalidades específicas para módulo de mascotas
function initModuloMascotas() {
    console.log('Inicializando módulo de mascotas...');
    
    const btnNueva = document.getElementById('btn-nueva-mascota');
    if (btnNueva) {
        btnNueva.addEventListener('click', abrirModalMascota);
        console.log('Botón nueva mascota encontrado');
    } else {
        console.log('Botón nueva mascota NO encontrado');
    }
    
    // Agregar funcionalidad de búsqueda
    const searchInput = document.getElementById('search-mascotas');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tabla-mascotas tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    // Agregar funcionalidad de filtros
    const filterEspecie = document.getElementById('filter-especie');
    const filterEstado = document.getElementById('filter-estado-mascota');
    
    if (filterEspecie && filterEstado) {
        [filterEspecie, filterEstado].forEach(select => {
            select.addEventListener('change', aplicarFiltros);
        });
    }
    
    // Cerrar modal al hacer clic fuera del contenido
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('modal-mascota');
        if (modal && event.target === modal) {
            cerrarModalMascota();
        }
    });
    
    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalMascota();
        }
    });
}

function aplicarFiltros() {
    const especie = document.getElementById('filter-especie').value.toLowerCase();
    const estado = document.getElementById('filter-estado-mascota').value.toLowerCase();
    const rows = document.querySelectorAll('#tabla-mascotas tr');
    
    rows.forEach(row => {
        const especieCell = row.cells[1]?.textContent.toLowerCase() || '';
        const estadoCell = row.cells[6]?.textContent.toLowerCase() || ''; // Asumiendo que el estado está en la columna 6
        
        const especieMatch = !especie || especieCell.includes(especie);
        const estadoMatch = !estado || estadoCell.includes(estado);
        
        row.style.display = (especieMatch && estadoMatch) ? '' : 'none';
    });
}

// Función para cargar propietarios desde la API
async function cargarPropietarios() {
    try {
        console.log('Cargando propietarios...');
        // Intentar múltiples endpoints por compatibilidad con distintas rutas de la API.
        const endpoints = [
            '/api/mascotas/propietarios', // endpoint que podría usar el método getPropietarios
            '/api/propietarios',
            '/mascotas/propietarios'
        ];

        let response = null;
        let propietarios = null;

        for (const url of endpoints) {
            try {
                console.log(`Intentando endpoint: ${url}`);
                response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (response && response.ok) {
                    propietarios = await response.json();
                    console.log(`Endpoint válido: ${url}`);
                    break;
                }
            } catch (err) {
                console.warn(`Fallo al llamar ${url}:`, err.message || err);
            }
        }

        if (!propietarios) {
            throw new Error('No se pudo obtener la lista de propietarios desde los endpoints configurados.');
        }
        
        const selectPropietario = document.getElementById('mascota-propietario');
        if (!selectPropietario) {
            console.error('No se encontró el elemento mascota-propietario');
            return [];
        }
        
        // Limpiar opciones existentes excepto la primera
        while (selectPropietario.children.length > 1) {
            selectPropietario.removeChild(selectPropietario.lastChild);
        }
        
        // Agregar propietarios al select
        // Manejar distintos shapes: {id,nombre} o {id_propietario,nombre_propietario}
        propietarios.forEach(propietario => {
            const option = document.createElement('option');
            option.value = propietario.id ?? propietario.id_propietario ?? propietario.idPropietario ?? propietario.ID ?? '';
            option.textContent = propietario.nombre ?? propietario.nombre_propietario ?? propietario.nombreCompleto ?? propietario.name ?? JSON.stringify(propietario);
            selectPropietario.appendChild(option);
        });
        
        console.log('Propietarios cargados:', propietarios.length);
        return propietarios;
    } catch (error) {
        console.error('Error cargando propietarios:', error);
        alert('Error al cargar la lista de propietarios');
        return [];
    }
}

async function abrirModalMascota() {
    const modal = document.getElementById('modal-mascota');
    if (modal) {
        console.log('Abriendo modal de mascota...');
        modal.style.display = 'flex';
        
        // Cargar propietarios cuando se abre el modal
        await cargarPropietarios();
        
        // Restablecer el formulario si es nuevo
        const titulo = document.getElementById('modal-mascota-titulo');
        if (titulo && titulo.textContent === 'Nueva Mascota') {
            const form = document.getElementById('form-mascota');
            if (form) {
                form.reset();
                // Remover el ID de edición si existe
                delete form.dataset.mascotaId;
                // Establecer estado por defecto a "activo"
                document.getElementById('mascota-estado').value = 'activo';
            }
        }
    } else {
        console.error('Modal de mascota no encontrado');
    }
}

function cerrarModalMascota() {
    const modal = document.getElementById('modal-mascota');
    if (modal) {
        modal.style.display = 'none';
    }
}

async function guardarMascota() {
    const form = document.getElementById('form-mascota');
    if (!form) {
        console.error('Formulario de mascota no encontrado');
        return;
    }
    
    // Validación básica
    const nombre = document.getElementById('mascota-nombre').value;
    const especie = document.getElementById('mascota-especie').value;
    const raza = document.getElementById('mascota-raza').value;
    const propietarioId = document.getElementById('mascota-propietario').value;
    
    if (!nombre || !especie || !raza || !propietarioId) {
        alert('Por favor, complete todos los campos obligatorios (*)');
        return;
    }
    
    // Preparar datos para enviar
    const mascotaData = {
        nombre: nombre,
        especie: especie,
        raza: raza,
        edad: document.getElementById('mascota-edad').value,
        peso: document.getElementById('mascota-peso').value,
        sexo: document.getElementById('mascota-sexo').value,
        estado: document.getElementById('mascota-estado').value,
        historial_medico: document.getElementById('mascota-historial')?.value || '',
        id_propietario: parseInt(propietarioId)
    };
    
    // Determinar si es creación o edición
    const mascotaId = form.dataset.mascotaId;
    const url = mascotaId ? `/api/mascotas/${mascotaId}` : '/api/mascotas';
    const method = mascotaId ? 'PUT' : 'POST';
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(mascotaData)
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al guardar la mascota');
        }
        
        const result = await response.json();
        
        // Cerrar modal y mostrar mensaje
        cerrarModalMascota();
        alert(mascotaId ? 'Mascota actualizada correctamente' : 'Mascota guardada correctamente');
        
        // Recargar la página para ver los cambios
        location.reload();
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar la mascota: ' + error.message);
    }
}

function verMascota(id) {
    console.log('Ver mascota:', id);
    // Aquí puedes implementar la lógica para ver detalles completos
    alert(`Funcionalidad de ver mascota - ID: ${id}\n\nEsta función mostraría todos los detalles de la mascota en un modal separado o página de detalle.`);
}

async function editarMascota(id) {
    console.log('Editando mascota ID:', id);
    
    // Primero abrir el modal y cargar propietarios
    await abrirModalMascota();
    const titulo = document.getElementById('modal-mascota-titulo');
    if (titulo) titulo.textContent = 'Editar Mascota';
    
    // Aquí iría la lógica para cargar los datos de la mascota desde la API
    try {
        const response = await fetch(`/api/mascotas/${id}`);
        if (!response.ok) {
            throw new Error('Error al cargar datos de la mascota');
        }
        
        const mascota = await response.json();
        
        // Llenar el formulario con los datos de la mascota
        document.getElementById('mascota-nombre').value = mascota.nombre || '';
        document.getElementById('mascota-especie').value = mascota.especie || '';
        document.getElementById('mascota-raza').value = mascota.raza || '';
        document.getElementById('mascota-propietario').value = mascota.id_propietario || '';
        document.getElementById('mascota-edad').value = mascota.edad || '';
        document.getElementById('mascota-peso').value = mascota.peso || '';
        document.getElementById('mascota-sexo').value = mascota.sexo || '';
        document.getElementById('mascota-estado').value = mascota.estado || 'activo';
        
        // Llenar historial médico si existe el campo
        const historialInput = document.getElementById('mascota-historial');
        if (historialInput) {
            historialInput.value = mascota.historial_medico || '';
        }
        
        // Guardar el ID de la mascota para la actualización
        document.getElementById('form-mascota').dataset.mascotaId = id;
        
    } catch (error) {
        console.error('Error cargando mascota:', error);
        
        // Como fallback, cargar datos de ejemplo basados en la tabla
        const button = document.querySelector(`button[onclick="editarMascota(${id})"]`);
        if (button) {
            const fila = button.closest('tr');
            if (fila) {
                const celdas = fila.cells;
                document.getElementById('mascota-nombre').value = celdas[0].querySelector('div > div:first-child').textContent || '';
                document.getElementById('mascota-especie').value = celdas[1].textContent || '';
                document.getElementById('mascota-raza').value = celdas[2].textContent || '';
                document.getElementById('mascota-edad').value = celdas[4].textContent || '';
            }
        }
        
        alert('Error al cargar los datos de la mascota. Se han cargado datos de ejemplo.');
    }
}