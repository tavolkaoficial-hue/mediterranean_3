<?php
/**
 * backup_center.php
 * Centro de Respaldo Mediterranean - Single file
 *
 * - Genera respaldo con mysqldump (descarga + guarda en /backups)
 * - Lista respaldos, descarga, elimina
 * - Permite restaurar subiendo un .sql
 * - Dashboard simple con Chart.js
 * - Selector de temas (Dorado, Aqua, 3D, Dashboard)
 *
 * Ajusta las rutas de mysqldump/mysql si es necesario.
 */

/* ===========================
   CONFIGURACIÓN - AJUSTA AQUÍ
   =========================== */
$DB_HOST = 'localhost';
$DB_NAME = 'mediterranean';
$DB_USER = 'PedroGuevara';
$DB_PASS = 'Peter1992';

// Rutas en XAMPP (ajusta si tu instalación usa rutas diferentes)
$mysqldump_path = 'C:/xampp/mysql/bin/mysqldump.exe';
$mysql_path = 'C:/xampp/mysql/bin/mysql.exe';

// Carpeta de respaldos (relativa al archivo)
$backup_dir = __DIR__ . '/backups';

// Max respaldos a conservar en auto-limpieza (0 = desactivar)
$max_backups_keep = 20;

/* ===========================
   FIN CONFIG
   =========================== */

// Crear carpeta si no existe
if (!is_dir($backup_dir)) {
    @mkdir($backup_dir, 0777, true);
}

// Manejar acciones: generar, eliminar, descargar, restaurar
$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generar respaldo
    if (isset($_POST['action']) && $_POST['action'] === 'generate') {
        $fecha = date('Y-m-d_H-i-s');
        $filename = "backup_{$DB_NAME}_{$fecha}.sql";
        $outpath = $backup_dir . '/' . $filename;

        // Construir comando seguro
        $cmd = '"' . $mysqldump_path . '" --user=' . escapeshellarg($DB_USER) .
               ' --password=' . escapeshellarg($DB_PASS) .
               ' --host=' . escapeshellarg($DB_HOST) .
               ' ' . escapeshellarg($DB_NAME) . ' > ' . escapeshellarg($outpath);

        exec($cmd . ' 2>&1', $output, $ret);
        if ($ret === 0 && file_exists($outpath)) {
            // Auto-limpieza
            $files = glob($backup_dir . '/*.sql');
            if ($max_backups_keep > 0 && count($files) > $max_backups_keep) {
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                for ($i = $max_backups_keep; $i < count($files); $i++) {
                    @unlink($files[$i]);
                }
            }

            // Forzar descarga (redireccionar con GET para descargar)
            header('Location: ?download=' . urlencode($filename) . '&msg=generated');
            exit;
        } else {
            $flash = ['type' => 'error', 'text' => 'Error generando respaldo. Revisa rutas y permisos. ' . implode("\n", $output)];
        }
    }

    // Restaurar respaldo (subida de archivo)
    if (isset($_POST['action']) && $_POST['action'] === 'restore' && isset($_FILES['sqlfile'])) {
        $f = $_FILES['sqlfile'];
        if ($f['error'] === UPLOAD_ERR_OK && strtolower(pathinfo($f['name'], PATHINFO_EXTENSION)) === 'sql') {
            $tmp = $f['tmp_name'];
            // Guardar copia por seguridad
            $fecha = date('Y-m-d_H-i-s');
            $saved = $backup_dir . '/restore_uploaded_' . $fecha . '.sql';
            if (!move_uploaded_file($tmp, $saved)) {
                $flash = ['type' => 'error', 'text' => 'No se pudo almacenar temporalmente el archivo subido.'];
            } else {
                // Ejecutar import con mysql CLI: mysql -u user -p pass db < file.sql
                $cmd = '"' . $mysql_path . '" --user=' . escapeshellarg($DB_USER) .
                       ' --password=' . escapeshellarg($DB_PASS) .
                       ' --host=' . escapeshellarg($DB_HOST) .
                       ' ' . escapeshellarg($DB_NAME) . ' < ' . escapeshellarg($saved);
                exec($cmd . ' 2>&1', $out, $ret);
                if ($ret === 0) {
                    $flash = ['type' => 'success', 'text' => 'Restauración completada correctamente.'];
                } else {
                    $flash = ['type' => 'error', 'text' => 'Error en la restauración: ' . implode("\n", $out)];
                }
            }
        } else {
            $flash = ['type' => 'error', 'text' => 'Archivo inválido. Sube un .sql válido.'];
        }
    }
}

// Manejo de eliminar vía GET (seguridad básica)
if (isset($_GET['delete'])) {
    $target = basename($_GET['delete']); // evita rutas relativas
    $path = $backup_dir . '/' . $target;
    if (file_exists($path)) {
        if (unlink($path)) {
            header('Location: ?msg=deleted');
            exit;
        } else {
            $flash = ['type' => 'error', 'text' => 'No se pudo eliminar el archivo. Revisa permisos.'];
        }
    } else {
        $flash = ['type' => 'error', 'text' => 'Archivo no encontrado.'];
    }
}

// Descargar archivo vía GET
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $path = $backup_dir . '/' . $file;
    if (file_exists($path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    } else {
        $flash = ['type' => 'error', 'text' => 'Archivo no encontrado para descargar.'];
    }
}

/* ===========================
   Datos para la interfaz
   =========================== */
$files = [];
foreach (glob($backup_dir . '/*.sql') as $f) {
    $files[] = [
        'name' => basename($f),
        'mtime' => date('Y-m-d H:i:s', filemtime($f)),
        'size' => round(filesize($f) / 1024, 2), // KB
        'path' => $f
    ];
}
usort($files, function($a, $b) {
    return strtotime($b['mtime']) - strtotime($a['mtime']);
});

// Stats para dashboard
$total_backups = count($files);
$total_size_kb = array_sum(array_map(function($x){ return $x['size']; }, $files));
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Mediterranean Backup Center</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

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
:root{
  --bg1: linear-gradient(120deg,#001428,#002b45);
  --glass: rgba(255,255,255,0.06);
  --accent: #00f2fe;
  --accent-2: #ffd166;
  --text: #eaf6ff;
}

/* Base */
*{box-sizing:border-box}
body{
  margin:0; font-family: 'Segoe UI', Tahoma, sans-serif;
  background: var(--bg1);
  color:var(--text);
  -webkit-font-smoothing:antialiased;
}
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

.main{ margin-left:280px; padding:36px; padding-top:26px;}
.header{ display:flex; gap:20px; align-items:center; justify-content:space-between;}
.title h2{ margin:0; font-size:28px; text-shadow:0 0 14px rgba(0,242,254,0.15);}
.subtitle{ color:rgba(255,255,255,0.75); margin-top:6px;}
.controls{ display:flex; gap:12px; align-items:center;}

.btn{
  background:var(--accent); color:#000; padding:10px 16px; border-radius:10px; font-weight:800; text-decoration:none;
  box-shadow:0 6px 20px rgba(0,242,254,0.12);
}
.btn.secondary{ background:transparent; color:var(--accent); border:2px solid rgba(255,255,255,0.04); padding:8px 12px; }

.grid{
  display:grid; grid-template-columns: 1.1fr 0.9fr; gap:20px; margin-top:25px;
}

/* cards */
.card{ background:var(--glass); border-radius:14px; padding:18px; box-shadow:0 8px 30px rgba(0,0,0,0.5);}
.card h3{ margin:0 0 8px 0; color:var(--accent);}

/* historia tabla */
.table{ width:100%; border-collapse:collapse; margin-top:12px;}
.table th, .table td{ text-align:left; padding:10px 8px; border-bottom:1px solid rgba(255,255,255,0.04); font-size:14px;}
.table th{ color:var(--accent); font-weight:700;}

/* responsive */
@media (max-width:900px){
  .grid{ grid-template-columns: 1fr; }
  .logo img{ width:130px;}
  .sidebar{ width:220px;}
  .main{ margin-left:240px;}
}

/* THEME SELECTOR UI */
.theme-picker{ display:flex; gap:8px; align-items:center;}
.theme-swatch{ width:34px;height:34px;border-radius:8px;border:2px solid rgba(255,255,255,0.06); cursor:pointer; box-shadow:0 6px 18px rgba(0,0,0,0.4);}
.theme-swatch[data-theme="aqua"]{ background: linear-gradient(180deg,#00f2fe,#0077b6);}
.theme-swatch[data-theme="dorado"]{ background: linear-gradient(180deg,#ffd166,#f9a826);}
.theme-swatch[data-theme="3d"]{ background: linear-gradient(180deg,#7b61ff,#00ffe0);}
.theme-swatch[data-theme="dashboard"]{ background: linear-gradient(180deg,#00b4d8,#0077b6);}

/* small helpers */
.right { text-align:right;}
.small { font-size:13px; color:rgba(255,255,255,0.7); }
.kpi { display:flex; gap:10px; align-items:center; margin-top:8px;}
.kpi .v { font-weight:800; font-size:20px; color:var(--accent);}
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

<!-- MAIN -->
<div class="main">

  <div class="header">
    <div class="title">
      <h2><i class="fas fa-database"></i> Centro de Respaldo Mediterranean</h2>
      <div class="subtitle">Gestiona respaldos, restauraciones y programación — seguro y sencillo.</div>
    </div>

    <div class="controls">
      <div class="theme-picker small">
        <div class="small" style="margin-right:8px;">Tema</div>
        <div class="theme-swatch" data-theme="aqua" title="Aqua"></div>
        <div class="theme-swatch" data-theme="dorado" title="Dorado"></div>
        <div class="theme-swatch" data-theme="3d" title="3D"></div>
        <div class="theme-swatch" data-theme="dashboard" title="Dashboard"></div>
      </div>

      <a class="btn" id="btn-generate" href="?action=generate_post" style="margin-left:12px;" onclick="event.preventDefault(); doGenerate();">
        <i class="fas fa-download"></i> Generar Respaldo
      </a>
    </div>
  </div>

  <!-- GRID -->
  <div class="grid">

    <!-- LEFT: acciones y tabla -->
    <div>
      <!-- Generar -->
      <div id="generate" class="card">
        <h3><i class="fas fa-play"></i> Generar Respaldo Ahora</h3>
        <p class="small">Al generar se creará un archivo .sql en la carpeta <code>/backups/</code> y se ofrecerá su descarga.</p>
        <div style="margin-top:12px;">
          <a class="btn" onclick="doGenerate();"><i class="fas fa-magnet"></i> Ejecutar Respaldo</a>
          <a class="btn secondary" href="?action=list" style="margin-left:8px;"><i class="fas fa-folder-open"></i> Ver carpeta</a>
        </div>
      </div>

      <!-- Historial -->
      <div id="history" class="card" style="margin-top:18px;">
        <h3><i class="fas fa-history"></i> Historial de Respaldos</h3>
        <p class="small">Descarga o elimina respaldos guardados en el servidor.</p>

        <table class="table" aria-live="polite">
          <thead>
            <tr><th>Archivo</th><th>Fecha</th><th>Tamaño (KB)</th><th class="right">Acciones</th></tr>
          </thead>
          <tbody>
            <?php if (empty($files)): ?>
              <tr><td colspan="4" class="small">No hay respaldos disponibles.</td></tr>
            <?php else: ?>
              <?php foreach ($files as $f): ?>
                <tr>
                  <td><?php echo htmlspecialchars($f['name']); ?></td>
                  <td><?php echo $f['mtime']; ?></td>
                  <td><?php echo $f['size']; ?></td>
                  <td class="right">
                    <a class="btn secondary" href="?download=<?php echo urlencode($f['name']); ?>"><i class="fas fa-cloud-download-alt"></i> Descargar</a>
                    <a class="btn secondary" href="?delete=<?php echo urlencode($f['name']); ?>" onclick="return confirm('¿Eliminar <?php echo addslashes($f['name']); ?> ?')"><i class="fas fa-trash"></i> Eliminar</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

      </div>

      <!-- Restore -->
      <div id="restore" class="card" style="margin-top:18px;">
        <h3><i class="fas fa-upload"></i> Restaurar desde .sql</h3>
        <p class="small">Sube un archivo .sql para restaurar la base de datos <strong><?php echo htmlspecialchars($DB_NAME); ?></strong>. La operación sobrescribirá datos en la base según el contenido del .sql.</p>

        <form method="post" enctype="multipart/form-data" onsubmit="return confirm('¿Estás seguro de que deseas restaurar la base de datos con el archivo seleccionado? Esta acción puede sobrescribir datos.');">
          <input type="hidden" name="action" value="restore">
          <input type="file" name="sqlfile" accept=".sql" required>
          <div style="margin-top:12px;">
            <button class="btn" type="submit"><i class="fas fa-upload"></i> Subir y Restaurar</button>
            <a class="btn secondary" href="#history" style="margin-left:8px;"><i class="fas fa-eye"></i> Ver Respaldos</a>
          </div>
        </form>
      </div>

    </div>

    <!-- RIGHT: dashboard -->
    <div>
      <div id="dashboard" class="card">
        <h3><i class="fas fa-chart-pie"></i> Dashboard</h3>
        <div class="kpi">
          <div>
            <div class="small">Respaldos totales</div>
            <div class="v"><?php echo $total_backups; ?></div>
          </div>
          <div>
            <div class="small">Tamaño total (KB)</div>
            <div class="v"><?php echo round($total_size_kb,2); ?></div>
          </div>
        </div>

        <canvas id="backupsChart" style="margin-top:16px; max-height:260px;"></canvas>

        <div style="margin-top:12px;" class="small">Información de servidor: <strong><?php echo htmlspecialchars(PHP_OS); ?></strong></div>
      </div>

      <div id="settings" class="card" style="margin-top:18px;">
        <h3><i class="fas fa-cog"></i> Ajustes</h3>
        <p class="small">Rutas y usuario mostrados son los configurados en el archivo PHP. Para cambiar credenciales o rutas edita el archivo <code>backup_center.php</code>.</p>
        <table style="width:100%; margin-top:8px;">
          <tr><td class="small">Host</td><td class="small"><?php echo htmlspecialchars($DB_HOST); ?></td></tr>
          <tr><td class="small">Base de datos</td><td class="small"><?php echo htmlspecialchars($DB_NAME); ?></td></tr>
          <tr><td class="small">Usuario</td><td class="small"><?php echo htmlspecialchars($DB_USER); ?></td></tr>
          <tr><td class="small">mysqldump</td><td class="small"><?php echo htmlspecialchars($mysqldump_path); ?></td></tr>
        </table>
      </div>
    </div>

  </div>

</div> <!-- /main -->



<script>
// Mostrar mensajes flash (provenientes del servidor)
<?php if ($flash): ?>
  Swal.fire({
    icon: '<?php echo $flash['type'] === "success" ? "success" : "error"; ?>',
    title: '<?php echo $flash['type'] === "success" ? "Éxito" : "Error"; ?>',
    html: '<?php echo addslashes(nl2br($flash['text'])); ?>'
  });
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'generated'): ?>
  Swal.fire({ icon: 'success', title: 'Respaldo creado', text: 'El respaldo se creó correctamente y comienza la descarga.' });
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
  Swal.fire({ icon: 'success', title: 'Eliminado', text: 'El respaldo fue eliminado.' });
<?php endif; ?>

// Función para generar respaldo mediante POST (evita problemas con GET)
function doGenerate(){
  Swal.fire({
    title: 'Generar respaldo',
    text: 'Se creará un respaldo de la base de datos y se descargará automáticamente. ¿Continuar?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, generar',
    cancelButtonText: 'Cancelar'
  }).then((res)=>{
    if(res.isConfirmed){
      // Creamos un form dinámico POST
      let f = document.createElement('form');
      f.method = 'POST';
      f.style.display='none';
      let a = document.createElement('input');
      a.name='action'; a.value='generate';
      f.appendChild(a);
      document.body.appendChild(f);
      f.submit();
    }
  });
}

// THEME SWITCHER (cambia variables CSS y añade efectos)
document.querySelectorAll('.theme-swatch').forEach(function(sw){
  sw.addEventListener('click', function(){
    let t = sw.getAttribute('data-theme');
    applyTheme(t);
  });
});

function applyTheme(name){
  const root = document.documentElement;
  if(name === 'aqua'){
    root.style.setProperty('--bg1', 'linear-gradient(120deg,#001428,#004e92)');
    root.style.setProperty('--accent', '#00f2fe');
    root.style.setProperty('--accent-2', '#90e0ef');
  } else if (name === 'dorado'){
    root.style.setProperty('--bg1', 'linear-gradient(120deg,#0b1020,#3b2f2f)');
    root.style.setProperty('--accent', '#ffd166');
    root.style.setProperty('--accent-2', '#f9a826');
  } else if (name === '3d'){
    root.style.setProperty('--bg1', 'linear-gradient(120deg,#0b1020,#06122b)');
    root.style.setProperty('--accent', '#7b61ff');
    root.style.setProperty('--accent-2', '#00ffe0');
    // Efecto 3D suave: añadir transform en cartas
    document.querySelectorAll('.card').forEach(c=> c.style.transition='transform 0.2s, box-shadow 0.2s');
    document.querySelectorAll('.card').forEach(c=> {
      c.addEventListener('mousemove', card3DEffect);
      c.addEventListener('mouseleave', ()=>{ c.style.transform=''; c.style.boxShadow=''; });
    });
  } else { // dashboard default
    root.style.setProperty('--bg1', 'linear-gradient(120deg,#001428,#002b45)');
    root.style.setProperty('--accent', '#00b4d8');
    root.style.setProperty('--accent-2', '#00f2fe');
    document.querySelectorAll('.card').forEach(c=> {
      c.removeEventListener('mousemove', card3DEffect);
      c.style.transform='';
    });
  }
}

function card3DEffect(e){
  const rect = this.getBoundingClientRect();
  const x = e.clientX - rect.left; // x position within the element.
  const y = e.clientY - rect.top;
  const cx = rect.width/2;
  const cy = rect.height/2;
  const dx = (x - cx) / cx;
  const dy = (y - cy) / cy;
  const rx = (-dy * 6).toFixed(2);
  const ry = (dx * 6).toFixed(2);
  this.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg) translateZ(3px)`;
  this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.6)';
}

// Inicializar chart con datos del servidor (respaldos por fecha)
const backups = <?php
  // agrupar por fecha (día)
  $group = [];
  foreach ($files as $f) {
    $d = substr($f['mtime'],0,10);
    if (!isset($group[$d])) $group[$d] = 0;
    $group[$d]++;
  }
  // ordenar por fecha asc para la grafica
  ksort($group);
  echo json_encode(array_values($group));
?>;
const labels = <?php echo json_encode(array_keys($group)); ?>;

const ctx = document.getElementById('backupsChart').getContext('2d');
const backupsChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [{
      label: 'Respaldos por día',
      data: backups,
      borderRadius: 6,
      barThickness: 22,
      backgroundColor: function(context){
        return context.chart.options.backgroundColor || 'rgba(0,242,254,0.9)';
      }
    }]
  },
  options: {
    responsive:true,
    plugins:{
      legend:{ display:false },
      tooltip:{ mode:'index' }
    },
    scales:{
      x:{ grid:{ display:false }, ticks:{ color: 'rgba(255,255,255,0.8)' } },
      y:{ beginAtZero:true, ticks:{ color: 'rgba(255,255,255,0.8)' } }
    },
    backgroundColor: 'rgba(0,242,254,0.9)'
  }
});
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
