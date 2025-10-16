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

<style>
   .floating-image {
    position: fixed; bottom: 20px; right: 20px; width: 200px; height: 120px; cursor: pointer; z-index: 1000;
  }
/* 🌊 Ventana principal */
.chat-window {
  position: fixed;
  bottom: 90px;
  right: 120px;
  width: 380px;
  max-height: 520px;
  background: linear-gradient(180deg, #ffffff, #e0f4f5);
  border-radius: 18px;
  display: none;
  flex-direction: column;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
  overflow: hidden;
  z-index: 1001;
  font-family: "Poppins", Arial, sans-serif;
}

/* 🟦 Encabezado */
.chat-header {
  background: linear-gradient(90deg, #0077b6, #00b4d8);
  color: #fff;
  padding: 14px 18px;
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top-left-radius: 18px;
  border-top-right-radius: 18px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.close-btn {
  cursor: pointer;
  font-size: 22px;
  transition: transform 0.2s;
}
.close-btn:hover {
  transform: scale(1.2);
}

/* 💬 Zona de mensajes */
.chat-messages {
  padding: 12px;
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
  background: #caf0f8;
}

/* ✨ Mensajes */
.message {
  max-width: 80%;
  padding: 10px 15px;
  border-radius: 18px;
  word-wrap: break-word;
  font-size: 14px;
  line-height: 1.4;
  animation: fadeIn 0.3s ease-in;
}

/* 👤 Usuario */
.message.user {
  align-self: flex-end;
  background: linear-gradient(135deg, #03045e, #0077b6);
  color: #ffffff;
  box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}

/* 🤖 Bot */
.message.bot {
  align-self: flex-start;
  background: #ffffff;
  color: #002b5c;
  border: 1px solid #d9ecf2;
  box-shadow: 0 3px 6px rgba(0,0,0,0.05);
}

/* 🖊️ Indicador "escribiendo..." */
.typing {
  font-style: italic;
  font-size: 12px;
  color: #005f73;
  margin-left: 10px;
}

/* 🧭 Contenedor del input + botón */
.chat-input {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  background: #f1faff;
  border-top: 1px solid #ccc;
  gap: 8px;
}

/* ✏️ Caja de texto amplia */
.chat-input input {
  flex: 1;
  padding: 10px 16px;
  border-radius: 25px;
  border: 1px solid #0077b6;
  outline: none;
  font-size: 15px;
  color: #000;
  background-color: #fff;
  caret-color: #0077b6;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
  transition: border 0.2s, box-shadow 0.2s;
}

.chat-input input:focus {
  border-color: #00b4d8;
  box-shadow: 0 0 6px rgba(0,180,216,0.4);
}

.chat-input input::placeholder {
  color: #7a7a7a;
}

/* ✈️ Botón con ícono de envío */
.chat-input button {
  background: linear-gradient(135deg, #00b4d8, #0077b6);
  border: none;
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
  box-shadow: 0 2px 6px rgba(0,0,0,0.25);
  flex-shrink: 0;
}

.chat-input button:hover {
  background: linear-gradient(135deg, #0096c7, #023e8a);
  transform: scale(1.1);
}


/* ✅ Caja de texto funcional y visible */
.chat-input input {
  flex: 1;
  padding: 10px 14px;
  border-radius: 25px;
  border: 1px solid #0077b6;
  outline: none;
  font-size: 14px;
  color: #000000;
  background-color: #ffffff;
  caret-color: #0077b6;
  box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
  transition: border 0.2s, box-shadow 0.2s;
}
.chat-input input:focus {
  border-color: #00b4d8;
  box-shadow: 0 0 6px rgba(0,180,216,0.5);
}
.chat-input input::placeholder {
  color: #7a7a7a;
}

/* 🚀 Botón Enviar — compacto y moderno */
.chat-input button {
  background: linear-gradient(135deg, #00b4d8, #0077b6);
  border: none;
  color: white;
  padding: 8px 16px;
  margin-left: 8px;
  border-radius: 25px;
  cursor: pointer;
  font-weight: 600;
  font-size: 13px;
  height: 38px;
  transition: all 0.25s ease;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.chat-input button:hover {
  background: linear-gradient(135deg, #0096c7, #023e8a);
  transform: scale(1.05);
}

/* ✨ Animación */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
.copyright-fixed {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  text-align: center;
  background: #508dddff;
  color: #ffffff;
  font-size: 13px;
  padding: 10px 0;
  font-family: "Poppins", Arial, sans-serif;
  box-shadow: 0 -2px 6px rgba(0,0,0,0.2);
  z-index: 1000;
}

.copyright-fixed a {
  color: #caf0f8;
  text-decoration: none;
  font-weight: 500;
  margin-left: 5px;
  transition: color 0.3s, text-decoration 0.3s;
}

.copyright-fixed a:hover {
  color: #90e0ef;
  text-decoration: underline;
}  

.policy-modal {
  display: none;
  position: fixed;
  z-index: 99999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  backdrop-filter: blur(4px);
  justify-content: center;
  align-items: center;
}
.policy-content {
  background: #ffffff;
  color: #000;
  padding: 25px;
  border-radius: 15px;
  width: 80%;
  max-width: 600px;
  box-shadow: 0 0 20px rgba(0,0,0,0.3);
  animation: fadeIn 0.3s ease;
}
.close-policy {
  float: right;
  font-size: 24px;
  cursor: pointer;
  color: #004e92;
  font-weight: bold;
}
.close-policy:hover { color: #00c6ff; }

  </style>
</style>
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

 <footer class="copyright-fixed">
  © 2025 Mediterranean Technologies  
  | <a href="#" id="openPolicy">Política de Privacidad y Seguridad</a>
</footer>

<!-- Modal -->
<div id="policyModal" class="policy-modal">
  <div class="policy-content">
    <span class="close-policy">&times;</span>
    <h2>Política de Privacidad y Seguridad</h2>
    <p>En Mediterranean Technologies, valoramos tu privacidad. Todos los datos son tratados conforme al RGPD y nuestras medidas de seguridad garantizan la integridad de la información...</p>
    <p><a href="politica-completa.html">Leer política completa</a></p>
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
          <h2 id="nombrePerfil"></h2>
          <p id="correoPerfil"></p>
          <p id="rolPerfil"><b></b></p>
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
        <input type="text" id="telefonoEdit" name="telefono" placeholder="Teléfono">
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
  // ============================
  // 📦 Cargar perfil del usuario
  // ============================
  async function cargarPerfil() {
    console.log("✅ Ejecutando cargarPerfil()...");

    try {
      const res = await fetch("obtener_usuarios.php");
      const data = await res.json();

      console.log("📦 Datos recibidos:", data);

      if (!data || data.error) {
        console.error(data.error || "Error al obtener datos del usuario");
        return;
      }

      document.getElementById("nombrePerfil").textContent = data.nombre;
      document.getElementById("correoPerfil").textContent = data.correo;
      document.getElementById("rolPerfil").innerHTML = "<b>Rol:</b> " + data.rol;
      document.getElementById("descripcionPerfil").textContent = data.descripcion;
      document.getElementById("estadoPerfil").textContent = data.estado;
      document.getElementById("estadoPerfil").className = "user-status " + data.estado.toLowerCase();
      document.getElementById("fotoPerfil").src = data.foto || "images/default-avatar.png";
      if (data.cv) document.getElementById("cvLink").href = data.cv;
    } catch (error) {
      console.error("❌ Error al cargar perfil:", error);
    }
  }

  // ============================
  // ✏️ Edición de perfil
  // ============================
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

  // ============================
  // 💾 Guardar cambios del perfil
  // ============================
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
      alert("❌ Error al actualizar el perfil");
    }
  });

  // ========================================================
  // 🌊 Ejecutar al cargar la página (Perfil + Fondo partículas)
  // ========================================================
  window.addEventListener("load", () => {
    // Cargar perfil del usuario
    cargarPerfil();

    // ===== Fondo de partículas =====
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
  });
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
        <img src="images/logoMediterranean1992.png" alt="Nuestra empresa" style="width:200px; border-radius:15px; box-shadow:0 0 15px rgba(0,242,254,0.5);">

        <!-- Texto -->
        <div style="flex:1; min-width:280px; color:white;">
          <h2 style="color:#4facfe;">Nuestra Historia</h2>
          <p style="line-height:1.6; font-size:16px;">
            Fundada en <b>2024</b>, <b>Mediterranean</b> nació con la visión de ofrecer la mas  alta calidad en control de <b>Inventarios</b> y reflejan el compromiso con nuestra Compañía "Mediterranean".
            <br><br>
            Lo que comenzó como un pequeño proyecto de la carrera de su creador <b>"Pedro Anotnio Guevara Rojas"</b>, se ha transformado en una empresa sólida, reconocida por su compromiso con la excelencia, la innovación y la satisfacción de nuestros clientes. 
            <br><br>
            Durante esta corta trayectoria, hemos construido relaciones duraderas con nuestros proveedores y aliados estratégicos, expandiendo nuestra presencia a clientes del país, manteniendo siempre nuestros valores de <b>Honestidad, Calidad y Conocimiento</b>.
          </p>
        </div>
      </div>

      <div style="margin-top:40px; text-align:center;">
        <img src="images/peter123.png" alt="Nuestro equipo" style="width:15%; border-radius:15px; box-shadow:0 0 20px rgba(79,172,254,0.4);">
        <p style="margin-top:20px; font-size:16px; color:#e0e0e0;">
          Nuestro equipo está conformado por profesionales apasionados que trabajan día a día para desarrollar lo mejor de nuestra Compañía.
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
  <body>

  <!-- Icono de chat flotante -->
  <img src="images/robot-removebg-preview.png" alt="Chat Bot" class="floating-image" id="chatBotIcon" />

<div id="chatWindow" class="chat-window">
  <div class="chat-header">
    Luxor-Chat IA
    <span id="closeChat" class="close-btn">&times;</span>
  </div>
  <div class="chat-messages" id="chatMessages"></div>
  <div class="chat-input">
  <input type="text" id="userInput" placeholder="Preguntale a Luxor-Chat IA" />
  <button id="sendBtn" title="Enviar mensaje">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="18px" height="18px">
      <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
    </svg>
  </button>
</div>

</div>

<script>
const chatIcon = document.getElementById('chatBotIcon');
const chatWindow = document.getElementById('chatWindow');
const closeBtn = document.getElementById('closeChat');
const sendBtn = document.getElementById('sendBtn');
const userInput = document.getElementById('userInput');
const chatMessages = document.getElementById('chatMessages');

// Abrir chat
chatIcon.addEventListener('click', () => { chatWindow.style.display = 'flex'; userInput.focus(); });
closeBtn.addEventListener('click', () => { chatWindow.style.display = 'none'; });

// Enviar mensaje
sendBtn.addEventListener('click', sendMessage);
userInput.addEventListener('keypress', e => { if(e.key==='Enter') sendMessage(); });

// Cargar historial
const history = JSON.parse(localStorage.getItem('chatHistory') || "[]");
history.forEach(msg => addMessage(msg.message, msg.type, false));

function sendMessage() {
  const message = userInput.value.trim();
  if(!message) return;
  addMessage(message, 'user');
  userInput.value = '';

  // Guardar historial
  history.push({message, type:'user'});
  localStorage.setItem('chatHistory', JSON.stringify(history));

  // Indicador "bot escribiendo..."
  const typing = document.createElement('div'); typing.classList.add('typing'); typing.textContent = 'Bot IA está escribiendo...';
  chatMessages.appendChild(typing); chatMessages.scrollTop = chatMessages.scrollHeight;

  setTimeout(() => {
    chatMessages.removeChild(typing);
    const botMsg = "Hola, procesé tu mensaje: " + message;
    addMessage(botMsg, 'bot');
    history.push({message: botMsg, type:'bot'});
    localStorage.setItem('chatHistory', JSON.stringify(history));
  }, 1000);
}

function addMessage(message, type, save=true) {
  const msgDiv = document.createElement('div');
  msgDiv.classList.add('message', type);
  msgDiv.textContent = message;
  chatMessages.appendChild(msgDiv);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}
  </script>
</body>

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
  <script>
document.getElementById("openPolicy").onclick = () => {
  document.getElementById("policyModal").style.display = "flex";
};
document.querySelector(".close-policy").onclick = () => {
  document.getElementById("policyModal").style.display = "none";
};
window.onclick = (e) => {
  if (e.target == document.getElementById("policyModal"))
    document.getElementById("policyModal").style.display = "none";
};
</script>

</body>
</html>
