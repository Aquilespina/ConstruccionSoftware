<div align="center">

# 🐾 VeteClini — Sistema de Gestión Veterinaria

**Plataforma web integral para la administración de clínicas veterinarias**

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-4.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vitejs.dev)

</div>

---

## Acerca del proyecto

VeteClini centraliza la información clínica de mascotas, propietarios y profesionales en una sola plataforma, reduciendo errores y optimizando cada consulta. Cubre todo el ciclo de atención — desde la cita hasta el alta — con módulos de recetas, hospitalización y facturación, lo que permite a la clínica ofrecer un servicio más personalizado, trazable y eficiente.

---

## Módulos

| Módulo | Descripción |
|---|---|
| **Mascotas** | Ficha clínica, historial y vacunación |
| **Propietarios** | Gestión de dueños y contacto |
| **Citas** | Agendamiento y calendario de consultas |
| **Recetas** | Generación y exportación en PDF |
| **Hospitalización** | Seguimiento de pacientes internados |
| **Profesionales** | Administración del equipo veterinario |
| **Honorarios** | Facturación y detalle de pagos |
| **Usuarios** | Control de acceso al sistema |

---

## Requisitos

- **PHP** 8.4+
- **Composer** 2.x
- **MySQL** 8.x
- **Node.js** 18+ y **npm**

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio>
cd Veterinaria
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=veterinaria
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Preparar la base de datos

```bash
php artisan migrate
php artisan db:seed   # opcional — datos de prueba
```

### 5. Compilar assets

```bash
npm run build
```

### 6. Levantar el servidor

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`.

---

## Desarrollo

Para trabajar con hot-reload, abre dos terminales:

```bash
# Terminal 1 — servidor PHP
php artisan serve

# Terminal 2 — Vite dev server
npm run dev
```

---

## Exportar PDF

El sistema usa **DomPDF** para exportar recetas y reportes. No requiere configuración adicional; se activa automáticamente con `composer install`.

---

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 · PHP 8.4 |
| Frontend | Blade · Tailwind CSS 4 · Vite 7 |
| Base de datos | MySQL 8 |
| PDF | barryvdh/laravel-dompdf |
| HTTP client | Axios |
| Testing | PestPHP · PHPUnit |

---

<div align="center">
  <sub>Desarrollado con ❤️ para mejorar la calidad de vida de las mascotas y sus familias.</sub>
</div>
