<?php
session_start();
include 'conexion.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit;
}

$nombre = $_POST['nombre'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$correo = $_POST['correo'] ?? '';
$rol = $_POST['rol'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$estado = $_POST['estado'] ?? 'activo';

$fotoRuta = null;
$cvRuta = null;

$carpetaFotos = "uploads/fotos/";
$carpetaCV = "uploads/cv/";

if (!file_exists($carpetaFotos)) mkdir($carpetaFotos, 0777, true);
if (!file_exists($carpetaCV)) mkdir($carpetaCV, 0777, true);

// Subir foto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $nombreFoto = time() . "_" . basename($_FILES['foto']['name']);
    $fotoRuta = $carpetaFotos . $nombreFoto;
    move_uploaded_file($_FILES['foto']['tmp_name'], $fotoRuta);
}

// Subir CV
if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
    $nombreCV = time() . "_" . basename($_FILES['cv']['name']);
    $cvRuta = $carpetaCV . $nombreCV;
    move_uploaded_file($_FILES['cv']['tmp_name'], $cvRuta);
}

// Construcción dinámica de la consulta
$sql = "UPDATE usuarios 
        SET usuarios=?, telefono=?, correo=?, rol=?, descripcion=?, estado=?";
$parametros = [$nombre, $telefono, $correo, $rol, $descripcion, $estado];
$tipos = "ssssss";

if ($fotoRuta) {
    $sql .= ", foto=?";
    $parametros[] = $fotoRuta;
    $tipos .= "s";
}

if ($cvRuta) {
    $sql .= ", cv=?";
    $parametros[] = $cvRuta;
    $tipos .= "s";
}

$sql .= " WHERE id=?";
$parametros[] = $usuario_id;
$tipos .= "i";

$stmt = $conn->prepare($sql);
$stmt->bind_param($tipos, ...$parametros);
$ok = $stmt->execute();

if (!$ok) {
    echo json_encode(["error" => $stmt->error]);
    exit;
}

echo json_encode(["success" => true]);
?>
