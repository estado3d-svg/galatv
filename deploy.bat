@echo off
echo ========================================
echo  GALATV - DEPLOYMENT SCRIPT
echo ========================================
echo.
echo Subiendo archivos a FTP...
echo.

REM Crear carpeta de deploy
mkdir deploy 2>nul
xcopy /E /I /Y . deploy\ >nul 2>&1

REM Subir archivos con ftp
echo open c2642305.ferozo.com | ftp -n
echo user tu_c2642305 Argentinaconsalud26/ | ftp -n
echo cd /public_html | ftp -n
echo put deploy\index.html | ftp -n
echo put deploy\index.php | ftp -n
echo put deploy\styles.css | ftp -n
echo put deploy\script.js | ftp -n
echo put deploy\server.js | ftp -n
echo put deploy\contacto.php | ftp -n
echo put deploy\contact.php | ftp -n
echo put deploy\favicon.svg | ftp -n
echo put deploy\base.jpeg | ftp -n
echo put deploy\assets\*.* | ftp -n
echo bye | ftp -n

echo.
echo ========================================
echo  ¡Listo! Archivos subidos
echo ========================================
pause
rmdir /s /q deploy
