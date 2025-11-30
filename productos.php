<?php
include("conexion.php");

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


 case "agregar":

    // Recibir valores del formulario
    $nombre         = $_POST['nombre'];
    $categorias     = $_POST['categorias'];
    $proveedores    = $_POST['proveedores'];
    $precio_compra  = $_POST['precio_compra'];
    $precio_venta   = $_POST['precio_venta'];
    $stock          = $_POST['stock'];
    $sucursal       = $_POST['sucursal'];
    $descripcion    = $_POST['descripcion'];
    $estado         = $_POST['estado'];

    // Procesar imagen
    $img = "";
    if (!empty($_FILES['img']['name'])) {
        $nombreImg = time() . "_" . basename($_FILES['img']['name']);
        $rutaImg = "uploads/" . $nombreImg;

        if (move_uploaded_file($_FILES['img']['tmp_name'], $rutaImg)) {
            $img = $rutaImg;
        }
    }

    // Insertar en base de datos
    $sql = "INSERT INTO productos 
            (nombre, categorias, proveedores, precio_compra, precio_venta, stock, sucursal, img, descripcion, estado, fecha_actualizacion)
            VALUES 
            ('$nombre', '$categorias', '$proveedores', '$precio_compra', '$precio_venta', '$stock', '$sucursal', '$img', '$descripcion', '$estado', NOW())";

    $conn->query($sql);
    if ($conn->error) {
    echo json_encode(["error_sql" => $conn->error]);
    exit;
}


    echo json_encode(["success" => true]);
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
