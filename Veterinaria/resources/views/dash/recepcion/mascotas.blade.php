@extends('dash.recepcion')
@section('page-title', 'Mascotas')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/mascotas.css') }}">
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

    /* Avatar de mascota */
    .pet-avatar {
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 50%;
      background-color: #d1fae5;
      color: var(--primary-dark);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.125rem;
      flex-shrink: 0;
    }

    /* Chip de estado */
    .chip {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
      background-color: #d1fae5;
      color: var(--primary-dark);
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
      max-width: 700px;
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
<section id="mod-mascotas" class="module active">
  <div class="module-header">
    <h2 class="module-title">Gestión de Mascotas</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nueva-mascota">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Nueva Mascota
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
      <input type="text" placeholder="Buscar mascota..." class="search-input" id="search-mascotas">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-especie">
        <option value="">Todas las especies</option>
        <option value="perro">Perro</option>
        <option value="gato">Gato</option>
        <option value="ave">Ave</option>
        <option value="roedor">Roedor</option>
        <option value="reptil">Reptil</option>
      </select>
      <select class="filter-select" id="filter-estado-mascota">
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
          <th>Mascota</th>
          <th>Especie</th>
          <th>Raza</th>
          <th>Propietario</th>
          <th>Edad</th>
          <th>Última Visita</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-mascotas">
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div class="pet-avatar">🐕</div>
              <div>
                <div style="font-weight: 600;">Max</div>
                <div style="font-size: 0.75rem; color: var(--gray-500);">ID: MAS001</div>
              </div>
            </div>
          </td>
          <td>Perro</td>
          <td>Golden Retriever</td>
          <td>María Rodríguez</td>
          <td>4 años</td>
          <td>15 Nov 2023</td>
          <td>
            <div style="display: flex; gap: 0.5rem;">
              <button class="btn-outline" onclick="verMascota(1)">Ver</button>
              <button class="btn-secondary" onclick="editarMascota(1)">Editar</button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div class="pet-avatar">🐈</div>
              <div>
                <div style="font-weight: 600;">Luna</div>
                <div style="font-size: 0.75rem; color: var(--gray-500);">ID: MAS002</div>
              </div>
            </div>
          </td>
          <td>Gato</td>
          <td>Siames</td>
          <td>Carlos Pérez</td>
          <td>2 años</td>
          <td>12 Nov 2023</td>
          <td>
            <div style="display: flex; gap: 0.5rem;">
              <button class="btn-outline" onclick="verMascota(2)">Ver</button>
              <button class="btn-secondary" onclick="editarMascota(2)">Editar</button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <div class="pet-avatar">🐕</div>
              <div>
                <div style="font-weight: 600;">Toby</div>
                <div style="font-size: 0.75rem; color: var(--gray-500);">ID: MAS003</div>
              </div>
            </div>
          </td>
          <td>Perro</td>
          <td>Border Collie</td>
          <td>Ana González</td>
          <td>3 años</td>
          <td>10 Nov 2023</td>
          <td>
            <div style="display: flex; gap: 0.5rem;">
              <button class="btn-outline" onclick="verMascota(3)">Ver</button>
              <button class="btn-secondary" onclick="editarMascota(3)">Editar</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Modal para nueva/editar mascota -->
  <div id="modal-mascota" class="modal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modal-mascota-titulo">Nueva Mascota</h3>
        <button class="modal-close" onclick="cerrarModalMascota()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-mascota">
          @csrf
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-nombre">Nombre *</label>
              <input type="text" id="mascota-nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="mascota-especie">Especie *</label>
              <select id="mascota-especie" name="especie" class="form-control" required>
                <option value="">Seleccionar especie</option>
                <option value="perro">Perro</option>
                <option value="gato">Gato</option>
                <option value="ave">Ave</option>
                <option value="roedor">Roedor</option>
                <option value="reptil">Reptil</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-raza">Raza *</label>
              <input type="text" id="mascota-raza" name="raza" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="mascota-propietario">Propietario *</label>
              <select id="mascota-propietario" name="propietario_id" class="form-control" required>
                <option value="">Seleccionar propietario</option>
                <option value="1">María Rodríguez</option>
                <option value="2">Carlos Pérez</option>
                <option value="3">Ana González</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-edad">Edad</label>
              <input type="text" id="mascota-edad" name="edad" class="form-control" placeholder="Ej: 3 años">
            </div>
            <div class="form-group">
              <label for="mascota-peso">Peso (kg)</label>
              <input type="number" id="mascota-peso" name="peso" class="form-control" step="0.1">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="mascota-sexo">Sexo</label>
              <select id="mascota-sexo" name="sexo" class="form-control">
                <option value="">Seleccionar</option>
                <option value="macho">Macho</option>
                <option value="hembra">Hembra</option>
              </select>
            </div>
            <div class="form-group">
              <label for="mascota-estado">Estado *</label>
              <select id="mascota-estado" name="estado" class="form-control" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalMascota()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="guardarMascota()">Guardar Mascota</button>
      </div>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const btnNueva = document.getElementById('btn-nueva-mascota');
    if (btnNueva) btnNueva.addEventListener('click', abrirModalMascota);
    
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
  });

  function aplicarFiltros() {
    const especie = document.getElementById('filter-especie').value.toLowerCase();
    const estado = document.getElementById('filter-estado-mascota').value.toLowerCase();
    const rows = document.querySelectorAll('#tabla-mascotas tr');
    
    rows.forEach(row => {
      const especieCell = row.cells[1].textContent.toLowerCase();
      const estadoCell = row.cells[6].textContent.toLowerCase(); // Asumiendo que el estado está en la columna 6
      
      const especieMatch = !especie || especieCell.includes(especie);
      const estadoMatch = !estado || estadoCell.includes(estado);
      
      row.style.display = (especieMatch && estadoMatch) ? '' : 'none';
    });
  }

  function abrirModalMascota() {
    const modal = document.getElementById('modal-mascota');
    if (modal) modal.style.display = 'flex';
  }
  
  function cerrarModalMascota() {
    const modal = document.getElementById('modal-mascota');
    if (modal) modal.style.display = 'none';
  }

  function guardarMascota() {
    const form = document.getElementById('form-mascota');
    if (!form) return;
    
    // Validación básica
    const nombre = document.getElementById('mascota-nombre').value;
    const especie = document.getElementById('mascota-especie').value;
    const raza = document.getElementById('mascota-raza').value;
    const propietario = document.getElementById('mascota-propietario').value;
    
    if (!nombre || !especie || !raza || !propietario) {
      alert('Por favor, complete todos los campos obligatorios (*)');
      return;
    }
    
    // Aquí iría la lógica para guardar la mascota
    // Por ahora solo cerramos el modal
    cerrarModalMascota();
    
    // Mostrar mensaje de éxito
    alert('Mascota guardada correctamente');
  }

  function verMascota(id) {
    // Lógica para ver detalles de la mascota
    console.log('Ver mascota:', id);
    alert('Funcionalidad de ver mascota - ID: ' + id);
  }

  function editarMascota(id) {
    // Lógica para editar mascota
    console.log('Editar mascota:', id);
    abrirModalMascota();
    document.getElementById('modal-mascota-titulo').textContent = 'Editar Mascota';
    
    // Aquí iría la lógica para cargar los datos de la mascota en el formulario
    // Por ahora solo mostramos un mensaje
    document.getElementById('mascota-nombre').value = 'Mascota ' + id;
    document.getElementById('mascota-especie').value = 'perro';
    document.getElementById('mascota-raza').value = 'Raza ' + id;
    document.getElementById('mascota-propietario').value = '1';
    document.getElementById('mascota-edad').value = '2 años';
    document.getElementById('mascota-peso').value = '5.5';
    document.getElementById('mascota-sexo').value = 'macho';
    document.getElementById('mascota-estado').value = 'activo';
  }
</script>
@endsection