<?php
session_start();
$logged = !empty($_SESSION['logged_in']);
$email = $_SESSION['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel GalaTV</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:Segoe UI,Arial,sans-serif;background:#050505;color:#fff;min-height:100vh}
  a{color:#FFD700}
  .wrap{max-width:1000px;margin:0 auto;padding:30px 20px}
  h1{color:#FFD700;font-size:26px;margin-bottom:5px}
  .sub{color:#999;font-size:14px;margin-bottom:25px}
  .card{background:#0c0c0c;border:1px solid #2c2410;border-radius:8px;padding:20px;margin-bottom:18px}
  .btn{display:inline-block;background:linear-gradient(180deg,#ffd46d,#e9ad32);border:1px solid #b88720;color:#1b1202;padding:12px 22px;border-radius:5px;font-weight:bold;font-size:14px;cursor:pointer;text-decoration:none;border:none}
  .btn:hover{filter:brightness(1.05)}
  .btn-ghost{background:transparent;border:1px solid #806116;color:#eee}
  .btn-danger{background:#5c1414;border:1px solid #8a1d1d;color:#ffd7d7}
  .btn-sm{padding:7px 12px;font-size:12px;border-radius:4px}
  .banner{display:flex;align-items:center;gap:15px;background:#0a0a0a;border:1px solid #2b2412;border-radius:6px;padding:12px;margin-bottom:12px}
  .banner img{width:120px;height:54px;object-fit:contain;background:#000;border-radius:4px;border:1px solid #333}
  .banner .info{flex:1;min-width:0}
  .banner .info label{font-size:11px;color:#FFD700;display:block;margin-bottom:3px}
  .banner .info input{width:100%;background:#111;border:1px solid #2c2410;color:#ddd;padding:7px 10px;border-radius:4px;font-size:13px}
  .banner .actions{display:flex;flex-direction:column;gap:6px}
  .login-box{max-width:420px;margin:80px auto;text-align:center}
  .login-box h1{margin-bottom:10px}
  .login-box p{color:#999;margin-bottom:25px}
  .google-btn{display:inline-flex;align-items:center;gap:10px;background:#fff;color:#333;border-radius:5px;padding:12px 24px;font-weight:bold;font-size:15px;text-decoration:none}
  .google-btn:hover{filter:brightness(0.95)}
  .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px}
  .topbar .user{font-size:13px;color:#999}
  .drop{border:2px dashed #4a3f14;border-radius:8px;padding:30px;text-align:center;color:#888;cursor:pointer;margin-bottom:15px;background:#0a0a0a}
  .drop:hover{border-color:#FFD700;color:#ccc}
  form.row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
  form.row input[type=text]{background:#111;border:1px solid #2c2410;color:#ddd;padding:9px 12px;border-radius:4px;font-size:13px;flex:1;min-width:180px}
  .msg{position:fixed;top:15px;right:15px;background:#17130a;border:1px solid #8d681c;color:#f4c747;padding:12px 18px;border-radius:5px;display:none;z-index:99}
</style>
</head>
<body>
<div class="msg" id="msg"></div>

<?php if (!$logged): ?>
  <div class="login-box">
    <h1>🔑 Panel GalaTV</h1>
    <p>Iniciá sesión con tu cuenta de Google para administrar los banners.</p>
    <a class="google-btn" href="google-login.php">
      <i class="fab fa-google"></i> Entrar con Google
    </a>
  </div>
<?php else: ?>
  <div class="wrap">
    <div class="topbar">
      <h1>📺 Panel de Banners</h1>
      <div>
        <span class="user">👤 <?php echo htmlspecialchars($email); ?></span>
        <a href="logout.php" class="btn btn-ghost btn-sm" style="margin-left:10px">Salir</a>
      </div>
    </div>

    <div class="card">
      <h2 style="font-size:17px;margin-bottom:15px">Agregar nuevo banner</h2>
      <div class="drop" id="drop">📥 Arrastrá un GIF/PNG aquí, o clickeá para elegir<br><small style="color:#666">Se subirá a 333×150px</small></div>
      <input type="file" id="newFile" accept=".gif,.png,.jpg,.jpeg" style="display:none">
      <form id="addForm" class="row" style="margin-top:10px">
        <input type="text" id="newLink" placeholder="Link (opcional, ej: https://...)">
        <input type="text" id="newAlt" placeholder="Texto alternativo" value="Banner">
        <button type="submit" class="btn">Subir banner</button>
      </form>
    </div>

    <div class="card">
      <h2 style="font-size:17px;margin-bottom:15px">Banners existentes</h2>
      <div id="list"></div>
    </div>
  </div>
<?php endif; ?>

<script>
<?php if ($logged): ?>
const API = 'api.php';
let pendingFile = null;

function showMsg(text) {
  const m = document.getElementById('msg');
  m.textContent = text;
  m.style.display = 'block';
  setTimeout(() => m.style.display = 'none', 3000);
}

async function loadBanners() {
  const res = await fetch(API + '?action=list');
  const data = await res.json();
  if (!data.success) return;
  render(data.banners);
}

function render(banners) {
  const list = document.getElementById('list');
  list.innerHTML = '';
  const row1 = banners.filter(b => (b.row || 1) === 1);
  const row2 = banners.filter(b => b.row === 2);

  const section = (title, arr) => {
    const box = document.createElement('div');
    box.innerHTML = `<div style="font-size:13px;color:#FFD700;margin:12px 0 8px">${title}</div>`;
    arr.forEach(b => {
      const el = document.createElement('div');
      el.className = 'banner';
      el.innerHTML = `
        <img src="../${b.src}" alt="">
        <div class="info">
          <label>Link (dejá vacío para que sea solo imagen)</label>
          <input data-id="${b.id}" data-field="link" value="${b.link || ''}">
        </div>
        <div class="actions">
          <button class="btn btn-sm" onclick="saveLink('${b.id}')">Guardar link</button>
          <button class="btn btn-ghost btn-sm" onclick="changeImage('${b.id}')">Cambiar imagen</button>
          <button class="btn btn-danger btn-sm" onclick="del('${b.id}')">Eliminar</button>
        </div>`;
      box.appendChild(el);
    });
    list.appendChild(box);
  };

  section('Fila 1 (banners de 333px)', row1);
  section('Fila 2 (banner grande 666px)', row2);
  attachLinkListeners();
}

function attachLinkListeners() {
}

async function saveLink(id) {
  const input = document.querySelector(`input[data-id="${id}"]`);
  const link = input.value.trim();
  const fd = new FormData();
  fd.append('action', 'update_link');
  fd.append('id', id);
  fd.append('link', link);
  const res = await fetch(API, { method: 'POST', body: fd });
  const data = await res.json();
  showMsg(data.success ? 'Link guardado ✓' : 'Error al guardar');
}

async function del(id) {
  if (!confirm('¿Eliminar este banner?')) return;
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id', id);
  const res = await fetch(API, { method: 'POST', body: fd });
  const data = await res.json();
  showMsg(data.success ? 'Banner eliminado ✓' : 'Error');
  loadBanners();
}

function changeImage(id) {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = '.gif,.png,.jpg,.jpeg';
  input.onchange = async () => {
    const fd = new FormData();
    fd.append('action', 'update_image');
    fd.append('id', id);
    fd.append('file', input.files[0]);
    const res = await fetch(API, { method: 'POST', body: fd });
    const data = await res.json();
    showMsg(data.success ? 'Imagen actualizada ✓' : (data.error || 'Error'));
    loadBanners();
  };
  input.click();
}

// Drag & drop / seleccionar para nuevo banner
const drop = document.getElementById('drop');
const fileInput = document.getElementById('newFile');
drop.addEventListener('click', () => fileInput.click());
drop.addEventListener('dragover', e => { e.preventDefault(); drop.style.borderColor = '#FFD700'; });
drop.addEventListener('dragleave', () => drop.style.borderColor = '#4a3f14');
drop.addEventListener('drop', e => {
  e.preventDefault();
  drop.style.borderColor = '#4a3f14';
  if (e.dataTransfer.files.length) { pendingFile = e.dataTransfer.files[0]; drop.textContent = '✅ ' + pendingFile.name; }
});
fileInput.addEventListener('change', () => { if (fileInput.files.length) { pendingFile = fileInput.files[0]; drop.textContent = '✅ ' + pendingFile.name; } });

document.getElementById('addForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  if (!pendingFile) { showMsg('Elegí un archivo primero'); return; }
  const fd = new FormData();
  fd.append('action', 'add');
  fd.append('file', pendingFile);
  fd.append('link', document.getElementById('newLink').value.trim());
  fd.append('alt', document.getElementById('newAlt').value.trim() || 'Banner');
  const res = await fetch(API, { method: 'POST', body: fd });
  const data = await res.json();
  showMsg(data.success ? 'Banner agregado ✓' : (data.error || 'Error'));
  loadBanners();
  document.getElementById('addForm').reset();
  pendingFile = null;
  drop.textContent = '📥 Arrastrá un GIF/PNG aquí, o clickeá para elegir';
});

loadBanners();
<?php endif; ?>
</script>
</body>
</html>
