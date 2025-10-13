<?php
session_start();
header('Content-Type: application/json');
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["success" => false, "message" => "No hay sesión activa"]);
    exit();
}

$id = $_SESSION['usuario_id'];
$nombre = $_POST['nombre'] ?? '';
$correo = $_POST['correo'] ?? '';
$rol = $_POST['rol'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$estado = $_POST['estado'] ?? 'activo';

$foto_path = null;
$cv_path = null;

// Subir foto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $foto_dir = "uploads/fotos/";
    if (!is_dir($foto_dir)) mkdir($foto_dir, 0777, true);
    $foto_path = $foto_dir . uniqid() . "_" . basename($_FILES["foto"]["name"]);
    move_uploaded_file($_FILES["foto"]["tmp_name"], $foto_path);
}

// Subir CV
if (isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {
    $cv_dir = "uploads/cv/";
    if (!is_dir($cv_dir)) mkdir($cv_dir, 0777, true);
    $cv_path = $cv_dir . uniqid() . "_" . basename($_FILES["cv"]["name"]);
    move_uploaded_file($_FILES["cv"]["tmp_name"], $cv_path);
}

// Construir SQL
$sql = "UPDATE usuarios SET nombre=?, correo=?, rol=?, descripcion=?, estado=?";
$params = [$nombre, $correo, $rol, $descripcion, $estado];
$types = "sssss";

if ($foto_path) {
    $sql .= ", foto=?";
    $params[] = $foto_path;
    $types .= "s";
}
if ($cv_path) {
    $sql .= ", cv=?";
    $params[] = $cv_path;
    $types .= "s";
}

$sql .= " WHERE id=?";
$params[] = $id;
$types .= "i";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$success = $stmt->execute();

if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar"]);
}
?>
