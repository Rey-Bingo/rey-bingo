@echo off
REM Inicia el servicio MySQL 8.4 (requiere ejecutar como Administrador)
echo Iniciando servicio MySQL84...
net start MySQL84
if %ERRORLEVEL% EQU 0 (
    echo.
    echo [OK] MySQL esta en ejecucion en el puerto 3306
    echo Puedes conectar HeidiSQL con:
    echo   Host: 127.0.0.1  Puerto: 3306  Usuario: root
    echo.
) else (
    echo.
    echo [ERROR] No se pudo iniciar. Haz clic derecho en este archivo
    echo y elige "Ejecutar como administrador"
    echo.
    echo O desde Laragon: boton "Start All"
    echo O Win+R - services.msc - MySQL84 - Iniciar
)
pause
