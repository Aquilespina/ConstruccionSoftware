@extends('dash.recepcion')

@section('page-title', 'Gestión de Profesionales')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/medicos.css') }}">
  <link rel="stylesheet" href="{{ asset('css/recepcion/form-validation.css') }}">
  <link rel="stylesheet" href="{{ asset('css/recepcion/entity-detail.css') }}">
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

    .field-hint {
      display: block;
      margin-top: 0.25rem;
      font-size: 0.75rem;
      color: var(--gray-500);
    }

    .field-error {
      display: none;
      margin-top: 0.25rem;
      font-size: 0.75rem;
      color: var(--danger);
    }

    .form-control:invalid:not(:placeholder-shown),
    .form-control.is-invalid {
      border-color: var(--danger);
    }

    .especialidades-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 0.5rem;
      margin-top: 0.375rem;
    }

    .especialidad-option {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 0.75rem;
      border: 1px solid var(--gray-200);
      border-radius: 0.5rem;
      cursor: pointer;
      font-size: 0.875rem;
      transition: border-color 0.2s, background-color 0.2s;
    }

    .especialidad-option:hover {
      background-color: var(--gray-50);
    }

    .especialidad-option:has(input:checked) {
      border-color: var(--primary);
      background-color: #ecfdf5;
    }

    .especialidades-grid.is-invalid {
      outline: 2px solid var(--danger);
      outline-offset: 2px;
      border-radius: 0.5rem;
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
      <button class="btn-secondary" id="btn-exportar-profesionales" type="button">
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
        @foreach ($especialidades ?? [] as $esp)
          <option value="{{ $esp }}">{{ $esp }}</option>
        @endforeach
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
                <button class="btn-outline" onclick="verProfesional('{{ $profesional->rfc }}')">Ver</button>
                <button class="btn-secondary" onclick="editarProfesional('{{ $profesional->rfc }}')">Editar</button>
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
        <form id="form-profesional" method="POST" action="{{ route('profesionales.store') }}" novalidate>
          @csrf
          <input type="hidden" id="profesional-id">
          
          <div class="form-section">
            <h4 class="form-section-title">Información del Profesional</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="profesional-rfc" id="label-profesional-rfc">RFC *</label>
                <input
                  type="text"
                  id="profesional-rfc"
                  name="rfc"
                  class="form-control"
                  maxlength="13"
                  minlength="12"
                  autocomplete="off"
                  autocapitalize="characters"
                  spellcheck="false"
                  pattern="^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$"
                  title="RFC válido: 3-4 letras, 6 dígitos y 3 caracteres alfanuméricos (12 o 13 caracteres). Ej: XAXX010101000"
                  placeholder="XAXX010101000"
                  required>
                <small class="field-hint" id="hint-profesional-rfc">12 o 13 caracteres. Solo letras, números y Ñ.</small>
                <small class="field-error" id="error-profesional-rfc"></small>
                <p class="field-info" id="info-profesional-rfc-edit">
                  El RFC es el identificador único del profesional y no se puede modificar al editar.
                  Puede actualizar nombre, correo, especialidades, turno y estado.
                  Si el RFC fue registrado por error, cree un profesional nuevo con el RFC correcto y desactive este.
                </p>
              </div>
              <div class="form-group">
                <label for="profesional-nombre">Nombre *</label>
                <input
                  type="text"
                  id="profesional-nombre"
                  name="nombre"
                  class="form-control"
                  minlength="2"
                  maxlength="100"
                  pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñÜü][A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\.\-]{1,99}$"
                  title="Solo letras, espacios, puntos y guiones (mínimo 2 caracteres)."
                  placeholder="Ej: María González"
                  required>
                <small class="field-error" id="error-profesional-nombre"></small>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="profesional-correo">Correo *</label>
                <input
                  type="email"
                  id="profesional-correo"
                  name="correo"
                  class="form-control"
                  maxlength="100"
                  inputmode="email"
                  autocomplete="email"
                  placeholder="nombre@clinica.com"
                  required>
                <small class="field-hint">Formato de correo electrónico válido.</small>
                <small class="field-error" id="error-profesional-correo"></small>
              </div>
            </div>
            <div class="form-group">
              <label>Especialidades *</label>
              <div class="especialidades-grid" id="especialidades-grid">
                @foreach ($especialidades ?? [] as $esp)
                  <label class="especialidad-option">
                    <input
                      type="checkbox"
                      name="especialidades[]"
                      value="{{ $esp }}"
                      class="profesional-especialidad-check">
                    <span>{{ $esp }}</span>
                  </label>
                @endforeach
              </div>
              <small class="field-hint">Seleccione al menos una especialidad.</small>
              <small class="field-error" id="error-profesional-especialidades"></small>
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
      <div class="modal-footer" style="justify-content: space-between;">
        <button
          type="button"
          class="btn-danger"
          id="btn-eliminar-profesional"
          style="display: none;"
          onclick="eliminarProfesional(document.getElementById('profesional-id').value)">
          Eliminar
        </button>
        <div style="display: flex; gap: 0.75rem; margin-left: auto;">
          <button type="button" class="btn-secondary" onclick="cerrarModalProfesional()">Cancelar</button>
          <button type="button" class="btn-primary" onclick="guardarProfesional()">Guardar Profesional</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de detalle del profesional -->
  <div id="modal-ver-profesional" class="modal" style="display: none;">
    <div class="modal-content modal-detail-content">
      <div class="modal-header">
        <div>
          <h3 id="ver-profesional-nombre">Detalle del Profesional</h3>
          <p id="ver-profesional-rfc" class="detail-subtitle">RFC: -</p>
        </div>
        <button class="modal-close" onclick="cerrarModalVerProfesional()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="detail-hero">
          <div class="detail-avatar" id="ver-profesional-avatar">DR</div>
          <div class="detail-summary">
            <div class="detail-badges">
              <span class="detail-badge" id="ver-profesional-especialidad">Especialidad: -</span>
              <span class="detail-badge" id="ver-profesional-estado">Estado: -</span>
              <span class="detail-badge" id="ver-profesional-turno">Turno: -</span>
            </div>
            <p class="detail-text" id="ver-profesional-correo">Correo: -</p>
          </div>
        </div>

        <div class="detail-grid">
          <div class="detail-card">
            <span class="detail-label">RFC</span>
            <strong class="detail-value" id="ver-profesional-rfc-valor">-</strong>
          </div>
          <div class="detail-card">
            <span class="detail-label">Turno</span>
            <strong class="detail-value" id="ver-profesional-turno-valor">-</strong>
          </div>
        </div>

        <div class="detail-section detail-stats">
          <div>
            <span class="detail-label">Total de citas</span>
            <strong class="detail-value" id="ver-profesional-total-citas">0</strong>
          </div>
          <div>
            <span class="detail-label">Mascotas atendidas</span>
            <strong class="detail-value" id="ver-profesional-total-mascotas">0</strong>
          </div>
        </div>

        <div class="detail-section">
          <h4>Citas registradas</h4>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Mascota</th>
                  <th>Especie</th>
                  <th>Servicio</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody id="tabla-citas-profesional">
                <tr>
                  <td colspan="6" style="text-align: center; padding: 1rem; color: var(--gray-500);">
                    Sin citas registradas
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer modal-footer-detail">
        <button type="button" class="btn-secondary" onclick="cerrarModalVerProfesional()">Cerrar</button>
        <button type="button" class="btn-primary" onclick="editarProfesionalDesdeDetalle()">Editar Profesional</button>
      </div>
    </div>
  </div>
</section>

<script>
const RFC_PATTERN = /^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/;
let profesionalDetalleActualRfc = null;

function urlProfesional(rfc) {
    return `/recepcion/profesionales/${encodeURIComponent(rfc)}`;
}

function opcionesFetchProfesional(opciones = {}) {
    const { headers: extraHeaders, ...rest } = opciones;

    return {
        credentials: 'include',
        ...rest,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            ...(extraHeaders || {}),
        },
    };
}

async function respuestaJsonProfesional(response) {
    const contentType = response.headers.get('content-type') || '';

    if (!response.ok) {
        if (contentType.includes('application/json')) {
            const errorData = await response.json();
            if (errorData.errors) {
                const mensajes = Object.values(errorData.errors).flat();
                throw new Error(mensajes.join('\n') || errorData.message || 'Error en la solicitud');
            }
            throw new Error(errorData.message || `Error ${response.status}`);
        }

        if (response.redirected || response.status === 401 || response.status === 403) {
            throw new Error('Sesión expirada o sin permisos. Recargue la página e inicie sesión nuevamente.');
        }

        throw new Error(`Error ${response.status}: ${response.statusText}`);
    }

    if (!contentType.includes('application/json')) {
        throw new Error('El servidor no devolvió JSON. Verifique su sesión o recargue la página.');
    }

    return response.json();
}

document.addEventListener('DOMContentLoaded', function() {
    const btnNuevoProfesional = document.getElementById('btn-nuevo-profesional');
    if (btnNuevoProfesional) {
        btnNuevoProfesional.addEventListener('click', abrirModalProfesional);
    }

    const btnExportarProfesionales = document.getElementById('btn-exportar-profesionales');
    if (btnExportarProfesionales) {
        btnExportarProfesionales.addEventListener('click', exportarProfesionales);
    }

    const searchInput = document.getElementById('search-profesionales');
    if (searchInput) {
        searchInput.addEventListener('input', filtrarProfesionales);
    }

    const filterEspecialidad = document.getElementById('filter-especialidad');
    const filterEstado = document.getElementById('filter-estado-profesional');

    if (filterEspecialidad && filterEstado) {
        [filterEspecialidad, filterEstado].forEach(select => {
            select.addEventListener('change', aplicarFiltrosProfesionales);
        });
    }

    const rfcInput = document.getElementById('profesional-rfc');
    if (rfcInput) {
        rfcInput.addEventListener('input', aplicarMascaraRfc);
        rfcInput.addEventListener('blur', () => validarCampoRfc(rfcInput));
    }

    const nombreInput = document.getElementById('profesional-nombre');
    if (nombreInput) {
        nombreInput.addEventListener('blur', () => validarCampoNombre(nombreInput));
    }

    const correoInput = document.getElementById('profesional-correo');
    if (correoInput) {
        correoInput.addEventListener('blur', () => validarCampoCorreo(correoInput));
    }

    document.querySelectorAll('.profesional-especialidad-check').forEach(checkbox => {
        checkbox.addEventListener('change', validarEspecialidades);
    });

    const modalProfesional = document.getElementById('modal-profesional');
    const modalVerProfesional = document.getElementById('modal-ver-profesional');

    document.addEventListener('click', function(event) {
        if (modalProfesional && event.target === modalProfesional) {
            cerrarModalProfesional();
        }
        if (modalVerProfesional && event.target === modalVerProfesional) {
            cerrarModalVerProfesional();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalProfesional();
            cerrarModalVerProfesional();
        }
    });
});

function aplicarMascaraRfc(event) {
    const input = event.target;
    input.value = input.value.toUpperCase().replace(/[^A-ZÑ&0-9]/g, '').slice(0, 13);
    limpiarErrorCampo('profesional-rfc');
}

function validarCampoRfc(input) {
    const valor = input.value.trim();
    if (!valor) {
        mostrarErrorCampo('profesional-rfc', 'El RFC es obligatorio.');
        return false;
    }
    if (valor.length < 12 || valor.length > 13) {
        mostrarErrorCampo('profesional-rfc', 'El RFC debe tener 12 o 13 caracteres.');
        return false;
    }
    if (!RFC_PATTERN.test(valor)) {
        mostrarErrorCampo('profesional-rfc', 'Formato inválido. Ejemplo: XAXX010101000');
        return false;
    }
    limpiarErrorCampo('profesional-rfc');
    return true;
}

function validarCampoNombre(input) {
    const valor = input.value.trim();
    const nombrePattern = /^[A-Za-zÁÉÍÓÚáéíóúÑñÜü][A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\.\-]{1,99}$/;
    if (!valor) {
        mostrarErrorCampo('profesional-nombre', 'El nombre es obligatorio.');
        return false;
    }
    if (!nombrePattern.test(valor)) {
        mostrarErrorCampo('profesional-nombre', 'Solo letras, espacios, puntos y guiones (mín. 2 caracteres).');
        return false;
    }
    limpiarErrorCampo('profesional-nombre');
    return true;
}

function validarCampoCorreo(input) {
    const valor = input.value.trim();
    if (!valor) {
        mostrarErrorCampo('profesional-correo', 'El correo es obligatorio.');
        return false;
    }
    if (!input.checkValidity()) {
        mostrarErrorCampo('profesional-correo', 'Ingrese un correo electrónico válido.');
        return false;
    }
    limpiarErrorCampo('profesional-correo');
    return true;
}

function validarEspecialidades() {
    const seleccionadas = document.querySelectorAll('.profesional-especialidad-check:checked');
    const grid = document.getElementById('especialidades-grid');
    const errorEl = document.getElementById('error-profesional-especialidades');

    if (seleccionadas.length === 0) {
        if (grid) grid.classList.add('is-invalid');
        if (errorEl) {
            errorEl.textContent = 'Seleccione al menos una especialidad.';
            errorEl.style.display = 'block';
        }
        return false;
    }

    if (grid) grid.classList.remove('is-invalid');
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.style.display = 'none';
    }
    return true;
}

function mostrarErrorCampo(fieldId, mensaje) {
    const input = document.getElementById(fieldId);
    const errorEl = document.getElementById('error-' + fieldId);
    if (input) input.classList.add('is-invalid');
    if (errorEl) {
        errorEl.textContent = mensaje;
        errorEl.style.display = 'block';
    }
}

function limpiarErrorCampo(fieldId) {
    const input = document.getElementById(fieldId);
    const errorEl = document.getElementById('error-' + fieldId);
    if (input) input.classList.remove('is-invalid');
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.style.display = 'none';
    }
}

function limpiarErroresFormulario() {
    ['profesional-rfc', 'profesional-nombre', 'profesional-correo'].forEach(limpiarErrorCampo);
    document.getElementById('especialidades-grid')?.classList.remove('is-invalid');
    const errorEsp = document.getElementById('error-profesional-especialidades');
    if (errorEsp) {
        errorEsp.textContent = '';
        errorEsp.style.display = 'none';
    }
}

function marcarEspecialidades(valorGuardado) {
    const valores = (valorGuardado || '')
        .split(',')
        .map(v => v.trim())
        .filter(Boolean);

    document.querySelectorAll('.profesional-especialidad-check').forEach(checkbox => {
        checkbox.checked = valores.includes(checkbox.value);
    });
}

function esEdicionProfesional() {
    return Boolean(document.getElementById('profesional-id')?.value?.trim());
}

function configurarCampoRfcModo(edicion) {
    const rfcInput = document.getElementById('profesional-rfc');
    const labelRfc = document.getElementById('label-profesional-rfc');
    const hintRfc = document.getElementById('hint-profesional-rfc');
    const infoEdicion = document.getElementById('info-profesional-rfc-edit');

    if (!rfcInput) {
        return;
    }

    if (edicion) {
        rfcInput.readOnly = true;
        rfcInput.disabled = false;
        rfcInput.removeAttribute('required');
        rfcInput.classList.add('form-control-readonly');
        if (labelRfc) {
            labelRfc.textContent = 'RFC (identificador)';
        }
        if (hintRfc) {
            hintRfc.textContent = 'Solo lectura: el RFC identifica al profesional en el sistema.';
        }
        if (infoEdicion) {
            infoEdicion.classList.add('is-visible');
        }
    } else {
        rfcInput.readOnly = false;
        rfcInput.disabled = false;
        rfcInput.setAttribute('required', 'required');
        rfcInput.classList.remove('form-control-readonly');
        if (labelRfc) {
            labelRfc.textContent = 'RFC *';
        }
        if (hintRfc) {
            hintRfc.textContent = '12 o 13 caracteres. Solo letras, números y Ñ.';
        }
        if (infoEdicion) {
            infoEdicion.classList.remove('is-visible');
        }
    }
}

function validarFormularioProfesional() {
    const esEdicion = esEdicionProfesional();
    const rfcOk = esEdicion ? true : validarCampoRfc(document.getElementById('profesional-rfc'));
    const nombreOk = validarCampoNombre(document.getElementById('profesional-nombre'));
    const correoOk = validarCampoCorreo(document.getElementById('profesional-correo'));
    const especialidadesOk = validarEspecialidades();
    const form = document.getElementById('form-profesional');

    if (esEdicion && form) {
        const camposEditables = form.querySelectorAll('input:not([readonly]):not([type="hidden"]), select, textarea');
        let formularioValido = true;
        camposEditables.forEach((campo) => {
            if (!campo.checkValidity()) {
                formularioValido = false;
            }
        });
        return nombreOk && correoOk && especialidadesOk && formularioValido;
    }

    return rfcOk && nombreOk && correoOk && especialidadesOk && (form ? form.checkValidity() : false);
}

function exportarProfesionales() {
    const params = new URLSearchParams();
    const searchInput = document.getElementById('search-profesionales');
    const filterEspecialidad = document.getElementById('filter-especialidad');
    const filterEstado = document.getElementById('filter-estado-profesional');

    if (searchInput?.value.trim()) {
        params.append('q', searchInput.value.trim());
    }
    if (filterEspecialidad?.value) {
        params.append('especialidad', filterEspecialidad.value);
    }
    if (filterEstado?.value !== '') {
        params.append('activo', filterEstado.value);
    }

    let url = '{{ route('profesionales.export') }}';
    if (params.toString()) {
        url += '?' + params.toString();
    }

    window.location.href = url;
}

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
        limpiarErroresFormulario();
        marcarEspecialidades('');
        const chk = document.getElementById('profesional-activo');
        if (chk) chk.checked = true;
        configurarCampoRfcModo(false);
        const btnEliminar = document.getElementById('btn-eliminar-profesional');
        if (btnEliminar) {
            btnEliminar.style.display = 'none';
        }
    }
}

function cerrarModalProfesional() {
    const modal = document.getElementById('modal-profesional');
    if (modal) modal.style.display = 'none';
}

async function guardarProfesional() {
    const form = document.getElementById('form-profesional');
    if (!form) return;

    if (!validarFormularioProfesional()) {
        alert('Revise los campos marcados antes de guardar.');
        return;
    }

  // Enviar al endpoint web configurado en el action del formulario
  const formData = new FormData(form);
  const profesionalRfc = document.getElementById('profesional-id').value;
  const method = 'POST';
  const baseUrl = '{{ url("recepcion/profesionales") }}';
  let url = form.action;
  if (profesionalRfc) {
    url = `${baseUrl}/${encodeURIComponent(profesionalRfc)}`;
    formData.append('_method', 'PUT');
  }

    try {
    const response = await fetch(url, opcionesFetchProfesional({
      method: method,
            body: formData
        }));

        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (!response.ok) {
            let errorMessage = `Error ${response.status}: ${response.statusText}`;
            
            // Intentar obtener más detalles del error
            try {
                if (contentType && contentType.includes('application/json')) {
                    const errorData = await response.json();
                    console.error('Respuesta JSON del servidor:', errorData);
                    
                    // Si hay errores de validación específicos
                    if (errorData.errors && typeof errorData.errors === 'object') {
                        const errorDetails = [];
                        for (const [field, messages] of Object.entries(errorData.errors)) {
                            if (Array.isArray(messages)) {
                                errorDetails.push(`${field}: ${messages.join(', ')}`);
                            } else {
                                errorDetails.push(`${field}: ${messages}`);
                            }
                        }
                        errorMessage = errorDetails.join('\n');
                        console.error('Errores de validación detallados:', errorData.errors);
                    } else if (errorData.message) {
                        errorMessage = errorData.message;
                    }
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

function obtenerDatosProfesionalRespuesta(payload) {
    return payload?.data ?? payload;
}

function formatearFechaCita(fecha) {
    if (!fecha) {
        return 'N/A';
    }
    const date = new Date(fecha);
    if (Number.isNaN(date.getTime())) {
        return fecha;
    }
    return date.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });
}

function obtenerInicialesProfesional(nombre) {
    const partes = String(nombre || 'DR').trim().split(/\s+/).filter(Boolean);
    if (partes.length === 0) {
        return 'DR';
    }
    if (partes.length === 1) {
        return partes[0].substring(0, 2).toUpperCase();
    }
    return (partes[0][0] + partes[1][0]).toUpperCase();
}

function renderizarCitasProfesional(citas) {
    const tbody = document.getElementById('tabla-citas-profesional');
    if (!tbody) {
        return;
    }

    if (!Array.isArray(citas) || citas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 1rem; color: var(--gray-500);">
                    Sin citas registradas
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = citas.map((cita) => `
        <tr>
            <td>${formatearFechaCita(cita.fecha)}</td>
            <td>${cita.horario ?? 'N/A'}</td>
            <td>${cita.mascota?.nombre ?? 'N/A'}</td>
            <td>${cita.mascota?.especie ?? 'N/A'}</td>
            <td>${cita.tipo_servicio ?? cita.tipo_cita ?? 'N/A'}</td>
            <td>${cita.estado ?? 'N/A'}</td>
        </tr>
    `).join('');
}

function cargarDetalleProfesionalEnVista(payload) {
    const profesional = obtenerDatosProfesionalRespuesta(payload);
    if (profesional?.rfc) {
        profesionalDetalleActualRfc = profesional.rfc;
    }
    const citas = payload?.citas ?? profesional?.citas ?? [];
    const totalCitas = payload?.total_citas ?? citas.length;
    const totalMascotas = payload?.total_mascotas ?? 0;
    const activo = Boolean(profesional?.activo);

    document.getElementById('ver-profesional-nombre').textContent = profesional?.nombre ?? 'Detalle del Profesional';
    document.getElementById('ver-profesional-rfc').textContent = `RFC: ${profesional?.rfc ?? '-'}`;
    document.getElementById('ver-profesional-rfc-valor').textContent = profesional?.rfc ?? '-';
    document.getElementById('ver-profesional-avatar').textContent = obtenerInicialesProfesional(profesional?.nombre);
    document.getElementById('ver-profesional-especialidad').textContent = `Especialidad: ${profesional?.especialidad ?? '-'}`;
    document.getElementById('ver-profesional-estado').textContent = `Estado: ${activo ? 'Activo' : 'Inactivo'}`;
    document.getElementById('ver-profesional-turno').textContent = `Turno: ${profesional?.turno ?? 'Sin asignar'}`;
    document.getElementById('ver-profesional-turno-valor').textContent = profesional?.turno ?? 'Sin asignar';
    document.getElementById('ver-profesional-correo').textContent = `Correo: ${profesional?.correo ?? '-'}`;
    document.getElementById('ver-profesional-total-citas').textContent = totalCitas;
    document.getElementById('ver-profesional-total-mascotas').textContent = totalMascotas;
    renderizarCitasProfesional(citas);
}

async function verProfesional(rfc) {
    profesionalDetalleActualRfc = rfc;
    const modal = document.getElementById('modal-ver-profesional');
    if (modal) {
        modal.style.display = 'flex';
    }

    document.getElementById('ver-profesional-nombre').textContent = 'Cargando...';
    renderizarCitasProfesional([]);

    try {
        const response = await fetch(urlProfesional(rfc), opcionesFetchProfesional());
        const payload = await respuestaJsonProfesional(response);
        cargarDetalleProfesionalEnVista(payload);
    } catch (error) {
        console.error('Error cargando profesional:', error);
        alert('Error al cargar los datos del profesional');
        cerrarModalVerProfesional();
    }
}

function cerrarModalVerProfesional() {
    const modal = document.getElementById('modal-ver-profesional');
    if (modal) {
        modal.style.display = 'none';
    }
    profesionalDetalleActualRfc = null;
}

function editarProfesionalDesdeDetalle() {
    const rfc = profesionalDetalleActualRfc
        || document.getElementById('ver-profesional-rfc-valor')?.textContent?.trim();

    if (!rfc || rfc === '-') {
        alert('No se pudo identificar el profesional a editar.');
        return;
    }

    cerrarModalVerProfesional();
    editarProfesional(rfc);
}

async function eliminarProfesional(rfc) {
    const rfcEliminar = rfc || document.getElementById('profesional-id')?.value;
    if (!rfcEliminar) {
        alert('No se pudo identificar el profesional a eliminar');
        return;
    }

    const confirmado = confirm('¿Está seguro de eliminar este profesional? Esta acción no se puede deshacer.');
    if (!confirmado) {
        return;
    }

    try {
        const response = await fetch(urlProfesional(rfcEliminar), opcionesFetchProfesional({
            method: 'DELETE',
        }));
        const data = await respuestaJsonProfesional(response);

        cerrarModalProfesional();
        cerrarModalVerProfesional();
        alert(data?.message || 'Profesional eliminado correctamente');
        location.reload();
    } catch (error) {
        console.error('Error eliminando profesional:', error);
        alert('Error al eliminar el profesional: ' + error.message);
    }
}

async function editarProfesional(rfc) {
    if (!rfc || rfc === 'null' || rfc === 'undefined') {
        alert('No se pudo identificar el profesional a editar.');
        return;
    }

    try {
  const response = await fetch(urlProfesional(rfc), opcionesFetchProfesional());
        const payload = await respuestaJsonProfesional(response);
        const profesional = obtenerDatosProfesionalRespuesta(payload);
        
        // Llenar el formulario con los datos del profesional
  document.getElementById('profesional-id').value = profesional?.rfc ?? rfc;
  const rfcValor = profesional?.rfc ?? rfc;
  const rfcInput = document.getElementById('profesional-rfc');
  if (rfcInput) {
    rfcInput.value = rfcValor;
  }
  configurarCampoRfcModo(true);
  document.getElementById('profesional-nombre').value = profesional?.nombre ?? '';
  document.getElementById('profesional-correo').value = profesional?.correo ?? '';
  marcarEspecialidades(profesional?.especialidad ?? '');
  limpiarErroresFormulario();
  document.getElementById('profesional-turno').value = profesional?.turno ?? '';
  document.getElementById('profesional-activo').checked = Boolean(profesional?.activo ?? true);

        const btnEliminar = document.getElementById('btn-eliminar-profesional');
        if (btnEliminar) {
            btnEliminar.style.display = 'inline-flex';
        }

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

</script>
@endsection