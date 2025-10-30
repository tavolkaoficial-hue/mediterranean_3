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

$producto_id = intval($_POST['productos'] ?? 0);
$tipo = $_POST['tipo'] ?? '';
$cantidad = intval($_POST['cantidad'] ?? 0);
$comentario = $_POST['comentario'] ?? null;
$sucursal = $_POST['sucursal'] ?? ''; // 👈 NUEVO

if ($producto_id <= 0 || $cantidad <= 0 || !in_array($tipo, ['Entrada', 'Salida']) || empty($sucursal)) {
    echo json_encode(["status" => "error", "message" => "Datos inválidos"]);
    exit;
}

// Seleccionamos stock actual
$sel = $conn->prepare("SELECT stock FROM productos WHERE id = ?");
$sel->bind_param("i", $producto_id);
$sel->execute();
$resSel = $sel->get_result();
if ($resSel->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Producto no encontrado"]);
    exit;
}
$row = $resSel->fetch_assoc();
$stock_actual = intval($row['stock']);

// Verificar stock
if ($tipo === 'Salida' && $stock_actual < $cantidad) {
    echo json_encode(["status" => "error", "message" => "Stock insuficiente"]);
    exit;
}

// Guardar movimiento (agregamos sucursal)
$stmt = $conn->prepare("INSERT INTO movimientos (productos, tipo, cantidad, comentario, sucursal, fecha) VALUES (?, ?, ?, ?, ?, NOW())");
if ($stmt === false) {
    echo json_encode(["status" => "error", "message" => "Error en la preparación de la consulta"]);
    exit;
}
$stmt->bind_param("isiss", $producto_id, $tipo, $cantidad, $comentario, $sucursal);

if ($stmt->execute()) {
    $nuevo_stock = ($tipo === 'Entrada') ? $stock_actual + $cantidad : $stock_actual - $cantidad;
    $update = $conn->prepare("UPDATE productos SET stock = ?, fecha_actualizacion = NOW() WHERE id = ?");
    $update->bind_param("ii", $nuevo_stock, $producto_id);
    $update->execute();
    echo json_encode(["status" => "success", "message" => "Movimiento registrado correctamente", "nuevo_stock" => $nuevo_stock]);
} else {
    echo json_encode(["status" => "error", "message" => "No se pudo registrar el movimiento"]);
}

$stmt->close();
$conn->close();
?>
