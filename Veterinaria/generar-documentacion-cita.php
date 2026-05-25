<?php

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'sans-serif');
$options->set('dpi', 150);

$dompdf = new Dompdf($options);

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
        .code-block {
            background-color: #f4f4f4;
            border-left: 4px solid #2c5aa0;
            padding: 15px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow: hidden;
            word-wrap: break-word;
            page-break-inside: avoid;
        }
        .method-box {
            background-color: #fafbfc;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            page-break-inside: avoid;
        }
        .method-box h4 {
            margin-top: 0;
            color: #2c5aa0;
        }
        .method-box .param {
            background-color: #f9f9f9;
            padding: 10px;
            margin: 8px 0;
            border-left: 3px solid #4a7ba7;
        }
        .method-box .return {
            background-color: #e8f5e9;
            padding: 10px;
            margin: 8px 0;
            border-left: 3px solid #4caf50;
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
        ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
        .badge {
            display: inline-block;
            background-color: #2c5aa0;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            margin-right: 5px;
        }
        .note {
            background-color: #fffacd;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin: 15px 0;
        }
        .flow-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 15px 0;
        }
    </style>
</head>
<body>

<!-- COVER PAGE -->
<div class="cover-page">
    <h1>DOCUMENTACIÓN TÉCNICA PROFUNDA</h1>
    <h2>CitaController - Sistema de Gestión Veterinaria</h2>
    <div class="meta">
        <p><strong>Módulo:</strong> Gestión de Citas</p>
        <p><strong>Archivo:</strong> app/Http/Controllers/Cita/CitaController.php</p>
        <p><strong>Versión:</strong> 1.0</p>
        <p><strong>Framework:</strong> Laravel 12 | PHP 8.2+</p>
        <p><strong>Fecha:</strong> 15 de Diciembre de 2025</p>
    </div>
</div>

<!-- TABLE OF CONTENTS -->
<div class="toc">
    <h2>TABLA DE CONTENIDOS</h2>
    <ul>
        <li>1. Introducción y Propósito</li>
        <li>2. Análisis de Dependencias</li>
        <li>3. Métodos del Controlador</li>
        <li>4. Método index() - Análisis Profundo</li>
        <li>5. Método store() - Análisis Profundo</li>
        <li>6. Método show() - Análisis Profundo</li>
        <li>7. Métodos Incompletos (create, edit, update, destroy)</li>
        <li>8. Validación y Seguridad</li>
        <li>9. Manejo de Errores</li>
        <li>10. Optimizaciones y Mejoras</li>
        <li>11. Flujos de Ejecución</li>
        <li>12. Testing y Recomendaciones</li>
    </ul>
</div>

<!-- INTRODUCCIÓN -->
<div class="section">
    <h1>1. INTRODUCCIÓN Y PROPÓSITO</h1>
    
    <div class="section-title">
        <h3>¿Qué es CitaController?</h3>
    </div>
    
    <p>CitaController es un controlador REST que gestiona todas las operaciones relacionadas con <strong>citas veterinarias</strong> en la clínica. Implementa el patrón CRUD (Create, Read, Update, Delete) aunque actualmente solo tiene implementados parcialmente los métodos.</p>
    
    <div class="section-title">
        <h3>Responsabilidades Principales</h3>
    </div>
    
    <ul>
        <li><strong>Listar Citas:</strong> Obtener todas las citas categorizadas por fecha</li>
        <li><strong>Crear Citas:</strong> Registrar nuevas citas con validación</li>
        <li><strong>Obtener Detalles:</strong> Recuperar información completa de una cita</li>
        <li><strong>Actualizar Citas:</strong> Modificar datos de citas existentes (no implementado)</li>
        <li><strong>Eliminar Citas:</strong> Cancelar/eliminar citas (no implementado)</li>
    </ul>

    <div class="section-title">
        <h3>Ubicación en la Arquitectura</h3>
    </div>
    
    <p>El controlador se ubica en la capa <strong>Controllers</strong> de la arquitectura MVC:</p>
    
    <div class="code-block">
Arquitectura de Carpetas:
app/
├── Http/
│   └── Controllers/
│       └── Cita/
│           └── CitaController.php ← Está aquí
├── Models/
│   ├── Cita/
│   │   └── Cita.php
│   ├── Mascota/
│   │   └── Mascota.php
│   └── Profesional.php
routes/
└── citas/
    └── index.php
    </div>
</div>

<!-- DEPENDENCIAS -->
<div class="section page-break">
    <h1>2. ANÁLISIS DE DEPENDENCIAS</h1>
    
    <div class="section-title">
        <h3>Imports y Namespaces</h3>
    </div>
    
    <table>
        <tr>
            <th>Import</th>
            <th>Propósito</th>
            <th>Ubicación</th>
        </tr>
        <tr>
            <td><code>App\Http\Controllers\Controller</code></td>
            <td>Clase base del controlador</td>
            <td>app/Http/Controllers/Controller.php</td>
        </tr>
        <tr>
            <td><code>Illuminate\Http\Request</code></td>
            <td>Manejo de solicitudes HTTP</td>
            <td>Laravel Framework</td>
        </tr>
        <tr>
            <td><code>Carbon\Carbon</code></td>
            <td>Manipulación de fechas/horas</td>
            <td>Librería Carbon</td>
        </tr>
        <tr>
            <td><code>App\Models\Cita\Cita</code></td>
            <td>Modelo de Cita</td>
            <td>app/Models/Cita/Cita.php</td>
        </tr>
        <tr>
            <td><code>App\Models\Mascota\Mascota</code></td>
            <td>Modelo de Mascota</td>
            <td>app/Models/Mascota/Mascota.php</td>
        </tr>
        <tr>
            <td><code>App\Models\Profesional</code></td>
            <td>Modelo de Profesional</td>
            <td>app/Models/Profesional.php</td>
        </tr>
    </table>

    <div class="section-title">
        <h3>Relaciones Entre Modelos</h3>
    </div>
    
    <div class="flow-box">
        <strong>Flujo de Relaciones:</strong><br><br>
        Cita → Mascota (belongsTo)<br>
        Mascota → Propietario (belongsTo)<br>
        Cita → Profesional (belongsTo)<br>
        <br>
        <strong>Eager Loading:</strong><br>
        El controlador carga las relaciones con: <code>with(['mascota.propietario', 'profesional'])</code><br>
        Esto evita el problema N+1 en consultas.
    </div>
</div>

<!-- MÉTODOS -->
<div class="section page-break">
    <h1>3. MÉTODOS DEL CONTROLADOR</h1>
    
    <div class="section-title">
        <h3>Resumen de Métodos</h3>
    </div>
    
    <table>
        <tr>
            <th>Método</th>
            <th>Ruta HTTP</th>
            <th>Estado</th>
            <th>Propósito</th>
        </tr>
        <tr>
            <td><code>index()</code></td>
            <td>GET /citas</td>
            <td style="color: green;"><strong>✓ Completo</strong></td>
            <td>Listar todas las citas categorizadas</td>
        </tr>
        <tr>
            <td><code>create()</code></td>
            <td>GET /citas/create</td>
            <td style="color: red;">✗ Vacío</td>
            <td>Mostrar formulario de creación</td>
        </tr>
        <tr>
            <td><code>store()</code></td>
            <td>POST /citas</td>
            <td style="color: green;"><strong>✓ Completo</strong></td>
            <td>Guardar nueva cita en BD</td>
        </tr>
        <tr>
            <td><code>show()</code></td>
            <td>GET /citas/{id}</td>
            <td style="color: green;"><strong>✓ Completo</strong></td>
            <td>Obtener detalles de una cita</td>
        </tr>
        <tr>
            <td><code>edit()</code></td>
            <td>GET /citas/{id}/edit</td>
            <td style="color: red;">✗ Vacío</td>
            <td>Mostrar formulario de edición</td>
        </tr>
        <tr>
            <td><code>update()</code></td>
            <td>PUT /citas/{id}</td>
            <td style="color: red;">✗ Vacío</td>
            <td>Actualizar cita existente</td>
        </tr>
        <tr>
            <td><code>destroy()</code></td>
            <td>DELETE /citas/{id}</td>
            <td style="color: red;">✗ Vacío</td>
            <td>Eliminar/cancelar cita</td>
        </tr>
    </table>
</div>

<!-- MÉTODO INDEX -->
<div class="section page-break">
    <h1>4. MÉTODO INDEX() - ANÁLISIS PROFUNDO</h1>
    
    <div class="method-box">
        <h4> Firma del Método</h4>
        <div class="code-block">public function index()
{
    // ... código
}</div>
        
        <h3>Propósito</h3>
        <p>Obtener todas las citas del sistema categorizadas por estado temporal (hoy, próximas, pasadas) y devolverlas a la vista de recepción.</p>

        <h3>Flujo de Ejecución</h3>
        <div class="code-block">
1. Cargar listados de mascotas y profesionales
2. Definir fecha actual (hoy)
3. Crear query base de citas con eager loading
4. Clonar query para obtener 4 colecciones diferentes:
   - citasTodas: Todas las citas
   - citasHoy: Citas del día actual
   - citasProximas: Citas futuras
   - citasPasadas: Citas históricas
5. Retornar vista con datos compactados
        </div>

        <h3>Desglose de Código</h3>
        
        <h4>1. Cargar Mascotas</h4>
        <div class="code-block">$mascotas = Mascota::orderBy('nombre')->get(['id_mascota','nombre']);</div>
        <div class="param">
            <strong>¿Qué hace?</strong> Obtiene todas las mascotas ordenadas alfabéticamente<br>
            <strong>Selección de Columnas:</strong> Solo id_mascota y nombre (optimización)<br>
            <strong>Uso:</strong> Llenar dropdown en formulario de citas
        </div>

        <h4>2. Cargar Profesionales</h4>
        <div class="code-block">$profesionales = Profesional::orderBy('nombre')->get(['rfc','nombre']);</div>
        <div class="param">
            <strong>¿Qué hace?</strong> Obtiene todos los veterinarios ordenados por nombre<br>
            <strong>Identificador Único:</strong> RFC (Registro Federal de Contribuyentes)<br>
            <strong>Uso:</strong> Llenar dropdown de profesionales disponibles
        </div>

        <h4>3. Definir Fecha Actual</h4>
        <div class="code-block">$hoy = Carbon::today();</div>
        <div class="param">
            <strong>¿Qué es Carbon?</strong> Librería para manejo de fechas en Laravel<br>
            <strong>Carbon::today():</strong> Retorna la fecha actual sin hora (00:00:00)<br>
            <strong>Ventaja:</strong> Comparaciones de fecha exacta sin problemas de hora
        </div>

        <h4>4. Query Base con Eager Loading</h4>
        <div class="code-block">$citasQuery = Cita::with(['mascota.propietario', 'profesional'])
    ->orderBy('fecha')
    ->orderBy('horario');</div>
        <div class="param">
            <strong>with():</strong> Eager loading de relaciones<br>
            <strong>mascota.propietario:</strong> Carga en cascada<br>
            <strong>Problema N+1 Evitado:</strong> Sin esto harían múltiples queries<br>
            <strong>Ordenamiento:</strong> Por fecha y luego por hora
        </div>

        <h4>5. Clonación de Query</h4>
        <div class="code-block">$citasTodas = (clone $citasQuery)->get();
$citasHoy = (clone $citasQuery)->whereDate('fecha', $hoy)->get();
$citasProximas = (clone $citasQuery)->whereDate('fecha', '>', $hoy)->get();
$citasPasadas = (clone $citasQuery)->whereDate('fecha', '<', $hoy)->get();</div>
        <div class="param">
            <strong>¿Por qué clone?</strong> Evitar que los where() afecten la query original<br>
            <strong>whereDate():</strong> Compara solo la fecha, ignora la hora<br>
            <strong>Resultado:</strong> 4 colecciones independientes con citas categorizadas
        </div>

        <h3>Retorno del Método</h3>
        <div class="return">
            <strong>Tipo:</strong> Vista Blade (view)<br>
            <strong>Ubicación:</strong> resources/views/dash/recepcion/citas.blade.php<br>
            <strong>Parámetros Pasados:</strong><br>
            - mascotas (Collection)<br>
            - profesionales (Collection)<br>
            - citasHoy (Collection)<br>
            - citasProximas (Collection)<br>
            - citasPasadas (Collection)<br>
            - citasTodas (Collection)
        </div>

        <h3>Complejidad y Performance</h3>
        <table>
            <tr>
                <th>Aspecto</th>
                <th>Análisis</th>
            </tr>
            <tr>
                <td>Queries SQL</td>
                <td>3 queries principales (Mascotas, Profesionales, Citas con relaciones)</td>
            </tr>
            <tr>
                <td>Eager Loading</td>
                <td>✓ Optimizado (evita N+1)</td>
            </tr>
            <tr>
                <td>Índices Recomendados</td>
                <td>fecha, profesional_id, mascota_id en tabla citas</td>
            </tr>
            <tr>
                <td>Escalabilidad</td>
                <td>Con 10K+ citas podría necesitar paginación</td>
            </tr>
        </table>
    </div>
</div>

<!-- MÉTODO STORE -->
<div class="section page-break">
    <h1>5. MÉTODO STORE() - ANÁLISIS PROFUNDO</h1>
    
    <div class="method-box">
        <h4>Firma del Método</h4>
        <div class="code-block">public function store(Request $request)
{
    // ... validación y creación
}</div>
        
        <h3>Propósito</h3>
        <p>Validar datos de solicitud HTTP y crear una nueva cita en la base de datos.</p>

        <h3>Flujo de Ejecución</h3>
        <div class="code-block">
1. Validar datos de entrada con reglas específicas
2. Si validación falla → Retornar errores (Laravel automático)
3. Parsear fecha a formato Y-m-d
4. Crear registro en tabla citas
5. Retornar respuesta JSON con código 201
        </div>

        <h3>Validación de Datos</h3>
        
        <table>
            <tr>
                <th>Campo</th>
                <th>Reglas</th>
                <th>Descripción</th>
            </tr>
            <tr>
                <td>id_mascota</td>
                <td>required|exists:mascota,id_mascota</td>
                <td>Debe existir mascota en BD con ese ID</td>
            </tr>
            <tr>
                <td>rfc_profesional</td>
                <td>required|exists:profesional,rfc</td>
                <td>Debe existir profesional con ese RFC</td>
            </tr>
            <tr>
                <td>tipo_servicio</td>
                <td>nullable|string|max:100</td>
                <td>Opcional, máximo 100 caracteres</td>
            </tr>
            <tr>
                <td>tipo_cita</td>
                <td>required|in:Consulta,Urgencia,Cirugía,Estética</td>
                <td>Solo valores específicos permitidos</td>
            </tr>
            <tr>
                <td>tarifa</td>
                <td>nullable|numeric|min:0</td>
                <td>Opcional, número positivo</td>
            </tr>
            <tr>
                <td>peso_mascota</td>
                <td>nullable|numeric|min:0</td>
                <td>Opcional, peso en kg sin negativos</td>
            </tr>
            <tr>
                <td>fecha</td>
                <td>required|date</td>
                <td>Fecha válida, formato YYYY-MM-DD</td>
            </tr>
            <tr>
                <td>horario</td>
                <td>required|date_format:H:i</td>
                <td>Hora en formato 24h, ej: 14:30</td>
            </tr>
            <tr>
                <td>diagnostico</td>
                <td>nullable|string</td>
                <td>Diagnóstico veterinario (opcional)</td>
            </tr>
            <tr>
                <td>observaciones</td>
                <td>nullable|string</td>
                <td>Notas adicionales (opcional)</td>
            </tr>
            <tr>
                <td>estado</td>
                <td>required|in:Programada,Completada,Cancelada</td>
                <td>Estado actual de la cita</td>
            </tr>
        </table>

        <h3>Transformación de Datos</h3>
        
        <div class="code-block">$data['fecha'] = Carbon::parse($data['fecha'])->format('Y-m-d');</div>
        
        <div class="param">
            <strong>¿Por qué?</strong> Asegurar formato consistente en BD<br>
            <strong>Carbon::parse():</strong> Interpreta varios formatos de fecha<br>
            <strong>format():</strong> Estandariza a YYYY-MM-DD<br>
            <strong>Ejemplo:</strong> "15/12/2025" → "2025-12-15"
        </div>

        <h3>Creación de Registro</h3>
        
        <div class="code-block">$cita = Cita::create($data);</div>
        
        <div class="param">
            <strong>Modelo Mass Assignment:</strong> Cita.php debe permitir estos campos<br>
            <strong>Retorno:</strong> Instancia del modelo Cita creado<br>
            <strong>Evento Disparado:</strong> created (observable del modelo)
        </div>

        <h3>Respuesta JSON</h3>
        
        <div class="code-block">return response()->json([
    'success' => true,
    'message' => 'Cita creada correctamente',
    'data' => $cita
], 201);</div>
        
        <div class="return">
            <strong>Código HTTP 201:</strong> Recurso creado exitosamente<br>
            <strong>Estructura JSON:</strong><br>
            {<br>
            &nbsp;&nbsp;"success": true,<br>
            &nbsp;&nbsp;"message": "Cita creada correctamente",<br>
            &nbsp;&nbsp;"data": { cita_object }<br>
            }<br>
            <strong>Cliente puede usar:</strong> Response.status === 201 para detectar creación
        </div>

        <h3>Potenciales Problemas</h3>
        
        <ul>
            <li><strong>Sin validación de conflictos:</strong> No verifica si profesional ya tiene cita en ese horario</li>
            <li><strong>Sin validación de horario:</strong> No comprueba que la hora esté dentro de horario laboral</li>
            <li><strong>Sin validación de capacidad:</strong> No limita citas por profesional por día</li>
            <li><strong>Fechas pasadas:</strong> Permite crear citas con fecha retroactiva</li>
        </ul>
    </div>
</div>

<!-- MÉTODO SHOW -->
<div class="section page-break">
    <h1>6. MÉTODO SHOW() - ANÁLISIS PROFUNDO</h1>
    
    <div class="method-box">
        <h4>Firma del Método</h4>
        <div class="code-block">public function show(string $id)
{
    // ... búsqueda y retorno
}</div>
        
        <h3>Propósito</h3>
        <p>Obtener detalles completos de una cita específica incluyendo mascota, propietario y profesional.</p>

        <h3>Estructura de Ejecución</h3>
        
        <div class="code-block">1. Try: Intentar obtener la cita
2. Buscar cita por ID con eager loading
3. If: Cita no existe → Retornar 404
4. Else: Retornar JSON con datos
5. Catch: Si hay excepción → Retornar error 500</div>

        <h3>Búsqueda de Cita</h3>
        
        <div class="code-block">$cita = Cita::with(['mascota.propietario', 'profesional'])->find($id);</div>
        
        <div class="param">
            <strong>find($id):</strong> Busca por primary key (id)<br>
            <strong>with():</strong> Eager loading de relaciones<br>
            <strong>Retorno:</strong> Objeto Cita o null
        </div>

        <h3>Validación de Existencia</h3>
        
        <div class="code-block">if (!$cita) {
    return response()->json([
        'success' => false,
        'message' => 'Cita no encontrada'
    ], 404);
}</div>
        
        <div class="param">
            <strong>Código 404:</strong> Recurso no encontrado<br>
            <strong>Respuesta JSON:</strong> Estructura consistente<br>
            <strong>¿Qué hacer?</strong> Cliente puede redirigir a 404 o mostrar mensaje
        </div>

        <h3>Respuesta Exitosa</h3>
        
        <div class="code-block">return response()->json([
    'success' => true,
    'data' => $cita,
    'message' => 'Cita obtenida correctamente'
]);</div>
        
        <div class="return">
            <strong>Código HTTP:</strong> 200 (implícito)<br>
            <strong>Estructura:</strong> success, data, message<br>
            <strong>Datos retornados:</strong> Cita completa con relaciones cargadas
        </div>

        <h3>Manejo de Excepciones</h3>
        
        <div class="code-block">catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'message' => 'Error al obtener la cita: ' . $e->getMessage()
    ], 500);
}</div>
        
        <div class="param">
            <strong>Código 500:</strong> Error interno del servidor<br>
            <strong>¿Cuándo?</strong> Problemas de BD, conexión, etc.<br>
            <strong>Mensaje:</strong> Incluye detalles del error (en producción, cambiar)<br>
            <strong>Seguridad:</strong> No exponer mensajes de error en producción
        </div>

        <h3>Datos Retornados (Estructura JSON)</h3>
        
        <div class="code-block">{
  "success": true,
  "data": {
    "id": 1,
    "id_mascota": 5,
    "rfc_profesional": "VET123456XYZ",
    "tipo_servicio": "Consulta General",
    "tipo_cita": "Consulta",
    "tarifa": 500.00,
    "peso_mascota": 15.5,
    "fecha": "2025-12-15",
    "horario": "14:30",
    "diagnostico": "Infección urinaria",
    "observaciones": "Prescribir antibióticos",
    "estado": "Completada",
    "created_at": "2025-12-15T10:30:00Z",
    "updated_at": "2025-12-15T16:45:00Z",
    "mascota": {
      "id_mascota": 5,
      "nombre": "Firulais",
      "propietario": { ... }
    },
    "profesional": {
      "rfc": "VET123456XYZ",
      "nombre": "Dr. García"
    }
  },
  "message": "Cita obtenida correctamente"
}</div>
    </div>
</div>

<!-- MÉTODOS INCOMPLETOS -->
<div class="section page-break">
    <h1>7. MÉTODOS INCOMPLETOS (create, edit, update, destroy)</h1>
    
    <div class="section-title">
        <h3>Estado Actual</h3>
    </div>
    
    <p>Los siguientes métodos están vacios y necesitan implementación:</p>

    <div class="method-box">
        <h4>create()</h4>
        <div class="code-block">public function create(Request $request)
{
    //
}</div>
        <p><strong>Propósito:</strong> Retornar vista con formulario para crear nueva cita</p>
        <p><strong>Implementación sugerida:</strong></p>
        <div class="code-block">public function create()
{
    $mascotas = Mascota::orderBy('nombre')->get();
    $profesionales = Profesional::orderBy('nombre')->get();
    return view('dash.recepcion.citas.create', compact('mascotas', 'profesionales'));
}</div>
    </div>

    <div class="method-box">
        <h4>edit()</h4>
        <div class="code-block">public function edit(string $id)
{
    //
}</div>
        <p><strong>Propósito:</strong> Retornar vista con formulario para editar cita existente</p>
        <p><strong>Implementación sugerida:</strong></p>
        <div class="code-block">public function edit(string $id)
{
    $cita = Cita::findOrFail($id);
    $mascotas = Mascota::orderBy('nombre')->get();
    $profesionales = Profesional::orderBy('nombre')->get();
    return view('dash.recepcion.citas.edit', compact('cita', 'mascotas', 'profesionales'));
}</div>
    </div>

    <div class="method-box">
        <h4>update()</h4>
        <div class="code-block">public function update(Request $request, string $id)
{
    //
}</div>
        <p><strong>Propósito:</strong> Actualizar cita existente con validación</p>
        <p><strong>Implementación sugerida:</strong></p>
        <div class="code-block">public function update(Request $request, string $id)
{
    $cita = Cita::findOrFail($id);
    
    $data = $request->validate([
        'id_mascota' => 'sometimes|required|exists:mascota,id_mascota',
        'rfc_profesional' => 'sometimes|required|exists:profesional,rfc',
        'tipo_cita' => 'sometimes|required|in:Consulta,Urgencia,Cirugía,Estética',
        'estado' => 'sometimes|required|in:Programada,Completada,Cancelada',
        // ... otros campos
    ]);
    
    if(isset($data['fecha'])) {
        $data['fecha'] = Carbon::parse($data['fecha'])->format('Y-m-d');
    }
    
    $cita->update($data);
    
    return response()->json([
        'success' => true,
        'message' => 'Cita actualizada correctamente',
        'data' => $cita
    ]);
}</div>
    </div>

    <div class="method-box">
        <h4>destroy()</h4>
        <div class="code-block">public function destroy(string $id)
{
    //
}</div>
        <p><strong>Propósito:</strong> Cancelar/eliminar cita</p>
        <p><strong>Implementación sugerida:</strong></p>
        <div class="code-block">public function destroy(string $id)
{
    $cita = Cita::findOrFail($id);
    
    // Opción 1: Soft delete (borrado lógico)
    $cita->delete();
    
    // O Opción 2: Cambiar estado (mejor para auditoría)
    // $cita->update(['estado' => 'Cancelada']);
    
    return response()->json([
        'success' => true,
        'message' => 'Cita cancelada correctamente'
    ]);
}</div>
    </div>
</div>

<!-- VALIDACIÓN Y SEGURIDAD -->
<div class="section page-break">
    <h1>8. VALIDACIÓN Y SEGURIDAD</h1>
    
    <div class="section-title">
        <h3>Validación de Entrada</h3>
    </div>
    
    <p>Laravel valida automáticamente los datos en el método store():</p>
    
    <ul>
        <li><strong>Exists Validator:</strong> Comprueba referential integrity</li>
        <li><strong>In Validator:</strong> Restringe valores a opciones válidas</li>
        <li><strong>Date Validator:</strong> Asegura formato de fecha correcto</li>
        <li><strong>Date Format:</strong> Valida exactamente H:i</li>
    </ul>

    <div class="section-title">
        <h3>Protecciones Implementadas</h3>
    </div>
    
    <div class="note">
        <strong>CSRF Protection:</strong> Laravel protege contra ataques CSRF automáticamente<br>
        <strong> SQL Injection:</strong> Eloquent ORM usa prepared statements<br>
        <strong> Type Hinting:</strong> string $id asegura tipo correcto<br>
        <strong> Exception Handling:</strong> Try-catch en show() captura errores
    </div>

    <div class="section-title">
        <h3>Vulnerabilidades Detectadas</h3>
    </div>
    
    <ul>
        <li><strong>Sin autenticación:</strong> No verifica que usuario esté logueado</li>
        <li><strong>Sin autorización:</strong> No comprueba permisos (rol admin/médico/recepción)</li>
        <li><strong>Sin rate limiting:</strong> No limita cantidad de requests</li>
        <li><strong>Sin auditoría:</strong> No registra quién hizo cambios</li>
        <li><strong>Exposición de errores:</strong> En show() expone mensajes de excepción</li>
    </ul>

    <div class="section-title">
        <h3>Mejoras Recomendadas</h3>
    </div>
    
    <div class="code-block">// Agregar en constructor
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('can:view-citas');
}

// O usar gate/policy en cada método
public function show(string $id)
{
    $cita = Cita::findOrFail($id);
    $this->authorize('view', $cita);
    // ...
}</div>
</div>

<!-- MANEJO DE ERRORES -->
<div class="section page-break">
    <h1>9. MANEJO DE ERRORES</h1>
    
    <div class="section-title">
        <h3>Errores en Método store()</h3>
    </div>
    
    <table>
        <tr>
            <th>Escenario</th>
            <th>Respuesta</th>
            <th>Código</th>
        </tr>
        <tr>
            <td>Validación fallida</td>
            <td>422 Unprocessable Entity + errores</td>
            <td>422</td>
        </tr>
        <tr>
            <td>Mascota no existe</td>
            <td>Validación falla (exists rule)</td>
            <td>422</td>
        </tr>
        <tr>
            <td>Profesional no existe</td>
            <td>Validación falla (exists rule)</td>
            <td>422</td>
        </tr>
        <tr>
            <td>Creación exitosa</td>
            <td>JSON con datos + código</td>
            <td>201</td>
        </tr>
    </table>

    <div class="section-title">
        <h3>Errores en Método show()</h3>
    </div>
    
    <table>
        <tr>
            <th>Escenario</th>
            <th>Respuesta</th>
            <th>Código</th>
        </tr>
        <tr>
            <td>Cita no existe</td>
            <td>JSON con error</td>
            <td>404</td>
        </tr>
        <tr>
            <td>Error BD/Conexión</td>
            <td>JSON con excepción</td>
            <td>500</td>
        </tr>
        <tr>
            <td>Cita encontrada</td>
            <td>JSON con datos completos</td>
            <td>200</td>
        </tr>
    </table>

    <div class="section-title">
        <h3>Ejemplo de Error JSON en Producción</h3>
    </div>
    
    <div class="code-block">{
  "success": false,
  "message": "Cita no encontrada",
  "errors": {}
}</div>

    <div class="note">
        <strong> IMPORTANTE:</strong> En archivo config/app.php, APP_DEBUG debe ser false en producción para no exponer detalles internos.
    </div>
</div>

<!-- OPTIMIZACIONES -->
<div class="section page-break">
    <h1>10. OPTIMIZACIONES Y MEJORAS</h1>
    
    <div class="section-title">
        <h3>Problema N+1 - Ya Optimizado ✓</h3>
    </div>
    
    <div class="code-block">//  MAL: Causa múltiples queries
foreach($citas as $cita) {
    echo $cita->mascota->nombre; // Query por cada cita
    echo $cita->profesional->nombre; // Otra query por cita
}

// ✓ BIEN: Una sola query (implementado en controlador)
$citas = Cita::with(['mascota', 'profesional'])->get();</div>

    <div class="section-title">
        <h3>Paginación Recomendada</h3>
    </div>
    
    <div class="code-block">// Modificar index() para paginar
public function index()
{
    $citasHoy = Cita::whereDate('fecha', today())
        ->with(['mascota.propietario', 'profesional'])
        ->paginate(15);
    
    return view('dash.recepcion.citas', compact('citasHoy'));
}</div>

    <div class="section-title">
        <h3>Caché para Listados Estáticos</h3>
    </div>
    
    <div class="code-block">// Mascotas y profesionales cambian raramente
public function index()
{
    $mascotas = Cache::remember('mascotas', 60, function() {
        return Mascota::orderBy('nombre')->get(['id_mascota', 'nombre']);
    });
    
    $profesionales = Cache::remember('profesionales', 60, function() {
        return Profesional::orderBy('nombre')->get(['rfc', 'nombre']);
    });
    
    // ...
}</div>

    <div class="section-title">
        <h3>Validación de Horarios Disponibles</h3>
    </div>
    
    <div class="code-block">// Antes de crear cita, verificar disponibilidad
$conflicto = Cita::where('rfc_profesional', $data['rfc_profesional'])
    ->where('fecha', $data['fecha'])
    ->where('horario', $data['horario'])
    ->where('estado', '!=', 'Cancelada')
    ->exists();

if($conflicto) {
    return response()->json([
        'success' => false,
        'message' => 'Horario no disponible'
    ], 422);
}</div>

    <div class="section-title">
        <h3>Índices de Base de Datos</h3>
    </div>
    
    <div class="code-block">// Agregar a migración de citas
Schema::create('cita', function (Blueprint $table) {
    // ... campos
    $table->index('fecha');
    $table->index('rfc_profesional');
    $table->index('id_mascota');
    $table->index(['rfc_profesional', 'fecha']); // Índice compuesto
});</div>
</div>

<!-- FLUJOS -->
<div class="section page-break">
    <h1>11. FLUJOS DE EJECUCIÓN</h1>
    
    <div class="section-title">
        <h3>Flujo 1: Listar Citas (GET /citas)</h3>
    </div>
    
    <div class="code-block">
Usuario abre página de citas
    ↓
GET /citas dispara CitaController@index()
    ↓
index() carga mascotas y profesionales
    ↓
index() carga citas categorizadas por fecha
    ↓
Retorna vista con datos compactados
    ↓
Blade renderiza HTML con tablas de citas
    ↓
Usuario ve: Citas hoy, próximas, pasadas
    </div>

    <div class="section-title">
        <h3>Flujo 2: Crear Cita (POST /citas)</h3>
    </div>
    
    <div class="code-block">
Usuario completa formulario y presiona "Guardar"
    ↓
JavaScript (AJAX) envía POST /citas con datos
    ↓
CitaController@store() recibe Request
    ↓
validate() verifica reglas
    ↓
├─ SI falla: Retorna 422 con errores
    ↓
└─ SI pasa: Parsea fecha, crea registro
    ↓
Cita::create($data) inserta en BD
    ↓
Retorna JSON 201 con cita creada
    ↓
JavaScript recibe respuesta
    ↓
│├─ Mostrar succes message
    │├─ Limpiar formulario
    │└─ Recargar tabla de citas
    </div>

    <div class="section-title">
        <h3>Flujo 3: Ver Detalles (GET /citas/{id})</h3>
    </div>
    
    <div class="code-block">
Usuario hace clic en una cita
    ↓
GET /citas/5 dispara CitaController@show(5)
    ↓
show() busca Cita con ID 5
    ↓
├─ NO existe: Retorna JSON 404
    ↓
└─ Existe: Carga relaciones (mascota, profesional)
    ↓
Retorna JSON 200 con datos completos
    ↓
JavaScript recibe datos
    ↓
Muestra modal/página con detalles
    </div>
</div>

<!-- TESTING -->
<div class="section page-break">
    <h1>12. TESTING Y RECOMENDACIONES</h1>
    
    <div class="section-title">
        <h3>Tests Unitarios Recomendados (Pest)</h3>
    </div>
    
    <div class="code-block">// tests/Feature/CitaControllerTest.php
use Tests\TestCase;

class CitaControllerTest extends TestCase
{
    /** @test */
    public function puede_listar_citas()
    {
        $response = $this->get('/citas');
        $response->assertStatus(200);
    }

    /** @test */
    public function puede_crear_cita()
    {
        $data = [
            'id_mascota' => 1,
            'rfc_profesional' => 'VET123',
            'tipo_cita' => 'Consulta',
            'fecha' => '2025-12-20',
            'horario' => '14:00',
            'estado' => 'Programada'
        ];
        
        $response = $this->post('/citas', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('cita', $data);
    }

    /** @test */
    public function puede_obtener_cita()
    {
        $cita = Cita::factory()->create();
        
        $response = $this->get("/citas/{$cita->id}");
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }
}</div>

    <div class="section-title">
        <h3>Recomendaciones Finales</h3>
    </div>
    
    <ul>
        <li>✓ Completar métodos create(), edit(), update(), destroy()</li>
        <li>✓ Agregar autenticación y autorización</li>
        <li>✓ Implementar validación de horarios disponibles</li>
        <li>✓ Agregar paginación para grandes volúmenes</li>
        <li>✓ Implementar caché para datos estáticos</li>
        <li>✓ Agregar logging/auditoría de cambios</li>
        <li>✓ Escribir tests unitarios e integración</li>
        <li>✓ Documentar API con Swagger/OpenAPI</li>
        <li>✓ Configurar rate limiting</li>
        <li>✓ Usar Form Requests para validación reutilizable</li>
    </ul>
</div>

<!-- FOOTER -->
<div class="footer">
    <p><strong>Documentación Técnica - CitaController</strong></p>
    <p>Sistema de Gestión Veterinaria | 15 de Diciembre de 2025</p>
    <p>© 2025 - Todos los derechos reservados</p>
</div>

</body>
</html>
HTML;

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = 'CitaController-Documentacion-Tecnica-Profunda-' . date('Y-m-d-His') . '.pdf';
$filepath = __DIR__ . DIRECTORY_SEPARATOR . $filename;
$publicPath = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $filename;

file_put_contents($filepath, $dompdf->output());
if (is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'public')) {
    file_put_contents($publicPath, $dompdf->output());
}

echo "✅ Documentación técnica profunda generada!\n";
echo "📁 Archivo: " . $filename . "\n";
echo "📊 Tamaño: " . round(filesize($filepath) / 1024, 2) . " KB\n";
