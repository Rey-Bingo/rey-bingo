@echo off
cd /d "%~dp0"

REM Usa php del PATH (Laragon) o busca la ultima version en bin\php
set "PHPCLI=php"
where php >nul 2>&1
if errorlevel 1 (
    for /f "delims=" %%D in ('dir /b /ad /o-n "C:\laragon\bin\php\php-*" 2^>nul') do (
        set "PHPCLI=C:\laragon\bin\php\%%D\php.exe"
        goto :found
    )
    echo [ERROR] No se encontro PHP. Abre la terminal desde Laragon.
    pause
    exit /b 1
)
:found
echo Usando: %PHPCLI%
"%PHPCLI%" -v
echo.

if not exist ".env" (
    echo Copiando env a .env...
    copy /Y env .env >nul
)

echo Servidor: http://localhost:8080  (Ctrl+C para detener)
echo Iniciando servidor PHP embebido con router dev...
"%PHPCLI%" -S localhost:8080 -t public dev-router.php
