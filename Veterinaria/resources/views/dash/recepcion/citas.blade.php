@extends('dash.recepcion')
@section('page-title', 'Citas')
@push('styles')
<style>
/* Estilos generales */
:root {
  --primary: #059669;
  --primary-light: #10b981;
  --primary-dark: #047857;
  --secondary: #0d9488;
  --accent: #06b6d4;
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
  --border-radius: 12px;
  --border-radius-sm: 8px;
  --border-radius-lg: 16px;
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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

/* Botones principales mejorados con gradientes verdes */
.btn-primary {
  background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
  color: white;
  border: none;
  border-radius: var(--border-radius);
  padding: 12px 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: var(--shadow-md);
  position: relative;
  overflow: hidden;
}

.btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.5s;
}

.btn-primary:hover::before {
  left: 100%;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-xl);
  background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
}

.btn-secondary {
  background: white;
  color: var(--gray-700);
  border: 2px solid var(--gray-300);
  border-radius: var(--border-radius);
  padding: 12px 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: var(--shadow);
}

.btn-secondary:hover {
  background: var(--gray-50);
  border-color: var(--primary);
  color: var(--primary);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

/* Filtros mejorados con diseño moderno verde */
.filters-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding: 20px;
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-md);
  border: 1px solid var(--gray-200);
  position: relative;
  overflow: hidden;
}

.filters-bar::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
}

.search-filter {
  flex: 1;
  max-width: 400px;
  position: relative;
}

.search-input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border: 2px solid var(--gray-300);
  border-radius: var(--border-radius);
  font-size: 14px;
  transition: all 0.3s ease;
  background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'%3E%3C/line%3E%3C/svg%3E") no-repeat 16px center;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'%3E%3C/line%3E%3C/svg%3E");
}

.filter-actions {
  display: flex;
  gap: 12px;
}

.filter-select {
  padding: 12px 16px;
  border: 2px solid var(--gray-300);
  border-radius: var(--border-radius);
  font-size: 14px;
  transition: all 0.3s ease;
  background: white;
  cursor: pointer;
  min-width: 160px;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 40px;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
}

/* Tabs mejoradas con diseño moderno verde */
.citas-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  padding: 8px;
  background: white;
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-md);
  border: 1px solid var(--gray-200);
  position: relative;
}

.citas-tabs::before {
  content: '';
  position: absolute;
  bottom: 8px;
  left: 8px;
  height: 2px;
  width: calc(25% - 8px);
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  border-radius: 2px;
  transition: all 0.3s ease;
}

.citas-tabs[data-active-tab="proximas"]::before {
  transform: translateX(100%);
}

.citas-tabs[data-active-tab="pasadas"]::before {
  transform: translateX(200%);
}

.citas-tabs[data-active-tab="todas"]::before {
  transform: translateX(300%);
}

.tab-button {
  padding: 12px 20px;
  border-radius: var(--border-radius);
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: var(--gray-600);
  transition: all 0.3s ease;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  justify-content: center;
  position: relative;
  z-index: 1;
}

.tab-button:hover {
  color: var(--primary);
  background: rgba(5, 150, 105, 0.1);
}

.tab-button.active {
  color: var(--primary);
  font-weight: 600;
}

/* Contenido de citas mejorado */
.citas-content {
  background: white;
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-md);
  border: 1px solid var(--gray-200);
  overflow: hidden;
  margin-bottom: 24px;
}

.citas-list-container {
  padding: 0;
}

.cita-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  border-bottom: 1px solid var(--gray-200);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.cita-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: transparent;
  transition: all 0.3s ease;
}

.cita-item:hover::before {
  background: linear-gradient(to bottom, var(--primary), var(--secondary));
}

.cita-item:hover {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  transform: translateX(4px);
}

.cita-item:last-child {
  border-bottom: none;
}

.cita-time {
  width: 80px;
  flex-shrink: 0;
  text-align: center;
  padding: 12px 8px;
  background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
  color: var(--primary-dark);
  border-radius: var(--border-radius);
  font-weight: 700;
  font-size: 14px;
  box-shadow: var(--shadow);
  border: 1px solid rgba(5, 150, 105, 0.1);
}

.cita-info {
  flex: 1;
  min-width: 0;
}

.cita-paciente {
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 4px;
  font-size: 16px;
}

.cita-propietario {
  color: var(--gray-600);
  font-size: 14px;
  margin-bottom: 4px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.cita-motivo {
  color: var(--gray-700);
  font-size: 14px;
  margin-bottom: 4px;
  font-weight: 500;
}

.cita-medico {
  color: var(--primary);
  font-size: 13px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
}

.cita-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

/* Botones de acción mejorados */
.btn-action {
  padding: 8px 16px;
  border-radius: var(--border-radius-sm);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid;
  display: flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-edit {
  background: rgba(5, 150, 105, 0.1);
  color: var(--primary);
  border-color: var(--primary);
}

.btn-edit:hover {
  background: var(--primary);
  color: white;
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.btn-complete {
  background: rgba(16, 185, 129, 0.1);
  color: var(--success);
  border-color: var(--success);
}

.btn-complete:hover {
  background: var(--success);
  color: white;
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.btn-cancel {
  background: rgba(239, 68, 68, 0.1);
  color: var(--danger);
  border-color: var(--danger);
}

.btn-cancel:hover {
  background: var(--danger);
  color: white;
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

/* Estados de citas mejorados */
.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-pending {
  background: linear-gradient(135deg, #fff7ed, #fed7aa);
  color: #b45309;
  border: 1px solid rgba(245, 158, 11, 0.2);
}

.status-confirmed {
  background: linear-gradient(135deg, #ecfdf5, #a7f3d0);
  color: #065f46;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.status-completed {
  background: linear-gradient(135deg, #eff6ff, #bfdbfe);
  color: #1e40af;
  border: 1px solid rgba(59, 130, 246, 0.2);
}

.status-cancelled {
  background: linear-gradient(135deg, #fef2f2, #fecaca);
  color: #991b1b;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

/* Estado vacío mejorado */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--gray-500);
}

.empty-state svg {
  width: 64px;
  height: 64px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.empty-state p {
  margin: 0;
  font-size: 16px;
  font-weight: 500;
}

/* Modal mejorado */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-content {
  background: white;
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-xl);
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid var(--gray-200);
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.modal-header h3 {
  font-size: 24px;
  font-weight: 700;
  color: var(--gray-900);
  margin: 0;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.modal-close {
  background: none;
  border: none;
  font-size: 28px;
  cursor: pointer;
  color: var(--gray-500);
  transition: all 0.3s ease;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  color: var(--danger);
  background: rgba(239, 68, 68, 0.1);
}

.modal-body {
  padding: 24px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid var(--gray-200);
  background: #f8fafc;
}

/* Formulario mejorado */
.form-section {
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--gray-200);
}

.form-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.form-section-title {
  font-size: 18px;
  font-weight: 600;
  color: var(--gray-800);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 0;
}

.form-section-title svg {
  color: var(--primary);
}

.form-control {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid var(--gray-300);
  border-radius: var(--border-radius);
  font-size: 14px;
  transition: all 0.3s ease;
  background: white;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
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
  border-radius: var(--border-radius-lg);
  padding: 1.5rem;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--gray-200);
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.stat-title {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--gray-700);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: var(--border-radius);
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
  color: var(--gray-900);
}

.panels-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 1.5rem;
}

.panel-card {
  background: white;
  border-radius: var(--border-radius-lg);
  padding: 1.5rem;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--gray-200);
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.panel-title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--gray-800);
}

.empty-state {
  text-align: center;
  padding: 1.5rem 0;
  color: var(--gray-500);
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
  border-radius: var(--border-radius);
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
  border-radius: var(--border-radius);
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
  border-radius: var(--border-radius);
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
  border-radius: var(--border-radius);
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
  border-radius: var(--border-radius);
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

/* Module Content Styles */
.module-content {
  background: white;
  border-radius: var(--border-radius-lg);
  padding: 2rem;
  box-shadow: var(--shadow-md);
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

/* Overlay */
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

  .filters-bar {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .search-filter {
    max-width: 100%;
  }

  .filter-actions {
    width: 100%;
    justify-content: space-between;
  }

  .filter-select {
    flex: 1;
    min-width: 0;
  }

  .cita-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .cita-time {
    width: auto;
    align-self: flex-start;
  }

  .cita-actions {
    width: 100%;
    justify-content: flex-start;
    flex-wrap: wrap;
  }

  .citas-tabs {
    flex-wrap: wrap;
  }

  .tab-button {
    flex: 1;
    min-width: calc(50% - 4px);
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

  .cita-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .cita-actions .btn-action {
    width: 100%;
    justify-content: center;
  }

  .tab-button {
    min-width: 100%;
  }

  .modal-content {
    margin: 0;
    border-radius: 0;
    max-height: 100vh;
  }
}
</style>
@endpush

@section('content')
<section id="mod-citas" class="module active">
    <div class="module-header">
        <h2 class="module-title">Gestión de Citas</h2>
        <div class="module-actions">
            <button class="btn-primary" id="btn-nueva-cita">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Nueva Cita
            </button>
            <button class="btn-secondary" onclick="abrirCalendario()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Calendario
            </button>
        </div>
    </div>
    
    <div class="filters-bar">
        <div class="search-filter">
            <input type="text" placeholder="Buscar citas..." class="search-input" id="search-citas">
        </div>
        <div class="filter-actions">
            <select class="filter-select" id="filter-estado-cita">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="confirmada">Confirmada</option>
                <option value="completada">Completada</option>
                <option value="cancelada">Cancelada</option>
            </select>
            <select class="filter-select" id="filter-medico">
                <option value="">Todos los médicos</option>
                <option value="1">Dra. Laura Méndez</option>
                <option value="2">Dr. Roberto García</option>
            </select>
        </div>
    </div>
    
    <div class="citas-tabs" data-active-tab="hoy">
        <button class="tab-button active" data-tab="hoy">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            Hoy
        </button>
        <button class="tab-button" data-tab="proximas">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Próximas
        </button>
        <button class="tab-button" data-tab="pasadas">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"></path>
                <path d="M16 2v4"></path>
                <path d="M8 2v4"></path>
                <path d="M3 10h5"></path>
                <path d="M17.5 17.5 16 16.25V14"></path>
                <path d="M22 16a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z"></path>
            </svg>
            Pasadas
        </button>
        <button class="tab-button" data-tab="todas">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Todas
        </button>
    </div>
    
    <div class="citas-content">
        <div class="citas-list-container" id="citas-hoy-content">
            <div class="cita-item">
                <div class="cita-time">09:00 AM</div>
                <div class="cita-info">
                    <div class="cita-paciente">Max - Golden Retriever</div>
                    <div class="cita-propietario">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        María Rodríguez
                    </div>
                    <div class="cita-motivo">Consulta general</div>
                    <div class="cita-medico">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Dra. Laura Méndez
                    </div>
                </div>
                <div class="cita-actions">
                    <span class="status-badge status-pending">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        Pendiente
                    </span>
                    <button class="btn-action btn-edit" onclick="editarCita(1)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Editar
                    </button>
                    <button class="btn-action btn-complete" onclick="completarCita(1)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Completar
                    </button>
                    <button class="btn-action btn-cancel" onclick="cancelarCita(1)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Cancelar
                    </button>
                </div>
            </div>
            
            <div class="cita-item">
                <div class="cita-time">10:30 AM</div>
                <div class="cita-info">
                    <div class="cita-paciente">Luna - Siames</div>
                    <div class="cita-propietario">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Carlos Pérez
                    </div>
                    <div class="cita-motivo">Vacunación anual</div>
                    <div class="cita-medico">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Dr. Roberto García
                    </div>
                </div>
                <div class="cita-actions">
                    <span class="status-badge status-confirmed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Confirmada
                    </span>
                    <button class="btn-action btn-edit" onclick="editarCita(2)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Editar
                    </button>
                    <button class="btn-action btn-complete" onclick="completarCita(2)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Completar
                    </button>
                    <button class="btn-action btn-cancel" onclick="cancelarCita(2)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Cancelar
                    </button>
                </div>
            </div>
        </div>

        <div class="citas-list-container" id="citas-proximas-content" style="display: none;">
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <p>No hay citas próximas programadas</p>
            </div>
        </div>

        <div class="citas-list-container" id="citas-pasadas-content" style="display: none;">
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"></path>
                    <path d="M16 2v4"></path>
                    <path d="M8 2v4"></path>
                    <path d="M3 10h5"></path>
                    <path d="M17.5 17.5 16 16.25V14"></path>
                    <path d="M22 16a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z"></path>
                </svg>
                <p>No hay citas pasadas para mostrar</p>
            </div>
        </div>

        <div class="citas-list-container" id="citas-todas-content" style="display: none;">
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <p>No hay citas para mostrar</p>
            </div>
        </div>
    </div>

    <!-- Modal para nueva/editar cita -->
    <div id="modal-cita" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-cita-titulo">Nueva Cita</h3>
                <button class="modal-close" onclick="cerrarModalCita()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-cita" method="POST" action="{{ route('citas.store') }}">
                    @csrf
                    <input type="hidden" id="cita-id" name="id">
                    
                    <div class="form-section">
                        <h4 class="form-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Información del Paciente
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cita-paciente">Paciente *</label>
                                <select id="cita-paciente" name="paciente_id" class="form-control" required>
                                    <option value="">Seleccionar paciente</option>
                                    <option value="1">Max - María Rodríguez</option>
                                    <option value="2">Luna - Carlos Pérez</option>
                                    <option value="3">Toby - Ana González</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cita-medico">Médico *</label>
                                <select id="cita-medico" name="medico_id" class="form-control" required>
                                    <option value="">Seleccionar médico</option>
                                    <option value="1">Dra. Laura Méndez</option>
                                    <option value="2">Dr. Roberto García</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4 class="form-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Fecha y Hora
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cita-fecha">Fecha *</label>
                                <input type="date" id="cita-fecha" name="fecha" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="cita-hora">Hora *</label>
                                <input type="time" id="cita-hora" name="hora" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4 class="form-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Detalles de la Cita
                        </h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cita-motivo">Motivo de la consulta *</label>
                                <select id="cita-motivo" name="motivo" class="form-control" required>
                                    <option value="">Seleccionar motivo</option>
                                    <option value="consulta">Consulta general</option>
                                    <option value="vacunacion">Vacunación</option>
                                    <option value="cirugia">Cirugía</option>
                                    <option value="urgencia">Urgencia</option>
                                    <option value="control">Control</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cita-estado">Estado *</label>
                                <select id="cita-estado" name="estado" class="form-control" required>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="cancelada">Cancelada</option>
                                    <option value="completada">Completada</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cita-observaciones">Observaciones</label>
                            <textarea id="cita-observaciones" name="observaciones" class="form-control" rows="3" placeholder="Agregue observaciones adicionales..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalCita()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="guardarCita()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Guardar Cita
                </button>
            </div>
        </div>
    </div>
</section>

<script>
// Inicialización del módulo de citas
function initModuloCitas() {
    console.log('📅 Inicializando módulo de citas...');
    
    // Tab switching
    const tabs = document.querySelectorAll('.citas-tabs .tab-button');
    const tabsContainer = document.querySelector('.citas-tabs');
    
    tabs.forEach(btn => {
        btn.addEventListener('click', function() {
            const target = this.getAttribute('data-tab');
            
            // Update active tab
            tabs.forEach(tab => tab.classList.remove('active'));
            this.classList.add('active');
            
            // Update tabs container data attribute for the indicator
            tabsContainer.setAttribute('data-active-tab', target);
            
            // Show corresponding content
            document.querySelectorAll('.citas-list-container').forEach(container => {
                container.style.display = 'none';
            });
            
            const targetContent = document.getElementById(`citas-${target}-content`);
            if (targetContent) {
                targetContent.style.display = 'block';
            }
        });
    });

    // Nueva cita modal
    const btnNuevaCita = document.getElementById('btn-nueva-cita');
    if (btnNuevaCita) {
        btnNuevaCita.addEventListener('click', abrirModalCita);
    }

    // Filtros
    const searchInput = document.getElementById('search-citas');
    const filterEstado = document.getElementById('filter-estado-cita');
    const filterMedico = document.getElementById('filter-medico');

    if (searchInput) {
        searchInput.addEventListener('input', debounce(filtrarCitas, 300));
    }
    if (filterEstado) {
        filterEstado.addEventListener('change', filtrarCitas);
    }
    if (filterMedico) {
        filterMedico.addEventListener('change', filtrarCitas);
    }

    // Set today's date as default
    const fechaInput = document.getElementById('cita-fecha');
    if (fechaInput) {
        const today = new Date().toISOString().split('T')[0];
        fechaInput.value = today;
        fechaInput.min = today; // Prevent past dates
    }

    // Setup modal events
    setupModalCitas();
}

function setupModalCitas() {
    const modal = document.getElementById('modal-cita');
    if (!modal) {
        console.warn('Modal de cita no encontrado');
        return;
    }

    const modalClose = modal.querySelector('.modal-close');
    const modalCancel = modal.querySelector('.btn-secondary');
    
    if (modalClose) {
        modalClose.addEventListener('click', cerrarModalCita);
    }
    if (modalCancel) {
        modalCancel.addEventListener('click', cerrarModalCita);
    }

    // Prevent modal close when clicking inside modal content
    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalCita();
        }
    });
}

function abrirModalCita() {
    const modal = document.getElementById('modal-cita');
    const titulo = document.getElementById('modal-cita-titulo');
    const form = document.getElementById('form-cita');
    
    if (modal && titulo && form) {
        titulo.textContent = 'Nueva Cita';
        form.reset();
        document.getElementById('cita-id').value = '';
        
        // Set today's date
        const fechaInput = document.getElementById('cita-fecha');
        if (fechaInput) {
            const today = new Date().toISOString().split('T')[0];
            fechaInput.value = today;
        }
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function cerrarModalCita() {
    const modal = document.getElementById('modal-cita');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function guardarCita() {
    const form = document.getElementById('form-cita');
    if (!form) return;
    
    const citaId = document.getElementById('cita-id').value;
    const formData = new FormData(form);
    
    // Basic validation
    const requiredFields = ['paciente_id', 'medico_id', 'fecha', 'hora', 'motivo', 'estado'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input && !input.value.trim()) {
            isValid = false;
            highlightInvalidField(input);
        }
    });
    
    if (!isValid) {
        showNotification('Por favor, complete todos los campos obligatorios', 'error');
        return;
    }
    
    // Determine URL and method based on whether we're creating or updating
    const url = citaId ? `/api/citas/${citaId}` : '/api/citas';
    const method = citaId ? 'PUT' : 'POST';
    
    // Simulate API call
    simulateAPICall(url, {
        method: method,
        body: formData
    })
    .then(data => {
        showNotification(citaId ? 'Cita actualizada correctamente' : 'Cita creada correctamente', 'success');
        cerrarModalCita();
        // In a real app, you would update the list here
    })
    .catch(error => {
        showNotification('Error al guardar la cita', 'error');
        console.error('Error:', error);
    });
}

function editarCita(id) {
    console.log(`Editando cita ID: ${id}`);
    const modal = document.getElementById('modal-cita');
    const titulo = document.getElementById('modal-cita-titulo');
    const form = document.getElementById('form-cita');
    
    if (!modal || !titulo || !form) return;
    
    titulo.textContent = 'Editar Cita';
    document.getElementById('cita-id').value = id;
    
    // Simulate loading cita data
    simulateAPICall(`/api/citas/${id}`)
        .then(cita => {
            // Populate form with cita data
            document.getElementById('cita-paciente').value = cita.paciente_id || '1';
            document.getElementById('cita-medico').value = cita.medico_id || '1';
            document.getElementById('cita-fecha').value = cita.fecha || '';
            document.getElementById('cita-hora').value = cita.hora || '';
            document.getElementById('cita-motivo').value = cita.motivo || 'consulta';
            document.getElementById('cita-estado').value = cita.estado || 'pendiente';
            document.getElementById('cita-observaciones').value = cita.observaciones || '';
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            showNotification('Error al cargar los datos de la cita', 'error');
            console.error('Error:', error);
        });
}

function completarCita(id) {
    if (confirm('¿Está seguro de que desea marcar esta cita como completada?')) {
        simulateAPICall(`/api/citas/${id}/completar`, { method: 'POST' })
            .then(data => {
                showNotification('Cita marcada como completada', 'success');
                // In a real app, you would update the UI here
            })
            .catch(error => {
                showNotification('Error al completar la cita', 'error');
                console.error('Error:', error);
            });
    }
}

function cancelarCita(id) {
    if (confirm('¿Está seguro de que desea cancelar esta cita?')) {
        simulateAPICall(`/api/citas/${id}/cancelar`, { method: 'POST' })
            .then(data => {
                showNotification('Cita cancelada correctamente', 'success');
                // In a real app, you would update the UI here
            })
            .catch(error => {
                showNotification('Error al cancelar la cita', 'error');
                console.error('Error:', error);
            });
    }
}

function abrirCalendario() {
    showNotification('Funcionalidad de calendario en desarrollo', 'info');
}

function filtrarCitas() {
    const searchTerm = document.getElementById('search-citas')?.value.toLowerCase() || '';
    const estado = document.getElementById('filter-estado-cita')?.value || '';
    const medico = document.getElementById('filter-medico')?.value || '';
    
    const activeTab = document.querySelector('.citas-tabs .tab-button.active');
    const tabContent = activeTab ? document.getElementById(`citas-${activeTab.getAttribute('data-tab')}-content`) : null;
    
    if (!tabContent) return;
    
    const citas = tabContent.querySelectorAll('.cita-item');
    
    citas.forEach(cita => {
        const textoCita = cita.textContent.toLowerCase();
        const estadoCita = cita.querySelector('.status-badge')?.textContent.toLowerCase() || '';
        const medicoCita = cita.querySelector('.cita-medico')?.textContent.toLowerCase() || '';
        
        const coincideBusqueda = !searchTerm || textoCita.includes(searchTerm);
        const coincideEstado = !estado || estadoCita.includes(estado);
        const coincideMedico = !medico || medicoCita.includes(medico.toLowerCase());
        
        if (coincideBusqueda && coincideEstado && coincideMedico) {
            cita.style.display = 'flex';
        } else {
            cita.style.display = 'none';
        }
    });
}

// Utilidades
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function highlightInvalidField(field) {
    field.style.borderColor = '#ef4444';
    field.addEventListener('input', function() {
        this.style.borderColor = '';
    }, { once: true });
}

function showNotification(message, type = 'info') {
    // Implementar sistema de notificaciones si es necesario
    alert(message);
}

function simulateAPICall(url, options = {}) {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            if (Math.random() > 0.1) {
                resolve({ 
                    success: true, 
                    message: 'Operación exitosa',
                    paciente_id: '1',
                    medico_id: '1',
                    fecha: '2024-01-15',
                    hora: '09:00',
                    motivo: 'consulta',
                    estado: 'pendiente',
                    observaciones: 'Primera consulta del paciente'
                });
            } else {
                reject(new Error('Error de servidor simulado'));
            }
        }, 1000);
    });
}

// Inicializar cuando el módulo esté activo
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el módulo de citas está activo
    const moduloCitas = document.getElementById('mod-citas');
    if (moduloCitas && moduloCitas.classList.contains('active')) {
        initModuloCitas();
    }
    
    // También escuchar cambios de módulo si estás usando el sistema de navegación SPA
    window.addEventListener('moduleChanged', function(e) {
        if (e.detail.module === 'citas') {
            setTimeout(initModuloCitas, 100);
        }
    });
});

// Exportar funciones para uso global
window.abrirModalCita = abrirModalCita;
window.cerrarModalCita = cerrarModalCita;
window.guardarCita = guardarCita;
window.editarCita = editarCita;
window.completarCita = completarCita;
window.cancelarCita = cancelarCita;
window.abrirCalendario = abrirCalendario;
</script>
@endsection