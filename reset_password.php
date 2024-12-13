<?php
include("conexion.php"); // Conexion a la base de datos

// Verificar si se proporciono un token en la URL
if (!isset($_GET['token'])) {
    die("Token no proporcionado.");
}

$token = $_GET['token'];
$mensaje = "";

// Verificar si el token es valido
$stmt = $conn->prepare("SELECT * FROM registros WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("El token es invalido o ha expirado.");
}

$usuario = $result->fetch_assoc();
$email = $usuario['email']; // Obtener el email asociado al token

// Manejar el formulario de restablecimiento de contrasena
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($nueva_password !== $confirm_password) {
        $mensaje = "Las contrasenas no coinciden.";
    } else {
        // Encriptar la nueva contrasena
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);

        // Actualizar la contrasena en la base de datos
        $stmt = $conn->prepare("UPDATE registros SET contrasena = ?, token = NULL WHERE email = ?");
        $stmt->bind_param("ss", $password_hash, $email);
        
        if ($stmt->execute()) {
            $mensaje = "Contrasena actualizada exitosamente. Ahora puedes iniciar sesion.";
        } else {
            $mensaje = "Error al actualizar la contrasena. Intentalo de nuevo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Manejamos el mejor parquiadero de Ibague">
    <meta name="robots" content="index,follow">
    <title>PARKING-TECH/Registrarse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="InicarSecion.css">

</head>
<body>
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container-fluid">
                <a href="index.html" class="navbar-brand d-flex align-items-center">
                    <img src="Imagenes/Logo-removebg-preview.png" width="70" alt="Logo">
                    <span class="ms-2">PARKING-TECH</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item d-flex align-items-center">
                            <img class="iconos-navbar" src="icon.html/apoyo-unscreen.gif" alt="" width="30" height="30">
                            <a href="Ayuda.html" class="nav-link ms-1">Contáctanos</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <div class="container mt-5">
    <h2 class="text-center">Restablecer Contraseña</h2>
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    <form action="" method="post" class="mt-4">
        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Ingresa tu nueva contrasena" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirma tu nueva contrasena" required>
        </div>
        <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
    </form>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 
</body>
</html>

<style>
    /* Fondo general */
    body {
        background-image: url('Imagenes/FONDO PARk.jpg');
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
        min-height: 100vh;
        margin: 0;
    }
    
    /* Navbar */
    .navbar-brand {
        font-size: 40px;
        font-weight: bold;
        font-family: 'Times New Roman', Times, serif;
        color: white;
        text-transform: uppercase;
        text-shadow: 2px 2px 2px rgba(0, 0, 0, 0.5);
    }
    
    .navbar-toggler {
        border-color: white;
    }
    
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28155, 155, 155, 1%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    
    /* Enlace "Contáctanos" */
    .nav-link {
        font-family: 'Times New Roman', serif;
        font-size: 26px;
        font-weight: bold;
        color: white;
        text-transform: capitalize;
        text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.5);
    }
    

    /* Responsive */
    @media (max-width: 768px) {
        .navbar-brand {
            font-size: 20px;
        }
    
        .nav-link {
            font-size: 24px;
        }
    }
    </style>