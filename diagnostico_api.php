<?php
header("Content-Type: application/json");

// RESULTADO FINAL
$data = [];


// -------------------------
// 1. CONEXIÓN A LA BD
// -------------------------
$mysqli = @new mysqli("localhost", "root", "", "mediterranean");

if ($mysqli->connect_errno) {
    $data["base_datos"] = "❌ Error: " . $mysqli->connect_error;
} else {
    $data["base_datos"] = "✔ Conexión exitosa a la base de datos";
}


// -------------------------
// 2. INFORMACIÓN DEL SERVIDOR
// -------------------------
$data["servidor"] = [
    "PHP version" => PHP_VERSION,
    "Sistema" => php_uname(),
    "Extensiones críticas" => [
        "mysqli" => extension_loaded("mysqli") ? "✔ Cargada" : "❌ Faltante",
        "gd" => extension_loaded("gd") ? "✔ Cargada" : "❌ Faltante",
        "curl" => extension_loaded("curl") ? "✔ Cargada" : "❌ Faltante"
    ]
];


// -------------------------
// 3. ESPACIO EN DISCO
// -------------------------
$free = disk_free_space("/");
$total = disk_total_space("/");
$data["espacio_disco"] = [
    "Libre" => round($free / 1024 / 1024 / 1024, 2) . " GB",
    "Total" => round($total / 1024 / 1024 / 1024, 2) . " GB"
];


// -------------------------
// 4. PERMISOS DE CARPETAS
// -------------------------
$carpetas = ["images", "respaldos", "logs", "temp"];
$permisos = [];

foreach ($carpetas as $c) {
    $permisos[$c] = is_writable($c) ? "✔ Correcto" : "❌ Sin permisos";
}

$data["permisos"] = $permisos;


// -------------------------
// 5. LEER ERROR_LOG
// -------------------------
$log_path = ini_get("error_log");
$errors = "";

if ($log_path && file_exists($log_path)) {
    $errors = file_get_contents($log_path);
}

$data["errores_php"] = $errors ?: "No hay errores recientes.";


// -------------------------
// RESPUESTA FINAL
// -------------------------
echo json_encode($data, JSON_PRETTY_PRINT);
?>
