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
    echo json_encode(["status" => "error", "message" => "Error de conexión a la base de datos"]);
    exit;
}

$producto_id = isset($_GET['productos']) ? intval($_GET['productos']) : 0;
$sucursal = $_GET['sucursal'] ?? "";

if ($producto_id > 0) {
    // 🔹 Movimientos por producto
    $sql = "SELECT m.id, m.tipo, m.cantidad, m.comentario, m.sucursal, m.fecha,
                   p.nombre AS producto
            FROM movimientos m
            JOIN productos p ON m.productos = p.id
            WHERE m.productos = ?
            ORDER BY m.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $producto_id);

} elseif (!empty($sucursal)) {
    // 🔹 Movimientos por sucursal
    $sql = "SELECT m.id, m.tipo, m.cantidad, m.comentario, m.sucursal, m.fecha,
                   p.nombre AS producto
            FROM movimientos m
            JOIN productos p ON m.productos = p.id
            WHERE m.sucursal = ?
            ORDER BY m.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sucursal);

} else {
    echo json_encode(["status" => "error", "message" => "Debe indicar productos o sucursal"]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$movimientos = [];
while ($row = $result->fetch_assoc()) {
    $row['fecha'] = date("Y-m-d H:i", strtotime($row['fecha']));
    $movimientos[] = $row;
}

echo json_encode([
    "status" => "success",
    "movimientos" => $movimientos
]);

$stmt->close();
$conn->close();
?>
