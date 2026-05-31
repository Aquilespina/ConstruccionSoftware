@extends('dash.recepcion')
@section('page-title', 'Honorarios')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recepcion/honorarios.css') }}">
@endpush
@section('content')
<section id="mod-honorarios" class="module active">
  <div class="module-header">
    <h2 class="module-title">Gestión de Honorarios</h2>
    <div class="module-actions">
      <button class="btn-primary" id="btn-nuevo-pago">Registrar Pago</button>
      <button class="btn-secondary">Exportar Reporte</button>
    </div>
  </div>
  
  <div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Ingresos del Mes</h3>
        <div class="stat-icon icon-green">💰</div>
      </div>
      <div class="stat-value">$24,580</div>
      <div class="stat-change">+12% vs mes anterior</div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Pagos Pendientes</h3>
        <div class="stat-icon icon-orange">⏳</div>
      </div>
      <div class="stat-value">$3,420</div>
      <div class="stat-change">8 facturas</div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Promedio por Consulta</h3>
        <div class="stat-icon icon-blue">📊</div>
      </div>
      <div class="stat-value">$85</div>
      <div class="stat-change">+$5 desde ayer</div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <h3 class="stat-title">Consultas del Mes</h3>
        <div class="stat-icon icon-purple">🩺</div>
      </div>
      <div class="stat-value">289</div>
      <div class="stat-change">+24 este mes</div>
    </div>
  </div>
  
  <div class="filters-bar">
    <div class="search-filter">
      <input type="text" placeholder="Buscar pago..." class="search-input" id="search-pagos">
    </div>
    <div class="filter-actions">
      <select class="filter-select" id="filter-estado-pago">
        <option value="">Todos los estados</option>
        <option value="pagado">Pagado</option>
        <option value="pendiente">Pendiente</option>
        <option value="vencido">Vencido</option>
      </select>
      <select class="filter-select" id="filter-mes-pago">
        <option value="">Todos los meses</option>
        <option value="11">Noviembre 2023</option>
        <option value="10">Octubre 2023</option>
        <option value="9">Septiembre 2023</option>
      </select>
    </div>
  </div>
  
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>Factura</th>
          <th>Paciente/Propietario</th>
          <th>Servicio</th>
          <th>Médico</th>
          <th>Fecha</th>
          <th>Monto</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tabla-honorarios">
        <tr>
          <td>
            <div style="font-weight: 600;">FAC-001</div>
            <div style="font-size: 12px; color: #64748b;">15 Nov 2023</div>
          </td>
          <td>
            <div style="font-weight: 600;">Max</div>
            <div style="font-size: 12px; color: #64748b;">María Rodríguez</div>
          </td>
          <td>Consulta general + medicamentos</td>
          <td>Dra. Laura Méndez</td>
          <td>15 Nov 2023</td>
          <td>
            <div style="font-weight: 600; color: #16a34a;">$150</div>
          </td>
          <td><span class="status-badge status-active">Pagado</span></td>
          <td>
            <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;" onclick="verFactura(1)">Ver</button>
            <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;" onclick="imprimirFactura(1)">Imprimir</button>
          </td>
        </tr>
        <tr>
          <td>
            <div style="font-weight: 600;">FAC-002</div>
            <div style="font-size: 12px; color: #64748b;">12 Nov 2023</div>
          </td>
          <td>
            <div style="font-weight: 600;">Luna</div>
            <div style="font-size: 12px; color: #64748b;">Carlos Pérez</div>
          </td>
          <td>Vacunación anual</td>
          <td>Dr. Roberto García</td>
          <td>12 Nov 2023</td>
          <td>
            <div style="font-weight: 600; color: #ea580c;">$85</div>
          </td>
          <td><span class="status-badge status-pending">Pendiente</span></td>
          <td>
            <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;" onclick="verFactura(2)">Ver</button>
            <button class="btn-primary" style="padding: 4px 8px; font-size: 12px;" onclick="registrarPago(2)">Pagar</button>
          </td>
        </tr>
        <tr>
          <td>
            <div style="font-weight: 600;">FAC-003</div>
            <div style="font-size: 12px; color: #64748b;">10 Nov 2023</div>
          </td>
          <td>
            <div style="font-weight: 600;">Toby</div>
            <div style="font-size: 12px; color: #64748b;">Ana González</div>
          </td>
          <td>Cirugía menor + hospitalización</td>
          <td>Dra. Laura Méndez</td>
          <td>10 Nov 2023</td>
          <td>
            <div style="font-weight: 600; color: #16a34a;">$420</div>
          </td>
          <td><span class="status-badge status-active">Pagado</span></td>
          <td>
            <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;" onclick="verFactura(3)">Ver</button>
            <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;" onclick="imprimirFactura(3)">Imprimir</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Modal para registrar pago -->
  <div id="modal-pago" class="modal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Registrar Pago</h3>
        <button class="modal-close" onclick="cerrarModalPago()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form-pago">
          @csrf
          <div class="form-group">
            <label for="pago-factura">Factura</label>
            <input type="text" id="pago-factura" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label for="pago-paciente">Paciente</label>
            <input type="text" id="pago-paciente" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label for="pago-monto">Monto a Pagar *</label>
            <input type="number" id="pago-monto" name="monto" class="form-control" step="0.01" required>
          </div>
          <div class="form-group">
            <label for="pago-metodo">Método de Pago *</label>
            <select id="pago-metodo" name="metodo_pago" class="form-control" required>
              <option value="">Seleccionar método</option>
              <option value="efectivo">Efectivo</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="transferencia">Transferencia</option>
            </select>
          </div>
          <div class="form-group">
            <label for="pago-observaciones">Observaciones</label>
            <textarea id="pago-observaciones" name="observaciones" class="form-control" rows="3"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="cerrarModalPago()">Cancelar</button>
        <button type="button" class="btn-primary" onclick="confirmarPago()">Confirmar Pago</button>
      </div>
    </div>
  </div>
</section>
@endsection