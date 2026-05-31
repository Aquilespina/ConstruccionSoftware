# 🚨 SOLUCIÓN URGENTE ERROR 500

## PASO 1: CONFIGURAR .env

1. En el administrador de archivos de InfinityFree:
   - Renombra `.env.infinityfree` a `.env`

2. Edita el archivo `.env` y cambia estas líneas:

```
DB_HOST=sql200.infinityfree.com
DB_DATABASE=epiz_35134099_veterinaria
DB_USERNAME=epiz_35134099
DB_PASSWORD=TU_PASSWORD_REAL
```

IMPORTANTE: Reemplaza estos valores con los datos reales que te proporcionó InfinityFree.

## PASO 2: CREAR BASE DE DATOS

1. Ve al panel de InfinityFree
2. MySQL Databases → Create Database
3. Nombre: `veterinaria` (será epiz_XXXXXXX_veterinaria)
4. Anota el usuario y contraseña

## PASO 3: VERIFICAR

Visita: http://veterinaria-clinica.infinityfreeapp.com/diagnostico.php

## PASO 4: EJECUTAR MIGRACIONES

Si el diagnóstico funciona, visita:
http://veterinaria-clinica.infinityfreeapp.com/migrate

## DATOS TÍPICOS DE INFINITYFREE:

```
DB_HOST=sql200.infinityfree.com (o sql201, sql202, etc.)
DB_DATABASE=epiz_XXXXXXX_veterinaria
DB_USERNAME=epiz_XXXXXXX
DB_PASSWORD=[la que te asignaron]
```

¿Ya completaste estos pasos?