<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "mediterranean";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
