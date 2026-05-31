<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Médico - VetClinic</title>
  <link rel="stylesheet" href="css/medico.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="logo">🩺</div>
        <div class="sidebar-header-text">
          <div class="brand-name">VetClinic</div>
          <div class="brand-subtitle">Panel Médico</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <button class="nav-item active" data-target="home">
          <span class="nav-icon">🏠</span>
          <span class="nav-label">Inicio</span>
        </button>
        <button class="nav-item" data-target="agenda">
          <span class="nav-icon">📅</span>
          <span class="nav-label">Mi Agenda</span>
        </button>
        <button class="nav-item" data-target="consultas">
          <span class="nav-icon">🩺</span>
          <span class="nav-label">Consultas</span>
        </button>
        <button class="nav-item" data-target="expedientes">
          <span class="nav-icon">📄</span>
          <span class="nav-label">Expedientes</span>
        </button>
        <button class="nav-item" data-target="recetas">
          <span class="nav-icon">💊</span>
          <span class="nav-label">Recetas</span>
        </button>
        <button class="nav-item" data-target="hospitalizaciones">
          <span class="nav-icon">🏥</span>
          <span class="nav-label">Hospitalizaciones</span>
        </button>
        <button class="nav-item" data-target="procedimientos">
          <span class="nav-icon">🔬</span>
          <span class="nav-label">Procedimientos</span>
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
            <input type="text" class="search-input" placeholder="Buscar paciente...">
          </div>

          <div class="user-profile">
            <div class="user-avatar">
              {{ strtoupper(substr(Auth::user()->nombre_usuario, 0, 2)) }}
            </div>
            <div class="user-info">
              <div class="user-name">{{ Auth::user()->nombre_usuario }}</div>
              <div class="user-role">Médico Veterinario</div>
            </div>
          </div>
        </div>
      </header>

      <div class="content-area">
        <!-- Home Module -->
        <section id="mod-home" class="module active">
          <div class="welcome-section">
            <h1 class="welcome-title">Bienvenido, Dr. {{ Auth::user()->nombre_usuario }}</h1>
            <p class="welcome-subtitle">Panel de control médico - Resumen de actividades del día</p>
          </div>

          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Consultas Hoy</h3>
                <div class="stat-icon icon-blue">🩺</div>
              </div>
              <div class="stat-value">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Pacientes Hospitalizados</h3>
                <div class="stat-icon icon-red">🏥</div>
              </div>
              <div class="stat-value">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Recetas Pendientes</h3>
                <div class="stat-icon icon-orange">💊</div>
              </div>
              <div class="stat-value">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Procedimientos Programados</h3>
                <div class="stat-icon icon-green">🔬</div>
              </div>
              <div class="stat-value">0</div>
            </div>
          </div>

          <div class="panels-grid">
            <div class="panel-card">
              <div class="panel-header">
                <h3 class="panel-title">Próximas Consultas</h3>
                <button class="btn-primary">Ver Agenda Completa</button>
              </div>
              <div class="appointments-list">
                <div class="empty-state">
                  <p>No hay consultas programadas para hoy</p>
                </div>
              </div>
            </div>
            
            <div class="panel-card">
              <div class="panel-header">
                <h3 class="panel-title">Pacientes Hospitalizados</h3>
                <button class="btn-secondary">Ver Todos</button>
              </div>
              <div class="hospitalizations-list">
                <div class="empty-state">
                  <p>No hay pacientes hospitalizados</p>
                </div>
              </div>
            </div>
            
            <div class="panel-card">
              <div class="panel-header">
                <h3 class="panel-title">Alertas y Recordatorios</h3>
              </div>
              <div class="alerts-list">
                <div class="alert-item">
                  <div class="alert-icon">⚠️</div>
                  <div class="alert-content">
                    <div class="alert-title">Vacunación pendiente</div>
                    <div class="alert-description">Max (Golden Retriever) necesita refuerzo de vacuna</div>
                  </div>
                </div>
                <div class="alert-item">
                  <div class="alert-icon">📋</div>
                  <div class="alert-content">
                    <div class="alert-title">Resultados de laboratorio</div>
                    <div class="alert-description">Disponibles para Luna (Gato Siames)</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Agenda Module -->
        <section id="mod-agenda" class="module">
          <div class="module-header">
            <h2 class="module-title">Mi Agenda</h2>
            <div class="module-actions">
              <button class="btn-primary">Nueva Cita</button>
              <div class="date-selector">
                <button class="date-nav">‹</button>
                <span class="current-date">Hoy, 15 Nov 2023</span>
                <button class="date-nav">›</button>
              </div>
            </div>
          </div>
          
          <div class="agenda-container">
            <div class="agenda-time-slots">
              <!-- Time slots would be generated dynamically -->
            </div>
            <div class="agenda-appointments">
              <div class="empty-state">
                <p>No hay citas programadas para hoy</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Consultas Module -->
        <section id="mod-consultas" class="module">
          <div class="module-header">
            <h2 class="module-title">Consultas Médicas</h2>
            <div class="module-actions">
              <button class="btn-primary">Nueva Consulta</button>
              <button class="btn-secondary">Historial</button>
            </div>
          </div>
          
          <div class="consultation-form">
            <div class="form-section">
              <h3 class="form-section-title">Datos del Paciente</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Mascota</label>
                  <select>
                    <option>Seleccionar mascota...</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Propietario</label>
                  <input type="text" readonly>
                </div>
              </div>
            </div>
            
            <div class="form-section">
              <h3 class="form-section-title">Historia Clínica</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Motivo de Consulta</label>
                  <textarea rows="3"></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Examen Físico</label>
                  <textarea rows="3"></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Diagnóstico</label>
                  <textarea rows="3"></textarea>
                </div>
              </div>
            </div>
            
            <div class="form-actions">
              <button class="btn-primary">Guardar Consulta</button>
              <button class="btn-secondary">Generar Receta</button>
              <button class="btn-outline">Hospitalizar</button>
            </div>
          </div>
        </section>

        <!-- Expedientes Module -->
        <section id="mod-expedientes" class="module">
          <div class="module-header">
            <h2 class="module-title">Expedientes Médicos</h2>
            <div class="module-actions">
              <button class="btn-primary">Buscar Expediente</button>
            </div>
          </div>
          
          <div class="records-container">
            <div class="records-sidebar">
              <div class="search-box">
                <input type="text" placeholder="Buscar por mascota o propietario...">
              </div>
              <div class="records-list">
                <!-- List of patient records would appear here -->
                <div class="empty-state">
                  <p>No se encontraron expedientes</p>
                </div>
              </div>
            </div>
            
            <div class="record-details">
              <div class="empty-state">
                <p>Seleccione un expediente para ver los detalles</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Recetas Module -->
        <section id="mod-recetas" class="module">
          <div class="module-header">
            <h2 class="module-title">Gestión de Recetas</h2>
            <div class="module-actions">
              <button class="btn-primary">Nueva Receta</button>
            </div>
          </div>
          
          <div class="prescriptions-list">
            <div class="prescription-card">
              <div class="prescription-header">
                <div class="prescription-info">
                  <h3>Receta #R-001</h3>
                  <p>Para: Max (Golden Retriever)</p>
                  <p>Propietario: Carlos Rodríguez</p>
                </div>
                <div class="prescription-status">
                  <span class="status-badge status-active">Activa</span>
                  <span class="prescription-date">15 Nov 2023</span>
                </div>
              </div>
              <div class="prescription-medications">
                <div class="medication-item">
                  <span class="medication-name">Antibiótico X - 500mg</span>
                  <span class="medication-dosage">1 tableta cada 12 horas por 7 días</span>
                </div>
              </div>
              <div class="prescription-actions">
                <button class="btn-outline">Editar</button>
                <button class="btn-secondary">Imprimir</button>
              </div>
            </div>
            
            <div class="empty-state">
              <p>No hay recetas registradas</p>
            </div>
          </div>
        </section>

        <!-- Hospitalizaciones Module -->
        <section id="mod-hospitalizaciones" class="module">
          <div class="module-header">
            <h2 class="module-title">Hospitalizaciones</h2>
            <div class="module-actions">
              <button class="btn-primary">Nueva Hospitalización</button>
              <button class="btn-secondary">Historial</button>
            </div>
          </div>
          
          <div class="hospitalizations-container">
            <div class="hospitalization-tabs">
              <button class="tab-button active">Activas</button>
              <button class="tab-button">Dadas de Alta</button>
            </div>
            
            <div class="hospitalizations-list">
              <div class="hospitalization-card">
                <div class="hospitalization-header">
                  <h3>Luna (Gato Siames)</h3>
                  <span class="hospitalization-status status-serious">Grave</span>
                </div>
                <div class="hospitalization-details">
                  <p><strong>Propietario:</strong> María González</p>
                  <p><strong>Diagnóstico:</strong> Insuficiencia renal aguda</p>
                  <p><strong>Fecha de ingreso:</strong> 14 Nov 2023</p>
                </div>
                <div class="hospitalization-actions">
                  <button class="btn-outline">Ver Evolución</button>
                  <button class="btn-secondary">Dar de Alta</button>
                </div>
              </div>
              
              <div class="empty-state">
                <p>No hay hospitalizaciones activas</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Procedimientos Module -->
        <section id="mod-procedimientos" class="module">
          <div class="module-header">
            <h2 class="module-title">Procedimientos Médicos</h2>
            <div class="module-actions">
              <button class="btn-primary">Nuevo Procedimiento</button>
            </div>
          </div>
          
          <div class="procedures-container">
            <div class="procedures-tabs">
              <button class="tab-button active">Programados</button>
              <button class="tab-button">Realizados</button>
            </div>
            
            <div class="procedures-list">
              <div class="procedure-card">
                <div class="procedure-header">
                  <h3>Cirugía - Esterilización</h3>
                  <span class="procedure-date">18 Nov 2023 - 10:00 AM</span>
                </div>
                <div class="procedure-details">
                  <p><strong>Paciente:</strong> Toby (Border Collie)</p>
                  <p><strong>Propietario:</strong> Roberto Sánchez</p>
                  <p><strong>Tipo:</strong> Cirugía programada</p>
                </div>
                <div class="procedure-actions">
                  <button class="btn-outline">Reagendar</button>
                  <button class="btn-secondary">Ver Detalles</button>
                </div>
              </div>
              
              <div class="empty-state">
                <p>No hay procedimientos programados</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Reportes Module -->
        <section id="mod-reportes" class="module">
          <div class="module-header">
            <h2 class="module-title">Reportes Médicos</h2>
            <div class="module-actions">
              <button class="btn-primary">Generar Reporte</button>
            </div>
          </div>
          
          <div class="reports-container">
            <div class="reports-grid">
              <div class="report-card">
                <h3>Consultas por Período</h3>
                <p>Genera un reporte de consultas realizadas en un rango de fechas</p>
                <button class="btn-outline">Generar</button>
              </div>
              
              <div class="report-card">
                <h3>Procedimientos Realizados</h3>
                <p>Listado de procedimientos realizados por tipo y fecha</p>
                <button class="btn-outline">Generar</button>
              </div>
              
              <div class="report-card">
                <h3>Diagnósticos Más Frecuentes</h3>
                <p>Estadísticas de diagnósticos más comunes en consultas</p>
                <button class="btn-outline">Generar</button>
              </div>
              
              <div class="report-card">
                <h3>Medicamentos Recetados</h3>
                <p>Reporte de medicamentos más utilizados en recetas</p>
                <button class="btn-outline">Generar</button>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
<script src="{{ asset('js/medico.js') }}"></script>
</body>
</html>