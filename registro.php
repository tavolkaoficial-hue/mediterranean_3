<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "mediterranean";

$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recoger datos del formulario
    $usuario = trim($_POST["usuarios"]);
    $correo = trim($_POST["correo"]);
    $telefono = trim($_POST["telefono"]);
    $password = trim($_POST["password"]);

    // Validar campos vacíos
    if (empty($usuario) || empty($correo) || empty($telefono) || empty($password))  {
        echo "<script>alert('⚠ Por favor completa todos los campos'); window.history.back();</script>";
        exit();
    }

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuarios = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('⚠ El usuario ya existe'); window.history.back();</script>";
        $stmt->close();
        $conn->close();
        exit();
    }

    // Verificar si el correo ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('⚠ El correo ya existe'); window.history.back();</script>";
        $stmt->close();
        $conn->close();
        exit();
    }

    // Encriptar la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario nuevo
    $stmt = $conn->prepare("INSERT INTO usuarios (usuarios, correo, telefono, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $usuario, $correo, $telefono, $passwordHash);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Usuario registrado correctamente'); window.location='/Mediterranean_3/login.html';</script>";
    } else {
        echo "<script>alert('❌ Error al registrar usuario: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
