<?php

// Usamos dompdf directamente sin la fachada de Laravel
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Crear instancia de DOMPDF con opciones
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'sans-serif');
$options->set('dpi', 150);

$dompdf = new Dompdf($options);

// HTML content for PDF
$html = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .cover-page {
            text-align: center;
            page-break-after: always;
            padding: 80px 20px;
        }
        .cover-page h1 {
            font-size: 48px;
            margin: 40px 0;
            color: #2c5aa0;
        }
        .cover-page h2 {
            font-size: 24px;
            color: #555;
            margin-bottom: 30px;
        }
        .cover-page .meta {
            margin-top: 100px;
            color: #666;
            font-size: 14px;
        }
        .toc {
            page-break-after: always;
            margin-bottom: 50px;
        }
        .toc h2 {
            color: #2c5aa0;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .toc ul {
            list-style: none;
            padding: 0;
        }
        .toc li {
            margin: 8px 0;
            padding-left: 20px;
        }
        h1 {
            color: #2c5aa0;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 10px;
            margin-top: 40px;
            page-break-after: avoid;
        }
        h2 {
            color: #4a7ba7;
            margin-top: 30px;
            page-break-after: avoid;
        }
        h3 {
            color: #6a9bc1;
            margin-top: 20px;
            page-break-after: avoid;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background-color: #f0f5fa;
            padding: 15px;
            border-left: 4px solid #2c5aa0;
            margin-bottom: 15px;
            page-break-after: avoid;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            margin: 10px 0;
            padding-left: 30px;
            position: relative;
        }
        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #2c5aa0;
            font-weight: bold;
            font-size: 18px;
        }
        .module-box {
            background-color: #fafbfc;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            page-break-inside: avoid;
        }
        .module-box h4 {
            margin-top: 0;
            color: #2c5aa0;
        }
        .tech-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 15px 0;
        }
        .tech-badge {
            background-color: #2c5aa0;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }
        .grid-2 {
            margin: 15px 0;
        }
        .grid-2-col {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background-color: #2c5aa0;
            color: white;
            padding: 12px;
            text-align: left;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .architecture-diagram {
            margin: 20px 0;
            padding: 20px;
            background-color: #f0f5fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
            text-align: right;
        }
        code {
            background-color: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
    </style>
</head>
<body>

<!-- COVER PAGE -->
<div class="cover-page">
    <h1>SISTEMA DE GESTIÓN VETERINARIA</h1>
    <h2>Documento de Desarrollo del Producto</h2>
    <div class="meta">
        <p><strong>Tipo de Proyecto:</strong> Aplicación Web</p>
        <p><strong>Framework:</strong> Laravel 12</p>
        <p><strong>Lenguaje:</strong> PHP 8.2+</p>
        <p><strong>Fecha de Generación:</strong> GENERATED_DATE</p>
    </div>
</div>

<!-- TABLE OF CONTENTS -->
<div class="toc">
    <h2>TABLA DE CONTENIDOS</h2>
    <ul>
        <li>Descripción General</li>
        <li>Características Principales</li>
        <li>Arquitectura del Sistema</li>
        <li>Stack Tecnológico</li>
        <li>Módulos del Proyecto</li>
        <li>Modelos de Datos</li>
        <li>API y Rutas</li>
        <li>Despliegue</li>
    </ul>
</div>

<!-- OVERVIEW -->
<div class="section">
    <h1>DESCRIPCIÓN GENERAL</h1>
    <div class="section-title">
        <h3>¿Qué es?</h3>
    </div>
    <p>El <strong>Sistema de Gestión Veterinaria</strong> es una aplicación web integral diseñada para la administración completa de una clínica veterinaria moderna. Permite gestionar de manera eficiente todas las operaciones diarias de una clínica, incluyendo citas, pacientes (mascotas), propietarios, servicios profesionales, recetas médicas y más.</p>
    
    <div class="section-title">
        <h3>Propósito</h3>
    </div>
    <p>Centralizar la información y los procesos de una clínica veterinaria en una plataforma única, mejorando la eficiencia operativa, la calidad de los registros médicos y la experiencia del cliente.</p>
</div>

<!-- FEATURES -->
<div class="section">
    <h1>CARACTERÍSTICAS PRINCIPALES</h1>
    <ul class="feature-list">
        <li><strong>Gestión de Citas:</strong> Programación y seguimiento de consultas veterinarias</li>
        <li><strong>Registro de Mascotas:</strong> Base de datos completa de pacientes animales con historial médico</li>
        <li><strong>Gestión de Propietarios:</strong> Información de contacto y datos de clientes</li>
        <li><strong>Personal Médico:</strong> Administración de profesionales veterinarios y su especialización</li>
        <li><strong>Servicios Profesionales:</strong> Catálogo de servicios con tarifas y tipos de atención</li>
        <li><strong>Recetas Médicas:</strong> Generación y registro de prescripciones</li>
        <li><strong>Hospitalizaciones:</strong> Seguimiento de mascotas internadas</li>
        <li><strong>Control de Honorarios:</strong> Gestión de facturas y pagos</li>
        <li><strong>Sistema de Usuarios:</strong> Control de acceso con diferentes roles</li>
        <li><strong>Reportes:</strong> Generación de informes y documentos</li>
    </ul>
</div>

<!-- ARCHITECTURE -->
<div class="section">
    <h1>ARQUITECTURA DEL SISTEMA</h1>
    
    <div class="architecture-diagram">
        <h3>Estructura MVC (Model-View-Controller)</h3>
        <p>La aplicación sigue el patrón arquitectónico MVC proporcionado por Laravel:</p>
        <table>
            <tr>
                <th>Componente</th>
                <th>Descripción</th>
                <th>Ubicación</th>
            </tr>
            <tr>
                <td><strong>Models</strong></td>
                <td>Representación de datos y lógica empresarial</td>
                <td>/app/Models</td>
            </tr>
            <tr>
                <td><strong>Views</strong></td>
                <td>Interfaz de usuario y plantillas Blade</td>
                <td>/resources/views</td>
            </tr>
            <tr>
                <td><strong>Controllers</strong></td>
                <td>Lógica de aplicación y manejo de requests</td>
                <td>/app/Http/Controllers</td>
            </tr>
            <tr>
                <td><strong>Routes</strong></td>
                <td>Definición de endpoints y URLs</td>
                <td>/routes</td>
            </tr>
            <tr>
                <td><strong>Database</strong></td>
                <td>Migraciones, seeders y esquema</td>
                <td>/database</td>
            </tr>
        </table>
    </div>
</div>

<!-- TECHNOLOGIES -->
<div class="section">
    <h1>STACK TECNOLÓGICO</h1>
    
    <div class="grid-2">
        <div class="grid-2-col">
            <div class="section-title">
                <h3>Backend</h3>
            </div>
            <div class="tech-stack">
                <span class="tech-badge">PHP 8.2+</span>
                <span class="tech-badge">Laravel 12</span>
                <span class="tech-badge">Eloquent ORM</span>
                <span class="tech-badge">PostgreSQL</span>
            </div>
            <p>Plataforma robusta y escalable para procesamiento servidor.</p>
        </div>
        <div class="grid-2-col">
            <div class="section-title">
                <h3>Frontend</h3>
            </div>
            <div class="tech-stack">
                <span class="tech-badge">JavaScript</span>
                <span class="tech-badge">HTML5</span>
                <span class="tech-badge">CSS3</span>
                <span class="tech-badge">Vite</span>
            </div>
            <p>Interfaz moderna y responsiva.</p>
        </div>
    </div>

    <div class="grid-2">
        <div class="grid-2-col">
            <div class="section-title">
                <h3>Dependencias Principales</h3>
            </div>
            <ul>
                <li><strong>laravel/framework</strong> - Framework web robusto</li>
                <li><strong>barryvdh/laravel-dompdf</strong> - Generación de PDFs</li>
                <li><strong>laravel/tinker</strong> - REPL interactivo para debug</li>
            </ul>
        </div>
        <div class="grid-2-col">
            <div class="section-title">
                <h3>Herramientas de Desarrollo</h3>
            </div>
            <ul>
                <li><strong>Pest</strong> - Framework de testing PHP</li>
                <li><strong>Laravel Pint</strong> - Code formatter y linter</li>
                <li><strong>FakerPHP</strong> - Generación de datos ficticios</li>
            </ul>
        </div>
    </div>
</div>

<!-- MODULES -->
<div class="section page-break">
    <h1>MÓDULOS DEL PROYECTO</h1>
    
    <div class="section-title">
        <h3>Componentes Funcionales</h3>
    </div>

    <div class="module-box">
        <h4>Módulo de Citas</h4>
        <p><strong>Propósito:</strong> Gestión completa del sistema de citas de la clínica.</p>
        <p><strong>Funcionalidades:</strong> Crear, editar, listar y cancelar citas. Visualización por fecha, estado (programada, completada, cancelada) y profesional asignado.</p>
        <p><strong>Ubicación:</strong> /routes/citas/ | /app/Http/Controllers/Cita/</p>
    </div>

    <div class="module-box">
        <h4>Módulo de Mascotas</h4>
        <p><strong>Propósito:</strong> Registro y seguimiento de pacientes animales.</p>
        <p><strong>Funcionalidades:</strong> Crear fichas de mascotas, asociar con propietarios, registrar historial médico, peso, raza y datos clínicos.</p>
        <p><strong>Ubicación:</strong> /routes/mascotas/ | /app/Http/Controllers/Mascota/</p>
    </div>

    <div class="module-box">
        <h4>Módulo de Propietarios</h4>
        <p><strong>Propósito:</strong> Gestión de información de clientes.</p>
        <p><strong>Funcionalidades:</strong> Registrar propietarios, contacto, datos personales y mascotas asociadas.</p>
        <p><strong>Ubicación:</strong> /routes/propietarios/ | /app/Http/Controllers/Propietario/</p>
    </div>

    <div class="module-box">
        <h4>Módulo de Profesionales</h4>
        <p><strong>Propósito:</strong> Administración del personal veterinario.</p>
        <p><strong>Funcionalidades:</strong> Registrar veterinarios, especialidades, horarios de disponibilidad y datos laborales.</p>
        <p><strong>Ubicación:</strong> /routes/profesionales/ | /app/Http/Controllers/Profesional/</p>
    </div>

    <div class="module-box">
        <h4>Módulo de Honorarios</h4>
        <p><strong>Propósito:</strong> Gestión de facturación y pagos.</p>
        <p><strong>Funcionalidades:</strong> Registrar servicios prestados, calcular honorarios, generar facturas.</p>
        <p><strong>Ubicación:</strong> /routes/honorarios/ | /app/Http/Controllers/Honorario/</p>
    </div>

    <div class="module-box">
        <h4>Módulo de Recetas</h4>
        <p><strong>Propósito:</strong> Generación de prescripciones médicas.</p>
        <p><strong>Funcionalidades:</strong> Crear recetas digitales, asociar medicamentos, instrucciones de uso.</p>
        <p><strong>Ubicación:</strong> /routes/recetas/ | /app/Http/Controllers/Receta/</p>
    </div>

    <div class="module-box">
        <h4>Módulo de Hospitalizaciones</h4>
        <p><strong>Propósito:</strong> Seguimiento de mascotas internadas.</p>
        <p><strong>Funcionalidades:</strong> Registrar ingresos, control diario de pacientes, fechas de alta.</p>
        <p><strong>Ubicación:</strong> /routes/hospitalizaciones/ | /app/Http/Controllers/Hospitalizacion/</p>
    </div>

    <div class="module-box">
        <h4>Módulo de Usuarios</h4>
        <p><strong>Propósito:</strong> Control de acceso y roles.</p>
        <p><strong>Funcionalidades:</strong> Crear usuarios, asignar roles (admin, médico, recepción), autenticación segura.</p>
        <p><strong>Ubicación:</strong> /routes/usuarios/ | /app/Http/Controllers/Usuario/</p>
    </div>
</div>

<!-- DATA MODELS -->
<div class="section page-break">
    <h1>MODELOS DE DATOS</h1>
    
    <div class="section-title">
        <h3>Entidades Principales</h3>
    </div>

    <table>
        <tr>
            <th>Modelo</th>
            <th>Descripción</th>
            <th>Relaciones Principales</th>
        </tr>
        <tr>
            <td><strong>Cita</strong></td>
            <td>Registro de consultas veterinarias</td>
            <td>Mascota, Profesional, Usuario</td>
        </tr>
        <tr>
            <td><strong>Mascota</strong></td>
            <td>Paciente animal</td>
            <td>Propietario, Citas, Hospitalizaciones</td>
        </tr>
        <tr>
            <td><strong>Propietario</strong></td>
            <td>Dueño de mascota</td>
            <td>Mascotas, Citas</td>
        </tr>
        <tr>
            <td><strong>Profesional</strong></td>
            <td>Veterinario</td>
            <td>Citas, Recetas, Hospitalizaciones</td>
        </tr>
        <tr>
            <td><strong>Receta</strong></td>
            <td>Prescripción médica</td>
            <td>Mascota, Profesional</td>
        </tr>
        <tr>
            <td><strong>Honorario</strong></td>
            <td>Registro de servicio y pago</td>
            <td>Cita, Propietario</td>
        </tr>
        <tr>
            <td><strong>Hospitalizacion</strong></td>
            <td>Registro de internamiento</td>
            <td>Mascota, Profesional</td>
        </tr>
        <tr>
            <td><strong>User</strong></td>
            <td>Usuario del sistema</td>
            <td>Rol, Profesional</td>
        </tr>
    </table>

    <div class="section-title">
        <h3>Características de la Base de Datos</h3>
    </div>
    <ul>
        <li>Integridad referencial mediante claves foráneas</li>
        <li>Índices para optimización de consultas</li>
        <li>Timestamps automáticos (created_at, updated_at)</li>
        <li>Soft deletes para borrado lógico</li>
        <li>Migraciones versionadas para control de cambios</li>
    </ul>
</div>

<!-- API -->
<div class="section page-break">
    <h1>API Y RUTAS</h1>
    
    <div class="section-title">
        <h3>Estructura de Rutas</h3>
    </div>

    <p>El proyecto organiza sus rutas de manera modular. Las principales rutas incluyen:</p>

    <h3>Rutas Web (web.php)</h3>
    <p>Rutas de la interfaz web tradicional, con autenticación y autorización basada en sesiones.</p>

    <h3>Rutas API (api.php)</h3>
    <p>Endpoints RESTful para integración y consumo desde aplicaciones cliente, con autenticación token-based.</p>

    <h3>Módulos de Rutas</h3>
    <ul>
        <li><strong>citas/</strong> - Endpoints para gestión de citas</li>
        <li><strong>mascotas/</strong> - Endpoints para mascotas y pacientes</li>
        <li><strong>propietarios/</strong> - Endpoints para clientes y propietarios</li>
        <li><strong>profesionales/</strong> - Endpoints para veterinarios y especialistas</li>
        <li><strong>honorarios/</strong> - Endpoints para facturación y pagos</li>
        <li><strong>recetas/</strong> - Endpoints para prescripciones médicas</li>
        <li><strong>hospitalizaciones/</strong> - Endpoints para internamiento</li>
        <li><strong>usuarios/</strong> - Endpoints para gestión de usuarios y roles</li>
    </ul>

    <div class="section-title">
        <h3>Métodos HTTP Estándar RESTful</h3>
    </div>

    <table>
        <tr>
            <th>Método HTTP</th>
            <th>Operación</th>
            <th>Ejemplo de Ruta</th>
            <th>Descripción</th>
        </tr>
        <tr>
            <td><strong>GET</strong></td>
            <td>Obtener listado</td>
            <td>GET /api/citas</td>
            <td>Recupera todas las citas</td>
        </tr>
        <tr>
            <td><strong>GET</strong></td>
            <td>Obtener detalles</td>
            <td>GET /api/citas/1</td>
            <td>Recupera una cita específica</td>
        </tr>
        <tr>
            <td><strong>POST</strong></td>
            <td>Crear recurso</td>
            <td>POST /api/citas</td>
            <td>Crea una nueva cita</td>
        </tr>
        <tr>
            <td><strong>PUT</strong></td>
            <td>Actualizar completo</td>
            <td>PUT /api/citas/1</td>
            <td>Actualiza una cita existente</td>
        </tr>
        <tr>
            <td><strong>PATCH</strong></td>
            <td>Actualizar parcial</td>
            <td>PATCH /api/citas/1</td>
            <td>Actualiza campos específicos</td>
        </tr>
        <tr>
            <td><strong>DELETE</strong></td>
            <td>Eliminar recurso</td>
            <td>DELETE /api/citas/1</td>
            <td>Elimina una cita</td>
        </tr>
    </table>
</div>

<!-- DEPLOYMENT -->
<div class="section page-break">
    <h1>DESPLIEGUE Y DISTRIBUCIÓN</h1>
    
    <div class="section-title">
        <h3>Entornos Soportados</h3>
    </div>

    <div class="module-box">
        <h4>Desarrollo Local</h4>
        <p>Para ejecutar el proyecto localmente:</p>
        <ul>
            <li><strong>Requisitos:</strong> PHP 8.2+, Composer, PostgreSQL/MySQL</li>
            <li><strong>Comando:</strong> <code>php artisan serve</code></li>
            <li><strong>Puerto por defecto:</strong> http://localhost:8000</li>
        </ul>
    </div>

    <div class="module-box">
        <h4>Cloud Platforms (render.yaml)</h4>
        <p>Configuración para despliegue en plataformas cloud modernas como Render.io, Heroku, etc.</p>
        <ul>
            <li>Archivo: <code>render.yaml</code></li>
            <li>Soporte para Variables de Entorno</li>
            <li>Migraciones automáticas en despliegue</li>
        </ul>
    </div>

    <div class="module-box">
        <h4>Hosting Compartido (InfinityFree)</h4>
        <p>Directorio con configuración específica para hospedaje compartido con PHP/MySQL.</p>
        <ul>
            <li><strong>Ubicación:</strong> /veterinaria-infinityfree/</li>
            <li>Compilado y listo para producción</li>
            <li>Optimizado para limitaciones de hosting compartido</li>
        </ul>
    </div>

    <div class="section-title">
        <h3>Proceso de Instalación</h3>
    </div>

    <ol>
        <li>Clonar o descargar el repositorio</li>
        <li>Ejecutar: <code>composer install</code></li>
        <li>Copiar archivo .env: <code>cp .env.example .env</code></li>
        <li>Generar clave de aplicación: <code>php artisan key:generate</code></li>
        <li>Configurar base de datos en .env</li>
        <li>Ejecutar migraciones: <code>php artisan migrate</code></li>
        <li>Instalar dependencias frontend: <code>npm install</code></li>
        <li>Compilar assets: <code>npm run build</code></li>
        <li>Iniciar servidor: <code>php artisan serve</code></li>
    </ol>

    <div class="section-title">
        <h3>Estructura de Directorios Clave</h3>
    </div>

    <table>
        <tr>
            <th>Directorio</th>
            <th>Descripción</th>
            <th>Propósito</th>
        </tr>
        <tr>
            <td><strong>/Veterinaria</strong></td>
            <td>Aplicación principal</td>
            <td>Código fuente completo y desarrollo</td>
        </tr>
        <tr>
            <td><strong>/veterinaria-infinityfree</strong></td>
            <td>Build compilado</td>
            <td>Distribución para hospedaje compartido</td>
        </tr>
        <tr>
            <td><strong>/app</strong></td>
            <td>Código de aplicación</td>
            <td>Models, Controllers, Middleware</td>
        </tr>
        <tr>
            <td><strong>/routes</strong></td>
            <td>Definiciones de rutas</td>
            <td>API endpoints y rutas web</td>
        </tr>
        <tr>
            <td><strong>/database</strong></td>
            <td>Base de datos</td>
            <td>Migraciones, seeders, factories</td>
        </tr>
        <tr>
            <td><strong>/resources</strong></td>
            <td>Recursos frontend</td>
            <td>Views, CSS, JavaScript</td>
        </tr>
        <tr>
            <td><strong>/storage</strong></td>
            <td>Almacenamiento</td>
            <td>Logs, cache, archivos</td>
        </tr>
        <tr>
            <td><strong>/public</strong></td>
            <td>Acceso público</td>
            <td>Assets compilados, punto de entrada</td>
        </tr>
    </table>
</div>

<!-- FOOTER -->
<div class="footer">
    <p><strong>Sistema de Gestión Veterinaria</strong></p>
    <p>Documento generado automáticamente | Actualizado: GENERATED_DATE</p>
    <p>© GENERATED_YEAR - Todos los derechos reservados</p>
</div>

</body>
</html>
HTML;

// Replace placeholders
$html = str_replace('GENERATED_DATE', date('d/m/Y H:i:s'), $html);
$html = str_replace('GENERATED_YEAR', date('Y'), $html);

// Load HTML into DOMPDF
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Save the PDF
$filename = 'Documentacion-Producto-Veterinaria-' . date('Y-m-d-His') . '.pdf';
$filepath = __DIR__ . DIRECTORY_SEPARATOR . $filename;

// Get PDF output and save to file
file_put_contents($filepath, $dompdf->output());

// Also save to public if exists
$publicPath = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $filename;
if (is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'public')) {
    file_put_contents($publicPath, $dompdf->output());
    echo "✅ PDF generado exitosamente!\n";
    echo "📁 Ubicación principal: " . $publicPath . "\n";
} else {
    echo "✅ PDF generado exitosamente!\n";
}

echo "📁 Ubicación alternativa: " . $filepath . "\n";
echo "📊 Nombre del archivo: " . $filename . "\n";
echo "📄 Tamaño: " . round(filesize($filepath) / 1024, 2) . " KB\n";
