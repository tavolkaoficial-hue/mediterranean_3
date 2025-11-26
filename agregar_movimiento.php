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
    echo json_encode(["status" => "error", "message" => "Error de conexión a la base de datos: " . $conn->connect_error]);
    exit;
}

// Aseguramos compatibilidad con UTF8MB4
$conn->set_charset("utf8mb4");

// Habilitar reporte de errores MySQLi (depuración)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// === OBTENER DATOS DEL FORMULARIO ===
$producto_id = intval($_POST['productos'] ?? 0);
$tipo        = trim($_POST['tipo'] ?? '');
$cantidad    = intval($_POST['cantidad'] ?? 0);
$comentario  = trim($_POST['comentario'] ?? '');
$sucursal    = trim($_POST['sucursal'] ?? '');

// === VALIDACIONES ===
$errores = [];

if ($producto_id <= 0) $errores[] = "Producto inválido (productos: $producto_id)";
if (!in_array($tipo, ['Entrada', 'Salida'])) $errores[] = "Tipo de movimiento inválido (tipo: $tipo)";
if ($cantidad <= 0) $errores[] = "Cantidad inválida (cantidad: $cantidad)";
if (empty($sucursal)) $errores[] = "Sucursal no puede estar vacía";

if (count($errores) > 0) {
    echo json_encode(["status" => "error", "message" => implode(", ", $errores)]);
    exit;
}

// === VERIFICAR STOCK ACTUAL ===
try {
    $stmt = $conn->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Producto no encontrado (ID: $producto_id)"]);
        exit;
    }

    $row = $res->fetch_assoc();
    $stock_actual = intval($row['stock']);
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error al obtener stock: " . $e->getMessage()]);
    exit;
}

// === VERIFICAR STOCK EN SALIDAS ===
if ($tipo === 'Salida' && $stock_actual < $cantidad) {
    echo json_encode(["status" => "error", "message" => "Stock insuficiente. Actual: $stock_actual, requerido: $cantidad"]);
    exit;
}

// === INSERTAR MOVIMIENTO ===
try {
    $stmt = $conn->prepare("INSERT INTO movimientos (productos, tipo, cantidad, comentario, sucursal, fecha) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    $stmt->bind_param("isiss", $producto_id, $tipo, $cantidad, $comentario, $sucursal);

    if ($stmt->execute()) {
        // === ACTUALIZAR STOCK ===
        $nuevo_stock = ($tipo === 'Entrada') ? $stock_actual + $cantidad : $stock_actual - $cantidad;

        $upd = $conn->prepare("UPDATE productos SET stock = ?, fecha_actualizacion = NOW() WHERE id = ?");
        if (!$upd) {
            throw new Exception("Movimiento registrado pero no se pudo actualizar stock: " . $conn->error);
        }
        $upd->bind_param("ii", $nuevo_stock, $producto_id);
        $upd->execute();
        $upd->close();

        echo json_encode([
            "status" => "success",
            "message" => "Movimiento registrado correctamente",
            "nuevo_stock" => $nuevo_stock
        ]);
    } else {
        throw new Exception("No se pudo registrar el movimiento: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>
