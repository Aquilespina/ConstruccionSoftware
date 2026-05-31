-- Script SQL corregido con el email que funcionaba localmente
-- Usar el mismo email y generar hash correcto

INSERT INTO usuario (
    correo_electronico,
    nombre_usuario,
    password,
    tipo_permiso,
    estado,
    created_at,
    updated_at
) VALUES (
    'admin@vet.com',  -- El mismo email que funcionaba local
    'Admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- Hash Laravel compatible
    'administrador',
    1,  -- Usar 1 en lugar de TRUE
    NOW(),
    NOW()
);

-- Verificar que se creó correctamente
SELECT id_usuario, nombre_usuario, correo_electronico, tipo_permiso, estado 
FROM usuario 
WHERE correo_electronico = 'admin@vet.com';