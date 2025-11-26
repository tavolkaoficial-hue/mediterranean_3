<?php
header("Content-Type: application/json; charset=UTF-8");
include("conexion.php");

$response = [
    "success" => false,
    "sucursales" => []
];

$sql = "SELECT nombre FROM sucursales ORDER BY nombre ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Solo enviamos el nombre como usas en el <select>
        $response["sucursales"][] = $row["nombre"];
    }
    $response["success"] = true;
} else {
    $response["success"] = false;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

$conn->close();
?>
