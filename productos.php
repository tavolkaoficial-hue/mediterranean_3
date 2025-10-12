<?php
include("conexion.php");
header("Content-Type: application/json");

// Asegurarse de que la carpeta uploads exista
$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$accion = $_GET['accion'] ?? '';

switch ($accion) {

    case "listar":
        $res = $conn->query("SELECT * FROM productos ORDER BY id DESC");
        $data = [];
        while ($row = $res->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
        break;

    case "agregar":
        $nombre = $_POST['nombre'] ?? '';
        $precio = $_POST['precio'] ?? 0;
        $stock  = $_POST['stock'] ?? 0;
        $desc   = $_POST['descripcion'] ?? '';

        // Manejo seguro de imagen
        if (isset($_FILES['img']) && $_FILES['img']['error'] === 0) {
            $imgName = time() . "_" . basename($_FILES['img']['name']);
            $ruta = $uploadDir . $imgName;
            if (!move_uploaded_file($_FILES['img']['tmp_name'], $ruta)) {
                echo json_encode(["success" => false, "error" => "No se pudo guardar la imagen."]);
                exit;
            }
        } else {
            $ruta = "";
        }

        $stmt = $conn->prepare("INSERT INTO productos (img, nombre, precio, stock, descripcion) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $ruta, $nombre, $precio, $stock, $desc);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        break;

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
?>
