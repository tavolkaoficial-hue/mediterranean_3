<?php
session_start();
header('Content-Type: application/json');
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["success" => false, "message" => "No hay sesión iniciada"]);
    exit();
}

$id = $_SESSION['usuario_id'];

$sql = "SELECT id, nombre, correo, rol, descripcion, estado, foto, cv FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
}
?>
