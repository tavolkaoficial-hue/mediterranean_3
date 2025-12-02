<?php
// api/sincronizar_inventario.php
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

    // Obtener diferencias (mismo query de comparar)
    $query = "SELECT p.id AS producto_id,
                     COALESCE(io.stock,0) AS stock_origen,
                     COALESCE(idt.stock,0) AS stock_destino
              FROM productos p
              LEFT JOIN inventario io ON io.producto_id = p.id AND io.sucursal_id = :origen
              LEFT JOIN inventario idt ON idt.producto_id = p.id AND idt.sucursal_id = :destino";
    if ($db instanceof PDO) {
        $stmt = $db->prepare($query);
        $stmt->execute([':origen'=>$origen, ':destino'=>$destino]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $inserted = 0; $updated = 0; $errors = [];

        // preparar statements
        $selectInv = $db->prepare("SELECT id FROM inventario WHERE producto_id = :pid AND sucursal_id = :suc LIMIT 1");
        $updateInv = $db->prepare("UPDATE inventario SET stock = :stock WHERE id = :id");
        $insertInv = $db->prepare("INSERT INTO inventario (producto_id, sucursal_id, stock) VALUES (:pid, :suc, :stock)");

        foreach($rows as $r){
            $pid = intval($r['producto_id']);
            $stockOrigen = intval($r['stock_origen']);
            // Buscar si existe en destino
            $selectInv->execute([':pid'=>$pid, ':suc'=>$destino]);
            $found = $selectInv->fetch(PDO::FETCH_ASSOC);
            if ($found) {
                $idInv = $found['id'];
                // si distinto, actualizar
                $updateInv->execute([':stock'=>$stockOrigen, ':id'=>$idInv]);
                $updated++;
            } else {
                // insertar
                $insertInv->execute([':pid'=>$pid, ':suc'=>$destino, ':stock'=>$stockOrigen]);
                $inserted++;
            }
        }

        // Registrar en tabla logs si existe
        try {
            $logStmt = $db->prepare("INSERT INTO sync_logs (fecha, usuario, sucursal_origen, sucursal_destino, inserted, updated) VALUES (NOW(), :usr, :o, :d, :ins, :upd)");
            $user = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'system';
            $logStmt->execute([':usr'=>$user, ':o'=>$origen, ':d'=>$destino, ':ins'=>$inserted, ':upd'=>$updated]);
        } catch(Exception $e){
            // Ignorar si tabla no existe
        }

        echo json_encode(['inserted'=>$inserted, 'updated'=>$updated, 'errors'=>$errors]);

    } else {
        // mysqli variant
        $res = $db->query($query);
        $inserted = 0; $updated = 0; $errors = [];
        while($r = $res->fetch_assoc()){
            $pid = intval($r['producto_id']);
            $stockOrigen = intval($r['stock_origen']);
            $check = $db->query("SELECT id FROM inventario WHERE producto_id = $pid AND sucursal_id = $destino LIMIT 1");
            if ($check->num_rows) {
                $idInv = $check->fetch_assoc()['id'];
                $db->query("UPDATE inventario SET stock = $stockOrigen WHERE id = $idInv");
                $updated++;
            } else {
                $db->query("INSERT INTO inventario (producto_id, sucursal_id, stock) VALUES ($pid, $destino, $stockOrigen)");
                $inserted++;
            }
        }
        echo json_encode(['inserted'=>$inserted, 'updated'=>$updated, 'errors'=>$errors]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
