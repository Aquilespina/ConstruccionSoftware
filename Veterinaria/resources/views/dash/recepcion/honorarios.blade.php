@extends('dash.recepcion')
@section('page-title', 'Honorarios')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/honorarios.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
<section id="mod-honorarios" class="module active">
    <div class="module-header">
        <h2 class="module-title">Gestión de Honorarios</h2>
        <div class="module-actions">
            <button class="btn-primary" id="btn-nuevo-honorario">
                <span class="btn-icon">+</span>
                Nuevo Honorario
            </button>
            <button class="btn-secondary">
                <span class="btn-icon">📊</span>
                Reporte General
            </button>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Total Pendiente</h3>
                <div class="stat-icon icon-orange">💰</div>
            </div>
            <div class="stat-value">${{ number_format($honorarios->where('estado', 'Pendiente')->sum('saldo_pendiente'), 2) }}</div>
            <div class="stat-change">{{ $honorarios->where('estado', 'Pendiente')->count() }} honorarios</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Pagado este Mes</h3>
                <div class="stat-icon icon-green">✅</div>
            </div>
            <div class="stat-value">${{ number_format($honorarios->where('estado', 'Pagado')->sum('total_pagado'), 2) }}</div>
            <div class="stat-change">{{ $honorarios->where('estado', 'Pagado')->count() }} completados</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Pagos Parciales</h3>
                <div class="stat-icon icon-blue">📊</div>
            </div>
            <div class="stat-value">${{ number_format($honorarios->where('estado', 'Parcial')->sum('total_pagado'), 2) }}</div>
            <div class="stat-change">{{ $honorarios->where('estado', 'Parcial')->count() }} en proceso</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Total Honorarios</h3>
                <div class="stat-icon icon-purple">📋</div>
            </div>
            <div class="stat-value">{{ $honorarios->count() }}</div>
            <div class="stat-change">Total registrados</div>
        </div>
    </div>

    <div class="filters-bar">
        <div class="search-filter">
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Buscar honorario..." class="search-input" id="search-honorarios">
            </div>
        </div>
        <div class="filter-actions">
            <select class="filter-select" id="filter-estado">
                <option value="">Todos los estados</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Parcial">Parcial</option>
                <option value="Pagado">Pagado</option>
            </select>
            <select class="filter-select" id="filter-mascota">
                <option value="">Todas las mascotas</option>
                @foreach($mascotas as $mascota)
                    <option value="{{ $mascota['id'] }}">{{ $mascota['display_name'] }}</option>
                @endforeach
            </select>
            <button class="btn-filter" id="btn-filtrar">
                <span class="btn-icon">🔍</span>
                Filtrar
            </button>
        </div>
    </div>

    <div class="table-container">
        @if($honorarios->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mascota</th>
                        <th>Propietario</th>
                        <th>Fecha Ingreso</th>
                        <th>Subtotal</th>
                        <th>Pagado</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($honorarios as $honorario)
                        <tr data-id="{{ $honorario->id_honorario }}">
                            <td>{{ $honorario->id_honorario }}</td>
                            <td>
                                <div class="pet-info">
                                    <div class="pet-avatar">🐾</div>
                                    <div class="pet-details">
                                        <strong>{{ $honorario->mascota_nombre }}</strong>
                                        <small>{{ $honorario->especie }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $honorario->propietario_nombre }}</td>
                            <td>{{ $honorario->fecha_ingreso ? \Carbon\Carbon::parse($honorario->fecha_ingreso)->format('d/m/Y') : '—' }}</td>
                            <td>${{ number_format($honorario->subtotal, 2) }}</td>
                            <td class="total-pagado">${{ number_format($honorario->total_pagado, 2) }}</td>
                            <td class="saldo-pendiente">${{ number_format($honorario->saldo_pendiente, 2) }}</td>
                            <td>
                                <span class="status-badge estado-badge status-{{ strtolower($honorario->estado) }}">
                                    {{ $honorario->estado }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('honorarios.honorarios.show', $honorario->id_honorario) }}" 
                                       class="btn-outline btn-small">Ver</a>
                                    @if($honorario->estado !== 'Pagado')
                                        <button class="btn-outline btn-small" 
                                                onclick="editarHonorario({{ $honorario->id_honorario }})">
                                            Editar
                                        </button>
                                        <button class="btn-outline btn-small" 
                                                onclick="abrirModalPago({{ $honorario->id_honorario }})">
                                            Pagar
                                        </button>
                                    @endif
                                    <a href="{{ route('honorarios.honorarios.pdf', $honorario->id_honorario) }}" 
                                       class="btn-outline btn-small" target="_blank">PDF</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">💰</div>
                <h3>No hay honorarios registrados</h3>
                <p>Registra el primer honorario haciendo clic en el botón "Nuevo Honorario".</p>
            </div>
        @endif
    </div>

    <!-- Modal para nuevo honorario -->
    <div id="modal-honorario" class="modal">
        <div class="modal-overlay" onclick="cerrarModalHonorario()"></div>
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Nuevo Honorario</h3>
                <button class="modal-close" onclick="cerrarModalHonorario()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-honorario" action="{{ route('honorarios.honorarios.store') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="honorario-mascota">Mascota *</label>
                            <select id="honorario-mascota" name="id_mascota" class="form-control" required>
                                <option value="">Seleccionar mascota</option>
                                @foreach($mascotas as $mascota)
                                    <option value="{{ $mascota['id'] }}" 
                                            data-especie="{{ $mascota['especie'] }}">
                                        {{ $mascota['display_name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="honorario-hospitalizacion">Hospitalización (Opcional)</label>
                            <select id="honorario-hospitalizacion" name="id_hospitalizacion" class="form-control">
                                <option value="">Sin hospitalización relacionada</option>
                                @foreach($hospitalizaciones as $hosp)
                                    <option value="{{ $hosp->id_hospitalizacion }}">
                                        {{ $hosp->mascota_nombre }} - {{ \Carbon\Carbon::parse($hosp->fecha_ingreso)->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="honorario-fecha-ingreso">Fecha de Ingreso *</label>
                            <input type="date" id="honorario-fecha-ingreso" name="fecha_ingreso" 
                                   class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="honorario-fecha-corte">Fecha de Corte</label>
                            <input type="date" id="honorario-fecha-corte" name="fecha_corte" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <div class="section-header">
                            <h4>Detalles del Honorario</h4>
                            <button type="button" class="btn-secondary btn-small" onclick="agregarDetalle()">
                                + Agregar Concepto
                            </button>
                        </div>
                        <div id="detalles-container">
                            <div class="detalle-item" data-index="0">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Concepto *</label>
                                        <input type="text" name="detalles[0][concepto]" 
                                               class="form-control concepto-input" 
                                               placeholder="Descripción del servicio" required>
                                        <div class="concepto-suggestions"></div>
                                    </div>
                                    <div class="form-group form-group-small">
                                        <label>Cantidad *</label>
                                        <input type="number" name="detalles[0][cantidad]" 
                                               class="form-control cantidad-input" 
                                               min="1" value="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Precio Unitario *</label>
                                        <input type="number" name="detalles[0][precio_unitario]" 
                                               class="form-control precio-input" 
                                               step="0.01" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Importe</label>
                                        <input type="text" class="form-control importe-display" readonly>
                                    </div>
                                    <div class="form-group form-group-actions">
                                        <button type="button" class="btn-danger btn-small" 
                                                onclick="eliminarDetalle(this)" style="margin-top: 25px;">
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="total-section">
                            <div class="total-row">
                                <strong>Subtotal: $<span id="subtotal-display">0.00</span></strong>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalHonorario()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="guardarHonorario()">Registrar Honorario</button>
            </div>
        </div>
    </div>

    <!-- Modal para registrar pago -->
    <div id="modal-pago" class="modal">
        <div class="modal-overlay" onclick="cerrarModalPago()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Registrar Pago</h3>
                <button class="modal-close" onclick="cerrarModalPago()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="info-honorario-pago" class="info-card" style="margin-bottom: 20px;">
                    <!-- Información del honorario se llenará dinámicamente -->
                </div>
                
                <form id="form-pago">
                    @csrf
                    <input type="hidden" id="pago-id-honorario" name="id_honorario">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pago-monto">Monto a Pagar *</label>
                            <input type="number" id="pago-monto" name="monto" 
                                   class="form-control" step="0.01" min="0.01" required
                                   placeholder="0.00">
                            <small class="form-text text-muted">Ingrese el monto total que pagará el cliente</small>
                        </div>
                        <div class="form-group">
                            <label for="pago-tipo">Tipo de Pago *</label>
                            <select id="pago-tipo" name="tipo_pago" class="form-control" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pago-notas">Notas (Opcional)</label>
                        <textarea id="pago-notas" name="notas" class="form-control" rows="3"
                                  placeholder="Notas adicionales sobre el pago..."></textarea>
                    </div>

                    <div class="pago-preview" id="pago-preview" style="display: none;">
                        <h4>Vista Previa del Pago</h4>
                        <div class="preview-content">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalPago()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="procesarPago()">Registrar Pago</button>
            </div>
        </div>
    </div>
</section>

<script src="{{ asset('js/recepcion/honorarios.js') }}"></script>
@endsection