# GalaTV Streaming

Sitio web de **GalaTV Streaming** (https://galatv.com.ar/) con tema premium **dark/gold**. Muestra el canal en vivo de YouTube y permite administrar contenido desde un panel seguro.

## ✨ Funcionalidades

- **Hero con video en vivo** de YouTube (usa el canal; si está offline reproduces un video de respaldo con loop configurable).
- **Programación semanal** en carrusel (autoavance, flechas, velocidad configurable, máx. 4 cards por vista).
- **Banners publicitarios** dinámicos de 1, 2 o 3 "cuerpos" (333/666/999px), con enlace opcional.
- **Panel de administración** (`/panel/`) con login Google (solo 2 emails) para gestionar banners, video de portada y programación.
- **Formulario de contacto** que envía emails por SMTP (PHPMailer).
- **Responsive** + toggle de idioma ES/EN.

## 🛠️ Stack

| Capa | Tecnología |
|------|-----------|
| Frontend | HTML5, CSS3, Vanilla JS, Font Awesome (CDN) |
| Backend | PHP 8.x (Ferozo/DonWeb), PDO MySQL |
| Base de datos | MySQL |
| Email | PHPMailer + SMTP |
| Login | Google OAuth 2.0 (2 usuarios permitidos) |
| Deploy | GitHub Actions → FTP (curl) |

## 📂 Estructura del proyecto

```
galatv/
├── index.html              # Página principal
├── styles.css              # Estilos
├── script.js               # Scripts auxiliares
├── banners_api.php         # Endpoint público → lee BD (banners + settings + programas)
├── contacto.php / contact.php  # Formulario de contacto (SMTP)
├── mail-config.php         # Credenciales SMTP (protegido por .htaccess)
├── server.js               # Servidor local (puerto 8000)
├── .htaccess               # Protección + rewrite de /contacto
├── gitup.ps1               # Script: commit + push (dispara deploy)
├── panel/                  # Panel de administración
│   ├── index.php           # UI (3 pestañas) + login
│   ├── api.php             # API interna (requiere sesión)
│   ├── config.php          # Config general
│   ├── config.local.php    # Credenciales reales (NO se sube a GitHub, se sube por FTP)
│   ├── db.php              # Conexión PDO a MySQL
│   ├── google-login.php / google-callback.php / logout.php
│   ├── gifs/               # GIFs de banners subidos desde el panel
│   └── img_prog/           # Imágenes de programas (333×450)
└── assets/                 # Imágenes (cards, hero, logo)
```

## 🚀 Puesta en marcha

### Local (servidor Node)
```bash
cd D:\ia\proyectos\galatv
node server.js
# abrir http://127.0.0.1:8000
```

### Despliegue (automático)
```powershell
.\gitup.ps1 -Message "tu mensaje"   # add + commit + push → GitHub Actions sube a FTP
```
El workflow `.github/workflows/deploy.yml` hace upload por FTP con `curl --ssl-reqd`.

### Subida manual al FTP (para cambios urgentes que no esperan el deploy)
> ⚠️ IMPORTANTE: usar comillas **simples** porque el password FTP contiene `*`.
```bash
curl --ssl-reqd --user 'c2642305:Argentinaconsalud26/' \
  --upload-file "panel/index.php" \
  "ftp://c2642305.ferozo.com/public_html/panel/index.php"
```

### Panel de administración
- URL: https://galatv.com.ar/panel/
- Login con Google (solo `galatvstreaming@gmail.com` y `bodorola@gmail.com`).
- **3 pestañas:**
  1. **Publicidad** — subir/editar/borrar/reordenar banners (1-3 cuerpos, link opcional).
  2. **Video de portada** — link del video que se muestra cuando el canal está OFF + checkbox "Repetir".
  3. **Programación** — CRUD de programas (título, categoría, días en círculos D-L-M-M2-J-V-S, hora 24h, imagen 333×450) + config del carrusel (velocidad 1-10s, autoavance, mostrar/ocultar).

## 🗄️ Base de datos (MySQL)

### Tabla `banners`
| Columna | Tipo | Descripción |
|---------|------|-------------|
| id | INT PK | Identificador |
| src | VARCHAR | Ruta del archivo (ej: `panel/gifs/banner-1-123.gif`) |
| link | VARCHAR | URL al clic (vacío = solo imagen) |
| bodies | TINYINT | 1, 2 o 3 cuerpos |
| alt | VARCHAR | Texto alternativo |
| position | INT | Orden en la página |

### Tabla `settings` (una sola fila, id=1)
| Columna | Tipo | Descripción |
|---------|------|-------------|
| id | PK | Siempre 1 |
| off_link | VARCHAR | Video de YouTube cuando el canal está OFF |
| off_loop | TINYINT | 1 = repetir en bucle |
| carousel_speed | INT | Segundos entre cards (1-10) |
| carousel_auto | TINYINT | 1 = autoavance, 0 = no |
| programacion_activa | TINYINT | 1 = mostrar sección, 0 = ocultar |

### Tabla `programas`
| Columna | Tipo | Descripción |
|---------|------|-------------|
| id | INT PK | Identificador |
| titulo | VARCHAR | Título del programa |
| categoria | VARCHAR | Categoría (ej: DRAMA) |
| dias | VARCHAR | Letras por coma: D,L,M,M2,J,V,S |
| hora | VARCHAR | HH:MM (24h, ej: 20:00) |
| imagen | VARCHAR | Ruta imagen (panel/img_prog/...) |
| posicion | INT | Orden del carrusel |

## 🔑 Datos de conexión (referencia)

| Servicio | Valor |
|----------|-------|
| FTP | `c2642305.ferozo.com` / user `c2642305` / pass en mensaje, carpeta `public_html` |
| BD | `localhost` / `c2642305_1` / user `c2642305_1` / pass en `config.local.php` |
| SMTP | `mail.galatv.com.ar:587` STARTTLS, user `contacto@galatv.com.ar` |
| YouTube | canal `@GalaTvStreaming`, channel ID `UCNbKWLI2_ivAZIAQQ5ot6Ng`, API key en `index.html` |

> ⚠️ Las contraseñas reales están en `panel/config.local.php` y `mail-config.php` (en el servidor), NO en el repo.

## 🔒 Seguridad

- `config.local.php` (Google OAuth + BD) **no se sube a GitHub** (`.gitignore`); se sube manual por FTP.
- Google OAuth restringido a 2 emails (validado en `google-callback.php`).
- API key de YouTube limitada por dominio (Referer).
- Archivos sensibles (`mail-config.php`, `config.php`, `config.local.php`, `db.php`, `*.log`) protegidos por `.htaccess` (403).
- Cocletas de sesión seguras (HttpOnly, Secure, SameSite).
- Consultas a BD con PDO preparadas (previene SQL injection).

## 📝 Notas para el desarrollador

- **Deploy y archivos no versionados:** `config.local.php` y `banners.json` (si se usa) deben existir en el servidor y se suben por FTP manual, no por git.
- **GIFs pesados:** `bellyco.gif` (~35MB) y `tubarao.gif` (~16MB) pueden hacer lenta la página en móvil.
- **Servidor local:** `node server.js` sirve estáticos en puerto 8000, pero **NO ejecuta PHP** (la parte dinámica y el panel necesitan el hosting).
- **Cambiar CSS/JS de la página principal:** editar `styles.css` / `index.html`, subir por FTP o con `gitup.ps1`.

## 📄 Licencia

© 2026 GalaTV. Todos los derechos reservados.
