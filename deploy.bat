@echo off
echo ========================================
echo  GALATV - DEPLOYMENT SCRIPT
echo ========================================
echo.
echo Subiendo archivos a FTP...
echo.

REM Variables (edita estas según tu FTP)
set FTP_SERVER=ftp.tudominio.com
set FTP_USER=tu_usuario
set FTP_PASS=tu_contraseña
set REMOTE_DIR=/public_html

REM Crear carpeta de deploy
mkdir deploy
xcopy /E /I /Y . deploy\

REM Conectar a FTP con lftp o FTP
REM Opción 1: Usando lftp (si está instalado)
lftp -u %FTP_USER%,%FTP_PASS% -e "set ftp:overwrite 1; open %FTP_SERVER%; cd %REMOTE_DIR%; put -c -r deploy/*; bye"

REM Opción 2: Usando FTP (más simple)
REM ftp %FTP_SERVER%
REM User: %FTP_USER%
REM Password: %FTP_PASS%
REM cd %REMOTE_DIR%
REM mput deploy/*

echo.
echo ========================================
echo  ¡Listo! Archivos subidos
echo ========================================
pause
