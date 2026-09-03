# GalaTV - Contexto del Proyecto

## Qué es
Sitio web de GalaTV Streaming (https://galatv.com.ar/) con tema dark/gold. Incluye:
- Página principal con hero (video en vivo de YouTube), programación semanal (carrusel), banners publicitarios.
- **Panel de administración** (`/panel/`) con login Google y gestión de banners, video de portada y programación.

## Ubicación
`D:\ia\proyectos\galatv\`

## Infraestructura
- **Hosting:** Ferozo/DonWeb (PHP 8.x + MySQL). FTP: `c2642305.ferozo.com`.
- **Dominio:** https://galatv.com.ar/
- **Repo:** https://github.com/estado3d-svg/galatv (privado).
- **Deploy automático:** GitHub Actions (`.github/workflows/deploy.yml`) sube a FTP con `curl --ssl-reqd` al hacer push a `main`.
- **Subida manual al FTP:** `curl --ssl-reqd --user 'c2642305:Argentinaconsalud26/' --upload-file "<archivo>" "ftp://c2642305.ferozo.com/public_html/<ruta>"` (comillas simples, el password tiene `*`).

## Estructura de archivos

### Raíz (página principal)
- `index.html` — Página principal (hero, programación carrusel, banners dinámicos, form contacto, footer).
- `styles.css` — Estilos del sitio (tema, animaciones, carrusel, cards).
- `script.js` — Scripts auxiliares.
- `server.js` — Servidor Node local (sirve estáticos en puerto 8000).
- `banners_api.php` — **Endpoint público** que lee de la BD (banners + settings + programas).
- `banners.json` — Config de banners (ya NO se usa: la BD es la fuente. Se excluyó del deploy/git).
- `contacto.php` / `contact.php` — Envío del formulario de contacto por SMTP (PHPMailer).
- `mail-config.php` — Credenciales SMTP (protegido por `.htaccess`).
- `.htaccess` — Rewrite de contacto + protección de archivos sensibles.
- `favicon.svg`, `logo.png`, `logogala.png`, `base.png` — Assets.
- `gitup.ps1` — Script para commit+push (dispara deploy).

### Panel (`/panel/`)
- `index.php` — UI del panel con 3 pestañas (Publicidad, Video de portada, Programación) + login Google.
- `api.php` — **API interna** (requiere sesión) que lee/escribe la BD.
- `config.php` — Config del panel + carga `config.local.php`.
- `config.local.php` — Credenciales reales (Google OAuth + BD). **NO se sube a GitHub** (en `.gitignore`), se sube manual por FTP.
- `db.php` — Conexión PDO a MySQL.
- `google-login.php`, `google-callback.php`, `logout.php` — Flujo de login con Google.
- `.htaccess` — Protege `config.php`, `config.local.php`.
- `gifs/` — GIFs de banners subidos desde el panel (con `.htaccess` no listable).
- `img_prog/` — Imágenes de programas subidas (con `.htaccess` no listable, se redimensionan a 333×450).

### Otros
- `phpmailer/` — Librería PHPMailer (envío de emails).
- `assets/` — Imágenes de cards (card1-4, hero, logo, subscription).

## Base de datos (MySQL: `c2642305_1`)

### Tabla `banners`
| Columna | Tipo | Descripción |
|---------|------|-------------|
| id | INT PK | Identificador |
| src | VARCHAR | Ruta del archivo (ej: `panel/gifs/banner-1-123.gif`) |
| link | VARCHAR | URL al clic (vacío = solo imagen) |
| bodies | TINYINT | 1, 2 o 3 cuerpos (333/666/999px) |
| alt | VARCHAR | Texto alternativo |
| position | INT | Orden en la página |

### Tabla `settings` (1 sola fila, id=1)
| Columna | Tipo | Descripción |
|---------|------|-------------|
| id | PK | Siempre 1 |
| off_link | VARCHAR | Video de YouTube cuando el canal está OFF |
| off_loop | TINYINT | 1 = repetir (loop), 0 = no |
| carousel_speed | INT | Segundos entre cards (1-10) |
| carousel_auto | TINYINT | 1 = autoavance, 0 = no |
| programacion_activa | TINYINT | 1 = mostrar sección programación, 0 = ocultar |

### Tabla `programas`
| Columna | Tipo | Descripción |
|---------|------|-------------|
| id | INT PK | Identificador |
| titulo | VARCHAR | Título del programa |
| categoria | VARCHAR | Categoría (ej: DRAMA) |
| dias | VARCHAR | Letras separadas por coma: D,L,M,M2,J,V,S |
| hora | VARCHAR | HH:MM (formato 24h, ej: `20:00`) |
| imagen | VARCHAR | Ruta imagen del programa (`panel/img_prog/...`) |
| posicion | INT | Orden en el carrusel |

## Funcionalidades

### Página principal
- **Hero:** reproductor de YouTube. Si hay live → `live_stream?channel=...`; si no → video offline configurable (con loop). Etiqueta EN VIVO (roja) u oculta.
- **Programación semanal:** carrusel con autoavance (velocidad configurable 1-10s), flechas ‹ ›, máx. 4 cards por fila, texto grande con "hs" en la hora.
- **Banners:** dinámicos desde BD, agrupados por cuerpos (máx. 3 cuerpos por fila).
- **Formulario de contacto:** envía por SMTP (PHPMailer) a la cuenta configurada.
- **Título:** "PROGRAMACIÓN SEMANAL" centrado, dorado con brillo animado.

### Panel (`/panel/`)
Login con Google (solo `galatvstreaming@gmail.com` y `bodorola@gmail.com`). Pestañas:
1. **Publicidad:** subir/editar/borrar/reordenar banners (por cuerpos 1-3), con link opcional, barra de progreso.
2. **Video de portada:** input con link de YouTube (para cuando está OFF) + checkbox "Repetir". Guarda en BD.
3. **Programación:** CRUD de programas (título, categoría, días en círculos D-L-M-M2-J-V-S, hora 24h, imagen que se redimensiona a 333×450) + config del carrusel (velocidad, auto, mostrar/ocultar sección).

## Seguridad
- `config.local.php` (Google OAuth + BD) NO está en git (`.gitignore`), se sube por FTP manualmente. Protegido por `.htaccess` (403).
- Google OAuth: solo 2 emails permitidos (validados en `google-callback.php`).
- API key de YouTube: restringida por dominio (`Referer` de galatv.com.ar).
- Archivos `mail-config.php`, `config.php`, `config.local.php`, `.log` → bloqueados por `.htaccess`.

## Datos de conexión (referencia)
- FTP: `c2642305.ferozo.com` / `c2642305` / `Argentinaconsalud26/` / carpeta `public_html`.
- BD: `localhost` / `c2642305_1` / `c2642305_1` / `dq0/***` (en `config.local.php`).
- SMTP: `mail.galatv.com.ar:587` STARTTLS, user `contacto@galatv.com.ar` (en `mail-config.php`).
- YouTube: canal `@GalaTvStreaming`, channel ID `UCNbKWLI2_ivAZIAQQ5ot6Ng`, API key en `index.html`.

## Deploy / Push
```powershell
cd D:\ia\proyectos\galatv
.\gitup.ps1 -Message "tu mensaje"   # add + commit + push → dispara deploy automático
```
Para cambios del panel que deben ir al servidor al toque (sin esperar deploy), se suben por curl FTP manual.

## Script útiles
- `gitup.ps1` — commit + push.
- `deploy.bat` / `deploy.ps1` / `DEPLOY.md` — métodos de deploy alternativos (legacy).
- `server.js` — servidor local (puerto 8000).

## Notas / Problemas conocidos
- Los GIF pesados (`tubarao.gif` 16MB, `bellyco.gif` 35MB) hacen lenta la carga en móvil.
- `git add` de archivos muy grandes puede crashear en Windows (agregar de a uno).
- `config.local.php` y `banners.json` deben existir en el servidor (no se suben por git).
```
---
Última actualización: 2026
Estado: Funcional
