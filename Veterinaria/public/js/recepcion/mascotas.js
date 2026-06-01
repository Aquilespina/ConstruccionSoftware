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

let mascotaDetalleActualId = null;

function normalizarTextoMascota(valor, fallback = '-') {
    if (valor === null || valor === undefined || valor === '') {
        return fallback;
    }

    return String(valor);
}

function obtenerAvatarMascota(especie) {
    const especieNormalizada = String(especie || '').toLowerCase();

    if (especieNormalizada.includes('gat')) {
        return '🐈';
    }

    if (especieNormalizada.includes('ave')) {
        return '🐦';
    }

    if (especieNormalizada.includes('rep')) {
        return '🦎';
    }

    if (especieNormalizada.includes('rod')) {
        return '🐹';
    }

    return '🐕';
}

function mostrarTextoDetalleMascota(id, valor, fallback = '-') {
    const elemento = document.getElementById(id);
    if (elemento) {
        elemento.textContent = normalizarTextoMascota(valor, fallback);
    }
}

function mostrarEtiquetaDetalleMascota(id, texto) {
    const elemento = document.getElementById(id);
    if (elemento) {
        elemento.textContent = texto;
    }
}

function normalizarSexoMascota(sexo) {
    const valor = String(sexo || '').trim().toLowerCase();

    if (valor === 'm' || valor === 'macho' || valor === 'masculino') {
        return 'macho';
    }

    if (valor === 'f' || valor === 'hembra' || valor === 'femenino') {
        return 'hembra';
    }

    return '';
}

function cargarDetalleMascotaEnVista(mascota) {
    const propietarioNombre = mascota?.propietario?.nombre || mascota?.propietario_nombre || 'Sin propietario';
    const especie = mascota?.especie || '-';
    const estado = mascota?.estado || '-';
    const sexo = mascota?.sexo || '-';
    const avatar = obtenerAvatarMascota(especie);

    mostrarTextoDetalleMascota('ver-mascota-nombre', mascota?.nombre || 'Detalle de Mascota');
    mostrarTextoDetalleMascota('ver-mascota-id', `ID: ${mascota?.id_mascota || mascota?.id || '-'}`);
    mostrarTextoDetalleMascota('ver-mascota-especie', `Especie: ${especie}`);
    mostrarTextoDetalleMascota('ver-mascota-estado', `Estado: ${estado}`);
    mostrarTextoDetalleMascota('ver-mascota-raza', `Raza: ${mascota?.raza || '-'}`);
    mostrarTextoDetalleMascota('ver-mascota-propietario', `Propietario: ${propietarioNombre}`);
    mostrarTextoDetalleMascota('ver-mascota-sexo', sexo);
    mostrarTextoDetalleMascota('ver-mascota-edad', mascota?.edad ?? '-');
    mostrarTextoDetalleMascota('ver-mascota-peso', mascota?.peso !== null && mascota?.peso !== undefined && mascota?.peso !== '' ? `${mascota.peso} kg` : '-');
    mostrarTextoDetalleMascota('ver-mascota-ultima-visita', mascota?.ultima_visita || '-');
    mostrarTextoDetalleMascota('ver-mascota-created-at', mascota?.created_at || '-');
    mostrarTextoDetalleMascota('ver-mascota-updated-at', mascota?.updated_at || '-');
    mostrarTextoDetalleMascota('ver-mascota-historial', mascota?.historial_medico || 'Sin historial médico registrado.');
    mostrarTextoDetalleMascota('ver-mascota-total-citas', mascota?.total_citas ?? 0);
    mostrarTextoDetalleMascota('ver-mascota-avatar', avatar);

    mostrarEtiquetaDetalleMascota('ver-mascota-estado', `Estado: ${estado}`);
}

function abrirModalVerMascota() {
    const modal = document.getElementById('modal-ver-mascota');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function cerrarModalVerMascota() {
    const modal = document.getElementById('modal-ver-mascota');
    if (modal) {
        modal.style.display = 'none';
    }
}

function limpiarModalVerMascota() {
    mostrarTextoDetalleMascota('ver-mascota-nombre', 'Detalle de Mascota');
    mostrarTextoDetalleMascota('ver-mascota-id', 'ID: -');
    mostrarTextoDetalleMascota('ver-mascota-especie', 'Especie: -');
    mostrarTextoDetalleMascota('ver-mascota-estado', 'Estado: -');
    mostrarTextoDetalleMascota('ver-mascota-raza', 'Raza: -');
    mostrarTextoDetalleMascota('ver-mascota-propietario', 'Propietario: -');
    mostrarTextoDetalleMascota('ver-mascota-sexo', '-');
    mostrarTextoDetalleMascota('ver-mascota-edad', '-');
    mostrarTextoDetalleMascota('ver-mascota-peso', '-');
    mostrarTextoDetalleMascota('ver-mascota-ultima-visita', '-');
    mostrarTextoDetalleMascota('ver-mascota-created-at', '-');
    mostrarTextoDetalleMascota('ver-mascota-updated-at', '-');
    mostrarTextoDetalleMascota('ver-mascota-historial', 'Sin historial médico registrado.');
    mostrarTextoDetalleMascota('ver-mascota-total-citas', '0');
    mostrarTextoDetalleMascota('ver-mascota-avatar', '🐾');
}

async function verMascota(id) {
    mascotaDetalleActualId = id;
    abrirModalVerMascota();
    limpiarModalVerMascota();
    mostrarTextoDetalleMascota('ver-mascota-nombre', 'Cargando...');
    mostrarTextoDetalleMascota('ver-mascota-id', `ID: ${id}`);

    try {
        const response = await fetch(`/api/mascotas/${id}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('No se pudo cargar el detalle de la mascota');
        }

        const mascota = await response.json();
        cargarDetalleMascotaEnVista(mascota);
    } catch (error) {
        console.error('Error cargando detalle de mascota:', error);
        mostrarTextoDetalleMascota('ver-mascota-nombre', 'Error al cargar detalle');
        mostrarTextoDetalleMascota('ver-mascota-historial', 'No fue posible obtener la información de la mascota.');
        alert('No se pudo cargar el detalle de la mascota.');
    }
}

function editarMascotaDesdeDetalle() {
    if (!mascotaDetalleActualId) {
        return;
    }

    cerrarModalVerMascota();
    editarMascota(mascotaDetalleActualId);
}

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
        const modalMascota = document.getElementById('modal-mascota');
        const modalVerMascota = document.getElementById('modal-ver-mascota');

        if (modalMascota && event.target === modalMascota) {
            cerrarModalMascota();
        }

        if (modalVerMascota && event.target === modalVerMascota) {
            cerrarModalVerMascota();
        }
    });
    
    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalMascota();
            cerrarModalVerMascota();
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
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(mascotaData)
        });
        
        if (!response.ok) {
            const contentType = response.headers.get('content-type') || '';
            let errorMessage = 'Error al guardar la mascota';

            if (contentType.includes('application/json')) {
                const errorData = await response.json();
                errorMessage = errorData.message || errorData.error || errorMessage;
            } else {
                const errorText = await response.text();
                errorMessage = errorText.includes('<!DOCTYPE') || errorText.includes('<script>')
                    ? 'El servidor devolvió una respuesta no válida. Revisa que no haya un dd() o un error 500 en el backend.'
                    : (errorText || errorMessage);
            }

            throw new Error(errorMessage);
        }

        const contentType = response.headers.get('content-type') || '';
        let result = null;

        if (contentType.includes('application/json')) {
            result = await response.json();
        } else {
            const responseText = await response.text();
            throw new Error(
                responseText.includes('<!DOCTYPE') || responseText.includes('<script>')
                    ? 'El servidor devolvió una respuesta no válida. Revisa que no haya un dd() o un error 500 en el backend.'
                    : (responseText || 'El servidor no devolvió JSON válido')
            );
        }
        
        // Cerrar modal y mostrar mensaje
        cerrarModalMascota();
        alert(mascotaId ? 'Mascota actualizada correctamente' : 'Mascota guardada correctamente');

        // Si la función cargarDashboard() está disponible (estamos en el home), actualizar el dashboard sin recargar
        if (typeof cargarDashboard === 'function') {
            try { cargarDashboard(); } catch (e) { console.warn('cargarDashboard falló:', e); location.reload(); }
        } else {
            // Si no está disponible, recargamos para asegurar consistencia
            location.reload();
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar la mascota: ' + error.message);
    }
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
        document.getElementById('mascota-sexo').value = normalizarSexoMascota(mascota.sexo);
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