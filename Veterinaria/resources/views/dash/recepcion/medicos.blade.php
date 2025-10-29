@extends('dash.recepcion')

@section('page-title', 'Gestión de Médicos')

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

    /* Avatar de médico */
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
<section class="module active">
  <div class="module-header">
    <h2 class="module-title">Gestión de Médicos</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nuevo-medico">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Nuevo Médico
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
      <input type="text" placeholder="Buscar médico..." class="search-input" id="search-medicos">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-especialidad">
        <option value="">Todas las especialidades</option>
        <option value="cirugia">Cirugía</option>
        <option value="dermatologia">Dermatología</option>
        <option value="cardiologia">Cardiología</option>
        <option value="oftalmologia">Oftalmología</option>
        <option value="neurologia">Neurología</option>
      </select>
      <select class="filter-select" id="filter-estado-medico">
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>
    </div>
  </div>
  
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>Médico</th>
          <th>Especialidad</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Horario</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-medicos">
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div class="user-avatar">LM</div>
              <div>
                <div style="font-weight: 600;">Dra. Laura Méndez</div>
                <div style="font-size: 0.75rem; color: var(--gray-500);">ID: MED001</div>
              </div>
            </div>
          </td>
          <td>
            <span class="specialty-badge">Cirugía</span>
          </td>
          <td>laura.mendez@vetclinic.com</td>
          <td>+1 234 567 890</td>
          <td>Lun-Vie: 8:00-16:00</td>
          <td><span class="status-badge status-active">Activo</span></td>
          <td>
            <div style="display: flex; gap: 0.5rem;">
              <button class="btn-outline" onclick="editarMedico(1)">Editar</button>
              <button class="btn-secondary" onclick="gestionarHorarios(1)">Horarios</button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div class="user-avatar">RG</div>
              <div>
                <div style="font-weight: 600;">Dr. Roberto García</div>
                <div style="font-size: 0.75rem; color: var(--gray-500);">ID: MED002</div>
              </div>
            </div>
          </td>
          <td>
            <span class="specialty-badge">Dermatología</span>
          </td>
          <td>roberto.garcia@vetclinic.com</td>
          <td>+1 234 567 891</td>
          <td>Mar-Jue: 9:00-17:00</td>
          <td><span class="status-badge status-active">Activo</span></td>
          <td>
            <div style="display: flex; gap: 0.5rem;">
              <button class="btn-outline" onclick="editarMedico(2)">Editar</button>
              <button class="btn-secondary" onclick="gestionarHorarios(2)">Horarios</button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div class="user-avatar">AS</div>
              <div>
                <div style="font-weight: 600;">Dra. Ana Sánchez</div>
                <div style="font-size: 0.75rem; color: var(--gray-500);">ID: MED003</div>
              </div>
            </div>
          </td>
          <td>
            <span class="specialty-badge">Cardiología</span>
          </td>
          <td>ana.sanchez@vetclinic.com</td>
          <td>+1 234 567 892</td>
          <td>Lun-Mie-Vie: 10:00-18:00</td>
          <td><span class="status-badge status-inactive">Inactivo</span></td>
          <td>
            <div style="display: flex; gap: 0.5rem;">
              <button class="btn-outline" onclick="editarMedico(3)">Editar</button>
              <button class="btn-secondary" onclick="gestionarHorarios(3)">Horarios</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Modal para nuevo/editar médico -->
  <div id="modal-medico" class="modal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modal-medico-titulo">Nuevo Médico</h3>
        <button class="modal-close" onclick="cerrarModalMedico()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-medico">
          @csrf
          <div class="form-section">
            <h4 class="form-section-title">Información Personal</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="medico-nombre">Nombre completo *</label>
                <input type="text" id="medico-nombre" name="nombre" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="medico-email">Email *</label>
                <input type="email" id="medico-email" name="email" class="form-control" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="medico-telefono">Teléfono *</label>
                <input type="tel" id="medico-telefono" name="telefono" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="medico-documento">Documento de identidad</label>
                <input type="text" id="medico-documento" name="documento" class="form-control">
              </div>
            </div>
          </div>

          <div class="form-section">
            <h4 class="form-section-title">Información Profesional</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="medico-especialidad">Especialidad *</label>
                <select id="medico-especialidad" name="especialidad" class="form-control" required>
                  <option value="">Seleccionar especialidad</option>
                  <option value="cirugia">Cirugía</option>
                  <option value="dermatologia">Dermatología</option>
                  <option value="cardiologia">Cardiología</option>
                  <option value="oftalmologia">Oftalmología</option>
                  <option value="neurologia">Neurología</option>
                </select>
              </div>
              <div class="form-group">
                <label for="medico-licencia">Número de licencia *</label>
                <input type="text" id="medico-licencia" name="licencia" class="form-control" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="medico-anios-experiencia">Años de experiencia</label>
                <input type="number" id="medico-anios-experiencia" name="anios_experiencia" class="form-control" min="0">
              </div>
              <div class="form-group">
                <label for="medico-estado">Estado *</label>
                <select id="medico-estado" name="estado" class="form-control" required>
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h4 class="form-section-title">Horario de Trabajo</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="medico-horario-entrada">Hora de entrada *</label>
                <input type="time" id="medico-horario-entrada" name="horario_entrada" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="medico-horario-salida">Hora de salida *</label>
                <input type="time" id="medico-horario-salida" name="horario_salida" class="form-control" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Días de trabajo *</label>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 0.375rem;">
                  <label style="display: flex; align-items: center; gap: 0.375rem; font-weight: normal;">
                    <input type="checkbox" name="dias_trabajo[]" value="lunes"> Lunes
                  </label>
                  <label style="display: flex; align-items: center; gap: 0.375rem; font-weight: normal;">
                    <input type="checkbox" name="dias_trabajo[]" value="martes"> Martes
                  </label>
                  <label style="display: flex; align-items: center; gap: 0.375rem; font-weight: normal;">
                    <input type="checkbox" name="dias_trabajo[]" value="miercoles"> Miércoles
                  </label>
                  <label style="display: flex; align-items: center; gap: 0.375rem; font-weight: normal;">
                    <input type="checkbox" name="dias_trabajo[]" value="jueves"> Jueves
                  </label>
                  <label style="display: flex; align-items: center; gap: 0.375rem; font-weight: normal;">
                    <input type="checkbox" name="dias_trabajo[]" value="viernes"> Viernes
                  </label>
                  <label style="display: flex; align-items: center; gap: 0.375rem; font-weight: normal;">
                    <input type="checkbox" name="dias_trabajo[]" value="sabado"> Sábado
                  </label>
                  <label style="display: flex; align-items: center; gap: 0.375rem; font-weight: normal;">
                    <input type="checkbox" name="dias_trabajo[]" value="domingo"> Domingo
                  </label>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalMedico()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="guardarMedico()">Guardar Médico</button>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar funcionalidades del módulo de médicos
    const btnNuevoMedico = document.getElementById('btn-nuevo-medico');
    if (btnNuevoMedico) {
        btnNuevoMedico.addEventListener('click', abrirModalMedico);
    }
    
    // Filtros de búsqueda
    const searchInput = document.getElementById('search-medicos');
    if (searchInput) {
        searchInput.addEventListener('input', filtrarMedicos);
    }

    // Filtros de especialidad y estado
    const filterEspecialidad = document.getElementById('filter-especialidad');
    const filterEstado = document.getElementById('filter-estado-medico');
    
    if (filterEspecialidad && filterEstado) {
        [filterEspecialidad, filterEstado].forEach(select => {
            select.addEventListener('change', aplicarFiltrosMedicos);
        });
    }
});

function aplicarFiltrosMedicos() {
    const especialidad = document.getElementById('filter-especialidad').value.toLowerCase();
    const estado = document.getElementById('filter-estado-medico').value.toLowerCase();
    const rows = document.querySelectorAll('#tabla-medicos tr');
    
    rows.forEach(row => {
        const especialidadCell = row.cells[1].textContent.toLowerCase();
        const estadoCell = row.cells[5].textContent.toLowerCase();
        
        const especialidadMatch = !especialidad || especialidadCell.includes(especialidad);
        const estadoMatch = !estado || estadoCell.includes(estado);
        
        row.style.display = (especialidadMatch && estadoMatch) ? '' : 'none';
    });
}

function filtrarMedicos() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tabla-medicos tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function abrirModalMedico() {
    const modal = document.getElementById('modal-medico');
    if (modal) modal.style.display = 'flex';
}

function cerrarModalMedico() {
    const modal = document.getElementById('modal-medico');
    if (modal) modal.style.display = 'none';
}

function guardarMedico() {
    const form = document.getElementById('form-medico');
    if (!form) return;
    
    // Validación básica
    const nombre = document.getElementById('medico-nombre').value;
    const email = document.getElementById('medico-email').value;
    const telefono = document.getElementById('medico-telefono').value;
    const especialidad = document.getElementById('medico-especialidad').value;
    const licencia = document.getElementById('medico-licencia').value;
    
    if (!nombre || !email || !telefono || !especialidad || !licencia) {
        alert('Por favor, complete todos los campos obligatorios (*)');
        return;
    }
    
    // Aquí iría la lógica para guardar el médico
    // Por ahora solo cerramos el modal
    cerrarModalMedico();
    
    // Mostrar mensaje de éxito
    alert('Médico guardado correctamente');
}

function editarMedico(id) {
    // Lógica para editar médico
    console.log('Editar médico:', id);
    abrirModalMedico();
    document.getElementById('modal-medico-titulo').textContent = 'Editar Médico';
    
    // Aquí iría la lógica para cargar los datos del médico en el formulario
    // Por ahora solo mostramos un mensaje
    document.getElementById('medico-nombre').value = 'Dr. Ejemplo ' + id;
    document.getElementById('medico-email').value = 'ejemplo' + id + '@vetclinic.com';
    document.getElementById('medico-telefono').value = '+1 234 567 89' + id;
    document.getElementById('medico-documento').value = 'DOC' + id;
    document.getElementById('medico-especialidad').value = 'cirugia';
    document.getElementById('medico-licencia').value = 'LIC' + id;
    document.getElementById('medico-anios-experiencia').value = '5';
    document.getElementById('medico-estado').value = 'activo';
    document.getElementById('medico-horario-entrada').value = '08:00';
    document.getElementById('medico-horario-salida').value = '16:00';
}

function gestionarHorarios(id) {
    alert(`Gestionar horarios del médico ID: ${id}`);
    // Aquí se podría abrir otro modal específico para gestionar horarios
}
</script>
@endsection