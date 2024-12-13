<?php
// Datos de conexion
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parking-tech1";

// Crear conexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexion
if ($conn->connect_error) {
    die("Conexion fallida: " . $conn->connect_error);
}

// Verificar si se recibe el email desde la solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Verificar si el correo ya existe en la base de datos
    $sql = "SELECT * FROM registros WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El correo ya existe
        echo json_encode(['status' => 'error', 'message' => 'El correo ya esta registrado.']);
    } else {
        // El correo no existe
        echo json_encode(['status' => 'success', 'message' => '']);
    }

    $stmt->close();
    $conn->close();
}
?>