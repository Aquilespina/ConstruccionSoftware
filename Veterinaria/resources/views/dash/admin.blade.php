<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Administrativo - VetClinic</title>
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="logo">⚙️</div>
        <div class="sidebar-header-text">
          <div class="brand-name">VetClinic</div>
          <div class="brand-subtitle">Panel Administrativo</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <button class="nav-item active" data-target="home">
          <span class="nav-icon">🏠</span>
          <span class="nav-label">Inicio</span>
        </button>
        <button class="nav-item" data-target="usuarios">
          <span class="nav-icon">👥</span>
          <span class="nav-label">Gestión de Usuarios</span>
        </button>
        <button class="nav-item" data-target="mascotas">
          <span class="nav-icon">🐕</span>
          <span class="nav-label">Gestión de Mascotas</span>
        </button>
        <button class="nav-item" data-target="medicos">
          <span class="nav-icon">🩺</span>
          <span class="nav-label">Gestión de Médicos</span>
        </button>
        <button class="nav-item" data-target="finanzas">
          <span class="nav-icon">💰</span>
          <span class="nav-label">Finanzas</span>
        </button>
        <button class="nav-item" data-target="inventario">
          <span class="nav-icon">📦</span>
          <span class="nav-label">Inventario</span>
        </button>
        <button class="nav-item" data-target="configuracion">
          <span class="nav-icon">⚙️</span>
          <span class="nav-label">Configuración</span>
        </button>
        <button class="nav-item" data-target="reportes">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Reportes</span>
        </button>
      </nav>

      <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="logout-btn">
            <span class="nav-icon">↩️</span>
            <span>Cerrar sesión</span>
          </button>
        </form>
      </div>
    </aside>

    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Content -->
    <main class="main-content">
      <header class="top-header">
        <div class="header-left">
          <button class="toggle-sidebar" id="toggleSidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>
          <h1 class="page-title" id="pageTitle">Inicio</h1>
        </div>

        <div class="header-right">
          <div class="search-container">
            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input type="text" class="search-input" placeholder="Buscar...">
          </div>

          <div class="user-profile">
            <div class="user-avatar">
              AD
            </div>
            <div class="user-info">
              <div class="user-name">Administrador</div>
              <div class="user-role">Administrador del Sistema</div>
            </div>
          </div>
        </div>
      </header>

      <div class="content-area">
        <!-- Home Module -->
        <section id="mod-home" class="module active">
          <div class="welcome-section">
            <h1 class="welcome-title">Bienvenido, Administrador</h1>
            <p class="welcome-subtitle">Panel de control administrativo - Resumen general del sistema</p>
          </div>

          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Total Usuarios</h3>
                <div class="stat-icon icon-blue">👥</div>
              </div>
              <div class="stat-value">127</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Mascotas Registradas</h3>
                <div class="stat-icon icon-red">🐕</div>
              </div>
              <div class="stat-value">342</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Médicos Activos</h3>
                <div class="stat-icon icon-orange">🩺</div>
              </div>
              <div class="stat-value">15</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Ingresos Mensuales</h3>
                <div class="stat-icon icon-green">💰</div>
              </div>
              <div class="stat-value">$24,580</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Consultas del Mes</h3>
                <div class="stat-icon icon-purple">📅</div>
              </div>
              <div class="stat-value">186</div>
            </div>
          </div>

          <div class="panels-grid">
            <div class="panel-card">
              <div class="panel-header">
                <h3 class="panel-title">Usuarios Recientes</h3>
                <button class="btn-primary">Ver Todos</button>
              </div>
              <div class="users-list">
                <div class="user-item">
                  <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">MR</div>
                  <div class="user-content">
                    <div class="user-name">María Rodríguez</div>
                    <div class="user-email">maria.rodriguez@email.com</div>
                  </div>
                  <span class="status-badge status-active">Activo</span>
                </div>
                <div class="user-item">
                  <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">CP</div>
                  <div class="user-content">
                    <div class="user-name">Carlos Pérez</div>
                    <div class="user-email">carlos.perez@email.com</div>
                  </div>
                  <span class="status-badge status-active">Activo</span>
                </div>
                <div class="user-item">
                  <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">AG</div>
                  <div class="user-content">
                    <div class="user-name">Ana González</div>
                    <div class="user-email">ana.gonzalez@email.com</div>
                  </div>
                  <span class="status-badge status-pending">Pendiente</span>
                </div>
              </div>
            </div>
            
            <div class="panel-card">
              <div class="panel-header">
                <h3 class="panel-title">Actividad Reciente</h3>
                <button class="btn-secondary">Ver Todo</button>
              </div>
              <div class="recent-activity">
                <div class="activity-item">
                  <div class="activity-icon">📋</div>
                  <div class="activity-content">
                    <div class="activity-title">Nuevo usuario registrado</div>
                    <div class="activity-description">Luis Martínez se registró en el sistema</div>
                  </div>
                  <span class="activity-time">Hace 2 horas</span>
                </div>
                <div class="activity-item">
                  <div class="activity-icon">💊</div>
                  <div class="activity-content">
                    <div class="activity-title">Stock bajo de medicamento</div>
                    <div class="activity-description">Antibiótico X está por debajo del nivel mínimo</div>
                  </div>
                  <span class="activity-time">Hace 5 horas</span>
                </div>
                <div class="activity-item">
                  <div class="activity-icon">💰</div>
                  <div class="activity-content">
                    <div class="activity-title">Pago procesado</div>
                    <div class="activity-description">Pago de $150 por consulta de Dra. Laura Méndez</div>
                  </div>
                  <span class="activity-time">Ayer, 14:30</span>
                </div>
              </div>
            </div>
            
            <div class="panel-card">
              <div class="panel-header">
                <h3 class="panel-title">Alertas del Sistema</h3>
              </div>
              <div class="alerts-list">
                <div class="alert-item">
                  <div class="alert-icon">⚠️</div>
                  <div class="alert-content">
                    <div class="alert-title">Backup pendiente</div>
                    <div class="alert-description">El backup semanal del sistema está pendiente</div>
                  </div>
                </div>
                <div class="alert-item">
                  <div class="alert-icon">📦</div>
                  <div class="alert-content">
                    <div class="alert-title">Inventario bajo</div>
                    <div class="alert-description">5 productos están por debajo del stock mínimo</div>
                  </div>
                </div>
                <div class="alert-item">
                  <div class="alert-icon">🔄</div>
                  <div class="alert-content">
                    <div class="alert-title">Actualización disponible</div>
                    <div class="alert-description">Nueva versión del sistema disponible (v2.1.3)</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
<!-- Usuarios Module -->
<section id="mod-usuarios" class="module">
    <div class="module-header">
        <h2 class="module-title">Gestión de Usuarios</h2>
        <div class="module-actions">
            <button class="btn-primary" onclick="userManager.showUserForm()">Nuevo Usuario</button>
            <button class="btn-secondary">Exportar</button>
        </div>
    </div>
    
    <!-- Mensajes -->
    <div id="messageContainer"></div>
    
    <!-- Formulario (oculto inicialmente) -->
    <div id="userFormContainer" style="display: none;">
        @include('usuarios.create')
    </div>
    
    <!-- Tabla de usuarios -->
    @include('usuarios.index')
</section>

        <!-- Mascotas Module -->
        <section id="mod-mascotas" class="module">
          <div class="module-header">
            <h2 class="module-title">Gestión de Mascotas</h2>
            <div class="module-actions">
              <button class="btn-primary">Nueva Mascota</button>
              <button class="btn-secondary">Exportar</button>
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
              <tbody>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f0f2f5; display: flex; align-items: center; justify-content: center;">🐕</div>
                      Max
                    </div>
                  </td>
                  <td>Perro</td>
                  <td>Golden Retriever</td>
                  <td>María Rodríguez</td>
                  <td>4 años</td>
                  <td>15 Nov 2023</td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Ver</button>
                    <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;">Editar</button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f0f2f5; display: flex; align-items: center; justify-content: center;">🐈</div>
                      Luna
                    </div>
                  </td>
                  <td>Gato</td>
                  <td>Siames</td>
                  <td>Carlos Pérez</td>
                  <td>2 años</td>
                  <td>12 Nov 2023</td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Ver</button>
                    <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;">Editar</button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f0f2f5; display: flex; align-items: center; justify-content: center;">🐕</div>
                      Toby
                    </div>
                  </td>
                  <td>Perro</td>
                  <td>Border Collie</td>
                  <td>Ana González</td>
                  <td>3 años</td>
                  <td>10 Nov 2023</td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Ver</button>
                    <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;">Editar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Médicos Module -->
        <section id="mod-medicos" class="module">
          <div class="module-header">
            <h2 class="module-title">Gestión de Médicos</h2>
            <div class="module-actions">
              <button class="btn-primary">Nuevo Médico</button>
              <button class="btn-secondary">Exportar</button>
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
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">LM</div>
                      Dra. Laura Méndez
                    </div>
                  </td>
                  <td>Cirugía</td>
                  <td>laura.mendez@vetclinic.com</td>
                  <td>+1 234 567 890</td>
                  <td><span class="status-badge status-active">Activo</span></td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Editar</button>
                    <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;">Horarios</button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">RG</div>
                      Dr. Roberto García
                    </div>
                  </td>
                  <td>Dermatología</td>
                  <td>roberto.garcia@vetclinic.com</td>
                  <td>+1 234 567 891</td>
                  <td><span class="status-badge status-active">Activo</span></td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Editar</button>
                    <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;">Horarios</button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">AS</div>
                      Dra. Ana Sánchez
                    </div>
                  </td>
                  <td>Cardiología</td>
                  <td>ana.sanchez@vetclinic.com</td>
                  <td>+1 234 567 892</td>
                  <td><span class="status-badge status-inactive">Inactivo</span></td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Editar</button>
                    <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;">Horarios</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Finanzas Module -->
        <section id="mod-finanzas" class="module">
          <div class="module-header">
            <h2 class="module-title">Finanzas</h2>
            <div class="module-actions">
              <button class="btn-primary">Nueva Transacción</button>
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
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Gastos del Mes</h3>
                <div class="stat-icon icon-red">💸</div>
              </div>
              <div class="stat-value">$8,420</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Balance Neto</h3>
                <div class="stat-icon icon-blue">📊</div>
              </div>
              <div class="stat-value">$16,160</div>
            </div>
          </div>
          
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Descripción</th>
                  <th>Categoría</th>
                  <th>Monto</th>
                  <th>Tipo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>15 Nov 2023</td>
                  <td>Consulta Dra. Laura Méndez</td>
                  <td>Consultas</td>
                  <td>$150</td>
                  <td><span class="status-badge status-active">Ingreso</span></td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Ver</button>
                  </td>
                </tr>
                <tr>
                  <td>14 Nov 2023</td>
                  <td>Compra de medicamentos</td>
                  <td>Inventario</td>
                  <td>$420</td>
                  <td><span class="status-badge status-inactive">Gasto</span></td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Ver</button>
                  </td>
                </tr>
                <tr>
                  <td>12 Nov 2023</td>
                  <td>Cirugía Dr. Roberto García</td>
                  <td>Procedimientos</td>
                  <td>$850</td>
                  <td><span class="status-badge status-active">Ingreso</span></td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Ver</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Inventario Module -->
        <section id="mod-inventario" class="module">
          <div class="module-header">
            <h2 class="module-title">Gestión de Inventario</h2>
            <div class="module-actions">
              <button class="btn-primary">Nuevo Producto</button>
              <button class="btn-secondary">Realizar Pedido</button>
            </div>
          </div>
          
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Categoría</th>
                  <th>Stock Actual</th>
                  <th>Stock Mínimo</th>
                  <th>Precio Unitario</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Antibiótico X - 500mg</td>
                  <td>Medicamentos</td>
                  <td>15</td>
                  <td>20</td>
                  <td>$25</td>
                  <td><span class="status-badge status-inactive">Bajo Stock</span></td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Editar</button>
                    <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;">Pedir</button>
                  </td>
                </tr>
                <tr>
                  <td>Vacuna Triple Felina</td>
                  <td>Vacunas</td>
                  <td>42</td>
                  <td>15</td>
                  <td>$35</td>
                  <td><span class="status-badge status-active">Disponible</span></td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Editar</button>
                    <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;">Pedir</button>
                  </td>
                </tr>
                <tr>
                  <td>Alimento Premium para Perros</td>
                  <td>Alimentos</td>
                  <td>28</td>
                  <td>10</td>
                  <td>$45</td>
                  <td><span class="status-badge status-active">Disponible</span></td>
                  <td>
                    <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;">Editar</button>
                    <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;">Pedir</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Configuración Module -->
        <section id="mod-configuracion" class="module">
          <div class="module-header">
            <h2 class="module-title">Configuración del Sistema</h2>
          </div>
          
          <div class="form-section">
            <h3 class="form-section-title">Configuración General</h3>
            <div class="form-row">
              <div class="form-group">
                <label>Nombre de la Clínica</label>
                <input type="text" value="VetClinic">
              </div>
              <div class="form-group">
                <label>Teléfono de Contacto</label>
                <input type="text" value="+1 234 567 890">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Email de Contacto</label>
                <input type="email" value="info@vetclinic.com">
              </div>
              <div class="form-group">
                <label>Dirección</label>
                <input type="text" value="Av. Principal #123">
              </div>
            </div>
          </div>
          
          <div class="form-section">
            <h3 class="form-section-title">Configuración de Horarios</h3>
            <div class="form-row">
              <div class="form-group">
                <label>Horario de Apertura</label>
                <input type="time" value="08:00">
              </div>
              <div class="form-group">
                <label>Horario de Cierre</label>
                <input type="time" value="18:00">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Duración de Consultas (minutos)</label>
                <input type="number" value="30">
              </div>
              <div class="form-group">
                <label>Días de Trabajo</label>
                <select multiple style="height: 120px;">
                  <option selected>Lunes</option>
                  <option selected>Martes</option>
                  <option selected>Miércoles</option>
                  <option selected>Jueves</option>
                  <option selected>Viernes</option>
                  <option>Sábado</option>
                  <option>Domingo</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-actions">
            <button class="btn-primary">Guardar Cambios</button>
            <button class="btn-outline">Restablecer</button>
          </div>
        </section>

        <!-- Reportes Module -->
        <section id="mod-reportes" class="module">
          <div class="module-header">
            <h2 class="module-title">Reportes del Sistema</h2>
            <div class="module-actions">
              <button class="btn-primary">Generar Reporte Personalizado</button>
            </div>
          </div>
          
          <div class="reports-container">
            <div class="reports-grid">
              <div class="report-card">
                <h3>Reporte de Consultas</h3>
                <p>Genera un reporte detallado de consultas realizadas en un período específico</p>
                <button class="btn-outline">Generar</button>
              </div>
              
              <div class="report-card">
                <h3>Reporte Financiero</h3>
                <p>Resumen de ingresos, gastos y balances por período seleccionado</p>
                <button class="btn-outline">Generar</button>
              </div>
              
              <div class="report-card">
                <h3>Reporte de Inventario</h3>
                <p>Estado actual del inventario y productos con stock bajo</p>
                <button class="btn-outline">Generar</button>
              </div>
              
              <div class="report-card">
                <h3>Reporte de Usuarios</h3>
                <p>Estadísticas de usuarios registrados y actividad en el sistema</p>
                <button class="btn-outline">Generar</button>
              </div>
              
              <div class="report-card">
                <h3>Reporte de Médicos</h3>
                <p>Rendimiento y actividad de los médicos del sistema</p>
                <button class="btn-outline">Generar</button>
              </div>
              
              <div class="report-card">
                <h3>Reporte de Procedimientos</h3>
                <p>Listado y estadísticas de procedimientos realizados</p>
                <button class="btn-outline">Generar</button>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>

<script src="{{ asset('js/admin.js') }}"></script>

<script src="{{ asset('js/usuarios.js') }}"></script>
</body>
</html>