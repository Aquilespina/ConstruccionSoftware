<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Recepción - VetClinic</title>
  <link rel="stylesheet" href="{{ asset('css/recepcion.css') }}">
  {{-- Stack para estilos específicos por sección (ej: mascotas, propietarios) --}}
  @stack('styles')
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="logo">🩺</div>
        <div class="sidebar-header-text">
          <div class="brand-name">VetClinic</div>
          <div class="brand-subtitle">Recepción</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <!-- Inicio -->
        <a href="{{ route('recepcion.home') }}"
           class="nav-item {{ request()->routeIs('recepcion.home') ? 'active' : '' }}">
          <span class="nav-icon">🏠</span>
          <span class="nav-label">Inicio</span>
        </a>

        <!-- Propietarios (nombre: propietarios.*) -->
        <a href="{{ route('propietarios.index') }}"
           class="nav-item {{ request()->routeIs('propietarios.*') ? 'active' : '' }}">
          <span class="nav-icon">👥</span>
          <span class="nav-label">Propietarios</span>
        </a>

        <!-- Mascotas (nombre: mascotas.*) -->
        <a href="{{ route('mascotas.index') }}"
           class="nav-item {{ request()->routeIs('mascotas.*') ? 'active' : '' }}">
          <span class="nav-icon">🐾</span>
          <span class="nav-label">Mascotas</span>
        </a>

        <!-- Profesionales (nombre: profesionales.*) -->
        <a href="{{ route('profesionales.index') }}"
           class="nav-item {{ request()->routeIs('profesionales.*') ? 'active' : '' }}">
          <span class="nav-icon">🧑‍⚕️</span>
          <span class="nav-label">Profesionales</span>
        </a>

        <!-- Citas (nombre: citas.*) -->
        <a href="{{ route('citas.index') }}"
           class="nav-item {{ request()->routeIs('citas.*') ? 'active' : '' }}">
          <span class="nav-icon">📅</span>
          <span class="nav-label">Citas</span>
        </a>

        <!-- Expedientes (nombre: recepcion.expedientes) -->
        <a href="{{ route('recepcion.expedientes') }}"
           class="nav-item {{ request()->routeIs('recepcion.expedientes') ? 'active' : '' }}">
          <span class="nav-icon">📄</span>
          <span class="nav-label">Expedientes</span>
        </a>

        <!-- Recetas (nombre: recepcion.recetas) -->
        <a href="{{ route('recepcion.recetas') }}"
           class="nav-item {{ request()->routeIs('recepcion.recetas') ? 'active' : '' }}">
          <span class="nav-icon">💊</span>
          <span class="nav-label">Recetas</span>
        </a>

        <!-- Honorarios (nombre: honorarios.honorarios.index) -->
        <a href="{{ route('honorarios.honorarios.index') }}"
           class="nav-item {{ request()->routeIs('honorarios.honorarios.*') ? 'active' : '' }}">
          <span class="nav-icon">💵</span>
          <span class="nav-label">Honorarios</span>
        </a>

        <!-- Hospitalizaciones -->
        <a href="{{ route('hospitalizaciones.index') }}"
           class="nav-item {{ request()->routeIs('hospitalizaciones.*') ? 'active' : '' }}">
          <span class="nav-icon">🏥</span>
          <span class="nav-label">Hospitalizaciones</span>
        </a>
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
          <h1 class="page-title" id="pageTitle">
            @hasSection('page-title')
              @yield('page-title')
            @else
              Inicio
            @endif
          </h1>
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
              {{-- Use optional() to avoid errors when no authenticated user is available --}}
              {{ strtoupper(substr(optional(Auth::user())->nombre_usuario ?? 'US', 0, 2)) }}
            </div>
            <div class="user-info">
              <div class="user-name">{{ optional(Auth::user())->nombre_usuario ?? 'Invitado' }}</div>
              <div class="user-role">{{ optional(Auth::user())->tipo_permiso ? ucfirst(optional(Auth::user())->tipo_permiso) : 'Usuario' }}</div>
            </div>
          </div>
        </div>
      </header>

      <div class="content-area">
        <!-- Contenido dinámico -->
        @yield('content')
      </div>
    </main>
  </div>
<script src="{{ asset('js/recepcion.js') }}"></script>
{{-- Stack para scripts específicos por sección (ej: expedientes, recetas) --}}
@stack('scripts')
</body>
</html>