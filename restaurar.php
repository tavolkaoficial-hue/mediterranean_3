<?php
// Datos de conexión
$usuario = "PedroGuevara";
$pass = "Peter1992";
$bd = "mediterranean";
$host = "localhost";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!isset($_FILES["archivo"]) || $_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
        die("Error al subir archivo SQL.");
    }

    $rutaTemp = $_FILES["archivo"]["tmp_name"];

    // Ruta de mysql.exe
    $mysql = "C:/xampp/mysql/bin/mysql.exe";

    // Comando para restaurar
    $cmd = "\"$mysql\" --user=$usuario --password=$pass --host=$host $bd < \"$rutaTemp\"";

    system($cmd, $resultado);

    if ($resultado !== 0) {
        die("Error al restaurar base de datos.");
    }

    echo "<h2>Restauración completada con éxito.</h2>";
    echo "<a href='backup_center.php'>Volver</a>";
}
?>
