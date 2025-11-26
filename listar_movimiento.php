<?php
// === CONFIGURACIÓN ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// === CONEXIÓN A DB ===
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "mediterranean";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión a la base de datos"]);
    exit;
}

// === OBTENER FILTROS ===
$producto_id = isset($_GET['productos']) ? intval($_GET['productos']) : 0;
$sucursal = isset($_GET['sucursal']) ? trim($_GET['sucursal']) : "";

// === CREAR CONSULTA ===
if ($producto_id > 0) {
    $sql = "SELECT m.id, m.tipo, m.cantidad, m.comentario, m.sucursal, m.fecha,
                   p.nombre AS producto
            FROM movimientos m
            JOIN productos p ON m.productos = p.id
            WHERE m.productos = ?
            ORDER BY m.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $producto_id);

} elseif (!empty($sucursal)) {
    $sql = "SELECT m.id, m.tipo, m.cantidad, m.comentario, m.sucursal, m.fecha,
                   p.nombre AS producto
            FROM movimientos m
            JOIN productos p ON m.productos = p.id
            WHERE m.sucursal = ?
            ORDER BY m.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sucursal);

} else {
    echo json_encode(["status" => "error", "message" => "Debe indicar un producto o una sucursal"]);
    $conn->close();
    exit;
}

// === EJECUTAR Y OBTENER RESULTADOS ===
if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Error al ejecutar la consulta"]);
    $stmt->close();
    $conn->close();
    exit;
}

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
