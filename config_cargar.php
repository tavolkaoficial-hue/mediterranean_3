<?php
$mysqli = new mysqli("localhost", "root", "", "mediterranean");

$res = $mysqli->query("SELECT * FROM configuracion LIMIT 1");

if ($res->num_rows == 0) {
    echo json_encode([
        "nombre_empresa" => "Mediterranean",
        "logo" => "images/LogoMediterranean1992.png",
        "modo_oscuro" => "si",
        "margen_default" => 20.00,
        "sucursal_principal" => "Central"
    ]);
    exit;
}

echo json_encode($res->fetch_assoc());
?>
