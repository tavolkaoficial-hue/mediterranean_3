<?php
// api/obtener_sucursales.php
header('Content-Type: application/json; charset=utf-8');

try {
    // Intenta usar conexion.php si existe, si no usa credenciales por defecto.
    if (file_exists(__DIR__ . '/../conexion.php')) {
        require_once __DIR__ . '/../conexion.php';
        // Asumimos que conexion.php deja $pdo (PDO) o $conn (mysqli). Adaptador:
        if (isset($pdo) && $pdo instanceof PDO) {
            $db = $pdo;
        } elseif (isset($conn) && $conn instanceof mysqli) {
            $db = $conn;
        } else {
            // crear PDO por defecto con credenciales (ajusta si necesitas)
            throw new Exception('conexion.php encontrada pero no provee $pdo ni $conn. Ajusta el archivo.');
        }
    } else {
        // Ajusta credenciales aquí si no usas conexion.php
        $host = '127.0.0.1';
        $dbName = 'mediterranean';
        $user = 'root';
        $pass = '';
        $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";
        $db = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    // Si $db es PDO
    if ($db instanceof PDO) {
        $stmt = $db->query("SELECT id, nombre FROM sucursales ORDER BY nombre ASC");
        $suc = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($suc);
        exit;
    } else {
        // mysqli
        $res = $db->query("SELECT id, nombre FROM sucursales ORDER BY nombre ASC");
        $out = [];
        while($r = $res->fetch_assoc()) $out[] = $r;
        echo json_encode($out);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
