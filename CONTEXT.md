# Contexto del Proyecto — GalaTV (para otro modelo/desarrollador)

## Qué es
Sitio web de GalaTV Streaming (https://galatv.com.ar/), tema dark/gold. Incluye página principal (hero con video en vivo, programación en carrusel, banners) y un **panel de administración** (`/panel/`) con login Google.

## Estado actual (funcional)
- ✅ Página principal con hero, programación (carrusel), banners, contacto, footer.
- ✅ Panel con 3 pestañas (Publicidad, Video de portada, Programación) + login Google.
- ✅ Deploy automático a FTP vía GitHub Actions.
- ✅ Login Google funciona (2 emails permitidos).

## Dónde está y cómo se actualiza
- **Ruta:** `D:\ia\proyectos\galatv\`
- **Repo:** https://github.com/estado3d-svg/galatv (privado)
- **Deploy:** `gitup.ps1` hace commit+push → GitHub Actions sube a FTP.
- **Subida manual al FTP** (urgente, sin esperar deploy):
  ```bash
  curl --ssl-reqd --user 'c2642305:Argentinaconsalud26/' \
    --upload-file "<archivo>" "ftp://c2642305.ferozo.com/public_html/<ruta>"
  ```
  ⚠️ Usar comillas simples: el password FTP tiene `*` y PowerShell con comillas dobles lo expande.

## Archivos clave

### Página principal (raíz)
- `index.html` — HTML + JS dinámico (carga banners/programas de la BD con fetch, carrusel, video).
- `styles.css` — Estilos (carrusel, cards, título dorado animado).
- `script.js` — auxiliares.
- `server.js` — servidor local Node (puerto 8000). NO ejecuta PHP.
- `banners_api.php` — endpoint público → lee BD (banners + settings + programas).
- `contacto.php` — formulario de contacto (PHPMailer SMTP).
- `mail-config.php` — credenciales SMTP.
- `.htaccess` — rewrite de /contacto + protección de archivos sensibles.

### Panel (`/panel/`)
- `index.php` — UI (login + 3 pestañas). HTML con CSS inline + JS.
- `api.php` — API interna (requiere sesión + email permitido). Acciones: list, update_link, update_bodies, update_image, add, delete, reorder, settings_get, settings_save, programas_list, programas_save, programas_delete, programas_image.
- `config.php` — define `$ALLOWED_USERS` variable + `ALLOWED_USERS` constante, `GOOGLE_REDIRECT_URI`.
- `config.local.php` — credenciales reales (Google OAuth + BD). **NO en git** → en `.gitignore`, se sube por FTP.
- `db.php` — conexión PDO.
- `google-login.php` / `google-callback.php` / `logout.php` — flujo login.

## Base de datos `c2642305_1`
- `banners` (id, src, link, bodies, alt, position)
- `settings` (id=1, off_link, off_loop, carousel_speed, carousel_auto, programacion_activa)
- `programas` (id, titulo, categoria, dias, hora, imagen, posicion)

## Flujo de carga de la página principal
1. `loadBanners()` en index.html hace `fetch('banners_api.php')` → recibe `{banners, settings, programas}`.
2. Pinta banners en `#bannersContainer` (agrupados por cuerpos, máx 3 por fila).
3. `applySettings()` → fija video offline y muestra/oculta sección programación.
4. `loadProgramas()` → llena `#programasGrid` (carrusel) y configura el autoavance.

## Lógica del carrusel
- `let carIdx` (índice global), `scrollCarousel(dir)` avanzan card por card, al llegar al final vuelve a 0.
- `setupCarousel()` lee `window.__settings` (carousel_speed, carousel_auto, programacion_activa). Si ≤4 cards o auto=0, no autoavanza.
- Flechas `#carPrev`/`#carNext` llaman `scrollCarousel(-1/1)`.
- En móvil: 2 cards por vista; en desktop: 4.

## Credenciales (referencia, NO hardcodear en docs públicos)
- FTP, BD, SMTP, Google OAuth: están en `config.local.php` y `mail-config.php` en el servidor. No agregar a git.

## ⚠️ Advertencias / problemas conocidos
1. **Login Google:** ya funciona con el secret `GOCSPX-a22GJ-...`. No tocar `google-callback.php` con verificación extra de token (rompía el login) salvo que se replantee bien.
2. **GIFs pesados** (bellyco ~35MB, tubarao ~16MB) → carga lenta en móvil.
3. **`git add` de archivos muy grandes** puede crashear en Windows (agregar de a uno).
4. **Servidor local** (`node server.js`) no corre PHP — para probar el panel/contacto usá el hosting.
5. **Dos configs:** mantener `$ALLOWED_USERS` (variable) y `ALLOWED_USERS` (constante) — algunos archivos usan uno u otro.

## Comandos útiles
```powershell
# Commit + push (dispara deploy)
.\gitup.ps1 -Message "mensaje"

# Validar sintaxis JS de un bloque de index.html (extraer y node -c)
# Subir archivo al FTP (manual)
curl --ssl-reqd --user 'c2642305:Argentinaconsalud26/' --upload-file "<file>" "ftp://c2642305.ferozo.com/public_html/<ruta>"
```
---
Última actualización: 2026
