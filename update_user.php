<?php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $id = $_POST['id'] ?? null;
    $usuarios = trim($_POST['usuarios'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'Invitado';
    $estado = $_POST['estado'] ?? 'activo';
    $foto = null;

    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID de usuario no especificado"]);
        exit;
    }

    // 🔹 Obtener datos actuales
    $stmt = $conn->prepare("SELECT password, foto FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuarioActual = $result->fetch_assoc();
    $stmt->close();

    if (!$usuarioActual) {
        echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
        exit;
    }

    $hash = $usuarioActual['password'];
    $fotoActual = $usuarioActual['foto'];

    // 🔹 Si hay nueva contraseña
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
    }

    // 🔹 Si hay nueva imagen base64
    if (!empty($_POST['foto']) && preg_match('/^data:image\/(\w+);base64,/', $_POST['foto'], $type)) {
        $data = substr($_POST['foto'], strpos($_POST['foto'], ',') + 1);
        $ext = strtolower($type[1]);
        $data = base64_decode($data);
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $foto = $targetDir . uniqid("usr_") . "." . $ext;
        file_put_contents($foto, $data);
    }

    // 🔹 Si se sube como archivo
    elseif (!empty($_FILES['foto']['name'])) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $foto = $targetDir . uniqid("usr_") . "_" . basename($_FILES["foto"]["name"]);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);
    }

    // 🔹 Mantener la foto anterior si no se envió una nueva
    if (!$foto) {
        $foto = $fotoActual;
    }

    // 🔹 Actualizar usuario
    $stmt = $conn->prepare("
        UPDATE usuarios 
        SET usuarios = ?, correo = ?, password = ?, rol = ?, estado = ?, foto = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssssssi", $usuarios, $correo, $hash, $rol, $estado, $foto, $id);
    $ok = $stmt->execute();

    if (!$ok) {
        echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
        exit;
    }

    echo json_encode(["success" => true, "message" => "Usuario actualizado correctamente"]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>
