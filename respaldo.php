<?php
// CONFIGURACIÓN DE LA BD
$usuario = "PedroGuevara";
$pass = "Peter1992";
$bd = "mediterranean";
$host = "localhost";

// Carpeta de destino
$carpeta = __DIR__ . "/backups/";
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}

$fecha = date("Y-m-d_H-i-s");
$nombreArchivo = "backup_{$bd}_{$fecha}.sql";
$rutaCompleta = $carpeta . $nombreArchivo;

// Ruta del mysqldump (XAMPP)
$mysqldump = "C:/xampp/mysql/bin/mysqldump.exe";

// Comando
$cmd = "\"$mysqldump\" --user=$usuario --password=$pass --host=$host $bd > \"$rutaCompleta\"";

// Ejecutar respaldo
system($cmd, $resultado);

// Validar
if (!file_exists($rutaCompleta) || filesize($rutaCompleta) == 0) {
    die("Error al generar respaldo. Verifica credenciales o ruta de mysqldump.");
}

// Descargar al navegador
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$nombreArchivo");
readfile($rutaCompleta);
exit;
?>
