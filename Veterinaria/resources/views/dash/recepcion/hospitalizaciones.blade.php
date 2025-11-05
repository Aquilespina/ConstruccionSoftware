@extends('dash.recepcion')
@section('page-title', 'Hospitalizaciones')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/hospitalizaciones.css') }}">
@endpush
@section('content')
<section id="mod-hospitalizaciones" class="module active">
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Hospitalizaciones</title>
    <link rel="stylesheet" href="hospitalizaciones.css">
</head>
<body>
    <section id="mod-hospitalizaciones" class="module active">
        <div class="module-header">
            <h2 class="module-title">Gestión de Hospitalizaciones</h2>
            <div class="module-actions">
                <button class="btn-primary" id="btn-nueva-hospitalizacion">
                    <span class="btn-icon">+</span>
                    Nueva Hospitalización
                </button>
                <button class="btn-secondary">
                    <span class="btn-icon">📊</span>
                    Reporte Diario
                </button>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Pacientes Hospitalizados</h3>
                    <div class="stat-icon icon-red">🏥</div>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-change">Activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Altas del Mes</h3>
                    <div class="stat-icon icon-green">✅</div>
                </div>
                <div class="stat-value">12</div>
                <div class="stat-change">+2 esta semana</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Camas Disponibles</h3>
                    <div class="stat-icon icon-blue">🛏️</div>
                </div>
                <div class="stat-value">7/10</div>
                <div class="stat-change">70% ocupación</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Promedio Estancia</h3>
                    <div class="stat-icon icon-purple">📅</div>
                </div>
                <div class="stat-value">3.2 días</div>
                <div class="stat-change">-0.5 vs mes anterior</div>
            </div>
        </div>
        
        <div class="filters-bar">
            <div class="search-filter">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" placeholder="Buscar hospitalización..." class="search-input" id="search-hospitalizaciones">
                </div>
            </div>
            <div class="filter-actions">
                <select class="filter-select" id="filter-estado-hospitalizacion">
                    <option value="">Todos los estados</option>
                    <option value="activa">Activa</option>
                    <option value="alta">Con alta</option>
                    <option value="observacion">En observación</option>
                </select>
                <select class="filter-select" id="filter-area-hospitalizacion">
                    <option value="">Todas las áreas</option>
                    <option value="uci">UCI</option>
                    <option value="general">Área General</option>
                    <option value="aislamiento">Aislamiento</option>
                </select>
                <button class="btn-filter" id="btn-filtrar">
                    <span class="btn-icon">🔍</span>
                    Filtrar
                </button>
            </div>
        </div>
        
        <div class="table-container">
            @if($hospitalizaciones->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mascota</th>
                            <th>Especie</th>
                            <th>Propietario</th>
                            <th>Fecha Ingreso</th>
                            <th>Fecha Egreso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hospitalizaciones as $hosp)
                            <tr>
                                <td>{{ $hosp->id_hospitalizacion }}</td>
                                <td>
                                    <div class="hospitalization-avatar">🐾</div>
                                    {{ $hosp->mascota_nombre }}
                                </td>
                                <td>{{ $hosp->especie }}</td>
                                <td>{{ $hosp->propietario_nombre }}</td>
                                <td>{{ $hosp->fecha_ingreso ? \Carbon\Carbon::parse($hosp->fecha_ingreso)->format('d/m/Y H:i') : '—' }}</td>
                                <td>{{ $hosp->fecha_egreso ? \Carbon\Carbon::parse($hosp->fecha_egreso)->format('d/m/Y H:i') : '—' }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($hosp->estado) }}">
                                        {{ $hosp->estado }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('hospitalizaciones.show', $hosp->id_hospitalizacion) }}" 
                                       class="btn-outline">Ver</a>
                                    <a href="{{ route('hospitalizaciones.edit', $hosp->id_hospitalizacion) }}" 
                                       class="btn-outline">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🏥</div>
                    <h3>No hay hospitalizaciones registradas</h3>
                    <p>Registra la primera hospitalización haciendo clic en el botón "Nueva Hospitalización".</p>
                </div>
            @endif
        </div>

        <!-- Modal para nueva hospitalización -->
        <div id="modal-hospitalizacion" class="modal">
            <div class="modal-overlay" onclick="cerrarModalHospitalizacion()"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Nueva Hospitalización</h3>
                    <button class="modal-close" onclick="cerrarModalHospitalizacion()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="form-hospitalizacion" action="{{ route('hospitalizaciones.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="hospitalizacion-mascota">Mascota *</label>
                                <select id="hospitalizacion-mascota" name="id_mascota" class="form-control" required>
                                    <option value="">Seleccionar mascota</option>
                                    @forelse($mascotas as $mascota)
                                        <option value="{{ $mascota['id'] }}" 
                                                data-especie="{{ $mascota['especie'] }}"
                                                data-raza="{{ $mascota['raza'] }}">
                                            {{ $mascota['display_name'] }}
                                        </option>
                                    @empty
                                        <option value="" disabled>No hay mascotas registradas</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="hospitalizacion-cita">Cita Relacionada (Opcional)</label>
                                <select id="hospitalizacion-cita" name="id_cita" class="form-control">
                                    <option value="">Sin cita relacionada</option>
                                    <!-- Las citas se cargarán dinámicamente según la mascota seleccionada -->
                                </select>
                                <small class="form-text text-muted">Si la hospitalización deriva de una cita específica</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="hospitalizacion-fecha-ingreso">Fecha de Ingreso *</label>
                                <input type="datetime-local" id="hospitalizacion-fecha-ingreso" name="fecha_ingreso" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="hospitalizacion-estado">Estado *</label>
                                <select id="hospitalizacion-estado" name="estado" class="form-control" required>
                                    <option value="">Seleccionar estado</option>
                                    <option value="Internado">Internado</option>
                                    <option value="Alta">Alta</option>
                                    <option value="Tratamiento">Tratamiento</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="hospitalizacion-observaciones">Observaciones</label>
                            <textarea id="hospitalizacion-observaciones" name="observaciones" class="form-control" rows="4" placeholder="Describa el motivo de la hospitalización, tratamiento requerido, etc."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="cerrarModalHospitalizacion()">Cancelar</button>
                    <button type="button" class="btn-primary" onclick="guardarHospitalizacion()">Registrar Hospitalización</button>
                </div>
            </div>
        </div>
    </section>
<script src="{{ asset('js/recepcion/hospitalizaciones.js') }}"></script>
</body>
</html>
@endsection