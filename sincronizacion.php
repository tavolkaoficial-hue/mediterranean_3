<?php
// sincronizacion.php
// Interfaz de Sincronización (frontend) - conectado con API endpoints en /api/

session_start();
// Opcional: validar sesión de usuario
// if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'admin';

?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Sincronización - Mediterranean</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  /* ===== Fondo ===== */
#background {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: linear-gradient(135deg,#004e92,#0a1f44);
  z-index: -2;
}
#particles {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  z-index: -1;
  pointer-events: none;
}
/* ===== Fondo Canvas ===== */
#bgCanvas {
  position: fixed;
  top:0; left:0;
  width:100%; height:100%;
  z-index:-2;
}
  .main {
  margin-left: 240px;  /* deja espacio para el sidebar */
  padding: 20px;
}

:root{
  --bg:#071021;
  --card: rgba(255,255,255,0.04);
  --accent: #00eaff;
  --glass: rgba(255,255,255,0.06);
}
*{box-sizing:border-box;font-family:Inter, system-ui, Arial; margin:0}
body{background:linear-gradient(135deg,#041226,#052a3a); color:#eaf6ff; min-height:100vh;}
/* ===== Sidebar ===== */
.sidebar {
  position: fixed;
  top: 0; left: 0;
  width: 240px; height: 100%;
  background: linear-gradient(135deg,#050d1c,#0a1f3d,#0e2a47,#122f68);
  background-size: 400% 400%;
  animation: sidebarAnim 10s ease infinite;
  padding-top: 40px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  z-index: 1000;
  box-shadow: 2px 0 25px rgba(0,0,0,0.8);
  overflow: hidden;
}
.sidebar::before {
  content: "";
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: radial-gradient(circle,rgba(0,242,254,0.4) 1px,transparent 1px);
  background-size: 40px 40px;
  animation: moverPuntos 6s linear infinite;
  opacity: 0.6;
}
.sidebar a {
  color: #00f2fe;
  text-decoration: none;
  padding: 12px 20px;
  font-weight: bold;
  transition: all 0.3s ease;
  border-radius: 8px;
  margin: 0 15px;
  text-shadow: 0 0 8px #00f2fe, 0 0 15px #4facfe;
  display: flex;
  align-items: center;
  gap: 10px;
  position: relative;
  z-index: 2;
}
.sidebar a:hover,
.sidebar a.active {
  background: rgba(255,255,255,0.1);
  color: #fff;
  transform: translateX(6px) scale(1.05);
  box-shadow: 0 0 15px #00f2fe, inset 0 0 10px #00f2fe;
}
@keyframes sidebarAnim { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
@keyframes moverPuntos { from{background-position:0 0} to{background-position:40px 40px} }

/* ===== Animaciones ===== */
@keyframes fadeInUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
@keyframes moverFondo {0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
@keyframes sidebarAnim {0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
@keyframes moverPuntos {from{background-position:0 0}to{background-position:40px 40px}}
@keyframes brillo {from{transform:translateY(-100%)}to{transform:translateY(100%)}}



.sidebar img {
  width: 120px;
  height: auto;
  margin: 0 auto 15px auto;
  display: block;
  filter: drop-shadow(0 0 6px rgba(0,242,254,0.4));
}

/* Layout */
.header{ display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; }
.title h1{ font-size:20px; color:var(--accent); margin-bottom:4px }
.subtitle{ color:rgba(234,250,255,0.7); font-size:13px }

/* Cards */
.grid{ display:grid; grid-template-columns: 1fr 420px; gap:20px; margin-top:16px; }
.card{ background:var(--card); border-radius:14px; padding:18px; border:1px solid rgba(255,255,255,0.03); box-shadow: 0 6px 20px rgba(0,0,0,0.4); }
.controls{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.select, .btn, .input { font-size:14px; padding:10px 12px; border-radius:10px; outline:none; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit; }
.btn { background:linear-gradient(90deg,var(--accent),#66f0ff); color:#052026; font-weight:700; cursor:pointer; }
.btn.secondary{ background:transparent; border:1px solid rgba(255,255,255,0.06); color:var(--accent) }
.kpi{ display:flex; gap:12px; margin-top:12px; }
.kpi .v{ background:var(--glass); padding:12px 16px; border-radius:10px; font-weight:700; color:var(--accent) }

/* Table */
.table-wrap{ max-height:420px; overflow:auto; margin-top:12px }
.table{ width:100%; border-collapse:collapse; font-size:14px; }
.table th, .table td{ padding:10px 8px; text-align:left; border-bottom:1px solid rgba(255,255,255,0.03) }
.table th{ color:var(--accent); font-weight:700 }

/* Console / logs */
.console{ background: #00111a; padding:12px; border-radius:10px; font-family:monospace; font-size:13px; height:220px; overflow:auto; border:1px solid rgba(0,234,255,0.06); }

/* Responsive */
@media(max-width:1000px){
  .grid{ grid-template-columns: 1fr; }
  .sidebar{ position:relative; width:100%; height:auto; padding:12px; border-right:none; display:flex; gap:12px; }
  .main{ margin-left:0; padding:12px; }
  footer{ left:0; width:100%; position:relative; padding:12px; margin-top:20px; text-align:center }
}
footer{ color:rgba(234,250,255,0.7); margin-top:18px; font-size:13px }
</style>
</head>
<body>
  <div id="background"></div>
<canvas id="particles"></canvas>

<!-- Sidebar -->
 <div class="sidebar">
  <img src="images/LogoMediterranean1992.png" alt="Logo Mediterranean">

  <a href="mediterranean.php"><i class="fas fa-home"></i> Menu Principal</a>
  <a href="productos.html"><i class="fas fa-boxes"></i> Productos</a>
  <a href="sucursales.html"><i class="fas fa-store"></i> Sucursales</a>
  <a href="usuarios.html"><i class="fas fa-users"></i> Usuarios</a>
  <a href="reportes.html"><i class="fas fa-chart-bar"></i> Reportes</a>
  <a href="utilidades.html"><i class="fas fa-cogs"></i> Utilidades</a>
   <a href="utilidades.html"><i class="fa-solid fa-arrow-left"></i> Volver</a>  
</div>

<!-- Main -->
<div class="main">
  <div class="header">
    <div class="title">
      <h1><i class="fas fa-sync"></i> Sincronización de Inventarios</h1>
      <div class="subtitle">Comparar y sincronizar stock entre sucursales (base: <strong>mediterranean</strong>)</div>
    </div>
    <div class="controls">
      <div class="kpi">
        <div class="v">Usuario: <?php echo htmlspecialchars($usuario); ?></div>
      </div>
    </div>
  </div>

  <div class="grid">
    <!-- LEFT: UI principal -->
    <div class="card">

      <h3>1) Selecciona sucursales</h3>
      <div style="display:flex; gap:10px; margin-top:8px; align-items:center;">
        <select id="sucursal_origen" class="select" style="flex:1;">
          <option value="">Cargando sucursales...</option>
        </select>

        <select id="sucursal_destino" class="select" style="flex:1;">
          <option value="">Cargando sucursales...</option>
        </select>

        <button id="btnComparar" class="btn secondary">Comparar</button>
      </div>

      <div style="margin-top:14px;">
        <button id="btnRevisarTodas" class="btn">Revisar todo (simular auditoría)</button>
        <button id="btnSincronizar" class="btn" style="margin-left:8px;">Sincronizar diferencias</button>
      </div>

      <div class="card" style="margin-top:14px;">
        <h3>2) Resultado de comparación</h3>
        <div style="font-size:13px; color:rgba(255,255,255,0.8);">Se mostrará la diferencia de stock por SKU y producto.</div>

        <div class="table-wrap">
          <table class="table" id="tablaDiferencias">
            <thead>
              <tr><th>SKU</th><th>Producto</th><th>Stock Origen</th><th>Stock Destino</th><th>Diferencia (origen - destino)</th></tr>
            </thead>
            <tbody>
              <tr><td colspan="5" style="opacity:0.7">Haz clic en <strong>Comparar</strong> para cargar diferencias</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div style="margin-top:12px;">
        <h3>3) Consola / Logs</h3>
        <div id="console" class="console">Aquí aparecerán logs de la sincronización...</div>
      </div>

    </div>

    <!-- RIGHT: panel de acciones -->
    <div class="card">
      <h3>Panel rápido</h3>
      <p style="color:rgba(255,255,255,0.8)">Opciones rápidas y estado</p>

      <div style="margin-top:10px;">
        <div><strong>Última comparación:</strong> <span id="lastCompare">—</span></div>
        <div style="margin-top:8px"><strong>Items mostrados:</strong> <span id="itemsShown">0</span></div>
      </div>

      <div style="margin-top:12px;">
        <button id="btnExportCSV" class="btn secondary" style="width:100%;">Exportar diferencias (CSV)</button>
      </div>

      <hr style="border-color:rgba(255,255,255,0.03); margin:16px 0;">

      <h4>Estado rápido</h4>
      <div style="display:flex; gap:10px; margin-top:8px;">
        <div style="flex:1; background:rgba(255,255,255,0.03); padding:10px; border-radius:8px;"><strong id="totalSKUs">0</strong><div style="font-size:12px">SKUs únicos</div></div>
        <div style="flex:1; background:rgba(255,255,255,0.03); padding:10px; border-radius:8px;"><strong id="totalDiff">0</strong><div style="font-size:12px">Diferencias</div></div>
      </div>

    </div>
  </div>

  

<script>
/*
  Cliente JS:
  - obtiene sucursales
  - compara inventarios (llama a api/comparar_inventarios.php)
  - muestra tabla de diferencias
  - sincroniza (llama a api/sincronizar_inventario.php)
*/

// Helper: append console
function log(msg){
  const c = document.getElementById('console');
  const ts = new Date().toLocaleString();
  c.innerHTML = '['+ts+'] ' + msg + '<br>' + c.innerHTML;
}

// Cargar sucursales al inicio
async function cargarSucursales(){
  const selO = document.getElementById('sucursal_origen');
  const selD = document.getElementById('sucursal_destino');
  selO.innerHTML = '<option value="">Cargando...</option>';
  selD.innerHTML = '<option value="">Cargando...</option>';
  try{
    const res = await fetch('api/obtener_sucursales.php');
    const data = await res.json();
    selO.innerHTML = '<option value="">-- Seleccionar origen --</option>';
    selD.innerHTML = '<option value="">-- Seleccionar destino --</option>';
    data.forEach(s => {
      const o = document.createElement('option');
      o.value = s.id; o.textContent = s.nombre;
      selO.appendChild(o);
      const d = o.cloneNode(true);
      selD.appendChild(d);
    });
    log('Sucursales cargadas ('+data.length+')');
  }catch(err){
    selO.innerHTML = '<option value="">Error cargando</option>';
    selD.innerHTML = '<option value="">Error cargando</option>';
    log('ERROR cargando sucursales: ' + err);
  }
}

document.getElementById('btnComparar').addEventListener('click', async ()=>{
  const origen = document.getElementById('sucursal_origen').value;
  const destino = document.getElementById('sucursal_destino').value;
  if(!origen || !destino){ alert('Selecciona ambas sucursales.'); return; }
  if(origen === destino){ alert('Origen y destino no pueden ser la misma sucursal.'); return; }

  log('Iniciando comparación entre ' + origen + ' → ' + destino);
  document.getElementById('lastCompare').textContent = 'Comparando...';
  try{
    const res = await fetch('api/comparar_inventarios.php', {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({ origen: origen, destino: destino })
    });
    const data = await res.json();
    if(data.error){ log('ERROR: '+data.error); alert(data.error); return; }

    const tbody = document.querySelector('#tablaDiferencias tbody');
    tbody.innerHTML = '';
    let diffCount = 0;
    let uniqueSKUs = 0;
    data.rows.forEach(r=>{
      uniqueSKUs++;
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${escapeHtml(r.sku)}</td>
                      <td>${escapeHtml(r.nombre)}</td>
                      <td>${r.stock_origen}</td>
                      <td>${r.stock_destino}</td>
                      <td>${r.diferencia}</td>`;
      tbody.appendChild(tr);
      if (r.diferencia !== 0) diffCount++;
    });

    document.getElementById('itemsShown').textContent = data.rows.length;
    document.getElementById('totalSKUs').textContent = data.rows.length;
    document.getElementById('totalDiff').textContent = diffCount;
    document.getElementById('lastCompare').textContent = new Date().toLocaleString();
    log('Comparación completada. Diferencias: ' + diffCount);
  }catch(err){
    log('ERROR comparando: '+err);
    alert('Error al comparar. Revisa consola.');
  }
});

// Sincronizar diferencias: envía rows con diferencia al servidor
document.getElementById('btnSincronizar').addEventListener('click', async ()=>{
  const origen = document.getElementById('sucursal_origen').value;
  const destino = document.getElementById('sucursal_destino').value;
  if(!origen || !destino){ alert('Selecciona ambas sucursales.'); return; }
  if(!confirm('¿Deseas sincronizar diferencias del origen al destino? Esto modificará stock en la sucursal destino.')) return;

  log('Iniciando sincronización desde ' + origen + ' → ' + destino);
  try{
    const res = await fetch('api/sincronizar_inventario.php', {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({ origen: origen, destino: destino })
    });
    const data = await res.json();
    if(data.error){ log('ERROR: '+data.error); alert(data.error); return; }
    log('Sincronización completada. Registros actualizados: ' + data.updated + ', insertados: ' + data.inserted);
    alert('Sincronización completada. Ver consola para detalles.');
  }catch(err){
    log('ERROR sincronizando: '+err);
    alert('Error al sincronizar. Revisa consola.');
  }
});

// Export CSV
document.getElementById('btnExportCSV').addEventListener('click', ()=>{
  const rows = Array.from(document.querySelectorAll('#tablaDiferencias tbody tr')).map(tr=>{
    return Array.from(tr.children).map(td=>td.textContent.replace(/\n/g,' ').trim());
  });
  if(rows.length === 0){ alert('No hay datos para exportar'); return; }
  let csv = 'SKU,Producto,Stock Origen,Stock Destino,Diferencia\n';
  rows.forEach(r=> csv += r.map(cell=>'"'+cell.replace(/"/g,'""')+'"').join(',') + '\n');
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = 'diferencias_sincronizacion.csv'; document.body.appendChild(a); a.click(); a.remove();
  URL.revokeObjectURL(url);
});

// Util
function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

// Cargar al inicio
cargarSucursales();
</script>

<script>
  // ===== Fondo de partículas =====
window.onload = function() {
  const canvas = document.getElementById("particles");
  const ctx = canvas.getContext("2d");
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
  let particles = [];

  class Particle {
    constructor() {
      this.x = Math.random() * canvas.width;
      this.y = Math.random() * canvas.height;
      this.radius = Math.random() * 2 + 1;
      this.speedX = (Math.random() - 0.5) * 1;
      this.speedY = (Math.random() - 0.5) * 1;
    }
    draw() {
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
      ctx.fillStyle = "rgba(0,242,254,0.7)";
      ctx.fill();
    }
    update() {
      this.x += this.speedX;
      this.y += this.speedY;
      if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
      if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
      this.draw();
    }
  }

  function initParticles() {
    particles = [];
    for (let i = 0; i < 120; i++) particles.push(new Particle());
  }

  function connectParticles() {
    for (let a = 0; a < particles.length; a++) {
      for (let b = a; b < particles.length; b++) {
        let dx = particles[a].x - particles[b].x;
        let dy = particles[a].y - particles[b].y;
        let dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 120) {
          ctx.beginPath();
          ctx.strokeStyle = `rgba(0,242,254,${1 - dist / 120})`;
          ctx.lineWidth = 1;
          ctx.moveTo(particles[a].x, particles[a].y);
          ctx.lineTo(particles[b].x, particles[b].y);
          ctx.stroke();
        }
      }
    }
  }

  function animateParticles() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles.forEach(p => p.update());
    connectParticles();
    requestAnimationFrame(animateParticles);
  }

  initParticles();
  animateParticles();

  window.addEventListener("resize", () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    initParticles();
  });
};
</script>

</body>
</html>
