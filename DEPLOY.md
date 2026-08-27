# 🔧 Manual Deployment Instructions

## Opción 1: Script Automático (Windows)

1. **Edita `deploy.bat`** con tus credenciales FTP:
   ```
   FTP_SERVER=ftp.tudominio.com
   FTP_USER=tu_usuario
   FTP_PASS=tu_contraseña
   REMOTE_DIR=/public_html
   ```

2. **Ejecuta:**
   ```
   deploy.bat
   ```

## Opción 2: FileZilla (Manual)

1. Conecta a tu FTP server
2. Sube los archivos de `D:\ia\proyectos\galatv\` a tu servidor
3. Archivos importantes:
   - `index.html`
   - `styles.css`
   - `contacto.php`
   - `contact.php`
   - `script.js`
   - `server.js`
   - `favicon.svg`
   - `assets/*`
   - `base.jpeg`

## Opción 3: GitHub Pages

Si prefieres hosting gratuito:
- Ve a: https://github.com/estado3d-svg/galatv
- Settings → Pages
- Enable GitHub Pages
- URL: `https://estado3d-svg.github.io/galatv/`
