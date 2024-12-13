<?php
session_start();
include("conexion.php"); // Incluir la conexion a la base de datos

// Verificar si el usuario esta autenticado y es administrador
if (!isset($_SESSION['email']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: IniciarSesion.html"); // Redirigir si no esta autenticado
    exit();
}

$mensaje = "";

// Verificar si se ha enviado un email para modificar un usuario
if (isset($_GET['email'])) {
    $emailActual = $_GET['email'];

    // Obtener los datos actuales del usuario
    $stmt = $conn->prepare("SELECT Nombre, Email, Placa, Roles FROM registros WHERE Email = ?");
    $stmt->bind_param("s", $emailActual);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();

    // Verificar si el usuario existe
    if (!$usuario) {
        $mensaje = "Usuario no encontrado.";
    }

    // Si se envia el formulario de modificacion
    if (isset($_POST['submit'])) {
        // Obtener los nuevos valores del formulario
        $nombre = $_POST['nombre'];
        $emailNuevo = $_POST['email'];
        $placa = $_POST['placa'];
        $roles = $_POST['roles'];

        // Validar que los datos no esten vacios
        if (empty($nombre) || empty($emailNuevo) || empty($placa) || empty($roles)) {
            $mensaje = "Todos los campos son obligatorios.";
        } else {
            // Validar si el nuevo correo electronico ya existe en la base de datos
            $stmt = $conn->prepare("SELECT Email FROM registros WHERE Email = ? AND Email != ?");
            $stmt->bind_param("ss", $emailNuevo, $emailActual);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $mensaje = "El nuevo correo electronico ya esta registrado.";
            } else {
                // Actualizar los datos en la base de datos
                $stmt = $conn->prepare("UPDATE registros SET Nombre = ?, Email = ?, Placa = ?, Roles = ? WHERE Email = ?");
                $stmt->bind_param("sssss", $nombre, $emailNuevo, $placa, $roles, $emailActual);
                if ($stmt->execute()) {
                    $mensaje = "Usuario modificado exitosamente.";
                } else {
                    $mensaje = "Error al modificar el usuario: " . $conn->error;
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="narvar.css">
    <link rel="stylesheet" href="modificar_usuario.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a href="admin.php" class="navbar-brand d-flex align-items-center">
                <img src="Imagenes/Logo-removebg-preview.png" width="70" alt="Logo">
                <span class="ms-2">ADMINISTRACIÓN-USUARIOS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
</header>

<div class="container mt-4">
    <!-- Mostrar mensaje de exito o error -->
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <?php if ($usuario): ?>
        <!-- Formulario de modificacion -->
        <form method="POST">
            <div class="mb-3">
                <div class="col-lg-12 d-flex flex-column justify-content-center align-items-center">
                    <img src="icon.html/Logo-removebg-preview.png" alt="Logo" style="width: 5rem;">
                    <h1 class="sesion">Modificar Usuarios</h1>
                </div>
                <label for="nombre" style="font-family:'Times New Roman', Times, serif;" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $usuario['Nombre']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" style="font-family:'Times New Roman', Times, serif;" class="form-label">Correo Electronico</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $usuario['Email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="placa" style="font-family:'Times New Roman', Times, serif;" class="form-label">Placa</label>
                <input type="text" class="form-control" id="placa" name="placa" value="<?php echo $usuario['Placa']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="roles" style="font-family:'Times New Roman', Times, serif;" class="form-label">Rol</label>
                <select class="form-control" id="roles" name="roles" required>
                    <option value="Administrador" <?php echo $usuario['Roles'] == 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                    <option value="Usuario" <?php echo $usuario['Roles'] == 'Usuario' ? 'selected' : ''; ?>>Usuario</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Modificar Usuario</button>
        </form>
    <?php else: ?>
        <p>No se encontro el usuario.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
