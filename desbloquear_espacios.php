<?php
// Configuracion de la conexion
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parking-tech1";

// Crear conexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexion
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexion: " . $conn->connect_error]);
    exit(); // Salir si la conexion falla
}

// Liberar espacios donde la hora actual ya paso la hora de salida
$current_time = date("Y-m-d H:i:s"); // Obtener la hora actual
$query_free_spaces = "UPDATE espacios 
                      JOIN reservas ON espacios.espacio = reservas.espacio 
                      SET espacios.estado = 'desbloqueado' 
                      WHERE reservas.fecha_hora_salida < ? AND espacios.estado = 'bloqueado'";

$stmt_free_spaces = $conn->prepare($query_free_spaces);

// Verificar si la consulta se prepara correctamente
if ($stmt_free_spaces === false) {
    echo json_encode(["success" => false, "message" => "Error al preparar la consulta."]);
    exit(); // Salir si hay error en la preparacion
}

$stmt_free_spaces->bind_param("s", $current_time); // Pasar el tiempo actual como parametro

// Ejecutar la consulta y manejar el resultado
if ($stmt_free_spaces->execute()) {
    // Verificar cuantas filas fueron afectadas
    if ($stmt_free_spaces->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Espacios desbloqueados correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "No se encontraron espacios para desbloquear."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Error al ejecutar la consulta."]);
}

// Cerrar conexion
$stmt_free_spaces->close();
$conn->close();
?>
