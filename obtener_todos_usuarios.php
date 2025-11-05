<?php
session_start();
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

// Si quieres exigir login, descomenta esto:
// if (!isset($_SESSION['usuario_id'])) {
//     echo json_encode(["success" => false, "error" => "No autenticado"]);
//     exit;
// }

$sql = "SELECT id, usuarios, correo, rol, LOWER(estado) AS estado, foto FROM usuarios";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {

    // 🔹 Si la foto está vacía, usar una imagen por defecto
    if (empty($row['foto'])) {
        $row['foto'] = 'https://via.placeholder.com/48';
    } 
    // 🔹 Si tiene ruta local, asegurar que tenga la carpeta 'uploads/'
    elseif (!preg_match('/^https?:\/\//', $row['foto'])) {
        $row['foto'] = 'uploads/' . basename($row['foto']);
    }

    $users[] = $row;
}

echo json_encode(["success" => true, "users" => $users]);
$conn->close();
?>
