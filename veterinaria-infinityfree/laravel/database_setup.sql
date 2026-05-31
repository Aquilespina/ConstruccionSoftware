-- SQL corregido para InfinityFree
-- Usar la base de datos correcta
USE if0_40325157_veterinaria;

-- ========================================
-- TABLAS PRINCIPALES
-- ========================================

-- Tabla Usuario (Laravel compatible)
CREATE TABLE usuario (
    id_usuario BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correo_electronico VARCHAR(100) UNIQUE NOT NULL,
    nombre_usuario VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    tipo_permiso ENUM('administrador','medico','recepcionista') DEFAULT 'recepcionista',
    estado BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Tabla Propietario
CREATE TABLE propietario (
    id_propietario BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    correo_electronico VARCHAR(100),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_propietario_nombre (nombre)
);

-- Tabla Mascota
CREATE TABLE mascota (
    id_mascota BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_propietario BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    raza VARCHAR(50),
    especie VARCHAR(50),
    años INT,
    peso DECIMAL(5,2),
    sexo ENUM('Macho', 'Hembra'),
    historial_medico TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_propietario) REFERENCES propietario(id_propietario) ON DELETE CASCADE,
    INDEX idx_mascota_nombre (nombre)
);

-- Tabla Profesional
CREATE TABLE profesional (
    rfc VARCHAR(13) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100),
    especialidad VARCHAR(100),
    turno ENUM('Matutino', 'Vespertino', 'Nocturno'),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Tabla Cita
CREATE TABLE cita (
    id_cita BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_mascota BIGINT UNSIGNED NOT NULL,
    rfc_profesional VARCHAR(13) NOT NULL,
    tipo_servicio VARCHAR(100),
    tipo_cita ENUM('Consulta', 'Urgencia', 'Cirugía', 'Estética'),
    tarifa DECIMAL(8,2),
    peso_mascota DECIMAL(5,2),
    fecha DATE NOT NULL,
    horario TIME NOT NULL,
    diagnostico TEXT,
    observaciones TEXT,
    estado ENUM('Programada', 'Completada', 'Cancelada') DEFAULT 'Programada',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_mascota) REFERENCES mascota(id_mascota) ON DELETE CASCADE,
    FOREIGN KEY (rfc_profesional) REFERENCES profesional(rfc) ON DELETE CASCADE,
    INDEX idx_cita_fecha (fecha)
);

-- Tabla Receta
CREATE TABLE receta (
    id_receta BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_mascota BIGINT UNSIGNED NOT NULL,
    id_cita BIGINT UNSIGNED NOT NULL,
    medicamento VARCHAR(100) NOT NULL,
    tipo_medicamento VARCHAR(50),
    dosis VARCHAR(100),
    indicaciones TEXT,
    proxima_cita DATE,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_mascota) REFERENCES mascota(id_mascota) ON DELETE CASCADE,
    FOREIGN KEY (id_cita) REFERENCES cita(id_cita) ON DELETE CASCADE
);

-- Tabla Hospitalizacion
CREATE TABLE hospitalizacion (
    id_hospitalizacion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_mascota BIGINT UNSIGNED NOT NULL,
    fecha_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_egreso TIMESTAMP NULL,
    estado ENUM('Internado', 'Alta', 'Tratamiento') DEFAULT 'Internado',
    observaciones TEXT,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_mascota) REFERENCES mascota(id_mascota) ON DELETE CASCADE
);

-- Tabla Honorario
CREATE TABLE honorario (
    id_honorario BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_mascota BIGINT UNSIGNED NOT NULL,
    id_hospitalizacion BIGINT UNSIGNED,
    fecha_ingreso DATE,
    fecha_corte DATE,
    subtotal DECIMAL(10,2),
    total_pagado DECIMAL(10,2) DEFAULT 0,
    saldo_pendiente DECIMAL(10,2),
    estado ENUM('Pendiente', 'Pagado', 'Parcial') DEFAULT 'Pendiente',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_mascota) REFERENCES mascota(id_mascota) ON DELETE CASCADE,
    FOREIGN KEY (id_hospitalizacion) REFERENCES hospitalizacion(id_hospitalizacion) ON DELETE SET NULL,
    INDEX idx_honorario_estado (estado)
);

-- Tabla Detalle_Honorario
CREATE TABLE detalle_honorario (
    id_detalle BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_honorario BIGINT UNSIGNED NOT NULL,
    concepto VARCHAR(200) NOT NULL,
    cantidad INT DEFAULT 1,
    precio_unitario DECIMAL(8,2),
    importe DECIMAL(10,2),
    fecha_pago TIMESTAMP NULL,
    monto_pagado DECIMAL(8,2),
    tipo_pago ENUM('Efectivo', 'Tarjeta', 'Transferencia'),
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_honorario) REFERENCES honorario(id_honorario) ON DELETE CASCADE
);

-- ========================================
-- TABLAS LARAVEL SISTEMA
-- ========================================

-- Tabla Sessions (Laravel)
CREATE TABLE sessions (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id BIGINT UNSIGNED DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
);

-- Tabla Cache (Laravel)
CREATE TABLE cache (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
);

CREATE TABLE cache_locks (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
);

-- ========================================
-- DATOS INICIALES
-- ========================================

-- Usuario administrador por defecto (password: admin123)
INSERT INTO usuario (correo_electronico, nombre_usuario, password, tipo_permiso, estado) VALUES 
('admin@veterinaria.com', 'Administrador', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1);

-- Profesional ejemplo
INSERT INTO profesional (rfc, nombre, correo, especialidad, turno, activo) VALUES 
('VETM901201ABC', 'Dr. Juan Pérez', 'dr.perez@veterinaria.com', 'Medicina General', 'Matutino', 1);