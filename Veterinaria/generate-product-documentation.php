<?php

require __DIR__ . '/vendor/autoload.php';

use Barryvdh\DomPDF\Facade\Pdf;

// Get all model files
$modelsPath = __DIR__ . '/app/Models';
$models = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($modelsPath),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $models[] = str_replace($modelsPath . '/', '', $file->getPathname());
    }
}

// Get all controller files
$controllersPath = __DIR__ . '/app/Http/Controllers';
$controllers = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($controllersPath),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $controllers[] = str_replace($controllersPath . '/', '', $file->getPathname());
    }
}

// Get route files
$routesPath = __DIR__ . '/routes';
$routeFiles = [];

if (file_exists($routesPath . '/web.php')) {
    $routeFiles[] = 'web.php (Web Routes)';
}
if (file_exists($routesPath . '/api.php')) {
    $routeFiles[] = 'api.php (API Routes)';
}

$routeDirs = array_diff(scandir($routesPath), ['.', '..', 'web.php', 'api.php', 'console.php']);
foreach ($routeDirs as $dir) {
    if (is_dir($routesPath . '/' . $dir)) {
        $routeFiles[] = $dir . '/ (Module Routes)';
    }
}

// HTML content for PDF
$html = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        .toc a {
            text-decoration: none;
            color: #2c5aa0;
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
            display: flex;
            gap: 20px;
            margin: 15px 0;
        }
        .grid-2-col {
            flex: 1;
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
        .date-generated {
            color: #999;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<!-- COVER PAGE -->
<div class="cover-page">
    <h1>📋 Sistema de Gestión Veterinaria</h1>
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
    <h2>📑 Tabla de Contenidos</h2>
    <ul>
        <li><a href="#overview">Descripción General</a></li>
        <li><a href="#features">Características Principales</a></li>
        <li><a href="#architecture">Arquitectura del Sistema</a></li>
        <li><a href="#technologies">Stack Tecnológico</a></li>
        <li><a href="#modules">Módulos del Proyecto</a></li>
        <li><a href="#data-models">Modelos de Datos</a></li>
        <li><a href="#api">API y Rutas</a></li>
        <li><a href="#deployment">Despliegue</a></li>
    </ul>
</div>

<!-- OVERVIEW -->
<div class="section">
    <h1 id="overview">📖 Descripción General</h1>
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
    <h1 id="features">✨ Características Principales</h1>
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
<div class="section page-break">
    <h1 id="architecture">🏗️ Arquitectura del Sistema</h1>
    
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
    <h1 id="technologies">🛠️ Stack Tecnológico</h1>
    
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
        </div>
    </div>

    <div class="grid-2">
        <div class="grid-2-col">
            <div class="section-title">
                <h3>Dependencias Principales</h3>
            </div>
            <ul>
                <li><strong>laravel/framework</strong> - Framework web</li>
                <li><strong>barryvdh/laravel-dompdf</strong> - Generación de PDFs</li>
                <li><strong>laravel/tinker</strong> - REPL interactivo</li>
            </ul>
        </div>
        <div class="grid-2-col">
            <div class="section-title">
                <h3>Herramientas de Desarrollo</h3>
            </div>
            <ul>
                <li><strong>Pest</strong> - Framework de testing</li>
                <li><strong>Laravel Pint</strong> - Code formatter</li>
                <li><strong>Faker</strong> - Generación de datos ficticios</li>
            </ul>
        </div>
    </div>
</div>

<!-- MODULES -->
<div class="section page-break">
    <h1 id="modules">📦 Módulos del Proyecto</h1>
    
    <div class="section-title">
        <h3>Componentes Funcionales</h3>
    </div>

    <div class="module-box">
        <h4>🗓️ Módulo de Citas</h4>
        <p><strong>Propósito:</strong> Gestión completa del sistema de citas de la clínica.</p>
        <p><strong>Funcionalidades:</strong> Crear, editar, listar y cancelar citas. Visualización por fecha, estado (programada, completada, cancelada) y profesional asignado.</p>
        <p><strong>Rutas:</strong> /routes/citas/</p>
    </div>

    <div class="module-box">
        <h4>🐾 Módulo de Mascotas</h4>
        <p><strong>Propósito:</strong> Registro y seguimiento de pacientes animales.</p>
        <p><strong>Funcionalidades:</strong> Crear fichas de mascotas, asociar con propietarios, registrar historial médico, peso, raza y datos clínicos.</p>
        <p><strong>Rutas:</strong> /routes/mascotas/</p>
    </div>

    <div class="module-box">
        <h4>👤 Módulo de Propietarios</h4>
        <p><strong>Propósito:</strong> Gestión de información de clientes.</p>
        <p><strong>Funcionalidades:</strong> Registrar propietarios, contacto, datos personales y mascotas asociadas.</p>
        <p><strong>Rutas:</strong> /routes/propietarios/</p>
    </div>

    <div class="module-box">
        <h4>👨‍⚕️ Módulo de Profesionales</h4>
        <p><strong>Propósito:</strong> Administración del personal veterinario.</p>
        <p><strong>Funcionalidades:</strong> Registrar veterinarios, especialidades, horarios de disponibilidad y datos laborales.</p>
        <p><strong>Rutas:</strong> /routes/profesionales/</p>
    </div>

    <div class="module-box">
        <h4>💰 Módulo de Honorarios</h4>
        <p><strong>Propósito:</strong> Gestión de facturación y pagos.</p>
        <p><strong>Funcionalidades:</strong> Registrar servicios prestados, calcular honorarios, generar facturas.</p>
        <p><strong>Rutas:</strong> /routes/honorarios/</p>
    </div>

    <div class="module-box">
        <h4>📋 Módulo de Recetas</h4>
        <p><strong>Propósito:</strong> Generación de prescripciones médicas.</p>
        <p><strong>Funcionalidades:</strong> Crear recetas digitales, asociar medicamentos, instrucciones de uso.</p>
        <p><strong>Rutas:</strong> /routes/recetas/</p>
    </div>

    <div class="module-box">
        <h4>🏥 Módulo de Hospitalizaciones</h4>
        <p><strong>Propósito:</strong> Seguimiento de mascotas internadas.</p>
        <p><strong>Funcionalidades:</strong> Registrar ingresos, control diario de pacientes, fechas de alta.</p>
        <p><strong>Rutas:</strong> /routes/hospitalizaciones/</p>
    </div>

    <div class="module-box">
        <h4>👥 Módulo de Usuarios</h4>
        <p><strong>Propósito:</strong> Control de acceso y roles.</p>
        <p><strong>Funcionalidades:</strong> Crear usuarios, asignar roles (admin, médico, recepción), autenticación segura.</p>
        <p><strong>Rutas:</strong> /routes/usuarios/</p>
    </div>
</div>

<!-- DATA MODELS -->
<div class="section page-break">
    <h1 id="data-models">💾 Modelos de Datos</h1>
    
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
            <td>Mascotas</td>
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
</div>

<!-- API -->
<div class="section page-break">
    <h1 id="api">🔌 API y Rutas</h1>
    
    <div class="section-title">
        <h3>Estructura de Rutas</h3>
    </div>

    <p>El proyecto organiza sus rutas de manera modular. Las principales rutas incluyen:</p>

    <h3>Rutas Web (web.php)</h3>
    <p>Rutas de la interfaz web tradicional, con autenticación y autorización.</p>

    <h3>Rutas API (api.php)</h3>
    <p>Endpoints RESTful para integración y consumo desde aplicaciones cliente.</p>

    <h3>Módulos de Rutas</h3>
    <ul>
        <li><strong>citas/</strong> - Endpoints para gestión de citas</li>
        <li><strong>mascotas/</strong> - Endpoints para mascotas</li>
        <li><strong>propietarios/</strong> - Endpoints para propietarios</li>
        <li><strong>profesionales/</strong> - Endpoints para profesionales</li>
        <li><strong>honorarios/</strong> - Endpoints para facturación</li>
        <li><strong>recetas/</strong> - Endpoints para recetas</li>
        <li><strong>hospitalizaciones/</strong> - Endpoints para internamiento</li>
        <li><strong>usuarios/</strong> - Endpoints para gestión de usuarios</li>
    </ul>

    <div class="section-title">
        <h3>Métodos HTTP Estándar</h3>
    </div>

    <table>
        <tr>
            <th>Método</th>
            <th>Operación</th>
            <th>Ejemplo</th>
        </tr>
        <tr>
            <td><strong>GET</strong></td>
            <td>Obtener datos</td>
            <td>GET /api/citas</td>
        </tr>
        <tr>
            <td><strong>POST</strong></td>
            <td>Crear recurso</td>
            <td>POST /api/citas</td>
        </tr>
        <tr>
            <td><strong>PUT</strong></td>
            <td>Actualizar recurso</td>
            <td>PUT /api/citas/{id}</td>
        </tr>
        <tr>
            <td><strong>DELETE</strong></td>
            <td>Eliminar recurso</td>
            <td>DELETE /api/citas/{id}</td>
        </tr>
    </table>
</div>

<!-- DEPLOYMENT -->
<div class="section page-break">
    <h1 id="deployment">🚀 Despliegue</h1>
    
    <div class="section-title">
        <h3>Entornos Soportados</h3>
    </div>

    <div class="module-box">
        <h4>Desarrollo Local</h4>
        <p>Para ejecutar el proyecto localmente, se requiere PHP 8.2+, Composer y una base de datos PostgreSQL.</p>
        <p><strong>Comando:</strong> <code>php artisan serve</code></p>
    </div>

    <div class="module-box">
        <h4>Azure/Render.io (render.yaml)</h4>
        <p>Configuración para despliegue en plataformas cloud modernas.</p>
    </div>

    <div class="module-box">
        <h4>InfinityFree (veterinaria-infinityfree/)</h4>
        <p>Directorio con configuración específica para hospedaje compartido con PHP/MySQL.</p>
    </div>

    <div class="section-title">
        <h3>Configuración Necesaria</h3>
    </div>

    <ul class="feature-list">
        <li>Archivo <code>.env</code> con variables de entorno</li>
        <li>Base de datos PostgreSQL configurada</li>
        <li>Migraciones ejecutadas: <code>php artisan migrate</code></li>
        <li>Seeders para datos iniciales (opcional)</li>
        <li>npm install y npm run build para assets</li>
    </ul>

    <div class="section-title">
        <h3>Estructura de Directorios Clave</h3>
    </div>

    <table>
        <tr>
            <th>Directorio</th>
            <th>Descripción</th>
        </tr>
        <tr>
            <td><strong>/Veterinaria</strong></td>
            <td>Aplicación principal con código fuente</td>
        </tr>
        <tr>
            <td><strong>/veterinaria-infinityfree</strong></td>
            <td>Build compilado para hospedaje compartido</td>
        </tr>
        <tr>
            <td><strong>/temp-working-fix</strong></td>
            <td>Archivos temporales de desarrollo</td>
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

// Generate PDF
$pdf = Pdf::loadHTML($html);
$pdf->setPaper('A4', 'portrait');
$pdf->setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);

// Save to public directory
$filename = 'documentacion-producto-' . date('Y-m-d-His') . '.pdf';
$path = public_path($filename);

$pdf->save($path);

// Also try to save to a more accessible location
$alternativePath = __DIR__ . '/' . $filename;
file_put_contents($alternativePath, $pdf->output());

echo "✅ PDF generado exitosamente!\n";
echo "📁 Ubicación principal: " . $path . "\n";
echo "📁 Ubicación alternativa: " . $alternativePath . "\n";
echo "📊 Nombre del archivo: " . $filename . "\n";
