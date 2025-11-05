<?php
ob_start();
session_start();
include 'conexion.php'; // ✅ usa tu archivo central de conexión

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // ✅ Incluimos 'rol' en la consulta
    $stmt = $conn->prepare("SELECT id, usuarios, password, rol FROM usuarios WHERE usuarios = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $row = $resultado->fetch_assoc();

        if (password_verify($password, $row["password"])) {
            // ✅ Guardamos los datos en sesión
            $_SESSION["usuarios"] = $row["usuarios"];
            $_SESSION["usuario_id"] = $row["id"];
            $_SESSION["rol"] = $row["rol"];

            header("Location: mediterranean.php");
            exit();
        } else {
            echo "<script>alert('⚠ Contraseña incorrecta'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('⚠ Usuario no encontrado'); window.history.back();</script>";
    }
}

ob_end_flush();
?>
