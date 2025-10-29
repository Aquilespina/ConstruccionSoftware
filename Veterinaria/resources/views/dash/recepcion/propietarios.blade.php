@extends('dash.recepcion')
@section('page-title', 'Gestión de Propietarios')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/propietarios.css') }}">
@endpush
@section('content')<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Propietarios - VetClinic</title>
  <style>
    /* Estilos generales */
    :root {
      --primary: #3b82f6;
      --primary-dark: #2563eb;
      --secondary: #64748b;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --light: #f8fafc;
      --dark: #1e293b;
      --gray-100: #f1f5f9;
      --gray-200: #e2e8f0;
      --gray-300: #cbd5e1;
      --gray-400: #94a3b8;
      --gray-500: #64748b;
      --gray-600: #475569;
      --gray-700: #334155;
      --gray-800: #1e293b;
      --gray-900: #0f172a;
      --border-radius: 8px;
      --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f1f5f9;
      color: var(--gray-800);
      line-height: 1.5;
    }

    /* Estructura del módulo */
    .module {
      display: none;
      padding: 24px;
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
      margin-bottom: 24px;
    }

    .module-title {
      font-size: 24px;
      font-weight: 700;
      color: var(--gray-900);
    }

    .module-actions {
      display: flex;
      gap: 12px;
    }

    /* Barra de filtros */
    .filters-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding: 16px;
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
    }

    .search-filter {
      flex: 1;
      max-width: 400px;
    }

    .filter-actions {
      display: flex;
      gap: 12px;
    }

    /* Botones */
    .btn-primary {
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: var(--border-radius);
      padding: 10px 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
      box-shadow: var(--shadow-md);
    }

    .btn-secondary {
      background-color: white;
      color: var(--gray-700);
      border: 1px solid var(--gray-300);
      border-radius: var(--border-radius);
      padding: 10px 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-secondary:hover {
      background-color: var(--gray-100);
      box-shadow: var(--shadow);
    }

    .btn-outline {
      background-color: transparent;
      color: var(--primary);
      border: 1px solid var(--primary);
      border-radius: var(--border-radius);
      padding: 6px 12px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-outline:hover {
      background-color: var(--primary);
      color: white;
    }

    /* Inputs */
    .search-input, .filter-select, .form-control {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid var(--gray-300);
      border-radius: var(--border-radius);
      font-size: 14px;
      transition: all 0.2s;
    }

    .search-input:focus, .filter-select:focus, .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Tabla */
    .table-container {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .data-table {
      width: 100%;
      border-collapse: collapse;
    }

    .data-table th {
      background-color: var(--gray-100);
      padding: 12px 16px;
      text-align: left;
      font-weight: 600;
      color: var(--gray-700);
      border-bottom: 1px solid var(--gray-200);
    }

    .data-table td {
      padding: 16px;
      border-bottom: 1px solid var(--gray-200);
    }

    .data-table tr:last-child td {
      border-bottom: none;
    }

    .data-table tr:hover {
      background-color: var(--gray-50);
    }

    /* Avatar de usuario */
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--primary);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 14px;
    }

    /* Chip de estado */
    .chip {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      background-color: #eef2ff;
      color: var(--primary);
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
      padding: 20px;
      animation: fadeIn 0.3s ease-in-out;
    }

    .modal-content {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-lg);
      width: 100%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 24px;
      border-bottom: 1px solid var(--gray-200);
    }

    .modal-header h3 {
      font-size: 20px;
      font-weight: 600;
      color: var(--gray-900);
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: var(--gray-500);
      transition: color 0.2s;
    }

    .modal-close:hover {
      color: var(--gray-700);
    }

    .modal-body {
      padding: 24px;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      padding: 20px 24px;
      border-top: 1px solid var(--gray-200);
    }

    /* Formularios */
    .form-row {
      display: flex;
      gap: 16px;
      margin-bottom: 16px;
    }

    .form-group {
      flex: 1;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
      color: var(--gray-700);
    }

    /* Mensajes */
    .message-container {
      padding: 12px 16px;
      border-radius: var(--border-radius);
      margin-bottom: 16px;
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
        gap: 16px;
      }

      .module-actions {
        width: 100%;
        justify-content: flex-end;
      }

      .filters-bar {
        flex-direction: column;
        gap: 16px;
      }

      .search-filter {
        max-width: 100%;
      }

      .form-row {
        flex-direction: column;
        gap: 16px;
      }

      .modal-content {
        margin: 20px;
      }
    }
  </style>
</head>
<body>
  <section id="mod-propietarios" class="module active">
    <div class="module-header">
      <h2 class="module-title">Gestión de Propietarios</h2>
      <div class="module-actions">
        <button class="btn-primary" id="btn-nuevo-propietario">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Nuevo Propietario
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
        <input type="text" placeholder="Buscar propietario..." class="search-input" id="search-propietarios">
      </div>
      <div class="filter-actions">
        <select class="filter-select" id="filter-estado-propietarios">
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
            <th>Propietario</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Mascotas</th>
            <th>Última Visita</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="tabla-propietarios">
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="user-avatar">MR</div>
                <div>
                  <div style="font-weight: 600;">María Rodríguez</div>
                  <div style="font-size: 12px; color: #64748b;">ID: PRO001</div>
                </div>
              </div>
            </td>
            <td>+1 234 567 890</td>
            <td>maria@email.com</td>
            <td>
              <span class="chip">2 mascotas</span>
            </td>
            <td>15 Nov 2023</td>
            <td>
              <div style="display: flex; gap: 8px;">
                <button class="btn-outline" onclick="verPropietario(1)">Ver</button>
                <button class="btn-secondary" onclick="editarPropietario(1)">Editar</button>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="user-avatar">CP</div>
                <div>
                  <div style="font-weight: 600;">Carlos Pérez</div>
                  <div style="font-size: 12px; color: #64748b;">ID: PRO002</div>
                </div>
              </div>
            </td>
            <td>+1 234 567 891</td>
            <td>carlos@email.com</td>
            <td>
              <span class="chip">1 mascota</span>
            </td>
            <td>14 Nov 2023</td>
            <td>
              <div style="display: flex; gap: 8px;">
                <button class="btn-outline" onclick="verPropietario(2)">Ver</button>
                <button class="btn-secondary" onclick="editarPropietario(2)">Editar</button>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="user-avatar">AG</div>
                <div>
                  <div style="font-weight: 600;">Ana González</div>
                  <div style="font-size: 12px; color: #64748b;">ID: PRO003</div>
                </div>
              </div>
            </td>
            <td>+1 234 567 892</td>
            <td>ana@email.com</td>
            <td>
              <span class="chip">3 mascotas</span>
            </td>
            <td>10 Nov 2023</td>
            <td>
              <div style="display: flex; gap: 8px;">
                <button class="btn-outline" onclick="verPropietario(3)">Ver</button>
                <button class="btn-secondary" onclick="editarPropietario(3)">Editar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal para nuevo/editar propietario -->
    <div id="modal-propietario" class="modal" style="display: none;">
      <div class="modal-content">
        <div class="modal-header">
          <h3 id="modal-propietario-titulo">Nuevo Propietario</h3>
          <button class="modal-close" onclick="cerrarModalPropietario()">&times;</button>
        </div>
        <div class="modal-body">
          <form id="form-propietario" method="POST" action="{{ route('propietarios.store') }}">
            @csrf
            <div class="form-row">
              <div class="form-group">
                <label for="propietario-nombre">Nombre completo *</label>
                <input type="text" id="propietario-nombre" name="nombre" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="propietario-telefono">Teléfono *</label>
                <input type="tel" id="propietario-telefono" name="telefono" class="form-control" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="propietario-email">Email</label>
                <input type="email" id="propietario-email" name="email" class="form-control">
              </div>
              <div class="form-group">
                <label for="propietario-direccion">Dirección</label>
                <input type="text" id="propietario-direccion" name="direccion" class="form-control">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="propietario-documento">Documento de identidad</label>
                <input type="text" id="propietario-documento" name="documento" class="form-control">
              </div>
              <div class="form-group">
                <label for="propietario-estado">Estado *</label>
                <select id="propietario-estado" name="estado" class="form-control" required>
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="cerrarModalPropietario()">Cancelar</button>
            <button type="button" class="btn-primary" onclick="guardarPropietario()">Guardar Propietario</button>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const btnNuevo = document.getElementById('btn-nuevo-propietario');
      if (btnNuevo) btnNuevo.addEventListener('click', abrirModalPropietario);
    });

    function abrirModalPropietario() {
      const modal = document.getElementById('modal-propietario');
      if (modal) modal.style.display = 'flex';
    }
    
    function cerrarModalPropietario() {
      const modal = document.getElementById('modal-propietario');
      if (modal) modal.style.display = 'none';
    }

    function guardarPropietario() {
      const form = document.getElementById('form-propietario');
      if (!form) return;
      const url = form.getAttribute('action');
      const formData = new FormData(form);

      fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
      })
      .then(resp => {
        if (!resp.ok) throw new Error('Network response was not ok');
        return resp.json().catch(() => ({ success: true }));
      })
      .then(data => {
        cerrarModalPropietario();
        const msg = (data && data.message) ? data.message : 'Propietario guardado';
        alert(msg);
        // opcional: actualizar la tabla o recargar segmento
      })
      .catch(err => {
        console.error(err);
        // fallback: submit normal
        form.submit();
      });
    }

    function verPropietario(id) {
      // Lógica para ver detalles del propietario
      console.log('Ver propietario:', id);
    }

    function editarPropietario(id) {
      // Lógica para editar propietario
      console.log('Editar propietario:', id);
      abrirModalPropietario();
      document.getElementById('modal-propietario-titulo').textContent = 'Editar Propietario';
    }
  </script>
</body>
</html>

@endsection
