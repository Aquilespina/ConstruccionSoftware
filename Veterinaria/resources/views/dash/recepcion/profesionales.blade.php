@extends('dash.recepcion')

@section('page-title', 'Gestión de Profesionales')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/medicos.css') }}">
  <style>
    /* Estilos generales */
    :root {
      --primary: #059669;
      --primary-light: #10b981;
      --primary-dark: #047857;
      --secondary: #3b82f6;
      --danger: #ef4444;
      --warning: #f59e0b;
      --success: #10b981;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --gray-900: #111827;
      --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
      background-color: var(--gray-50);
      color: var(--gray-800);
      line-height: 1.5;
    }

    /* Estructura del módulo */
    .module {
      display: none;
      padding: 1.5rem;
      animation: fadeIn 0.3s ease-in-out;
    }

    .module.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Encabezado del módulo */
    .module-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .module-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--gray-900);
    }

    .module-actions {
      display: flex;
      gap: 0.75rem;
    }

    /* Barra de filtros */
    .filters-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      padding: 1rem;
      background: white;
      border-radius: 0.5rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--gray-200);
    }

    .search-filter {
      flex: 1;
      max-width: 400px;
    }

    .filter-actions {
      display: flex;
      gap: 0.75rem;
    }

    /* Botones */
    .btn-primary {
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: 0.5rem;
      padding: 0.625rem 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
      box-shadow: var(--shadow-md);
    }

    .btn-secondary {
      background-color: white;
      color: var(--gray-700);
      border: 1px solid var(--gray-300);
      border-radius: 0.5rem;
      padding: 0.625rem 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
    }

    .btn-secondary:hover {
      background-color: var(--gray-100);
      box-shadow: var(--shadow);
    }

    .btn-outline {
      background-color: transparent;
      color: var(--primary);
      border: 1px solid var(--primary);
      border-radius: 0.375rem;
      padding: 0.375rem 0.75rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      font-size: 0.75rem;
    }

    .btn-outline:hover {
      background-color: var(--primary);
      color: white;
    }

    /* Inputs */
    .search-input, .filter-select, .form-control {
      width: 100%;
      padding: 0.625rem 0.75rem;
      border: 1px solid var(--gray-300);
      border-radius: 0.5rem;
      font-size: 0.875rem;
      transition: all 0.2s;
      background-color: white;
    }

    .search-input:focus, .filter-select:focus, .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    /* Tabla */
    .table-container {
      background: white;
      border-radius: 0.75rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--gray-200);
      overflow: hidden;
    }

    .data-table {
      width: 100%;
      border-collapse: collapse;
    }

    .data-table th {
      background-color: var(--gray-50);
      padding: 0.75rem 1rem;
      text-align: left;
      font-weight: 600;
      color: var(--gray-700);
      border-bottom: 1px solid var(--gray-200);
      font-size: 0.875rem;
    }

    .data-table td {
      padding: 1rem;
      border-bottom: 1px solid var(--gray-200);
      font-size: 0.875rem;
    }

    .data-table tr:last-child td {
      border-bottom: none;
    }

    .data-table tr:hover {
      background-color: var(--gray-50);
    }

    /* Avatar de profesional */
    .user-avatar {
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 50%;
      background-color: var(--primary);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 0.875rem;
      flex-shrink: 0;
    }

    /* Badge de especialidad */
    .specialty-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 0.375rem;
      font-size: 0.75rem;
      font-weight: 500;
      background: #e0f2fe;
      color: #0369a1;
      display: inline-block;
    }

    /* Badge de estado */
    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
      display: inline-block;
    }

    .status-active {
      background-color: #d1fae5;
      color: var(--primary-dark);
    }

    .status-inactive {
      background-color: #fef2f2;
      color: var(--danger);
    }

    /* Modal */
    .modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      padding: 1.25rem;
      animation: fadeIn 0.3s ease-in-out;
    }

    .modal-content {
      background: white;
      border-radius: 0.75rem;
      box-shadow: var(--shadow-lg);
      width: 100%;
      max-width: 800px;
      max-height: 90vh;
      overflow-y: auto;
      border: 1px solid var(--gray-200);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--gray-200);
    }

    .modal-header h3 {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--gray-900);
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--gray-500);
      transition: color 0.2s;
      width: 2rem;
      height: 2rem;
      border-radius: 0.375rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-close:hover {
      background-color: var(--gray-100);
      color: var(--gray-700);
    }

    .modal-body {
      padding: 1.5rem;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 0.75rem;
      padding: 1.25rem 1.5rem;
      border-top: 1px solid var(--gray-200);
    }

    /* Formularios */
    .form-row {
      display: flex;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .form-group {
      flex: 1;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.375rem;
      font-weight: 500;
      color: var(--gray-700);
      font-size: 0.875rem;
    }

    .form-section {
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--gray-200);
    }

    .form-section-title {
      font-size: 1rem;
      font-weight: 600;
      color: var(--gray-800);
      margin-bottom: 1rem;
    }

    /* Mensajes */
    .message-container {
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      display: none;
    }

    .message-success {
      background-color: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }

    .message-error {
      background-color: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .module-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .module-actions {
        width: 100%;
        justify-content: flex-end;
      }

      .filters-bar {
        flex-direction: column;
        gap: 1rem;
      }

      .search-filter {
        max-width: 100%;
      }

      .form-row {
        flex-direction: column;
        gap: 1rem;
      }

      .modal-content {
        margin: 1.25rem;
      }
    }
  </style>
@endpush

@section('content')
<section id="mod-profesionales" class="module active">
  <div class="module-header">
    <h2 class="module-title">Gestión de Profesionales</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nuevo-profesional">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Nuevo Profesional
      </button>
      <button class="btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="7 10 12 15 17 10"></polyline>
          <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Exportar
      </button>
    </div>
  </div>
  
  <div class="filters-bar">
    <div class="search-filter">
      <input type="text" placeholder="Buscar profesional..." class="search-input" id="search-profesionales">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-especialidad">
        <option value="">Todas las especialidades</option>
        <option value="Cirugía">Cirugía</option>
        <option value="Dermatología">Dermatología</option>
        <option value="Cardiología">Cardiología</option>
        <option value="Oftalmología">Oftalmología</option>
        <option value="Neurología">Neurología</option>
        <option value="Medicina General">Medicina General</option>
      </select>
      <select class="filter-select" id="filter-estado-profesional">
        <option value="">Todos</option>
        <option value="1">Activos</option>
        <option value="0">Inactivos</option>
      </select>
    </div>
  </div>
  
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>RFC</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Especialidad</th>
          <th>Turno</th>
          <th>Activo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-profesionales">
        <!-- Los datos se cargarán dinámicamente desde la base de datos -->
        @if(isset($profesionales) && count($profesionales) > 0)
          @foreach($profesionales as $profesional)
          <tr>
            <td>{{ $profesional->rfc }}</td>
            <td>{{ $profesional->nombre }}</td>
            <td>{{ $profesional->correo }}</td>
            <td><span class="specialty-badge">{{ $profesional->especialidad }}</span></td>
            <td>{{ $profesional->turno ?? '—' }}</td>
            <td>
              <span class="status-badge {{ $profesional->activo ? 'status-active' : 'status-inactive' }}">
                {{ $profesional->activo ? 'Sí' : 'No' }}
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 0.5rem;">
                <button class="btn-outline" onclick="editarProfesional('{{ $profesional->rfc }}')">Editar</button>
              </div>
            </td>
          </tr>
          @endforeach
        @else
          <tr>
            <td colspan="7" style="text-align: center; padding: 2rem;">
              <div style="color: var(--gray-500);">
                No hay profesionales registrados
              </div>
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>

  <!-- Modal para nuevo/editar profesional -->
  <div id="modal-profesional" class="modal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modal-profesional-titulo">Nuevo Profesional</h3>
        <button class="modal-close" onclick="cerrarModalProfesional()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-profesional" method="POST" action="{{ route('profesionales.store') }}">
          @csrf
          <input type="hidden" id="profesional-id">
          
          <div class="form-section">
            <h4 class="form-section-title">Información del Profesional</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="profesional-rfc">RFC *</label>
                <input type="text" id="profesional-rfc" name="rfc" class="form-control" maxlength="13" required>
              </div>
              <div class="form-group">
                <label for="profesional-nombre">Nombre *</label>
                <input type="text" id="profesional-nombre" name="nombre" class="form-control" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="profesional-correo">Correo</label>
                <input type="email" id="profesional-correo" name="correo" class="form-control">
              </div>
              <div class="form-group">
                <label for="profesional-especialidad">Especialidad</label>
                <input type="text" id="profesional-especialidad" name="especialidad" class="form-control">
              </div>
            </div>
          </div>

          <div class="form-section">
            <h4 class="form-section-title">Asignación</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="profesional-turno">Turno</label>
                <select id="profesional-turno" name="turno" class="form-control">
                  <option value="">Sin asignar</option>
                  <option value="Matutino">Matutino</option>
                  <option value="Vespertino">Vespertino</option>
                  <option value="Nocturno">Nocturno</option>
                </select>
              </div>
              <div class="form-group" style="display:flex;align-items:center;gap:0.5rem;">
                <input type="checkbox" id="profesional-activo" name="activo" value="1" checked>
                <label for="profesional-activo" style="margin:0;">Activo</label>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalProfesional()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="guardarProfesional()">Guardar Profesional</button>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar funcionalidades del módulo de profesionales
    const btnNuevoProfesional = document.getElementById('btn-nuevo-profesional');
    if (btnNuevoProfesional) {
        btnNuevoProfesional.addEventListener('click', abrirModalProfesional);
    }
    
    // Filtros de búsqueda
    const searchInput = document.getElementById('search-profesionales');
    if (searchInput) {
        searchInput.addEventListener('input', filtrarProfesionales);
    }

    // Filtros de especialidad y estado
    const filterEspecialidad = document.getElementById('filter-especialidad');
    const filterEstado = document.getElementById('filter-estado-profesional');
    
    if (filterEspecialidad && filterEstado) {
        [filterEspecialidad, filterEstado].forEach(select => {
            select.addEventListener('change', aplicarFiltrosProfesionales);
        });
    }
});

function aplicarFiltrosProfesionales() {
  const especialidad = document.getElementById('filter-especialidad').value.toLowerCase();
  const estado = document.getElementById('filter-estado-profesional').value; // '1', '0' o ''
  const rows = document.querySelectorAll('#tabla-profesionales tr');
    
  rows.forEach(row => {
    const especialidadCell = row.cells[3]?.textContent.toLowerCase() || '';
    const activoText = (row.cells[5]?.textContent || '').trim().toLowerCase(); // 'sí' o 'no'
        
    const especialidadMatch = !especialidad || especialidadCell.includes(especialidad);
    let estadoMatch = true;
    if (estado === '1') {
      estadoMatch = activoText.includes('sí');
    } else if (estado === '0') {
      estadoMatch = activoText.includes('no');
    }
        
    row.style.display = (especialidadMatch && estadoMatch) ? '' : 'none';
  });
}

function filtrarProfesionales() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tabla-profesionales tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function abrirModalProfesional() {
    const modal = document.getElementById('modal-profesional');
    if (modal) {
        modal.style.display = 'flex';
        document.getElementById('modal-profesional-titulo').textContent = 'Nuevo Profesional';
        document.getElementById('form-profesional').reset();
        document.getElementById('profesional-id').value = '';
    const chk = document.getElementById('profesional-activo');
    if (chk) chk.checked = true;
    const rfcInput = document.getElementById('profesional-rfc');
    if (rfcInput) rfcInput.disabled = false;
    }
}

function cerrarModalProfesional() {
    const modal = document.getElementById('modal-profesional');
    if (modal) modal.style.display = 'none';
}

async function guardarProfesional() {
    const form = document.getElementById('form-profesional');
    if (!form) return;
    
    // Validación básica
  const rfc = document.getElementById('profesional-rfc').value;
  const nombre = document.getElementById('profesional-nombre').value;
    
  if (!rfc || !nombre) {
        alert('Por favor, complete todos los campos obligatorios (*)');
        return;
    }
  // Enviar al endpoint web configurado en el action del formulario
  const formData = new FormData(form);
  const profesionalRfc = document.getElementById('profesional-id').value;
  const method = 'POST';
  const baseUrl = '{{ url("recepcion/profesionales") }}';
  let url = form.action;
  if (profesionalRfc) {
    url = `${baseUrl}/${profesionalRfc}`;
    formData.append('_method', 'PUT');
  }

    try {
        console.log('Enviando profesional a:', url);
        
    const response = await fetch(url, {
      method: method,
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
        
        cerrarModalProfesional();
        const msg = (data && data.message) ? data.message : 'Profesional guardado correctamente';
        alert(msg);
        
        // Recargar la página para mostrar los cambios
        setTimeout(() => {
            location.reload();
        }, 1000);
        
    } catch (err) {
        console.error('Error completo:', err);
        alert('Error al guardar el profesional: ' + err.message);
    }
}

async function editarProfesional(rfc) {
  console.log('Editando profesional RFC:', rfc);
    
    try {
  const response = await fetch(`/recepcion/profesionales/${rfc}`);
        if (!response.ok) {
            throw new Error('Error al cargar datos del profesional');
        }
        
        const profesional = await response.json();
        
        // Llenar el formulario con los datos del profesional
  document.getElementById('profesional-id').value = profesional.data?.rfc ?? profesional.rfc;
  const rfcInput = document.getElementById('profesional-rfc');
  rfcInput.value = profesional.data?.rfc ?? profesional.rfc;
  rfcInput.disabled = true; // No permitir editar RFC (PK)
  document.getElementById('profesional-nombre').value = profesional.data?.nombre ?? profesional.nombre ?? '';
  document.getElementById('profesional-correo').value = profesional.data?.correo ?? profesional.correo ?? '';
  document.getElementById('profesional-especialidad').value = profesional.data?.especialidad ?? profesional.especialidad ?? '';
  document.getElementById('profesional-turno').value = profesional.data?.turno ?? profesional.turno ?? '';
  document.getElementById('profesional-activo').checked = Boolean(profesional.data?.activo ?? profesional.activo ?? true);
        
        // Abrir modal y cambiar título
        const modal = document.getElementById('modal-profesional');
        if (modal) {
            modal.style.display = 'flex';
            document.getElementById('modal-profesional-titulo').textContent = 'Editar Profesional';
        }
        
    } catch (error) {
        console.error('Error cargando profesional:', error);
        alert('Error al cargar los datos del profesional');
    }
}

function gestionarHorarios(rfc) {
  alert(`Gestionar turnos del profesional RFC: ${rfc}`);
}
</script>
@endsection