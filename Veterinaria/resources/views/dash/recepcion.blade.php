<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Recepción - VetClinic</title>
  <style>
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

    .dashboard-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styles */
    .sidebar {
      width: 260px;
      background: white;
      border-right: 1px solid var(--gray-200);
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
      box-shadow: var(--shadow);
      z-index: 10;
    }

    .sidebar.collapsed {
      width: 70px;
    }

    .sidebar-header {
      padding: 1.25rem;
      border-bottom: 1px solid var(--gray-200);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .logo {
      width: 40px;
      height: 40px;
      background: var(--primary);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.25rem;
      flex-shrink: 0;
    }

    .sidebar-header-text {
      overflow: hidden;
      transition: opacity 0.3s ease;
    }

    .sidebar.collapsed .sidebar-header-text {
      opacity: 0;
      width: 0;
    }

    .brand-name {
      font-size: 1rem;
      font-weight: 600;
      line-height: 1.2;
    }

    .brand-subtitle {
      font-size: 0.75rem;
      color: var(--gray-500);
      margin-top: 0.125rem;
    }

    .sidebar-nav {
      flex: 1;
      padding: 0.75rem;
      overflow-y: auto;
    }

    .nav-item {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.625rem 0.75rem;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
      color: var(--gray-700);
      text-decoration: none;
      margin-bottom: 0.25rem;
      border: none;
      background: transparent;
      cursor: pointer;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .nav-item:hover {
      background-color: var(--gray-100);
    }

    .nav-item.active {
      background-color: var(--primary);
      color: white;
      box-shadow: var(--shadow);
    }

    .nav-icon {
      width: 20px;
      text-align: center;
      font-size: 1.125rem;
      flex-shrink: 0;
    }

    .nav-label {
      white-space: nowrap;
      overflow: hidden;
      transition: opacity 0.3s ease;
    }

    .sidebar.collapsed .nav-label {
      opacity: 0;
      width: 0;
    }

    .sidebar-footer {
      padding: 0.75rem;
      border-top: 1px solid var(--gray-200);
    }

    .logout-btn {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.625rem 0.75rem;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
      color: var(--gray-700);
      text-decoration: none;
      border: 1px solid var(--gray-200);
      background: white;
      cursor: pointer;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .logout-btn:hover {
      background-color: var(--gray-50);
    }

    .sidebar.collapsed .logout-btn span:last-child {
      display: none;
    }

    /* Main Content Styles */
    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .top-header {
      background: white;
      border-bottom: 1px solid var(--gray-200);
      padding: 1rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: var(--shadow);
    }

    .header-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .toggle-sidebar {
      background: none;
      border: none;
      width: 40px;
      height: 40px;
      border-radius: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: var(--gray-600);
      transition: all 0.2s ease;
    }

    .toggle-sidebar:hover {
      background-color: var(--gray-100);
      color: var(--gray-800);
    }

    .page-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--gray-800);
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .search-container {
      position: relative;
    }

    .search-input {
      padding: 0.625rem 1rem 0.625rem 2.5rem;
      border: 1px solid var(--gray-300);
      border-radius: 0.5rem;
      background-color: white;
      font-size: 0.875rem;
      width: 240px;
      transition: all 0.2s ease;
    }

    .search-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .search-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray-400);
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.5rem;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .user-profile:hover {
      background-color: var(--gray-100);
    }

    .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background-color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 0.875rem;
    }

    .user-info {
      display: flex;
      flex-direction: column;
    }

    .user-name {
      font-size: 0.875rem;
      font-weight: 500;
    }

    .user-role {
      font-size: 0.75rem;
      color: var(--gray-500);
    }

    .content-area {
      flex: 1;
      padding: 1.5rem;
      overflow-y: auto;
    }

    .module {
      display: none;
    }

    .module.active {
      display: block;
    }

    /* Dashboard Home Styles */
    .welcome-section {
      margin-bottom: 2rem;
    }

    .welcome-title {
      font-size: 1.875rem;
      font-weight: 700;
      color: var(--gray-900);
      margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
      font-size: 1rem;
      color: var(--gray-600);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: white;
      border-radius: 0.75rem;
      padding: 1.5rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--gray-200);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .stat-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }

    .stat-title {
      font-size: 0.875rem;
      font-weight: 500;
      text-align: center;
      color: var(--gray-700);
    }

    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.25rem;
    }

    .icon-blue {
      background-color: var(--secondary);
    }

    .icon-green {
      background-color: var(--success);
    }

    .icon-orange {
      background-color: var(--warning);
    }

    .icon-red {
      background-color: var(--danger);
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      text-align: center;
      color: var(--gray-900);
    }

    .panels-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
      gap: 1.5rem;
    }

    .panel-card {
      background: white;
      border-radius: 0.75rem;
      padding: 1.5rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--gray-200);
    }

    .panel-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }

    .panel-title {
      font-size: 1rem;
      font-weight: 600;
      text-align: center;
      color: var(--gray-800);
    }

    .empty-state {
      text-align: center;
      padding: 1.5rem 0;
      color: var(--gray-500);
    }

    /* Module Content Styles */
    .module-content {
      background: white;
      border-radius: 0.75rem;
      padding: 2rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--gray-200);
      max-width: 800px;
      margin: 0 auto;
      text-align: center;
    }

    .module-title {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--gray-800);
    }

    .module-message {
      color: var(--gray-600);
      font-size: 1rem;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      .sidebar {
        position: fixed;
        height: 100%;
        transform: translateX(-100%);
      }
      
      .sidebar.open {
        transform: translateX(0);
      }
      
      .sidebar.collapsed {
        transform: translateX(-100%);
      }
      
      .overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 5;
        display: none;
      }
      
      .overlay.active {
        display: block;
      }
    }

    @media (max-width: 768px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .panels-grid {
        grid-template-columns: 1fr;
      }
      
      .search-input {
        width: 180px;
      }
      
      .user-info {
        display: none;
      }
    }

    @media (max-width: 640px) {
      .top-header {
        padding: 1rem;
      }
      
      .content-area {
        padding: 1rem;
      }
      
      .search-container {
        display: none;
      }
    }
  </style>
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