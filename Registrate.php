<?php
// Inicializar la variable de mensaje
$mensaje = "";

// Datos de conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parking-tech1";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Validar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Asegurarse de obtener los valores correctamente
    $nombre = $_POST['name'] ?? ''; // Debe coincidir con el nombre del campo HTML
    $email = $_POST['email'] ?? '';
    $contrasena = $_POST['password'] ?? '';
    $placa = $_POST['placa'] ?? '';

    // Validar si los campos están vacíos
    if (empty($nombre) || empty($email) || empty($contrasena) || empty($placa)) {
        echo "<script>alert('Por favor, completa todos los campos.');</script>";
    } else {
        // Verificar si el correo ya está registrado
        $stmt_verificar = $conn->prepare("SELECT email FROM registros WHERE email = ?");
        if (!$stmt_verificar) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt_verificar->bind_param("s", $email);
        $stmt_verificar->execute();
        $stmt_verificar->store_result();

        if ($stmt_verificar->num_rows > 0) {
            // Si el correo ya está registrado, mostrar la alerta con JavaScript y no hacer nada más
            echo "<script>alert('Tu correo ya está registrado'); window.location.href = 'IniciarSesion.html';</script>";
        } else {
            // Si el correo no está registrado, proceder con la inserción de datos
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO registros (nombre, email, contrasena, placa) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssss", $nombre, $email, $contrasena_hash, $placa);
                if ($stmt->execute()) {
                    // Si el usuario se guarda correctamente, redirigir a la página de inicio de sesión
                    echo "<script>alert('Usuario registrado exitosamente'); window.location.href = 'IniciarSesion.html';</script>";
                } else {
                    // Si ocurre un error al guardar los datos, mostrar un mensaje de error
                    echo "<script>alert('Error al guardar los datos. Intenta nuevamente.');</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Error en la preparación de la consulta: " . $conn->error . "');</script>";
            }
        }
        $stmt_verificar->close();
    }
}

// Cerrar conexión
$conn->close();
?>
