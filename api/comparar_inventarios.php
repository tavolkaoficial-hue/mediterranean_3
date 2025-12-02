<?php
// api/comparar_inventarios.php
header('Content-Type: application/json; charset=utf-8');

$body = json_decode(file_get_contents('php://input'), true);
if (!$body || !isset($body['origen']) || !isset($body['destino'])) {
    echo json_encode(['error'=>'Parámetros invalidos']);
    exit;
}
$origen = intval($body['origen']);
$destino = intval($body['destino']);

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

    // Consulta: unimos productos con inventario en ORIGEN y DESTINO
    if ($db instanceof PDO) {
        $sql = "SELECT p.id AS producto_id, p.sku, p.nombre,
                 COALESCE(io.stock,0) AS stock_origen,
                 COALESCE(idt.stock,0) AS stock_destino,
                 (COALESCE(io.stock,0) - COALESCE(idt.stock,0)) AS diferencia
                FROM productos p
                LEFT JOIN inventario io ON io.producto_id = p.id AND io.sucursal_id = :origen
                LEFT JOIN inventario idt ON idt.producto_id = p.id AND idt.sucursal_id = :destino
                ORDER BY p.nombre";
        $stmt = $db->prepare($sql);
        $stmt->execute([':origen'=>$origen, ':destino'=>$destino]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['rows'=>$rows]);
    } else {
        $sql = "SELECT p.id as producto_id, p.sku, p.nombre,
                 COALESCE(io.stock,0) AS stock_origen,
                 COALESCE(idt.stock,0) AS stock_destino,
                 (COALESCE(io.stock,0) - COALESCE(idt.stock,0)) AS diferencia
                FROM productos p
                LEFT JOIN inventario io ON io.producto_id = p.id AND io.sucursal_id = $origen
                LEFT JOIN inventario idt ON idt.producto_id = p.id AND idt.sucursal_id = $destino
                ORDER BY p.nombre";
        $res = $db->query($sql);
        $rows = [];
        while($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode(['rows'=>$rows]);
    }

} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
