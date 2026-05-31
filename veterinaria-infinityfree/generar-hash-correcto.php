<?php
// Generar hash exacto como lo hace Laravel
echo "<h2>Generar Hash Correcto para InfinityFree</h2>";

$password = 'admin123';

// Laravel usa bcrypt con cost 10 por defecto (no 12)
$laravelHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

echo "<h3>Hash generado (compatible con Laravel):</h3>";
echo "<code style='background: #f0f0f0; padding: 10px; display: block;'>";
echo $laravelHash;
echo "</code>";

echo "<h3>SQL para ejecutar en phpMyAdmin:</h3>";
echo "<pre style='background: #f0f0f0; padding: 15px;'>";
echo "-- Eliminar usuario anterior si existe
DELETE FROM usuario WHERE correo_electronico = 'admin@vet.com';

-- Insertar usuario con hash correcto
INSERT INTO usuario (
    correo_electronico,
    nombre_usuario,
    password,
    tipo_permiso,
    estado,
    created_at,
    updated_at
) VALUES (
    'admin@vet.com',
    'Admin',
    '$laravelHash',
    'administrador',
    1,
    NOW(),
    NOW()
);";
echo "</pre>";

echo "<h3>Credenciales de login:</h3>";
echo "<ul>";
echo "<li><strong>Email:</strong> admin@vet.com</li>";
echo "<li><strong>Password:</strong> admin123</li>";
echo "</ul>";

// Verificar el hash
if (password_verify($password, $laravelHash)) {
    echo "<p style='color: green;'>✅ Hash verificado correctamente</p>";
} else {
    echo "<p style='color: red;'>❌ Error en la verificación del hash</p>";
}
?>