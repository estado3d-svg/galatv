@echo off
set FTP_SERVER=c2642305.ferozo.com
set FTP_USER=tu_c2642305
set FTP_PASS=Argentinaconsalud26/
set REMOTE_DIR=/public_html

echo ========================================
echo  GALATV - DEPLOYMENT SCRIPT (curl)
echo ========================================
echo.
echo Subiendo archivos a FTPS con curl...
echo.

REM Crear carpeta de deploy
if not exist deploy mkdir deploy
xcopy /E /I /Y . deploy\ >nul 2>&1

REM Subir archivos usando curl con FTPS
echo Conectando con FTPS...
curl --ftp-method SITE -U %FTP_USER%:%FTP_PASS% -T deploy\index.html -upload-file ftp://%FTP_USER%:%FTP_PASS%@%FTP_SERVER%/%REMOTE_DIR%/index.html

echo Subiendo styles.css...
curl --ftp-method SITE -U %FTP_USER%:%FTP_PASS% -T deploy\styles.css -upload-file ftp://%FTP_USER%:%FTP_PASS%@%FTP_SERVER%/%REMOTE_DIR%/styles.css

echo Subiendo contacto.php...
curl --ftp-method SITE -U %FTP_USER%:%FTP_PASS% -T deploy\contacto.php -upload-file ftp://%FTP_USER%:%FTP_PASS%@%FTP_SERVER%/%REMOTE_DIR%/contacto.php

echo Subiendo contacto.php...
curl --ftp-method SITE -U %FTP_USER%:%FTP_PASS% -T deploy\contact.php -upload-file ftp://%FTP_USER%:%FTP_PASS%@%FTP_SERVER%/%REMOTE_DIR%/contact.php

echo Subiendo script.js...
curl --ftp-method SITE -U %FTP_USER%:%FTP_PASS% -T deploy\script.js -upload-file ftp://%FTP_USER%:%FTP_PASS%@%FTP_SERVER%/%REMOTE_DIR%/script.js

echo Subiendo server.js...
curl --ftp-method SITE -U %FTP_USER%:%FTP_PASS% -T deploy\server.js -upload-file ftp://%FTP_USER%:%FTP_PASS%@%FTP_SERVER%/%REMOTE_DIR%/server.js

echo Subiendo favicon.svg...
curl --ftp-method SITE -U %FTP_USER%:%FTP_PASS% -T deploy\favicon.svg -upload-file ftp://%FTP_USER%:%FTP_PASS%@%FTP_SERVER%/%REMOTE_DIR%/favicon.svg

echo Subiendo base.jpeg...
curl --ftp-method SITE -U %FTP_USER%:%FTP_PASS% -T deploy\base.jpeg -upload-file ftp://%FTP_USER%:%FTP_PASS%@%FTP_SERVER%/%REMOTE_DIR%/base.jpeg

echo.
echo ========================================
echo  ¡Listo! Archivos subidos
echo ========================================
pause
rmdir /s /q deploy
