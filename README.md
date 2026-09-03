# GalaTV Streaming

Sitio web de **GalaTV Streaming** (https://galatv.com.ar/) con tema premium **dark/gold**, que muestra el canal en vivo y permite administrar contenido desde un panel.

## ✨ Características

- **Hero con video en vivo** de YouTube (usa el canal; si está offline reproduce un video de respaldo con loop).
- **Programación semanal** en carrusel (autoavance, flechas, vel. configurable, máx. 4 cards).
- **Banners publicitarios** dinámicos (de 1, 2 o 3 cuerpos), con enlace opcional.
- **Panel de administración** (`/panel/`) con login de Google para gestionar banners, video de portada y programación.
- **Formulario de contacto** que envía emails por SMTP (PHPMailer).
- **Tema oscuro/dorado** con fondos de luz dorada, responsive y con toggler de idioma ES/EN.

## 🛠️ Stack

| Capa | Tecnología |
|------|-----------|
| Frontend | HTML5, CSS3, Vanilla JS, Three.js no (es estático), Font Awesome (CDN) |
| Backend | PHP 8.x (Ferozo/DonWeb), PDO MySQL |
| Base de datos | MySQL (`c2642305_1`) |
| Email | PHPMailer + SMTP |
| Login | Google OAuth 2.0 (2 usuarios permitidos) |
| Deploy | GitHub Actions → FTP (curl) |

## 📂 Estructura

```
galatv/
├── index.html              # Página principal
├── styles.css              # Estilos
├── script.js               # Scripts auxiliares
├── banners_api.php         # Endpoint público (BD: banners + settings + programas)
├── contacto.php / contact.php  # Formulario de contacto (SMTP)
├── mail-config.php         # Credenciales SMTP (protegido)
├── server.js               # Servidor local (puerto 8000)
├── .htaccess               # Protección + rewrite
├── gitup.ps1               # Commit + push (dispara deploy)
├── panel/                  # Panel de administración
│   ├── index.php           # UI (3 pestañas) + login
│   ├── api.php             # API interna (requiere sesión)
│   ├── config.php          # Config
│   ├── config.local.php    # Credenciales (NO en git)
│   ├── db.php              # Conexión PDO
│   ├── google-login.php / google-callback.php / logout.php
│   ├── gifs/               # GIFs de banners subidos
│   └── img_prog/           # Imágenes de programas subidas
└── assets/                 # Imágenes
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
.\gitup.ps1 -Message "tu mensaje"   # commit + push → GitHub Actions sube a FTP
```

### Panel
- URL: https://galatv.com.ar/panel/
- Login con Google (solo `galatvstreaming@gmail.com` y `bodorola@gmail.com`).
- Pestañas:
  1. **Publicidad** — subir/editar/borrar/reordenar banners.
  2. **Video de portada** — link de video offline + opción "Repetir".
  3. **Programación** — CRUD de programas + config del carrusel (velocidad, auto, mostrar/ocultar).

## 🗄️ Base de datos

Tablas: `banners`, `settings` (off_link, off_loop, carousel_speed, carousel_auto, programacion_activa), `programas` (titulo, categoria, dias, hora, imagen, posicion).

## 🔒 Seguridad

- `config.local.php` (Google OAuth + BD) no se sube a GitHub (`.gitignore`), se sube por FTP manual.
- Google OAuth restringido a 2 emails.
- API key de YouTube limitada por dominio.
- Archivos sensibles protegidos por `.htaccess` (403).

## 📝 Notas

- Los GIF grandes (bellyco 35MB, tubarao 16MB) pueden hacer lenta la carga en móvil.
- Para cambios urgentes del panel, se suben por curl FTP manual (sin esperar el deploy).

## 📄 Licencia

© 2026 GalaTV. Todos los derechos reservados.
