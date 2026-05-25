# Guía de Despliegue para InfinityFree - Sistema Veterinaria

## 📦 Archivos de Despliegue Disponibles

### 1. veterinaria-sistema-2025-11-06.zip (49.7MB)
- **Contenido**: Proyecto completo con todas las dependencias
- **Incluye**: vendor/, node_modules/, archivos de desarrollo
- **Uso**: Para desarrollo local o servidores con composer/npm

### 2. veterinaria-infinityfree-2025-11-06.zip (683KB) ⭐ RECOMENDADO
- **Contenido**: Archivos esenciales para producción
- **Excluye**: vendor/, node_modules/, archivos de desarrollo
- **Uso**: Específico para InfinityFree (requiere instalación manual de dependencias)

### 3. veterinaria-infinityfree-FINAL-V2.zip (28MB)
- **Contenido**: Versión anterior con dependencias incluidas
- **Uso**: Alternativa si hay problemas con la versión optimizada

## 🚀 Proceso de Despliegue en InfinityFree

### Paso 1: Preparación del Hosting
1. **Acceder al cPanel** de InfinityFree
2. **Crear una base de datos** MySQL
   - Anotar: nombre_db, usuario_db, contraseña_db, host_db
3. **Ubicar la carpeta public_html**

### Paso 2: Subida de Archivos
1. **Extraer** `veterinaria-infinityfree-2025-11-06.zip`
2. **Subir todos los archivos** a `public_html/`
3. **Estructura resultante en el servidor**:
   ```
   public_html/
   ├── app/
   ├── bootstrap/
   ├── config/
   ├── database/
   ├── public/
   ├── resources/
   ├── routes/
   ├── storage/
   ├── .env.example
   ├── artisan
   ├── composer.json
   └── otros archivos...
   ```

### Paso 3: Configuración de la Base de Datos
1. **Acceder a phpMyAdmin** en cPanel
2. **Importar el esquema de base de datos**:
   
   ```sql
   -- CREAR LAS TABLAS PRINCIPALES
   
   -- Tabla usuarios
   CREATE TABLE `usuario` (
     `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
     `usuario` varchar(50) NOT NULL,
     `password` varchar(255) NOT NULL,
     `email` varchar(100) NOT NULL,
     `rol` enum('admin','medico','recepcion') NOT NULL,
     `nombre` varchar(100) NOT NULL,
     `apellido` varchar(100) NOT NULL,
     `telefono` varchar(20) DEFAULT NULL,
     `activo` tinyint(1) DEFAULT 1,
     `remember_token` varchar(100) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT NULL,
     `updated_at` timestamp NULL DEFAULT NULL,
     PRIMARY KEY (`id_usuario`),
     UNIQUE KEY `usuario` (`usuario`),
     UNIQUE KEY `email` (`email`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   
   -- Tabla propietarios
   CREATE TABLE `propietarios` (
     `id_propietario` int(11) NOT NULL AUTO_INCREMENT,
     `nombre` varchar(100) NOT NULL,
     `apellido` varchar(100) NOT NULL,
     `telefono` varchar(20) DEFAULT NULL,
     `email` varchar(100) DEFAULT NULL,
     `direccion` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT NULL,
     `updated_at` timestamp NULL DEFAULT NULL,
     PRIMARY KEY (`id_propietario`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   
   -- Tabla mascotas
   CREATE TABLE `mascota` (
     `id_mascota` int(11) NOT NULL AUTO_INCREMENT,
     `nombre` varchar(100) NOT NULL,
     `especie` varchar(50) NOT NULL,
     `raza` varchar(100) DEFAULT NULL,
     `edad` int(11) DEFAULT NULL,
     `peso` decimal(5,2) DEFAULT NULL,
     `color` varchar(50) DEFAULT NULL,
     `sexo` enum('macho','hembra') DEFAULT NULL,
     `id_propietario` int(11) NOT NULL,
     `created_at` timestamp NULL DEFAULT NULL,
     `updated_at` timestamp NULL DEFAULT NULL,
     PRIMARY KEY (`id_mascota`),
     KEY `fk_mascota_propietario` (`id_propietario`),
     CONSTRAINT `fk_mascota_propietario` FOREIGN KEY (`id_propietario`) REFERENCES `propietarios` (`id_propietario`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   
   -- Tabla hospitalizaciones
   CREATE TABLE `hospitalizacion` (
     `id_hospitalizacion` int(11) NOT NULL AUTO_INCREMENT,
     `id_mascota` int(11) NOT NULL,
     `fecha_ingreso` date NOT NULL,
     `fecha_alta` date DEFAULT NULL,
     `motivo` text NOT NULL,
     `estado` enum('activa','alta','transferida') DEFAULT 'activa',
     `observaciones` text DEFAULT NULL,
     `costo_diario` decimal(10,2) DEFAULT NULL,
     `total_dias` int(11) DEFAULT NULL,
     `total_costo` decimal(10,2) DEFAULT NULL,
     PRIMARY KEY (`id_hospitalizacion`),
     KEY `fk_hospitalizacion_mascota` (`id_mascota`),
     CONSTRAINT `fk_hospitalizacion_mascota` FOREIGN KEY (`id_mascota`) REFERENCES `mascota` (`id_mascota`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   
   -- Tabla honorarios
   CREATE TABLE `honorario` (
     `id_honorario` int(11) NOT NULL AUTO_INCREMENT,
     `id_mascota` int(11) NOT NULL,
     `fecha` date NOT NULL,
     `total` decimal(10,2) NOT NULL DEFAULT 0.00,
     `estado` enum('pendiente','pagado','parcial') DEFAULT 'pendiente',
     `observaciones` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT NULL,
     `updated_at` timestamp NULL DEFAULT NULL,
     PRIMARY KEY (`id_honorario`),
     KEY `fk_honorario_mascota` (`id_mascota`),
     CONSTRAINT `fk_honorario_mascota` FOREIGN KEY (`id_mascota`) REFERENCES `mascota` (`id_mascota`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   
   -- Tabla detalles de honorarios
   CREATE TABLE `detalle_honorario` (
     `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
     `id_honorario` int(11) NOT NULL,
     `concepto` varchar(255) NOT NULL,
     `cantidad` int(11) NOT NULL DEFAULT 1,
     `precio_unitario` decimal(10,2) NOT NULL,
     `subtotal` decimal(10,2) NOT NULL,
     `pagado` decimal(10,2) DEFAULT 0.00,
     PRIMARY KEY (`id_detalle`),
     KEY `fk_detalle_honorario` (`id_honorario`),
     CONSTRAINT `fk_detalle_honorario` FOREIGN KEY (`id_honorario`) REFERENCES `honorario` (`id_honorario`) ON DELETE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   
   -- Tabla pagos de honorarios
   CREATE TABLE `pago_honorario` (
     `id_pago` int(11) NOT NULL AUTO_INCREMENT,
     `id_honorario` int(11) NOT NULL,
     `monto` decimal(10,2) NOT NULL,
     `fecha_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
     `metodo_pago` enum('efectivo','tarjeta','transferencia') DEFAULT 'efectivo',
     `observaciones` text DEFAULT NULL,
     PRIMARY KEY (`id_pago`),
     KEY `fk_pago_honorario` (`id_honorario`),
     CONSTRAINT `fk_pago_honorario` FOREIGN KEY (`id_honorario`) REFERENCES `honorario` (`id_honorario`) ON DELETE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   
   -- Tabla citas
   CREATE TABLE `cita` (
     `id_cita` int(11) NOT NULL AUTO_INCREMENT,
     `id_mascota` int(11) NOT NULL,
     `fecha` date NOT NULL,
     `hora` time NOT NULL,
     `motivo` text NOT NULL,
     `estado` enum('programada','confirmada','completada','cancelada') DEFAULT 'programada',
     `observaciones` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT NULL,
     `updated_at` timestamp NULL DEFAULT NULL,
     PRIMARY KEY (`id_cita`),
     KEY `fk_cita_mascota` (`id_mascota`),
     CONSTRAINT `fk_cita_mascota` FOREIGN KEY (`id_mascota`) REFERENCES `mascota` (`id_mascota`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   
   -- Tabla recetas
   CREATE TABLE `receta` (
     `id_receta` int(11) NOT NULL AUTO_INCREMENT,
     `id_mascota` int(11) NOT NULL,
     `fecha` date NOT NULL,
     `diagnostico` text NOT NULL,
     `medicamentos` text NOT NULL,
     `instrucciones` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT NULL,
     `updated_at` timestamp NULL DEFAULT NULL,
     PRIMARY KEY (`id_receta`),
     KEY `fk_receta_mascota` (`id_mascota`),
     CONSTRAINT `fk_receta_mascota` FOREIGN KEY (`id_mascota`) REFERENCES `mascota` (`id_mascota`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   ```

### Paso 4: Configuración del Archivo .env
1. **Copiar** `.env.example` a `.env`
2. **Editar** `.env` con los datos de InfinityFree:

   ```env
   APP_NAME="Sistema Veterinaria"
   APP_ENV=production
   APP_KEY=base64:TU_CLAVE_AQUI_GENERAR_CON_ARTISAN
   APP_DEBUG=false
   APP_URL=https://tudominio.infinityfreeapp.com

   LOG_CHANNEL=stack
   LOG_DEPRECATIONS_CHANNEL=null
   LOG_LEVEL=error

   DB_CONNECTION=mysql
   DB_HOST=TU_HOST_DB_INFINITYFREE
   DB_PORT=3306
   DB_DATABASE=TU_NOMBRE_DB
   DB_USERNAME=TU_USUARIO_DB
   DB_PASSWORD=TU_PASSWORD_DB

   BROADCAST_DRIVER=log
   CACHE_DRIVER=file
   FILESYSTEM_DISK=local
   QUEUE_CONNECTION=sync
   SESSION_DRIVER=file
   SESSION_LIFETIME=120

   MEMCACHED_HOST=127.0.0.1

   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379

   MAIL_MAILER=smtp
   MAIL_HOST=mailpit
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS="hello@example.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

### Paso 5: Configuración Específica para InfinityFree

#### A. Crear archivo .htaccess en public_html
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Angular / Vue / React / etc.
    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### B. Modificar permisos de directorios
- `storage/` → 755 o 777
- `bootstrap/cache/` → 755 o 777

#### C. Generar clave de aplicación
Si tienes acceso SSH (poco probable en InfinityFree), ejecutar:
```bash
php artisan key:generate
```

Si no tienes SSH, generar manualmente:
```php
<?php
// Archivo temporal: generar_key.php
echo 'base64:' . base64_encode(random_bytes(32));
?>
```

### Paso 6: Configuración de Rutas Laravel para InfinityFree

#### A. Crear index.php personalizado en public_html
```php
<?php
// Verificar si estamos en un subdirectorio
$publicPath = __DIR__ . '/public';
if (file_exists($publicPath . '/index.php')) {
    // Si existe public/index.php, redirigir ahí
    require_once $publicPath . '/index.php';
} else {
    // Si no, usar el index.php de Laravel directamente
    use Illuminate\Contracts\Http\Kernel;
    use Illuminate\Http\Request;

    define('LARAVEL_START', microtime(true));

    // Autoload
    if (file_exists(__DIR__.'/vendor/autoload.php')) {
        require __DIR__.'/vendor/autoload.php';
    } else {
        // Buscar autoload en estructura alternativa
        require __DIR__.'/bootstrap/../vendor/autoload.php';
    }

    // Bootstrap Laravel
    $app = require_once __DIR__.'/bootstrap/app.php';

    // Handle Request
    $kernel = $app->make(Kernel::class);
    $response = $kernel->handle(
        $request = Request::capture()
    )->send();

    $kernel->terminate($request, $response);
}
?>
```

### Paso 7: Datos de Prueba (Opcional)

#### Insertar usuario administrador:
```sql
INSERT INTO `usuario` (`usuario`, `password`, `email`, `rol`, `nombre`, `apellido`, `activo`) 
VALUES ('admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@veterinaria.com', 'admin', 'Administrador', 'Sistema', 1);
-- Password: password
```

#### Insertar datos de ejemplo:
```sql
-- Propietario ejemplo
INSERT INTO `propietarios` (`nombre`, `apellido`, `telefono`, `email`) 
VALUES ('Juan', 'Pérez', '555-0123', 'juan@email.com');

-- Mascota ejemplo
INSERT INTO `mascota` (`nombre`, `especie`, `raza`, `edad`, `peso`, `sexo`, `id_propietario`) 
VALUES ('Firulais', 'Perro', 'Pastor Alemán', 3, 25.50, 'macho', 1);
```

## 🔧 Instalación de Dependencias PHP (Si es necesario)

### Opción 1: Si InfinityFree soporta Composer
```bash
composer install --no-dev --optimize-autoloader
```

### Opción 2: Subir vendor/ manualmente
1. Usar el ZIP completo (`veterinaria-sistema-2025-11-06.zip`)
2. Subir también la carpeta `vendor/`

## 📋 Lista de Verificación Post-Despliegue

- [ ] Base de datos creada e importada
- [ ] Archivo .env configurado correctamente
- [ ] Permisos de directorios configurados
- [ ] .htaccess en public_html
- [ ] Clave de aplicación generada
- [ ] Usuario administrador creado
- [ ] Acceso al sitio web funcionando
- [ ] Login de administrador funcionando
- [ ] Sistema de honorarios funcionando
- [ ] Generación de PDF funcionando

## 🌐 URLs del Sistema

- **Login**: `https://tudominio.infinityfreeapp.com/login`
- **Dashboard Admin**: `https://tudominio.infinityfreeapp.com/admin`
- **Recepción**: `https://tudominio.infinityfreeapp.com/recepcion`
- **Honorarios**: `https://tudominio.infinityfreeapp.com/recepcion/honorarios`

## 🐛 Solución de Problemas Comunes

### Error 500 - Internal Server Error
1. Verificar permisos de `storage/` y `bootstrap/cache/`
2. Verificar configuración de base de datos en `.env`
3. Verificar que `APP_KEY` está configurado

### Error de Base de Datos
1. Verificar credenciales en `.env`
2. Verificar que las tablas existen
3. Verificar conexión desde phpMyAdmin

### Rutas no funcionan
1. Verificar `.htaccess` en public_html
2. Verificar que mod_rewrite está habilitado
3. Verificar estructura de directorios

### PDFs no se generan
1. Verificar que DomPDF está en vendor/
2. Verificar permisos de storage/
3. Verificar configuración de memoria PHP

## 📞 Soporte

Si encuentras problemas durante el despliegue:

1. **Verificar logs** en `storage/logs/laravel.log`
2. **Comprobar configuración** de InfinityFree
3. **Revisar documentación** de Laravel para hosting compartido
4. **Contactar soporte** de InfinityFree para problemas específicos del hosting

---

**Creado**: Noviembre 6, 2025  
**Versión**: 1.0  
**Sistema**: Laravel 11 - Clínica Veterinaria  
**Hosting**: InfinityFree Compatible