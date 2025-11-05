<?php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $usuarios = $_POST['usuarios'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'Invitado';
    $estado = $_POST['estado'] ?? 'activo';
    $foto = null;

    if (!$usuarios || !$correo || !$password) {
        echo json_encode(["success" => false, "message" => "Faltan campos obligatorios"]);
        exit;
    }

    // Manejo de foto: Base64 o archivo
    if (isset($_POST['foto']) && !empty($_POST['foto'])) {
        // Si viene en Base64
        $data = $_POST['foto'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $ext = strtolower($type[1]);
            $data = base64_decode($data);
            $targetDir = "uploads/";
            if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
            $foto = $targetDir . uniqid() . "." . $ext;
            file_put_contents($foto, $data);
        }
    } elseif (!empty($_FILES['foto']['name'])) {
        // Si se envía como archivo físico
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $foto = $targetDir . uniqid() . "_" . basename($_FILES["foto"]["name"]);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (usuarios, correo, password, rol, estado, foto) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $usuarios, $correo, $hash, $rol, $estado, $foto);
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
$conn->close();
?>
