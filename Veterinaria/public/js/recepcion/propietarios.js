document.addEventListener('DOMContentLoaded', function() {
  const btnNuevo = document.getElementById('btn-nuevo-propietario');
  if (btnNuevo) btnNuevo.addEventListener('click', abrirModalPropietario);
  
  // Cerrar modal al hacer clic fuera del contenido
  const modal = document.getElementById('modal-propietario');
  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        cerrarModalPropietario();
      }
    });
  }
});

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
    }
    
    const url = form.getAttribute('action');
    const formData = new FormData(form);

    try {
        console.log('Enviando propietario a:', url);
        
        const response = await fetch(url, {
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
                    errorMessage = errorData.message || errorData.errors || errorMessage;
                } else {
                    const textResponse = await response.text();
                    console.error('Respuesta de error (HTML):', textResponse.substring(0, 500));
                    
                    // Si es HTML, podría ser redirección de login
                    if (textResponse.includes('login') || response.status === 419) {
                        errorMessage = 'Error de autenticación. Por favor, inicie sesión nuevamente.';
                    } else if (response.status === 422) {
                        errorMessage = 'Error de validación. Verifique los datos ingresados.';
                    } else if (response.status === 500) {
                        errorMessage = 'Error interno del servidor. Contacte al administrador.';
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
  // Lógica para ver detalles del propietario
  console.log('Ver propietario:', id);
  alert(`Funcionalidad de ver detalles para el propietario ID: ${id}`);
}

function editarPropietario(id) {
  // Lógica para editar propietario
  console.log('Editar propietario:', id);
  
  // Mostrar loading
  const modal = document.getElementById('modal-propietario');
  if (modal) {
    modal.style.display = 'flex';
    document.getElementById('modal-propietario-titulo').textContent = 'Cargando...';
  }
  
  // Primero, obtener los datos del propietario
  fetch(`/recepcion/propietarios/${id}`, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => {
    console.log('Response status:', response.status);
    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }
    return response.json();
  })
  .then(propietario => {
    console.log('Datos recibidos:', propietario);
    
    // Llenar el formulario con los datos del propietario
    document.getElementById('propietario-id').value = propietario.id_propietario;
    document.getElementById('propietario-nombre').value = propietario.nombre || '';
    document.getElementById('propietario-telefono').value = propietario.telefono || '';
    document.getElementById('propietario-email').value = propietario.correo_electronico || '';
    document.getElementById('propietario-direccion').value = propietario.direccion || '';
    
    // Formatear fecha para el input date
    if (propietario.fecha_registro) {
      const fecha = new Date(propietario.fecha_registro);
      document.getElementById('propietario-fecha').value = fecha.toISOString().split('T')[0];
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