<?php
// Conexión
$mysqli = new mysqli("localhost", "root", "", "mediterranean");

if ($mysqli->connect_errno) {
    die(json_encode(["error" => "Error de conexión: " . $mysqli->connect_error]));
}

// Recibir JSON
$data = json_decode(file_get_contents("php://input"), true);

// Validación
if (!$data) { 
    die(json_encode(["error" => "No se enviaron datos."])); 
}

$nombre_empresa = $mysqli->real_escape_string($data['nombre_empresa']);
$logo = $mysqli->real_escape_string($data['logo']);
$modo = $mysqli->real_escape_string($data['modo_oscuro']);
$margen = floatval($data['margen_default']);
$sucursal = $mysqli->real_escape_string($data['sucursal_principal']);

// Verificar si existe registro
$check = $mysqli->query("SELECT id FROM configuracion LIMIT 1");

if ($check->num_rows == 0) {
    // Insertar
    $mysqli->query("
        INSERT INTO configuracion (nombre_empresa, logo, modo_oscuro, margen_default, sucursal_principal)
        VALUES ('$nombre_empresa', '$logo', '$modo', $margen, '$sucursal')
    ");
} else {
    // Actualizar siempre el primer (y único) registro
    $mysqli->query("
        UPDATE configuracion SET 
            nombre_empresa='$nombre_empresa',
            logo='$logo',
            modo_oscuro='$modo',
            margen_default=$margen,
            sucursal_principal='$sucursal'
        WHERE id=1
    ");
}

echo json_encode(["ok" => true]);
?>
