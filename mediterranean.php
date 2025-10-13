<?php
session_start();
if (!isset($_SESSION["usuarios"])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mediterranean</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <!-- Olas -->
  <div class="olas">
    <div class="ola ola1"></div>
    <div class="ola ola2"></div>
    <div class="ola ola3"></div>
  </div>

  <!-- Burbujas -->
  <div class="burbujas">
    <div class="burbuja"></div>
    <div class="burbuja"></div>
    <div class="burbuja"></div>
    <div class="burbuja"></div>
    <div class="burbuja"></div>
    <div class="burbuja"></div>
  </div>

  <!-- Barra superior -->
  <div class="top-bar">
    <div class="logo-area">
      <a onclick="mostrarSeccion('inicio')">MEDITERRANEAN</a>
    </div>
    <div class="menu">
      <a onclick="mostrarSeccion('acerca')">Acerca De Nosotros</a>
      <a onclick="mostrarSeccion('usuarios')">Perfil</a>
      <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    </div>
  </div>

  <!-- Sección INICIO -->
  <div id="inicio" class="section active">
    <div class="content">
      <div class="bienvenida">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION["usuarios"]); ?> 👋</h2>
        <p>Selecciona una opción para continuar</p>
      </div>

      <!-- Contenedor de tarjetas -->
      <div class="tarjetas">
        <div class="card" onclick="window.location.href='productos.html'">
          <img src="images/productoswood.png" alt="Productos" />
          <span>Productos</span>
        </div>

        <div class="card" onclick="window.location.href='sucursales.html'">
          <img src="images/sucursaleswood.png" alt="Sucursales" />
          <span>Sucursales</span>
        </div>

        <div class="card" onclick="window.location.href='usuarios.html'">
          <img src="images/usuariowood.png" alt="Usuarios" />
          <span>Usuarios</span>
        </div>

        <div class="card" onclick="window.location.href='reportes.html'">
          <img src="images/reportewood.png" alt="Reportes" />
          <span>Reportes</span>
        </div>

        <div class="card" onclick="window.location.href='utilidades.html'">
          <img src="images/stockwood.png" alt="utilidades" />
          <span>Utilidades</span>
        </div>
      </div>
    </div>
  </div>

 <!-- SECCIÓN USUARIOS -->
<div id="usuarios" class="section" style="display:none;">
  <h1>Mi Perfil Profesional</h1>

  <div class="form-box" style="max-width:800px; margin:auto; background:rgba(255,255,255,0.05); backdrop-filter:blur(10px); border-radius:20px; padding:30px; color:white;">

    <!-- Vista de perfil -->
    <div id="perfilView">
      <div style="display:flex; align-items:center; gap:30px; flex-wrap:wrap; justify-content:center;">
        <img id="fotoPerfil" src="images/default-avatar.png" alt="Foto de perfil" style="width:150px; height:150px; border-radius:50%; object-fit:cover; border:3px solid #00f2fe; box-shadow:0 0 15px rgba(0,242,254,0.5);">
        <div>
          <h2 id="nombrePerfil">Cargando...</h2>
          <p id="correoPerfil">...</p>
          <p id="rolPerfil"><b>Rol:</b> ...</p>
          <p id="estadoPerfil" class="user-status activo">Activo</p>
          <a id="cvLink" href="#" target="_blank" class="cv-link" style="color:#00f2fe;">📄 Ver Hoja de Vida</a>
        </div>
      </div>

      <div style="margin-top:30px;">
        <h3>Descripción Profesional</h3>
        <p id="descripcionPerfil" style="line-height:1.6;">...</p>
      </div>

      <div style="margin-top:20px; text-align:center;">
        <button onclick="editarPerfil()" style="padding:10px 20px; border:none; border-radius:10px; background:linear-gradient(90deg,#00f2fe,#4facfe); color:white; font-weight:bold; cursor:pointer;">✏️ Editar Perfil</button>
        <button class="back-btn" onclick="mostrarSeccion('inicio')" style="margin-left:10px;">Volver al Inicio</button>
      </div>
    </div>

    <!-- Formulario de edición -->
    <div id="perfilEdit" style="display:none;">
      <form id="formPerfil" enctype="multipart/form-data">
        <input type="text" id="nombreEdit" name="nombre" placeholder="Nombre completo" required>
        <input type="email" id="correoEdit" name="correo" placeholder="Correo electrónico" required>
        <input type="text" id="rolEdit" name="rol" placeholder="Rol profesional" required>
        <textarea id="descripcionEdit" name="descripcion" placeholder="Descripción profesional" style="width:100%; height:100px; border-radius:10px; padding:10px; border:none; margin-bottom:10px;"></textarea>
        <label>Foto de perfil:</label>
        <input type="file" id="fotoEdit" name="foto" accept="image/*">
        <label>Actualizar Hoja de Vida (PDF):</label>
        <input type="file" id="cvEdit" name="cv" accept="application/pdf">
        <select id="estadoEdit" name="estado" required>
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>

        <div style="margin-top:20px; text-align:center;">
          <button type="submit" style="padding:10px 20px; border:none; border-radius:10px; background:linear-gradient(90deg,#00f2fe,#4facfe); color:white; font-weight:bold; cursor:pointer;">💾 Guardar Cambios</button>
          <button type="button" onclick="cancelarEdicion()" style="padding:10px 20px; border:none; border-radius:10px; background:rgba(255,255,255,0.2); color:white; margin-left:10px; cursor:pointer;">❌ Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Cargar perfil al abrir sección
  async function cargarPerfil() {
    const res = await fetch('perfil.php');
    const data = await res.json();

    if (data.success) {
      document.getElementById("nombrePerfil").textContent = data.nombre;
      document.getElementById("correoPerfil").textContent = data.correo;
      document.getElementById("rolPerfil").innerHTML = "<b>Rol:</b> " + data.rol;
      document.getElementById("descripcionPerfil").textContent = data.descripcion;
      document.getElementById("estadoPerfil").textContent = data.estado;
      document.getElementById("estadoPerfil").className = "user-status " + data.estado.toLowerCase();
      document.getElementById("fotoPerfil").src = data.foto || "images/default-avatar.png";
      if (data.cv) document.getElementById("cvLink").href = data.cv;
    }
  }

  function editarPerfil() {
    document.getElementById("perfilView").style.display = "none";
    document.getElementById("perfilEdit").style.display = "block";

    // Rellenar campos con datos actuales
    document.getElementById("nombreEdit").value = document.getElementById("nombrePerfil").textContent;
    document.getElementById("correoEdit").value = document.getElementById("correoPerfil").textContent;
    document.getElementById("rolEdit").value = document.getElementById("rolPerfil").textContent.replace("Rol: ", "");
    document.getElementById("descripcionEdit").value = document.getElementById("descripcionPerfil").textContent;
    document.getElementById("estadoEdit").value = document.getElementById("estadoPerfil").textContent.toLowerCase();
  }

  function cancelarEdicion() {
    document.getElementById("perfilEdit").style.display = "none";
    document.getElementById("perfilView").style.display = "block";
  }

  document.getElementById("formPerfil").addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(document.getElementById("formPerfil"));
    const res = await fetch("actualizar_perfil.php", { method: "POST", body: formData });
    const data = await res.json();

    if (data.success) {
      alert("Perfil actualizado correctamente ✅");
      cancelarEdicion();
      cargarPerfil();
    } else {
      alert("Error al actualizar el perfil ❌");
    }
  });

  document.addEventListener("DOMContentLoaded", cargarPerfil);
</script>


  <!-- Sección REPORTES -->
  <div id="reportes" class="section" style="display:none;">
    <h1>Gestión de Reportes</h1>
    <div class="form-box">
      <input type="text" placeholder="Tipo de Reporte" />
      <input type="date" placeholder="Fecha de Reporte" />
      <input type="text" placeholder="Sucursal" />
      <button onclick="alert('Reporte Generado')">Generar Reporte</button>
      <button class="back-btn" onclick="mostrarSeccion('inicio')">Volver al Inicio</button>
    </div>
  </div>

  <!-- Sección STOCK -->
  <div id="stock" class="section" style="display:none;">
    <h1>Gestión de Stock</h1>
    <div class="form-box">
      <input type="text" placeholder="Stock por Tienda" />
      <input type="text" placeholder="Stock General" />
      <input type="text" placeholder="Faltantes" />
      <button onclick="alert('Stock Actualizado')">Actualizar</button>
      <button class="back-btn" onclick="mostrarSeccion('inicio')">Volver al Inicio</button>
    </div>
  </div>

  <!-- Sección ACERCA DE NOSOTROS -->
  <div id="acerca" class="section" style="display:none;">
    <h1 style="text-align:center; margin-bottom:20px; color:#00f2fe;">Acerca de Nosotros</h1>

    <div class="form-box" style="max-width:900px; margin:auto; background:rgba(255,255,255,0.05); backdrop-filter:blur(12px); padding:30px; border-radius:20px; box-shadow:0 8px 25px rgba(0,0,0,0.4);">
      
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:30px;">
        <!-- Imagen izquierda -->
        <img src="images/empresa1.jpg" alt="Nuestra empresa" style="width:300px; border-radius:15px; box-shadow:0 0 15px rgba(0,242,254,0.5);">

        <!-- Texto -->
        <div style="flex:1; min-width:280px; color:white;">
          <h2 style="color:#4facfe;">Nuestra Historia</h2>
          <p style="line-height:1.6; font-size:16px;">
            Fundada en <b>1992</b>, <b>Comercializadora Mediterranean</b> nació con la visión de ofrecer productos de alta calidad que reflejan el auténtico sabor del Mediterráneo.
            <br><br>
            Lo que comenzó como un pequeño negocio familiar se ha transformado en una empresa sólida, reconocida por su compromiso con la excelencia, la innovación y la satisfacción de nuestros clientes. 
            <br><br>
            Durante más de tres décadas, hemos construido relaciones duraderas con nuestros proveedores y aliados estratégicos, expandiendo nuestra presencia a lo largo del país, manteniendo siempre nuestros valores de <b>honestidad, calidad y tradición</b>.
          </p>
        </div>
      </div>

      <div style="margin-top:40px; text-align:center;">
        <img src="images/equipo.jpg" alt="Nuestro equipo" style="width:80%; border-radius:15px; box-shadow:0 0 20px rgba(79,172,254,0.4);">
        <p style="margin-top:20px; font-size:16px; color:#e0e0e0;">
          Nuestro equipo está conformado por profesionales apasionados que trabajan día a día para llevar lo mejor de nuestra cultura y sabor a cada cliente.
        </p>
      </div>

      <div style="text-align:center; margin-top:30px;">
        <button class="back-btn" onclick="mostrarSeccion('inicio')" 
          style="padding:12px 25px; border:none; border-radius:10px; background:linear-gradient(90deg,#00f2fe,#4facfe); color:white; font-weight:bold; cursor:pointer;">
          <i class="fas fa-arrow-left"></i> Volver al Inicio
        </button>
      </div>
    </div>
  </div>

  <!-- Imagen flotante -->
  <img src="images/robot-removebg-preview.png" alt="Decoración" class="floating-image" />

  <!-- Marca de agua -->
  <img src="images/LogoMediterranean1992.png" alt="Marca de agua" class="watermark" />

  <!-- Script de secciones -->
  <script>
  function mostrarSeccion(id) {
    document.querySelectorAll(".section").forEach(sec => sec.style.display = "none");
    document.getElementById(id).style.display = "block";
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
  </script>

  <canvas id="particles"></canvas>
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
        this.speedX = (Math.random() - 0.5);
        this.speedY = (Math.random() - 0.5);
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
