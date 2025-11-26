<?php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

// Opcional: filtrar por sucursal
$sucursal = $_GET['sucursal'] ?? null;

$sql = "SELECT id, nombre AS nombre_producto, stock, sucursal AS nombre_sucursal FROM productos";

if ($sucursal) {
    $sql .= " WHERE sucursal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sucursal);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    // Si la sucursal está vacía, poner "Desconocida"
    if (!$row['nombre_sucursal']) $row['nombre_sucursal'] = 'Desconocida';
    $productos[] = $row;
}

echo json_encode($productos);

$stmt->close();
$conn->close();
?>
