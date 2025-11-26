<?php
include("conexion.php");
header("Content-Type: application/json");

// Carpeta para subir imágenes
$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$accion = $_GET['accion'] ?? '';

switch ($accion) {

    /* === LISTAR PRODUCTOS === */
    case "listar":
        $res = $conn->query("SELECT * FROM productos ORDER BY id DESC");
        $data = [];
        while ($row = $res->fetch_assoc()) {
            // Asegurar que las rutas de imagen sean correctas
            if (!empty($row['img']) && !str_starts_with($row['img'], 'http') && !str_starts_with($row['img'], 'uploads/')) {
                $row['img'] = 'uploads/' . $row['img'];
            }
            $data[] = $row;
        }
        echo json_encode($data);
        break;


    /* === AGREGAR PRODUCTO === */
   case "agregar":
    $nombre        = $_POST['nombre'] ?? '';
    $categoria = ($_POST['categoria'] === "__manual__") 
    ? $_POST['categoria_manual'] 
    : $_POST['categoria'];

$proveedor = ($_POST['proveedor'] === "__manual__") 
    ? $_POST['proveedor_manual'] 
    : $_POST['proveedor'];

    $precio_compra = floatval($_POST['precio_compra'] ?? 0);
    $precio_venta  = floatval($_POST['precio_venta'] ?? 0);
    $stock         = intval($_POST['stock'] ?? 0);
    $descripcion   = $_POST['descripcion'] ?? '';
    $estado        = $_POST['estado'] ?? 'Activo';
    $sucursal      = $_POST['sucursal'] ?? ''; // 👈 ahora guardamos directamente el nombre

    // Manejo de imagen
    $ruta = '';
    if (isset($_FILES['img']) && $_FILES['img']['error'] === 0) {
        $imgName = time() . "_" . basename($_FILES['img']['name']);
        $ruta = "uploads/" . $imgName;
        move_uploaded_file($_FILES['img']['tmp_name'], $ruta);
    }

    $stmt = $conn->prepare("
        INSERT INTO productos 
        (nombre, categoria, proveedor, precio_compra, precio_venta, stock, sucursal, descripcion, estado, img)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssddissss",
        $nombre,
        $categoria,
        $proveedor,
        $precio_compra,
        $precio_venta,
        $stock,
        $sucursal,
        $descripcion,
        $estado,
        $ruta
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    break;




    /* === ELIMINAR PRODUCTO === */
    case "eliminar":
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        break;


    /* === ACTUALIZAR STOCK === */
    case "actualizarStock":
        $id = intval($_POST['id'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $stmt = $conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $stock, $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        break;


    default:
        echo json_encode(["error" => "Acción no válida"]);
        break;
}

$conn->close();
?>
