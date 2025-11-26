<?php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

$limit = intval($_GET['limit'] ?? 10);

$sql = "SELECT m.id, m.tipo AS tipo_movimiento, m.cantidad, m.comentario, m.fecha,
               p.nombre AS nombre_producto, s.nombre AS nombre_sucursal
        FROM movimientos m
        JOIN productos p ON m.productos = p.id
        LEFT JOIN sucursales s ON m.sucursal = s.nombre
        ORDER BY m.fecha DESC
        LIMIT ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $limit);
$stmt->execute();

$result = $stmt->get_result();
$movimientos = [];
while ($row = $result->fetch_assoc()) {
    $row['fecha'] = date("Y-m-d H:i", strtotime($row['fecha']));
    $movimientos[] = $row;
}

echo json_encode($movimientos);

$stmt->close();
$conn->close();
?>
