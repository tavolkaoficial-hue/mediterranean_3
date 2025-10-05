<?php
ob_start();
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "mediterranean";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, usuarios, password FROM usuarios WHERE usuarios = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $row = $resultado->fetch_assoc();

        if (password_verify($password, $row["password"])) {
            $_SESSION["usuarios"] = $row["usuarios"];
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
