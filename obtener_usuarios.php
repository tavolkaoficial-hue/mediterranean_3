<?php
session_start();
include 'conexion.php';

// Mostrar errores temporalmente para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit;
}

// En tu tabla el campo se llama "usuarios", no "nombre"
$sql = "SELECT usuarios AS nombre, correo, telefono, rol, descripcion, foto, cv, estado 
        FROM usuarios 
        WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Error en la consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

echo json_encode($usuario);
?>
