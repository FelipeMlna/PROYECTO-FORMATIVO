<?php
// Configuración para la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parking-tech1";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Definir respuesta por defecto
$response = [
    'success' => false,
    'message' => 'Ocurrió un error al procesar la solicitud.'
];

// Validar si el formulario fue enviado con la información necesaria
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $fecha_hora_entrada = $_POST['fecha_hora_entrada'] ?? '';
    $fecha_hora_salida = $_POST['fecha_hora_salida'] ?? '';
    $espacio = $_POST['espacio'] ?? '';

    // Verificar que los campos no estén vacíos
    if (empty($fecha_hora_entrada) || empty($fecha_hora_salida) || empty($espacio)) {
        $response['message'] = 'Por favor, completa todos los campos.';
    } else {
        // Verificar solapamiento de horarios
        $query_check = "SELECT 1 FROM reservas 
                        WHERE espacio = ? 
                        AND NOT (fecha_hora_salida <= ? OR fecha_hora_entrada >= ?)";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param("sss", $espacio, $fecha_hora_entrada, $fecha_hora_salida);
        $stmt_check->execute();
        $resultado = $stmt_check->get_result();

        if ($resultado->num_rows > 0) {
            $response['message'] = "El espacio ya está reservado en el horario seleccionado.";
        } else {
            $response['success'] = true;
            $response['message'] = "El espacio está disponible para la reserva.";
        }
        $stmt_check->close();
    }
}

$conn->close();

// Enviar respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

