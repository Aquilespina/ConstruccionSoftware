function inicializarPropietarios() {
    const btnNuevo = document.getElementById('btn-nuevo-propietario');
    if (btnNuevo && !btnNuevo.dataset.listenerAsignado) {
        btnNuevo.addEventListener('click', abrirModalPropietario);
        btnNuevo.dataset.listenerAsignado = '1';
    }

    // Cerrar modal al hacer clic fuera del contenido
    const modal = document.getElementById('modal-propietario');
    if (modal && !modal.dataset.listenerAsignado) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                cerrarModalPropietario();
            }
        });
        modal.dataset.listenerAsignado = '1';
    }

    const input = document.getElementById('search-propietarios');
    if (input && !input.dataset.listenerAsignado) {
        input.addEventListener('input', buscarPropietarios);
        input.dataset.listenerAsignado = '1';
    }

    const filtroEstado = document.getElementById('filter-estado-propietarios');
    if (filtroEstado && !filtroEstado.dataset.listenerAsignado) {
        filtroEstado.addEventListener('change', buscarPropietarios);
        filtroEstado.dataset.listenerAsignado = '1';
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarPropietarios);
} else {
    inicializarPropietarios();
}

function abrirModalPropietario() {
  const modal = document.getElementById('modal-propietario');
  if (modal) {
    modal.style.display = 'flex';
    document.getElementById('modal-propietario-titulo').textContent = 'Nuevo Propietario';
    document.getElementById('form-propietario').reset();
    
    // Establecer fecha actual por defecto
    const fechaInput = document.getElementById('propietario-fecha');
    if (fechaInput) {
      fechaInput.value = new Date().toISOString().split('T')[0];
    }

        const estadoInput = document.getElementById('propietario-estado');
        if (estadoInput) {
            estadoInput.value = '1';
            estadoInput.disabled = true;
        }
    
    // Asegurarse de que el método sea POST para nuevo
    const form = document.getElementById('form-propietario');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', '/recepcion/propietarios');
    
    // Limpiar ID si existe
    const idInput = document.getElementById('propietario-id');
    if (idInput) idInput.value = '';
    
    // Remover campo _method si existe (para nuevos registros)
    let methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) {
      methodInput.remove();
    }
  }
}

function cerrarModalPropietario() {
  const modal = document.getElementById('modal-propietario');
  if (modal) modal.style.display = 'none';
}
async function guardarPropietario() {
    const form = document.getElementById('form-propietario');
    if (!form) {
        console.error('Formulario de propietario no encontrado');
        return;
    }
    
    // Validar campos requeridos
    const nombre = document.getElementById('propietario-nombre').value;
    if (!nombre.trim()) {
        alert('El nombre es requerido');
        return;
    }if (!form.checkValidity()) {
    form.reportValidity();
    return;
}
    
    const url = form.getAttribute('action');
    const formData = new FormData(form);

    try {
        const response = await fetch(url, {
            credentials: 'include',
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        });

        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (!response.ok) {
            let errorMessage = `Error ${response.status}: ${response.statusText}`;
            
            // Intentar obtener más detalles del error
            try {
                if (contentType && contentType.includes('application/json')) {
                    const errorData = await response.json();

    if (errorData.errors) {

        let mensajes = [];

        Object.values(errorData.errors).forEach(error => {
            mensajes.push(error[0]);
        });

        errorMessage = mensajes.join('\n');

    } else {

        errorMessage = errorData.message || errorMessage;

    }
}
            } catch (parseError) {
                console.error('Error parseando respuesta:', parseError);
            }
            
            throw new Error(errorMessage);
        }

        // Verificar que la respuesta sea JSON
        if (!contentType || !contentType.includes('application/json')) {
            const textResponse = await response.text();
            console.warn('Respuesta no JSON recibida:', textResponse.substring(0, 200));
            throw new Error('El servidor respondió con un formato inesperado');
        }

        const data = await response.json();
        
        cerrarModalPropietario();
        const msg = (data && data.message) ? data.message : 'Propietario guardado correctamente';
        alert(msg);
        
        // Recargar la página para mostrar los cambios
        setTimeout(() => {
            location.reload();
        }, 1000);
        
    } catch (err) {
        console.error('Error completo:', err);
        alert('Error al guardar el propietario: ' + err.message);
    }
}


function verPropietario(id) {

    fetch(`/recepcion/propietarios/${id}`, {
        credentials: 'include',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        return response.json();
    })
    .then(propietario => {

        document.getElementById('ver-nombre').value =
            propietario.nombre || '';

        document.getElementById('ver-telefono').value =
            propietario.telefono || '';

        document.getElementById('ver-correo').value =
            propietario.correo_electronico || '';

        document.getElementById('ver-direccion').value =
            propietario.direccion || '';

        document.getElementById('ver-fecha').value =
            propietario.fecha_registro || '';

        let mascotasHtml = '';

        if (propietario.mascotas &&
            propietario.mascotas.length > 0) {

            propietario.mascotas.forEach(mascota => {

                mascotasHtml += `
                    <tr>
                        <td>${mascota.nombre ?? 'N/A'}</td>
                        <td>${mascota.especie ?? 'N/A'}</td>
                        <td>${mascota.citas ? mascota.citas.length : 0}</td>
                    </tr>
                `;
            });

        } else {

            mascotasHtml = `
                <tr>
                    <td colspan="3">
                        No hay mascotas registradas
                    </td>
                </tr>
            `;
        }

        document.getElementById(
            'tabla-mascotas-propietario'
        ).innerHTML = mascotasHtml;

        document.getElementById(
            'modal-ver-propietario'
        ).style.display = 'flex';
    })
    .catch(error => {

        alert(
            'Error al cargar los datos del propietario: '
            + error.message
        );
    });
}


function editarPropietario(id) {
  // Lógica para editar propietario
  // Mostrar loading
  const modal = document.getElementById('modal-propietario');
  if (modal) {
    modal.style.display = 'flex';
    document.getElementById('modal-propietario-titulo').textContent = 'Cargando...';
  }
  
  // Primero, obtener los datos del propietario
    fetch(`/recepcion/propietarios/${id}`, {
        credentials: 'include',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => {
    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }
    return response.json();
  })
  .then(propietario => {
    // Llenar el formulario con los datos del propietario
    document.getElementById('propietario-id').value = propietario.id_propietario;
    document.getElementById('propietario-nombre').value = propietario.nombre || '';
    document.getElementById('propietario-telefono').value = propietario.telefono || '';
    document.getElementById('propietario-email').value = propietario.correo_electronico || '';
    document.getElementById('propietario-direccion').value = propietario.direccion || '';
        const estadoInput = document.getElementById('propietario-estado');
        if (estadoInput) {
            estadoInput.disabled = false;
            estadoInput.value = propietario.estado ? '1' : '0';
        }
    
    // Formatear fecha para el input date
if (propietario.fecha_registro) {
    const fechaInput = document.getElementById('propietario-fecha');

    if (fechaInput) {
        const fecha = new Date(propietario.fecha_registro);

        if ('value' in fechaInput) {
            fechaInput.value = fecha.toISOString().split('T')[0];
        } else {
            fechaInput.textContent = fecha.toLocaleDateString('es-MX');
        }
    }
}
    
    // Cambiar la acción del formulario para update
    const form = document.getElementById('form-propietario');
    form.setAttribute('action', `/recepcion/propietarios/${id}`);
    
    // Agregar campo _method para simular PUT/PATCH en Laravel
    let methodInput = form.querySelector('input[name="_method"]');
    if (!methodInput) {
      methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      form.appendChild(methodInput);
    }
    methodInput.value = 'PUT';
    
    // Actualizar título del modal
    document.getElementById('modal-propietario-titulo').textContent = 'Editar Propietario';
  })
  .catch(error => {
    console.error('Error al cargar propietario:', error);
    
    // Cerrar modal o mostrar error
    if (modal) {
      modal.style.display = 'none';
    }
    
    alert('Error al cargar los datos del propietario: ' + error.message);
  });
}

// Función adicional para eliminar propietario (opcional)
function eliminarPropietario(id) {
  if (confirm('¿Estás seguro de que deseas eliminar este propietario?')) {
    fetch(`/recepcion/propietarios/${id}`, {
            credentials: 'include',
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error('Error al eliminar');
      return response.json();
    })
    .then(data => {
      alert(data.message || 'Propietario eliminado correctamente');
      location.reload();
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al eliminar el propietario');
    });
  }
}

function cerrarModalVerPropietario() {

    const modal = document.getElementById('modal-ver-propietario');

    if (modal) {
        modal.style.display = 'none';
    }

    document.getElementById('ver-nombre').value = '';
    document.getElementById('ver-telefono').value = '';
    document.getElementById('ver-correo').value = '';
    document.getElementById('ver-fecha').value = '';
    document.getElementById('ver-direccion').value = '';

    const tablaMascotas =
        document.getElementById('tabla-mascotas-propietario');

    if (tablaMascotas) {
        tablaMascotas.innerHTML = '';
    }
}

    async function buscarPropietarios() {
        const input = document.getElementById('search-propietarios');
            const filtroEstado = document.getElementById('filter-estado-propietarios');
        if (!input) {
            return;
        }

        const texto = input.value;
            const estado = filtroEstado ? filtroEstado.value : '';

    const response = await fetch(
            `/recepcion/propietarios/buscar?q=${encodeURIComponent(texto)}&estado=${encodeURIComponent(estado)}`,
            {
            credentials: 'include'
            }
    );

    const data = await response.json();
    actualizarTablaPropietarios(data);
    
}

function actualizarTablaPropietarios(propietarios) {

    const tbody =
        document.getElementById('tabla-propietarios');

    tbody.innerHTML = '';

    if (propietarios.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    No se encontraron resultados
                </td>
            </tr>
        `;

        return;
    }

    propietarios.forEach(propietario => {

tbody.innerHTML += `
<tr>
    <td>
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="user-avatar">
                ${propietario.nombre ? propietario.nombre.substring(0,2).toUpperCase() : 'NA'}
            </div>
            <div>
                <div style="font-weight: 600;">
                    ${propietario.nombre ?? 'N/A'}
                </div>
                <div style="font-size: 12px; color: #64748b;">
                    ID: PRO${String(propietario.id_propietario).padStart(3, '0')}
                </div>
            </div>
        </div>
    </td>

    <td>${propietario.telefono ?? 'N/A'}</td>

    <td>${propietario.correo_electronico ?? 'N/A'}</td>

    <td>${propietario.direccion ?? 'N/A'}</td>

    <td>
        <span>
            ${propietario.estado ? 'Activo' : 'Inactivo'}
        </span>
    </td>

    <td>
        ${propietario.fecha_registro
            ? new Date(propietario.fecha_registro).toLocaleDateString('es-MX')
            : 'N/A'}
    </td>

    <td>
        <div style="display: flex; gap: 8px;">
            <button class="btn-outline"
                onclick="verPropietario(${propietario.id_propietario})">
                Ver
            </button>

            <button class="btn-secondary"
                onclick="editarPropietario(${propietario.id_propietario})">
                Editar
            </button>
        </div>
    </td>
</tr>
`;
    });
}
