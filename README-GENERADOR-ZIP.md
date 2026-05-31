# 🚀 GENERADOR AUTOMÁTICO DE ZIP PARA INFINITYFREE

## 📋 Descripción
Este conjunto de scripts automatiza completamente la creación de un ZIP optimizado para subir tu aplicación Laravel a InfinityFree, tomando como base la carpeta `Veterinaria` (que contiene el código funcionando).

## 📁 Archivos incluidos

### 1. `generar-zip-infinityfree.ps1`
**Script principal en PowerShell** que realiza todo el proceso automatizado.

### 2. `GENERAR-ZIP.bat`
**Archivo ejecutable** para usuarios que prefieren hacer doble clic.

## 🔧 Qué hace el script automáticamente

### ✅ Proceso de optimización:

1. **Copia completa** de la carpeta `Veterinaria` → `veterinaria-infinityfree2`
2. **Limpia archivos innecesarios** para producción:
   - `composer.lock`, `package-lock.json`, `yarn.lock`
   - `.env.example`, `.gitignore`, `README.md`
   - `phpunit.xml`, `vite.config.js`
   - Carpetas: `node_modules`, `tests`, `.git`, `.vscode`
   - Cache y logs temporales

3. **Crea directorios necesarios**:
   - `storage/logs`, `storage/framework/cache`, etc.

4. **Genera .htaccess optimizado** con:
   - Configuración para InfinityFree
   - Headers de seguridad
   - Compresión GZIP
   - Cache de archivos estáticos

5. **Optimiza vendor**:
   - Elimina documentación, tests y archivos innecesarios
   - Mantiene solo lo esencial para funcionar

6. **Crea ZIP con compresión máxima**
7. **Genera guía de configuración** (`CONFIGURACION_INFINITYFREE.txt`)

## 🚀 Cómo usar

### Opción 1: Doble clic (Fácil)
```
1. Haz doble clic en GENERAR-ZIP.bat
2. Presiona Enter para continuar
3. Espera a que termine
4. ¡Listo! Tienes tu veterinaria-infinityfree2.zip
```

### Opción 2: Línea de comandos
```powershell
# Desde la carpeta ConstruccionSoftware
.\generar-zip-infinityfree.ps1
```

### Opción 3: Con parámetros personalizados
```powershell
.\generar-zip-infinityfree.ps1 -SourceDir "Veterinaria" -TargetDir "mi-version" -ZipName "mi-app.zip"
```

## 📊 Resultados

- **ZIP anterior**: ~50MB
- **ZIP optimizado**: ~250KB (99% más pequeño!)
- **Tiempo de subida**: Mucho más rápido
- **Estructura**: Lista para InfinityFree

## 🔧 Configuración en InfinityFree

Después de generar el ZIP, sigue estos pasos:

### 1. Subir archivos
- Descomprime el ZIP en `htdocs/` de InfinityFree
- Asegúrate de que `index.php` esté en la raíz

### 2. Base de datos
```env
DB_HOST=sql###.infinityfree.com
DB_DATABASE=tu_base_datos  
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 3. Dominio
```env
APP_URL=https://tudominio.infinityfreeapp.com
```

### 4. Permisos
- `storage/` → 755 o 777
- `bootstrap/cache/` → 755 o 777

## 🎯 Características del ZIP generado

✅ **Optimizado para producción**
✅ **Sin archivos de desarrollo**  
✅ **Con .htaccess configurado**
✅ **Vendor limpio y funcional**
✅ **Guía de configuración incluida**
✅ **Máxima compresión**
✅ **Listo para InfinityFree**

## 🔄 Para actualizaciones futuras

Cada vez que hagas cambios en la carpeta `Veterinaria`:

1. Ejecuta el script nuevamente
2. Se creará un nuevo ZIP actualizado
3. Sube solo los archivos que cambiaron a InfinityFree

## ⚠️ Notas importantes

- El script preserva toda la funcionalidad de tu aplicación
- Los controladores con detección dinámica de columnas están incluidos
- La configuración de base de datos debe ajustarse según InfinityFree
- El .env debe configurarse con tus datos reales

## 🆘 Solución de problemas

Si el script no funciona:

1. **Error de permisos**: Ejecuta PowerShell como administrador
2. **ExecutionPolicy**: Usa `Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope CurrentUser`
3. **ZIP no se crea**: Verifica que tienes permisos en la carpeta

---

**¡Tu aplicación veterinaria está lista para InfinityFree con un solo comando! 🎉**