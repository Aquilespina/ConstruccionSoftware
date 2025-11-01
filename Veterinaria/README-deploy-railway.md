# Despliegue en Railway (Laravel + Nixpacks) — Subcarpeta `ConstruccionSoftware/Veterinaria`

Este proyecto Laravel vive en una subcarpeta. Con estos archivos en el repo:

- `ConstruccionSoftware/Veterinaria/Procfile`
- `ConstruccionSoftware/Veterinaria/nixpacks.toml`

puedes desplegar en Railway sin depender tanto de la UI.

## 1) Apuntar el servicio a la subcarpeta

En Railway → Project → Service → Settings:
- Root Directory (Service Path): `Veterinaria`  (si el repo raíz tiene `Veterinaria/`)
  - Si tu repo real tiene `ConstruccionSoftware/Veterinaria`, usa esa ruta.

## 2) Variables mínimas

Agrega en Railway → Variables (si la UI las ignora, Nixpacks leerá `nixpacks.toml` para algunas):

Aplicación
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=base64:...` (généralo localmente con `php artisan key:generate --show`)
- `APP_URL=https://<tu-servicio>.up.railway.app`
- `LOG_CHANNEL=stderr`
- `SESSION_DRIVER=cookie`
- `SESSION_SECURE_COOKIE=true`
- `CACHE_DRIVER=file`
- `QUEUE_CONNECTION=sync`

Builder/Runtime
- `COMPOSER_NO_DEV=1`         ← evita instalar require-dev
- `NIXPACKS_PHP_VERSION=8.2`
- `NIXPACKS_NODE_VERSION=20`
- `PHP_EXTENSIONS=pdo_pgsql,openssl,mbstring,exif,pcntl,bcmath,gd`

Base de datos (cuando uses Neon)
- `DB_CONNECTION=pgsql`
- `DB_HOST=...`
- `DB_PORT=5432`
- `DB_DATABASE=...`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`
- `DB_SSLMODE=require`

## 3) Build / Start / Post-deploy

Gracias al `Procfile` y `nixpacks.toml`:
- Start Command queda fijado a:
  ```
  php artisan serve --host 0.0.0.0 --port $PORT
  ```

En la UI de Railway puedes (opcionalmente) sobreescribir Build Command para compilar Vite:
- Si compilarás assets en Railway:
  ```
  composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader && php artisan config:clear && npm ci && npm run build
  ```
- Si ya comiteaste `public/build`:
  ```
  composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader && php artisan config:clear
  ```

Post-deploy (actívalo cuando tengas DB):
```
php artisan migrate --force && php artisan storage:link || true
```

## 4) Checklist rápido

- [ ] Root Directory apunta a `Veterinaria` (o `ConstruccionSoftware/Veterinaria` según tu repo)
- [ ] `APP_KEY` presente
- [ ] `COMPOSER_NO_DEV=1` (o PHP 8.3 si quieres require-dev)
- [ ] (Con Neon) DB_* + `DB_SSLMODE=require`
- [ ] (Con Vite) Build Command con `npm ci && npm run build` o `public/build` comiteado

## 5) Problemas comunes

- Railpack: `start.sh not found`
  - Asegúrate de que el Root Directory apunte a la carpeta con `composer.json`.
- Composer instala dev en PHP 8.2 y falla por Pest/PHPUnit:
  - Define `COMPOSER_NO_DEV=1` o sube `NIXPACKS_PHP_VERSION=8.3`.
- 419 CSRF al login:
  - `APP_URL` exacto (https), `SESSION_DRIVER=cookie`, `SESSION_SECURE_COOKIE=true`, y `php artisan optimize:clear` tras cambios.
- DB SSL en Neon:
  - `DB_SSLMODE=require` y `pdo_pgsql` en `PHP_EXTENSIONS`.
- Assets no cargan:
  - Ejecuta `npm ci && npm run build` en el build, o comitea `public/build`.

## 6) Extras

Para un entorno más productivo, considera una imagen con Nginx + PHP-FPM (Dockerfile multi-stage). Si la quieres, abrimos un PR con los archivos.
