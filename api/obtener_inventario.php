<?php
// api/obtener_inventario.php
header('Content-Type: application/json; charset=utf-8');

$input = $_GET;
if (!isset($input['sucursal'])) {
    echo json_encode(['error'=>'Falta parámetro sucursal']);
    exit;
}
$sucursal = intval($input['sucursal']);

try {
    if (file_exists(__DIR__ . '/../conexion.php')) {
        require_once __DIR__ . '/../conexion.php';
        if (isset($pdo) && $pdo instanceof PDO) $db = $pdo;
        elseif (isset($conn) && $conn instanceof mysqli) $db = $conn;
        else throw new Exception('conexion.php no devuelve $pdo ni $conn');
    } else {
        $host = '127.0.0.1';
        $dbName = 'mediterranean';
        $user = 'root';
        $pass = '';
        $db = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    }

    // Estructura asumida:
    // productos (id, sku, nombre)
    // inventario (id, producto_id, sucursal_id, stock)
    if ($db instanceof PDO) {
        $sql = "SELECT p.sku, p.nombre, COALESCE(i.stock,0) AS stock
                FROM productos p
                LEFT JOIN inventario i ON i.producto_id = p.id AND i.sucursal_id = :suc
                ORDER BY p.nombre";
        $stmt = $db->prepare($sql);
        $stmt->execute([':suc'=>$sucursal]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['rows'=>$rows]);
    } else {
        $sql = "SELECT p.sku, p.nombre, COALESCE(i.stock,0) AS stock
                FROM productos p
                LEFT JOIN inventario i ON i.producto_id = p.id AND i.sucursal_id = $sucursal
                ORDER BY p.nombre";
        $res = $db->query($sql);
        $rows = [];
        while($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode(['rows'=>$rows]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
