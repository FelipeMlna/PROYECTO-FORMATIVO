<?php
// Iniciar sesión para verificar autenticación
session_start();

// Inicializar mensaje de error o éxito
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

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $contrasena = $_POST['password'] ?? '';

    // Validar que los campos no estén vacíos
    if (empty($email) || empty($contrasena)) {
        $mensaje = "Por favor, completa todos los campos.";
    } else {
        // Preparar consulta para obtener al usuario
        $stmt = $conn->prepare("SELECT nombre, contrasena, roles FROM registros WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            // Verificar si se encontró el usuario
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($nombre, $contrasena_hash, $rol);
                $stmt->fetch();

                // Verificar la contraseña
                if (password_verify($contrasena, $contrasena_hash)) {
                    // Almacenar el email, nombre y rol en la sesión
                    $_SESSION['email'] = $email;
                    $_SESSION['nombre'] = $nombre;
                    $_SESSION['rol'] = $rol;

                    // Redireccionar según el rol del usuario
                    if ($rol === 'Administrador') {
                        header("Location: admin.php");
                    } elseif ($rol === 'Operador') {
                        header("Location: operador.php");
                    } else {
                        header("Location: login.php");
                    }
                    exit();
                } else {
                    $mensaje = "Contraseña incorrecta.";
                }
            } else {
                $mensaje = "No se encontró una cuenta asociada a este correo.";
            }
            $stmt->close();
        } else {
            $mensaje = "Error en la preparación de la consulta: " . $conn->error;
        }
    }
}

// Cerrar conexión
$conn->close();
?>

<!-- Mostrar el mensaje en caso de error o éxito -->
<?php if ($mensaje): ?>
    <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>
