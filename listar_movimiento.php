<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "mediterranean";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión"]);
    exit;
}

$producto_id = intval($_GET['productos'] ?? 0);
if ($producto_id <= 0) {
    echo json_encode(["status" => "error", "message" => "ID de producto inválido"]);
    exit;
}

$sql = "SELECT id, tipo, cantidad, comentario, fecha FROM movimientos WHERE productos = ? ORDER BY fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$result = $stmt->get_result();

$movimientos = [];
while ($row = $result->fetch_assoc()) {
    // opcional: formatea fecha si quieres
    $movimientos[] = $row;
}

echo json_encode([
    "status" => "success",
    "productos" => $producto_id,
    "movimientos" => $movimientos
]);

$stmt->close();
$conn->close();
?>
