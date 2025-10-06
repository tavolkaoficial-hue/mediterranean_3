<?php
include("conexion.php");
header("Content-Type: application/json");

$accion = $_GET['accion'] ?? '';

switch ($accion) {
  case "listar":
    $res = $conn->query("SELECT * FROM productos ORDER BY id DESC");
    $data = [];
    while ($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
    break;

  case "agregar":
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock  = $_POST['stock'];
    $desc   = $_POST['descripcion'];

    // Manejo de imagen
    if (isset($_FILES['img'])) {
      $imgName = time() . "_" . basename($_FILES['img']['name']);
      $ruta = "uploads/" . $imgName;
      move_uploaded_file($_FILES['img']['tmp_name'], $ruta);
    } else {
      $ruta = "";
    }

    $stmt = $conn->prepare("INSERT INTO productos (img, nombre, precio, stock, descripcion) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $ruta, $nombre, $precio, $stock, $desc);
    $stmt->execute();

    echo json_encode(["success" => true]);
    break;

  case "eliminar":
    $id = $_POST['id'];
    $conn->query("DELETE FROM productos WHERE id=$id");
    echo json_encode(["success" => true]);
    break;

  case "actualizarStock":
    $id = $_POST['id'];
    $stock = $_POST['stock'];
    $conn->query("UPDATE productos SET stock=$stock WHERE id=$id");
    echo json_encode(["success" => true]);
    break;

  default:
    echo json_encode(["error" => "Acción no válida"]);
}
?>
