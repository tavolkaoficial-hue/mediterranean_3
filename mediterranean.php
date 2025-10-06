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
      <a onclick="mostrarSeccion('usuario')">Perfil</a>
      <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    </div>
  </div>

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

      <div class="card" onclick="mostrarSeccion('usuarios')">
        <img src="images/usuariowood.png" alt="Usuarios" />
        <span>Usuarios</span>
      </div>

      <div class="card" onclick="mostrarSeccion('reportes')">
        <img src="images/reportewood.png" alt="Reportes" />
        <span>Reportes</span>
      </div>

      <div class="card" onclick="mostrarSeccion('stock')">
        <img src="images/stockwood.png" alt="Stock" />
        <span>Stock</span>
      </div>
    </div>
  </div>
</div>


  <!-- Sección usuarios -->
  <div id="usuarios" class="section">
    <h1>Gestión de Usuarios</h1>
    <div class="form-box">
      <input type="text" placeholder="Nombre de usuario" />
      <input type="email" placeholder="Correo electrónico" />
      <input type="password" placeholder="Contraseña" />
      <button onclick="alert('Ingresar')">Ingresar</button>
      <button onclick="alert('Usuario Creado')">Crear Usuario</button>
      <button class="back-btn" onclick="mostrarSeccion('inicio')">Volver al Inicio</button>
    </div>
  </div>

  <!-- Sección reportes -->
  <div id="reportes" class="section">
    <h1>Gestión de Reportes</h1>
    <div class="form-box">
      <input type="text" placeholder="Tipo de Reporte" />
      <input type="date" placeholder="Fecha de Reporte" />
      <input type="text" placeholder="Sucursal" />
      <button onclick="alert('Reporte Generado')">Generar Reporte</button>
      <button class="back-btn" onclick="mostrarSeccion('inicio')">Volver al Inicio</button>
    </div>
  </div>

  <!-- Sección stock -->
  <div id="stock" class="section">
    <h1>Gestión de Stock</h1>
    <div class="form-box">
      <input type="text" placeholder="Stock por Tienda" />
      <input type="text" placeholder="Stock General" />
      <input type="text" placeholder="Faltantes" />
      <button onclick="alert('Stock Actualizado')">Actualizar</button>
      <button class="back-btn" onclick="mostrarSeccion('inicio')">Volver al Inicio</button>
    </div>
  </div>

  <!-- Sección acerca de nosotros -->
  <div id="acerca" class="section">
    <h1>Acerca de Nosotros</h1>
    <div class="form-box">
      <p>Somos Mediterranean, una empresa que tiene el compromiso de ofrecer el mejor servicio de control de inventarios del mercado en Bogotá y Colombia.</p>
      <button class="back-btn" onclick="mostrarSeccion('inicio')">Volver al Inicio</button>
    </div>
  </div>

  <!-- Imagen flotante -->
  <img src="images/robot-removebg-preview.png" alt="Decoración" class="floating-image" />

  <!-- Marca de agua -->
  <img src="images/LogoMediterranean1992.png" alt="Marca de agua" class="watermark" />

  <!-- Script -->
  <script>
    function mostrarSeccion(id) {
      document.querySelectorAll(".section").forEach(sec => sec.classList.remove("active"));
      document.getElementById(id).classList.add("active");
    }
  </script>

</body>
</html>
