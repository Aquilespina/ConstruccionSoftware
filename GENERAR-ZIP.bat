@echo off
echo ====================================
echo GENERADOR DE ZIP PARA INFINITYFREE
echo ====================================
echo.
echo Este script creara un ZIP optimizado desde la carpeta Veterinaria
echo.
pause

powershell -ExecutionPolicy Bypass -File "%~dp0generar-zip-infinityfree.ps1"

echo.
echo Presiona cualquier tecla para cerrar...
pause >nul