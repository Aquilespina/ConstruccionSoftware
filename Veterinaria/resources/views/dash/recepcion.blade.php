<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Recepción - VetClinic</title>
  <link rel="stylesheet" href="css/recepcion.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="logo">🩺</div>
        <div class="sidebar-header-text">
          <div class="brand-name">VetClinic</div>
          <div class="brand-subtitle">Sistema de Gestión</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <button class="nav-item active" data-target="home">
          <span class="nav-icon">🏠</span>
          <span class="nav-label">Inicio</span>
        </button>
        <button class="nav-item" data-target="usuarios">
          <span class="nav-icon">👤</span>
          <span class="nav-label">Usuarios</span>
        </button>
        <button class="nav-item" data-target="propietarios">
          <span class="nav-icon">👥</span>
          <span class="nav-label">Propietarios</span>
        </button>
        <button class="nav-item" data-target="mascotas">
          <span class="nav-icon">🐾</span>
          <span class="nav-label">Mascotas</span>
        </button>
        <button class="nav-item" data-target="profesionales">
          <span class="nav-icon">🧑‍⚕️</span>
          <span class="nav-label">Profesionales</span>
        </button>
        <button class="nav-item" data-target="citas">
          <span class="nav-icon">📅</span>
          <span class="nav-label">Citas</span>
        </button>
        <button class="nav-item" data-target="expedientes">
          <span class="nav-icon">📄</span>
          <span class="nav-label">Expedientes</span>
        </button>
        <button class="nav-item" data-target="recetas">
          <span class="nav-icon">💊</span>
          <span class="nav-label">Recetas</span>
        </button>
        <button class="nav-item" data-target="honorarios">
          <span class="nav-icon">💵</span>
          <span class="nav-label">Honorarios</span>
        </button>
        <button class="nav-item" data-target="hospitalizaciones">
          <span class="nav-icon">🏥</span>
          <span class="nav-label">Hospitalizaciones</span>
        </button>
        <button class="nav-item" data-target="servicios">
          <span class="nav-icon">✅</span>
          <span class="nav-label">Catálogo Servicios</span>
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
        {{ strtoupper(substr(Auth::user()->nombre_usuario, 0, 2)) }}
    </div>
    <div class="user-info">
        <div class="user-name">{{ Auth::user()->nombre_usuario }}</div>
        <div class="user-role">{{ ucfirst(Auth::user()->tipo_permiso) }}</div>
    </div>
</div>
      </header>

      <div class="content-area">
        <!-- Home Module -->
        <section id="mod-home" class="module active">
          <div class="welcome-section">
            <h1 class="welcome-title">Bienvenido al Sistema de Gestión Veterinaria</h1>
            <p class="welcome-subtitle">Panel de control y resumen de actividades</p>
          </div>

          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Total Propietarios</h3>
                <div class="stat-icon icon-blue">👥</div>
              </div>
              <div class="stat-value">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Total Mascotas</h3>
                <div class="stat-icon icon-green">🐾</div>
              </div>
              <div class="stat-value">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Citas Hoy</h3>
                <div class="stat-icon icon-orange">📅</div>
              </div>
              <div class="stat-value">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-header">
                <h3 class="stat-title">Hospitalizaciones Activas</h3>
                <div class="stat-icon icon-red">🏥</div>
              </div>
              <div class="stat-value">0</div>
            </div>
          </div>

          <div class="panels-grid">
            <div class="panel-card">
              <div class="panel-header">
                <h3 class="panel-title">Próximas Citas</h3>
              </div>
              <div class="empty-state">
                <p>No hay citas programadas</p>
              </div>
            </div>
            <div class="panel-card">
              <div class="panel-header">
                <h3 class="panel-title">Hospitalizaciones Activas</h3>
              </div>
              <div class="empty-state">
                <p>No hay hospitalizaciones activas</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Other Modules -->
        <section id="mod-usuarios" class="module">
          <div class="module-content">
            <h2 class="module-title">Gestión de Usuarios</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>

        <section id="mod-propietarios" class="module">
          <div class="module-content">
            <h2 class="module-title">Gestión de Propietarios</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>

        <section id="mod-mascotas" class="module">
          <div class="module-content">
            <h2 class="module-title">Gestión de Mascotas</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>

        <section id="mod-profesionales" class="module">
          <div class="module-content">
            <h2 class="module-title">Gestión de Profesionales</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>

        <section id="mod-citas" class="module">
          <div class="module-content">
            <h2 class="module-title">Gestión de Citas</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>

        <section id="mod-expedientes" class="module">
          <div class="module-content">
            <h2 class="module-title">Gestión de Expedientes</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>

        <section id="mod-recetas" class="module">
          <div class="module-content">
            <h2 class="module-title">Gestión de Recetas</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>

        <section id="mod-honorarios" class="module">
          <div class="module-content">
            <h2 class="module-title">Gestión de Honorarios</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>

        <section id="mod-hospitalizaciones" class="module">
          <div class="module-content">
            <h2 class="module-title">Gestión de Hospitalizaciones</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>

        <section id="mod-servicios" class="module">
          <div class="module-content">
            <h2 class="module-title">Catálogo de Servicios</h2>
            <p class="module-message">Módulo en desarrollo - Próximamente</p>
          </div>
        </section>
      </div>
    </main>
  </div>
<script src="{{ asset('js/recepcion.js') }}"></script>
</body>
</html>