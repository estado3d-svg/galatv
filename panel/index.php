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
  .loading-overlay{position:fixed;inset:0;background:rgba(0,0,0,.85);display:none;align-items:center;justify-content:center;flex-direction:column;z-index:1000;color:#fff;text-align:center;padding:20px}
  .loading-overlay.show{display:flex}
  .loading-overlay p{color:#ccc;margin-bottom:15px;font-size:14px}
  .progress-track{width:320px;max-width:90%;height:10px;background:#1c1c1c;border-radius:5px;overflow:hidden;border:1px solid #333}
  .progress-bar{height:100%;width:0%;background:linear-gradient(90deg,#e9b62e,#FFD700);transition:width .2s}
  .loading-overlay .pct{font-size:13px;color:#c9a94a;margin-top:10px}
  .tabs{display:flex;gap:8px;margin-bottom:20px;border-bottom:1px solid #2c2410;padding-bottom:12px}
  .tab{background:transparent;border:1px solid #2c2410;color:#aaa;padding:10px 20px;border-radius:6px;cursor:pointer;font-size:14px;font-weight:bold}
  .tab:hover{color:#fff;border-color:#4a3f14}
  .tab.active{background:linear-gradient(180deg,#ffd46d,#e9ad32);border-color:#b88720;color:#1b1202}
  .pane{display:none}
  .pane.active{display:block}
  .checkbox-row{display:flex;align-items:center;gap:8px;margin-top:10px}
  .checkbox-row input{width:18px;height:18px}
  .programa{display:flex;align-items:center;gap:12px;background:#0a0a0a;border:1px solid #2b2412;border-radius:6px;padding:12px;margin-bottom:10px}
  .programa img{width:166px;height:225px;object-fit:contain;background:#000;border-radius:4px;border:1px solid #333}
  .programa .info{flex:1;min-width:0}
  .programa .info input{width:100%;background:#111;border:1px solid #2c2410;color:#ddd;padding:7px 10px;border-radius:4px;font-size:13px;margin-bottom:6px}
  .programa .info .pf{display:flex;gap:8px}
  .programa .info .pf input{flex:1;margin:0}
  .dias{display:flex;gap:6px;margin:8px 0}
  .dia{width:34px;height:34px;border-radius:50%;border:2px solid #444;color:#666;display:flex;align-items:center;justify-content:center;font-weight:bold;cursor:pointer;font-size:13px;transition:.2s;background:#0a0a0a;user-select:none}
  .dia.on{border-color:#FFD700;color:#FFD700;background:rgba(255,215,0,.15);box-shadow:0 0 8px rgba(255,215,0,.5)}
</style>
</head>
<body>
<div class="msg" id="msg"></div>
<div class="loading-overlay" id="loadingOverlay">
  <p id="loadingText">Subiendo banner...</p>
  <div class="progress-track"><div class="progress-bar" id="progressBar"></div></div>
  <div class="pct" id="progressPct">0%</div>
</div>

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
      <h1>📺 Panel GalaTV</h1>
      <div>
        <span class="user">👤 <?php echo htmlspecialchars($email); ?></span>
        <a href="logout.php" class="btn btn-ghost btn-sm" style="margin-left:10px">Salir</a>
      </div>
    </div>

    <div class="tabs">
      <button class="tab active" data-tab="publicidad">Publicidad</button>
      <button class="tab" data-tab="videoportada">Video de portada</button>
      <button class="tab" data-tab="programacion">Programación</button>
    </div>

    <!-- ===== PESTAÑA: PUBLICIDAD (banners) ===== -->
    <div class="pane active" id="pane-publicidad">
      <div class="card">
        <h2 style="font-size:17px;margin-bottom:15px">Agregar nuevo banner</h2>
        <div class="drop" id="drop">📥 Arrastrá un GIF/PNG aquí, o clickeá para elegir<br><small style="color:#666">1 cuerpo = 333px · 2 cuerpos = 666px · 3 cuerpos = 999px</small></div>
        <input type="file" id="newFile" accept=".gif,.png,.jpg,.jpeg" style="display:none">
        <form id="addForm" class="row" style="margin-top:10px">
          <select id="newBodies" style="background:#111;border:1px solid #2c2410;color:#ddd;padding:9px 12px;border-radius:4px;font-size:13px">
            <option value="1">1 cuerpo (333px)</option>
            <option value="2">2 cuerpos (666px)</option>
            <option value="3">3 cuerpos (999px)</option>
          </select>
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

    <!-- ===== PESTAÑA: VIDEO DE PORTADA ===== -->
    <div class="pane" id="pane-videoportada">
      <div class="card">
        <h2 style="font-size:17px;margin-bottom:15px">Video cuando el canal está OFF</h2>
        <p style="color:#999;font-size:13px;margin-bottom:15px">Este video de YouTube se reproduce en la portada cuando el canal no está en vivo.</p>
        <label style="font-size:13px;color:#FFD700;display:block;margin-bottom:6px">Link del video de YouTube (offline)</label>
        <input type="text" id="offLink" placeholder="https://www.youtube.com/watch?v=..." style="width:100%;background:#111;border:1px solid #2c2410;color:#ddd;padding:10px 12px;border-radius:5px;font-size:14px">
        <div class="checkbox-row">
          <input type="checkbox" id="offLoop" checked>
          <label for="offLoop" style="font-size:13px;color:#ddd">Repetir (loop)</label>
        </div>
        <button class="btn" style="margin-top:15px" onclick="saveSettings()">Guardar video de portada</button>
      </div>
    </div>

    <!-- ===== PESTAÑA: PROGRAMACIÓN ===== -->
    <div class="pane" id="pane-programacion">
      <div class="card">
        <h2 style="font-size:17px;margin-bottom:15px">Agregar programa</h2>
        <form id="addPrograma" class="row" style="flex-direction:column;align-items:flex-start">
          <div style="display:flex;gap:10px;width:100%;flex-wrap:wrap;align-items:flex-end">
            <input type="text" id="npTitulo" placeholder="Título (ej: EL HEREDERO)" style="flex:1;min-width:150px">
            <input type="text" id="npCategoria" placeholder="Categoría (ej: DRAMA)" style="flex:1;min-width:120px">
            <input type="number" id="npHh" min="0" max="23" placeholder="HH" style="flex:0 0 64px;background:#111;border:1px solid #2c2410;color:#ddd;padding:9px 10px;border-radius:4px;font-size:13px" title="Hora (0-23)">
            <span style="color:#888;font-size:16px">:</span>
            <input type="number" id="npMm" min="0" max="59" placeholder="MM" value="00" style="flex:0 0 64px;background:#111;border:1px solid #2c2410;color:#ddd;padding:9px 10px;border-radius:4px;font-size:13px" title="Minutos">
            <div style="display:flex;flex-direction:column">
              <label style="font-size:11px;color:#FFD700;margin-bottom:3px">Imagen</label>
              <button type="button" class="btn btn-ghost btn-sm" onclick="pickNewProgImage()">Elegir imagen</button>
              <input type="file" id="npImage" accept=".gif,.png,.jpg,.jpeg,.webp" style="display:none">
              <span id="npImgName" style="font-size:11px;color:#888;margin-top:3px"></span>
            </div>
          </div>
          <div class="dias" id="npDias">
            <span class="dia" data-d="D">D</span>
            <span class="dia" data-d="L">L</span>
            <span class="dia" data-d="M">M</span>
            <span class="dia" data-d="M2">M</span>
            <span class="dia" data-d="J">J</span>
            <span class="dia" data-d="V">V</span>
            <span class="dia" data-d="S">S</span>
          </div>
          <input type="hidden" id="npDiasVal">
          <button type="submit" class="btn">Agregar programa</button>
        </form>
      </div>
      <div class="card">
        <h2 style="font-size:17px;margin-bottom:15px">Programas</h2>
        <div id="programasList"></div>
      </div>
    </div>
  </div>
<?php endif; ?>

<script>
<?php if ($logged): ?>
const API = 'api.php';
let pendingFile = null;
let currentBanners = [];

function showMsg(text, type) {
  const m = document.getElementById('msg');
  m.textContent = text;
  m.style.display = 'block';
  m.style.background = type === 'error' ? '#2a0d0d' : '#0d2a14';
  m.style.borderColor = type === 'error' ? '#8a1d1d' : '#1d8a3a';
  m.style.color = type === 'error' ? '#ffd7d7' : '#b7f7c8';
  clearTimeout(m._t);
  m._t = setTimeout(() => m.style.display = 'none', 3500);
}

async function loadBanners() {
  const res = await fetch(API + '?action=list');
  const data = await res.json();
  if (!data.success) return;
  currentBanners = data.banners;
  render(data.banners);
}

// Reordenar banner (dir: -1 subir, +1 bajar) en el array global
async function moveBanner(id, dir) {
  const idx = currentBanners.findIndex(b => String(b.id) === String(id));
  if (idx < 0) return;
  const newIdx = idx + dir;
  if (newIdx < 0 || newIdx >= currentBanners.length) return;
  // intercambiar posiciones
  [currentBanners[idx], currentBanners[newIdx]] = [currentBanners[newIdx], currentBanners[idx]];
  render(currentBanners);
  // persistir el nuevo orden
  const order = currentBanners.map(b => String(b.id));
  const fd = new FormData();
  fd.append('action', 'reorder');
  fd.append('order', JSON.stringify(order));
  const res = await fetch(API, { method: 'POST', body: fd });
  const data = await res.json();
  if (!data.success) { showMsg('No se pudo guardar el orden'); loadBanners(); }
}

function render(banners) {
  const list = document.getElementById('list');
  list.innerHTML = '';
  const bodies = b => Math.max(1, Math.min(3, parseInt(b.bodies || 1, 10) || 1));

  if (banners.length === 0) {
    list.innerHTML = '<div style="color:#777;padding:10px">No hay banners.</div>';
    return;
  }

  // Mostrar todos en una lista plana en el orden del array
  const box = document.createElement('div');
  box.innerHTML = `<div style="font-size:13px;color:#FFD700;margin:0 0 8px">Usá ▲▼ para cambiar el orden (se respeta en la página).</div>`;
  banners.forEach((b, i) => {
    const el = document.createElement('div');
    el.className = 'banner';
    el.innerHTML = `
      <img src="../${b.src}" alt="">
      <div class="info">
        <div style="font-size:12px;color:#999;margin-bottom:6px">#${i+1} · ${bodies(b)} cuerpo(s) (${bodies(b)*333}px)</div>
        <label>Cuerpos (1=333px, 2=666px, 3=999px)</label>
        <select data-id="${b.id}" data-field="bodies" style="width:100%;background:#111;border:1px solid #2c2410;color:#ddd;padding:7px 10px;border-radius:4px;margin-bottom:8px">
          <option value="1" ${bodies(b)===1?'selected':''}>1 cuerpo (333px)</option>
          <option value="2" ${bodies(b)===2?'selected':''}>2 cuerpos (666px)</option>
          <option value="3" ${bodies(b)===3?'selected':''}>3 cuerpos (999px)</option>
        </select>
        <label>Link (dejá vacío para que sea solo imagen)</label>
        <input data-id="${b.id}" data-field="link" value="${b.link || ''}">
      </div>
      <div class="actions">
        <button class="btn btn-sm" title="Subir" onclick="moveBanner('${b.id}',-1)">▲</button>
        <button class="btn btn-sm" title="Bajar" onclick="moveBanner('${b.id}',1)">▼</button>
        <button class="btn btn-sm" onclick="saveChanges('${b.id}')">Guardar</button>
        <button class="btn btn-ghost btn-sm" onclick="changeImage('${b.id}')">Cambiar imagen</button>
        <button class="btn btn-danger btn-sm" onclick="del('${b.id}')">Eliminar</button>
      </div>`;
    box.appendChild(el);
  });
  list.appendChild(box);
}

async function saveChanges(id) {
  const linkInput = document.querySelector(`input[data-id="${id}"][data-field="link"]`);
  const bodiesSel = document.querySelector(`select[data-id="${id}"][data-field="bodies"]`);
  const link = linkInput.value.trim();
  const bodies = bodiesSel.value;

  let fd = new FormData();
  fd.append('action', 'update_link');
  fd.append('id', id);
  fd.append('link', link);
  await fetch(API, { method: 'POST', body: fd });

  fd = new FormData();
  fd.append('action', 'update_bodies');
  fd.append('id', id);
  fd.append('bodies', bodies);
  const res = await fetch(API, { method: 'POST', body: fd });
  const data = await res.json();
  showMsg(data.success ? 'Guardado ✓' : 'Error al guardar');
  loadBanners();
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
    uploadWithProgress(fd, data => {
      showMsg(data.success ? 'Imagen actualizada ✓' : (data.error || 'Error'));
      loadBanners();
      hideLoading();
    });
  };
  input.click();
}

// Upload con XMLHttpRequest: muestra barra de progreso y bloquea la UI
function showLoading(text) {
  document.getElementById('loadingText').textContent = text || 'Subiendo banner...';
  document.getElementById('loadingOverlay').classList.add('show');
  document.getElementById('progressBar').style.width = '0%';
  document.getElementById('progressPct').textContent = '0%';
}
function hideLoading() {
  document.getElementById('loadingOverlay').classList.remove('show');
}
function uploadWithProgress(formData, onDone) {
  showLoading();
  const xhr = new XMLHttpRequest();
  xhr.open('POST', API);
  xhr.upload.onprogress = (e) => {
    if (e.lengthComputable) {
      const pct = Math.round((e.loaded / e.total) * 100);
      document.getElementById('progressBar').style.width = pct + '%';
      document.getElementById('progressPct').textContent = pct + '%';
    }
  };
  xhr.onload = () => {
    let data = {};
    try { data = JSON.parse(xhr.responseText); } catch (e) {}
    if (onDone) onDone(data);
  };
  xhr.onerror = () => {
    hideLoading();
    showMsg('Error de conexión al subir');
    if (onDone) onDone({ success: false, error: 'Error de conexión' });
  };
  xhr.send(formData);
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

document.getElementById('addForm').addEventListener('submit', (e) => {
  e.preventDefault();
  if (!pendingFile) { showMsg('Elegí un archivo primero'); return; }
  const fd = new FormData();
  fd.append('action', 'add');
  fd.append('file', pendingFile);
  fd.append('bodies', document.getElementById('newBodies').value);
  fd.append('link', document.getElementById('newLink').value.trim());
  fd.append('alt', document.getElementById('newAlt').value.trim() || 'Banner');
  uploadWithProgress(fd, data => {
    showMsg(data.success ? 'Banner agregado ✓' : (data.error || 'Error'));
    loadBanners();
    hideLoading();
    document.getElementById('addForm').reset();
    pendingFile = null;
    drop.textContent = '📥 Arrastrá un GIF/PNG aquí, o clickeá para elegir';
  });
});

loadBanners();

    // ===== Pestañas =====
    document.querySelectorAll('.tab').forEach(t => {
      t.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
        document.querySelectorAll('.pane').forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        document.getElementById('pane-' + t.dataset.tab).classList.add('active');
        if (t.dataset.tab === 'videoportada') loadSettings();
        if (t.dataset.tab === 'programacion') loadProgramas();
      });
    });

    // ===== Programación =====
    const DIAS = ['D','L','M','M2','J','V','S'];

    function diasStringFromSel(selId) {
      const vals = [];
      document.querySelectorAll(`#${selId} .dia.on`).forEach(d => vals.push(d.dataset.d));
      return vals.join(',');
    }
    function setDias(containerId, str) {
      const on = String(str || '').split(',').map(s => s.trim()).filter(Boolean);
      document.querySelectorAll(`#${containerId} .dia`).forEach(d => {
        d.classList.toggle('on', on.includes(d.dataset.d));
      });
    }
    function bindDias(containerId) {
      document.querySelectorAll(`#${containerId} .dia`).forEach(d => {
        d.addEventListener('click', () => d.classList.toggle('on'));
      });
    }

    async function loadProgramas() {
      const res = await fetch(API + '?action=programas_list');
      const data = await res.json();
      if (!data.success) return;
      const list = document.getElementById('programasList');
      list.innerHTML = '';
      if (!data.programas.length) { list.innerHTML = '<div style="color:#777">No hay programas.</div>'; return; }
      data.programas.forEach(p => {
        const el = document.createElement('div');
        el.className = 'programa';
        const img = p.imagen ? `<img src="../${p.imagen}" alt="">` : '<img src="" alt="" style="opacity:.2">';
        const hp = parseHora(p.hora); // {hh, mm, ampm}
        el.innerHTML = `
          ${img}
          <div class="info">
            <input data-pid="${p.id}" data-f="titulo" value="${(p.titulo||'').replace(/"/g,'&quot;')}">
            <div class="pf">
              <input data-pid="${p.id}" data-f="categoria" value="${(p.categoria||'').replace(/"/g,'&quot;')}" placeholder="Categoría">
              <div style="display:flex;gap:5px;align-items:center">
                <input type="number" data-pid="${p.id}" data-f="hh" min="0" max="23" value="${hp.hh}" style="flex:0 0 58px;background:#111;border:1px solid #2c2410;color:#ddd;padding:7px 9px;border-radius:4px;font-size:12px" placeholder="HH">
                <span style="color:#888">:</span>
                <input type="number" data-pid="${p.id}" data-f="mm" min="0" max="59" value="${hp.mm}" style="flex:0 0 58px;background:#111;border:1px solid #2c2410;color:#ddd;padding:7px 9px;border-radius:4px;font-size:12px" placeholder="MM">
              </div>
            </div>
            <div class="dias" id="dias-${p.id}">
              ${DIAS.map(dd => `<span class="dia" data-d="${dd}">${dd === 'M2' ? 'M' : dd}</span>`).join('')}
            </div>
          </div>
          <div class="actions">
            <button class="btn btn-sm" onclick="savePrograma('${p.id}')">Guardar</button>
            <button class="btn btn-ghost btn-sm" onclick="changeProgramaImage('${p.id}')">Imagen</button>
            <button class="btn btn-danger btn-sm" onclick="delPrograma('${p.id}')">Eliminar</button>
          </div>`;
        list.appendChild(el);
        setDias('dias-' + p.id, p.dias);
        bindDias('dias-' + p.id);
      });
    }

    function parseHora(hora) {
      const m = String(hora || '').trim().match(/^(\d{1,2}):(\d{2})/);
      if (m) {
        return { hh: String(parseInt(m[1], 10)).padStart(2, '0'), mm: m[2] || '00' };
      }
      return { hh: '00', mm: '00' };
    }

    function buildHora(hh, mm) {
      const h = String(parseInt(hh || '0', 10) || 0).padStart(2, '0');
      const mi = String(parseInt(mm || '0', 10) || 0).padStart(2, '0');
      return h + ':' + mi;
    }

    async function savePrograma(id) {
      const g = f => document.querySelector(`input[data-pid="${id}"][data-f="${f}"]`).value.trim();
      const fd = new FormData();
      fd.append('action', 'programas_save');
      fd.append('id', id);
      fd.append('titulo', g('titulo'));
      fd.append('categoria', g('categoria'));
      fd.append('dias', diasStringFromSel('dias-' + id));
      fd.append('hora', buildHora(g('hh'), g('mm')));
      const res = await fetch(API, { method: 'POST', body: fd });
      const data = await res.json();
      showMsg(data.success ? 'Programa guardado ✓' : (data.error || 'Error al guardar'), data.success ? 'ok' : 'error');
      loadProgramas();
    }

    async function delPrograma(id) {
      if (!confirm('¿Eliminar este programa?')) return;
      const fd = new FormData();
      fd.append('action', 'programas_delete');
      fd.append('id', id);
      const res = await fetch(API, { method: 'POST', body: fd });
      const data = await res.json();
      showMsg(data.success ? 'Programa eliminado ✓' : 'Error');
      loadProgramas();
    }

    function changeProgramaImage(id) {
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = '.gif,.png,.jpg,.jpeg,.webp';
      input.onchange = () => {
        const fd = new FormData();
        fd.append('action', 'programas_image');
        fd.append('id', id);
        fd.append('file', input.files[0]);
        uploadWithProgress(fd, data => {
          showMsg(data.success ? 'Imagen actualizada ✓' : (data.error || 'Error'));
          loadProgramas();
          hideLoading();
        });
      };
      input.click();
    }

    document.getElementById('addPrograma').addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData();
      fd.append('action', 'programas_save');
      fd.append('id', 0);
      fd.append('titulo', document.getElementById('npTitulo').value.trim());
      fd.append('categoria', document.getElementById('npCategoria').value.trim());
      fd.append('dias', diasStringFromSel('npDias'));
      fd.append('hora', buildHora(document.getElementById('npHh').value, document.getElementById('npMm').value));
      const res = await fetch(API, { method: 'POST', body: fd });
      const data = await res.json();
      if (data.success) {
        // Si se eligió imagen, subirla al programa recién creado
        const imgFile = document.getElementById('npImage').files[0];
        if (imgFile) {
          const cols = await (await fetch(API + '?action=programas_list')).json();
          const nuevo = cols.programas[cols.programas.length - 1];
          if (nuevo && nuevo.id) {
            const ifd = new FormData();
            ifd.append('action', 'programas_image');
            ifd.append('id', nuevo.id);
            ifd.append('file', imgFile);
            await new Promise(resolve => {
              const xhr = new XMLHttpRequest();
              xhr.open('POST', API);
              xhr.onload = resolve;
              xhr.send(ifd);
            });
          }
        }
        showMsg('Programa agregado ✓');
        document.getElementById('addPrograma').reset();
        document.getElementById('npMm').value = '00';
        document.getElementById('npImgName').textContent = '';
        document.querySelectorAll('#npDias .dia').forEach(d => d.classList.remove('on'));
        loadProgramas();
      } else {
        showMsg(data.error || 'Error al agregar');
      }
    });

    // Elegir imagen para el nuevo programa
    function pickNewProgImage() {
      document.getElementById('npImage').click();
    }
    document.getElementById('npImage').addEventListener('change', function() {
      document.getElementById('npImgName').textContent = this.files.length ? '✅ ' + this.files[0].name : '';
    });

    bindDias('npDias');

// ===== Video de portada =====
async function loadSettings() {
  const res = await fetch(API + '?action=settings_get');
  const data = await res.json();
  if (!data.success) return;
  // Si no hay link guardado, mostrar el video por defecto que usa la página
  const defOff = 'https://www.youtube.com/watch?v=pDrOzULyCpo';
  document.getElementById('offLink').value = data.settings.off_link || defOff;
  document.getElementById('offLoop').checked = parseInt(data.settings.off_loop) === 1;
}
async function saveSettings() {
  const fd = new FormData();
  fd.append('action', 'settings_save');
  fd.append('off_link', document.getElementById('offLink').value.trim());
  fd.append('off_loop', document.getElementById('offLoop').checked ? '1' : '0');
  const res = await fetch(API, { method: 'POST', body: fd });
  const data = await res.json();
  showMsg(data.success ? 'Video de portada guardado ✓' : 'Error al guardar');
}
<?php endif; ?>
</script>
</body>
</html>
