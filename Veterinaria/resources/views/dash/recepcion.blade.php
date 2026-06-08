<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Recepción - VetClinic</title>
  <link rel="stylesheet" href="{{ asset('css/recepcion.css') }}">
  @stack('styles')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    /* ── Buscador global ───────────────────────────────────────────── */
    .search-container { position: relative; }

    #gs-panel {
      position: absolute;
      top: calc(100% + 6px);
      right: 0;
      min-width: 380px;
      max-width: 460px;
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      box-shadow: 0 8px 28px rgba(0,0,0,0.13);
      z-index: 9999;
      max-height: 440px;
      overflow-y: auto;
    }

    .gs-group { border-bottom: 1px solid #f1f5f9; }
    .gs-group:last-of-type { border-bottom: none; }

    .gs-group-header {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px 4px;
      font-size: 10px;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .gs-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .gs-badge {
      margin-left: auto;
      background: #f1f5f9;
      color: #64748b;
      padding: 1px 6px;
      border-radius: 999px;
      font-size: 10px;
    }

    .gs-item {
      display: block;
      padding: 7px 14px;
      text-decoration: none;
      transition: background 0.12s;
      cursor: pointer;
    }
    .gs-item:hover,
    .gs-item.gs-active { background: #f0f9ff; }

    .gs-item-titulo {
      font-size: 13px;
      font-weight: 600;
      color: #1e293b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .gs-item-subtitulo {
      font-size: 11px;
      color: #94a3b8;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .gs-empty, .gs-loading {
      padding: 22px 14px;
      text-align: center;
      font-size: 13px;
      color: #94a3b8;
    }

    .gs-spinner {
      display: inline-block;
      width: 13px;
      height: 13px;
      border: 2px solid #e2e8f0;
      border-top-color: #3b82f6;
      border-radius: 50%;
      animation: gs-spin 0.6s linear infinite;
      vertical-align: middle;
      margin-right: 6px;
    }
    @keyframes gs-spin { to { transform: rotate(360deg); } }

    .gs-footer {
      padding: 5px 14px;
      font-size: 10px;
      color: #cbd5e1;
      text-align: right;
      border-top: 1px solid #f1f5f9;
    }

    .gs-kbd {
      display: inline-block;
      padding: 1px 4px;
      border: 1px solid #e2e8f0;
      border-radius: 3px;
      font-size: 9px;
      color: #94a3b8;
      margin-left: 3px;
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

        <!-- Historial Médico (nombre: recepcion.expedientes) -->
        <a href="{{ route('recepcion.expedientes') }}"
           class="nav-item {{ request()->routeIs('recepcion.expedientes') ? 'active' : '' }}">
          <span class="nav-icon">📋</span>
          <span class="nav-label">Historial Médico</span>
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
            <input type="text" id="global-search" class="search-input"
                   placeholder="Buscar propietarios, mascotas, citas..."
                   autocomplete="off" spellcheck="false">
            <div id="gs-panel" style="display:none;" role="listbox" aria-label="Resultados"></div>
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
@stack('scripts')
<script>
(function () {
    const input = document.getElementById('global-search');
    const panel = document.getElementById('gs-panel');
    const CSRF  = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const BASE  = '{{ route("recepcion.buscar") }}';

    if (!input || !panel) return;

    let timer       = null;
    let activeIndex = -1;
    let allItems    = [];

    // ── Eventos del input ─────────────────────────────────────────────
    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { close(); return; }
        showLoading();
        timer = setTimeout(() => fetch(BASE + '?q=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }
        }).then(r => r.json()).then(d => render(d, q)).catch(showError), 300);
    });

    input.addEventListener('keydown', function (e) {
        if (panel.style.display === 'none') return;
        if (e.key === 'ArrowDown')  { e.preventDefault(); setActive(activeIndex + 1); }
        else if (e.key === 'ArrowUp')   { e.preventDefault(); setActive(activeIndex - 1); }
        else if (e.key === 'Enter')     { e.preventDefault(); if (allItems[activeIndex]) window.location.href = allItems[activeIndex].href; }
        else if (e.key === 'Escape')    { close(); input.blur(); }
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !panel.contains(e.target)) close();
    });

    // ── Render ────────────────────────────────────────────────────────
    function render(data, q) {
        activeIndex = -1;
        allItems    = [];

        if (!data.grupos || data.grupos.length === 0) {
            panel.innerHTML = `<div class="gs-empty">Sin resultados para <strong>${esc(q)}</strong></div>`;
            open(); return;
        }

        let html = '';
        data.grupos.forEach(grupo => {
            html += `<div class="gs-group">
                <div class="gs-group-header">
                    <span class="gs-dot" style="background:${grupo.color}"></span>
                    ${esc(grupo.modulo)}
                    <span class="gs-badge">${grupo.items.length}</span>
                </div>`;
            grupo.items.forEach(item => {
                html += `<a class="gs-item" href="${item.url}" role="option">
                    <div class="gs-item-titulo">${esc(item.titulo)}</div>
                    <div class="gs-item-subtitulo">${esc(item.subtitulo)}</div>
                </a>`;
            });
            html += '</div>';
        });

        html += `<div class="gs-footer">
            ${data.total} resultado${data.total !== 1 ? 's' : ''}
            &nbsp;<span class="gs-kbd">↑↓</span> navegar
            &nbsp;<span class="gs-kbd">↵</span> abrir
            &nbsp;<span class="gs-kbd">Esc</span> cerrar
        </div>`;

        panel.innerHTML = html;
        allItems = Array.from(panel.querySelectorAll('.gs-item'));
        open();
    }

    function setActive(idx) {
        allItems.forEach(el => el.classList.remove('gs-active'));
        if (!allItems.length) return;
        activeIndex = ((idx % allItems.length) + allItems.length) % allItems.length;
        allItems[activeIndex].classList.add('gs-active');
        allItems[activeIndex].scrollIntoView({ block: 'nearest' });
    }

    function showLoading() {
        panel.innerHTML = '<div class="gs-loading"><span class="gs-spinner"></span>Buscando...</div>';
        open();
    }

    function showError() {
        panel.innerHTML = '<div class="gs-empty">Error al buscar. Intente de nuevo.</div>';
        open();
    }

    function open()  { panel.style.display = 'block'; }
    function close() { panel.style.display = 'none'; activeIndex = -1; }
    function esc(s)  {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(String(s)));
        return d.innerHTML;
    }
})();
</script>
</body>
</html>