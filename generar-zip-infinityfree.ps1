# Script para generar ZIP optimizado para InfinityFree
# Toma la carpeta Veterinaria como base y genera veterinaria-infinityfree2.zip

param(
    [string]$SourceDir = "Veterinaria",
    [string]$TargetDir = "veterinaria-infinityfree2",
    [string]$ZipName = "veterinaria-infinityfree2.zip"
)

$CurrentDir = Get-Location
$SourcePath = Join-Path $CurrentDir $SourceDir
$TargetPath = Join-Path $CurrentDir $TargetDir
$ZipPath = Join-Path $CurrentDir $ZipName

Write-Host "=== GENERANDO ZIP PARA INFINITYFREE ===" -ForegroundColor Green
Write-Host "Directorio fuente: $SourcePath" -ForegroundColor Yellow
Write-Host "Directorio destino: $TargetPath" -ForegroundColor Yellow
Write-Host "Archivo ZIP: $ZipPath" -ForegroundColor Yellow
Write-Host ""

# Paso 1: Limpiar directorio destino si existe
if (Test-Path $TargetPath) {
    Write-Host "Eliminando directorio destino existente..." -ForegroundColor Cyan
    Remove-Item $TargetPath -Recurse -Force
}

# Paso 2: Copiar toda la estructura desde Veterinaria
Write-Host "Copiando estructura desde $SourceDir..." -ForegroundColor Cyan
Copy-Item $SourcePath $TargetPath -Recurse

# Paso 3: Eliminar archivos y carpetas innecesarios para producción
Write-Host "Limpiando archivos innecesarios para producción..." -ForegroundColor Cyan

$ItemsToRemove = @(
    # Archivos de desarrollo
    "composer.lock",
    "package-lock.json",
    "yarn.lock",
    ".env.example",
    ".gitignore",
    ".gitattributes",
    "README.md",
    "phpunit.xml",
    "vite.config.js",
    
    # Directorios de desarrollo
    "node_modules",
    "tests",
    ".git",
    ".vscode",
    ".idea",
    
    # Cache y logs (se recrearán)
    "storage\logs\*",
    "storage\framework\cache\*",
    "storage\framework\sessions\*",
    "storage\framework\views\*",
    "bootstrap\cache\*"
)

foreach ($item in $ItemsToRemove) {
    $itemPath = Join-Path $TargetPath $item
    if (Test-Path $itemPath) {
        Write-Host "  Eliminando: $item" -ForegroundColor DarkGray
        Remove-Item $itemPath -Recurse -Force -ErrorAction SilentlyContinue
    }
}

# Paso 4: Crear directorios necesarios que podrían haberse eliminado
Write-Host "Creando directorios necesarios..." -ForegroundColor Cyan
$DirsToCreate = @(
    "storage\logs",
    "storage\framework\cache\data",
    "storage\framework\sessions",
    "storage\framework\views",
    "bootstrap\cache"
)

foreach ($dir in $DirsToCreate) {
    $dirPath = Join-Path $TargetPath $dir
    if (-not (Test-Path $dirPath)) {
        Write-Host "  Creando: $dir" -ForegroundColor DarkGray
        New-Item $dirPath -ItemType Directory -Force | Out-Null
    }
}

# Paso 5: Crear archivo .htaccess optimizado para InfinityFree
Write-Host "Creando .htaccess optimizado..." -ForegroundColor Cyan
$htaccessContent = @"
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Forzar HTTPS (opcional)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Manejar rutas de Laravel
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Proteger archivos sensibles
<Files ".env">
    Order Allow,Deny
    Deny from all
</Files>

<Files "composer.json">
    Order Allow,Deny
    Deny from all
</Files>

<Files "composer.lock">
    Order Allow,Deny
    Deny from all
</Files>

# Headers de seguridad
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Compresión GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Cache de archivos estáticos
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/ico "access plus 1 month"
    ExpiresByType image/icon "access plus 1 month"
    ExpiresByType text/x-icon "access plus 1 month"
    ExpiresByType image/x-icon "access plus 1 month"
</IfModule>
"@

$htaccessPath = Join-Path $TargetPath ".htaccess"
$htaccessContent | Out-File -FilePath $htaccessPath -Encoding UTF8

# Paso 6: Crear archivo de configuración para InfinityFree
Write-Host "Creando guía de configuración..." -ForegroundColor Cyan
$configGuide = @"
GUÍA DE CONFIGURACIÓN PARA INFINITYFREE
======================================

1. SUBIR ARCHIVOS:
   - Sube TODO el contenido de esta carpeta a htdocs/ en InfinityFree
   - Asegúrate de que index.php esté en la raíz de htdocs/

2. CONFIGURAR BASE DE DATOS:
   - Crea una base de datos MySQL en InfinityFree
   - Importa tus tablas SQL
   - Actualiza el archivo .env con los datos de tu BD:
     DB_HOST=sql###.infinityfree.com
     DB_DATABASE=tu_base_datos
     DB_USERNAME=tu_usuario
     DB_PASSWORD=tu_contraseña

3. CONFIGURAR DOMINIO:
   - Actualiza APP_URL en .env con tu dominio
   - Ejemplo: APP_URL=https://tudominio.infinityfreeapp.com

4. PERMISOS:
   - storage/ debe tener permisos 755 o 777
   - bootstrap/cache/ debe tener permisos 755 o 777

5. VERIFICACIÓN:
   - Accede a tu sitio para verificar que funciona
   - Revisa que no hay errores 500
   - Prueba las funciones principales

ARCHIVOS IMPORTANTES:
- .env (configuración de BD y dominio)
- .htaccess (configurado para InfinityFree)
- storage/ (logs y cache)
- public/ (archivos estáticos)

¡Tu aplicación está lista para InfinityFree!
"@

$guidePath = Join-Path $TargetPath "CONFIGURACION_INFINITYFREE.txt"
$configGuide | Out-File -FilePath $guidePath -Encoding UTF8

# Paso 7: Optimizar vendor (mantener solo lo esencial)
$vendorPath = Join-Path $TargetPath "vendor"
if (Test-Path $vendorPath) {
    Write-Host "Optimizando directorio vendor..." -ForegroundColor Cyan
    
    # Eliminar archivos de documentación y tests del vendor
    $VendorFilesToRemove = @(
        "*/tests/*",
        "*/test/*", 
        "*/Test/*",
        "*/docs/*",
        "*/doc/*",
        "*/.git/*",
        "*/examples/*",
        "*/example/*",
        "*/*.md",
        "*/README*",
        "*/CHANGELOG*",
        "*/LICENSE*",
        "*/.github/*"
    )
    
    foreach ($pattern in $VendorFilesToRemove) {
        $fullPattern = Join-Path $vendorPath $pattern
        Get-ChildItem $vendorPath -Recurse | Where-Object {
            $_.Name -like ($pattern -split '/')[-1] -or 
            $_.FullName -like $fullPattern
        } | Remove-Item -Force -Recurse -ErrorAction SilentlyContinue
    }
}

# Paso 8: Eliminar ZIP anterior si existe
if (Test-Path $ZipPath) {
    Write-Host "Eliminando ZIP anterior..." -ForegroundColor Cyan
    Remove-Item $ZipPath -Force
}

# Paso 9: Crear ZIP optimizado
Write-Host "Creando archivo ZIP..." -ForegroundColor Cyan
try {
    # Usar .NET para crear ZIP con mejor compresión
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    [System.IO.Compression.ZipFile]::CreateFromDirectory($TargetPath, $ZipPath, [System.IO.Compression.CompressionLevel]::Optimal, $false)
    
    # Obtener información del archivo
    $zipInfo = Get-Item $ZipPath
    $zipSizeMB = [math]::Round($zipInfo.Length / 1MB, 2)
    
    Write-Host ""
    Write-Host "=== ZIP CREADO EXITOSAMENTE ===" -ForegroundColor Green
    Write-Host "Archivo: $ZipName" -ForegroundColor Yellow
    Write-Host "Tamaño: $zipSizeMB MB" -ForegroundColor Yellow
    Write-Host "Ubicación: $ZipPath" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "El ZIP está listo para subir a InfinityFree!" -ForegroundColor Green
    Write-Host "Lee el archivo CONFIGURACION_INFINITYFREE.txt para instrucciones." -ForegroundColor Cyan
    
} catch {
    Write-Host "Error al crear ZIP: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Paso 10: Limpiar directorio temporal (opcional)
$cleanup = Read-Host "¿Eliminar directorio temporal $TargetDir? (y/N)"
if ($cleanup -eq "y" -or $cleanup -eq "Y") {
    Remove-Item $TargetPath -Recurse -Force
    Write-Host "Directorio temporal eliminado." -ForegroundColor Green
}

Write-Host ""
Write-Host "¡Proceso completado!" -ForegroundColor Green