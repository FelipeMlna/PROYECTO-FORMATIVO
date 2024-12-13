<?php
session_start();
include("conexion.php");

// Verificar si el usuario esta autenticado
if (!isset($_SESSION['email']) || !in_array($_SESSION['rol'], ['Administrador', 'Operador', 'Usuario'])) {
    header("Location: IniciarSesion.html"); // Redirigir si no esta autenticado o no tiene un rol permitido
    exit();
}

$mensaje = "";

// Verificar si se ha recibido una solicitud para modificar una reserva
if (isset($_GET['id_reserva'])) {
    $id_reserva = $_GET['id_reserva'];

    // Verificar que el usuario tenga permiso para modificar esta reserva
    if ($_SESSION['rol'] === 'Usuario') {
        // Para usuarios, solo permitir modificar sus propias reservas
        $stmt = $conn->prepare("SELECT * FROM reservas WHERE id_reserva = ? AND email = ?");
        $stmt->bind_param("is", $id_reserva, $_SESSION['email']);
    } else {
        // Para operadores y administradores, pueden modificar cualquier reserva
        $stmt = $conn->prepare("SELECT * FROM reservas WHERE id_reserva = ?");
        $stmt->bind_param("i", $id_reserva);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $reserva = $result->fetch_assoc();
    $stmt->close();

    // Verificar si la reserva existe
    if (!$reserva) {
        $mensaje = "Reserva no encontrada o no tiene permiso para modificarla.";
    }

    // Si se envia el formulario de modificacion
    if (isset($_POST['submit'])) {
        // Obtener los nuevos valores del formulario
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $espacio = $_POST['espacio']; // Asumiendo que el espacio se recibe correctamente
        $fecha_entrada = $_POST['fecha_entrada']; // Fecha y hora de entrada
        $fecha_salida = $_POST['fecha_salida']; // Fecha y hora de salida
        $email = $_POST['email'];

        // Actualizar la reserva en la base de datos
        $stmt = $conn->prepare("UPDATE reservas SET nombre = ?, telefono = ?, espacio = ?, fecha_hora_entrada = ?, fecha_hora_salida = ?, email = ? WHERE id_reserva = ?");
        $stmt->bind_param("ssssssi", $nombre, $telefono, $espacio, $fecha_entrada, $fecha_salida, $email, $id_reserva);
        if ($stmt->execute()) {
            $mensaje = "Reserva modificada exitosamente.";
        } else {
            $mensaje = "Error al modificar la reserva: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="narvar.css">
    <link rel="stylesheet" href="modificar_reserva.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a href="admin.php" class="navbar-brand d-flex align-items-center">
                <img src="Imagenes/Logo-removebg-preview.png" width="10" alt="Logo">
                <span class="ms-2">ADMINISTRACÓN-RESERVAS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
        </div>
    </nav>
</header>

<div class="container mt-5">
    <!-- Mostrar mensaje de exito o error -->
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <?php if ($reserva): ?>
        <!-- Formulario de modificacion de reserva -->
        <form method="POST">
            <div class="row g-3">
                <div class="col-lg-12 d-flex flex-column justify-content-center align-items-center">
                    <img src="icon.html/Logo-removebg-preview.png" alt="Logo" style="width: 5rem;">
                    <h1 style="font-family:'Times New Roman', Times, serif;" class="sesion">Modificar Reserva</h1>
                </div>
            
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nombre" style="font-family:'Times New Roman', Times, serif;" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $reserva['nombre']; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telefono" style="font-family:'Times New Roman', Times, serif;" class="form-label">Telefono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $reserva['telefono']; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="espacio" style="font-family:'Times New Roman', Times, serif;" class="form-label">Espacio</label>
                        <input type="text" class="form-control" id="espacio" name="espacio" value="<?php echo $reserva['espacio']; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_entrada" style="font-family:'Times New Roman', Times, serif;" class="form-label">Fecha y Hora de Entrada</label>
                        <input type="datetime-local" class="form-control" id="fecha_entrada" name="fecha_entrada" value="<?php echo date('Y-m-d\TH:i', strtotime($reserva['fecha_hora_entrada'])); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_salida" style="font-family:'Times New Roman', Times, serif;" class="form-label">Fecha y Hora de Salida</label>
                        <input type="datetime-local" class="form-control" id="fecha_salida" name="fecha_salida" value="<?php echo date('Y-m-d\TH:i', strtotime($reserva['fecha_hora_salida'])); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electronico</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $reserva['email']; ?>" required>
                    </div>
                </div>
            </div>
            <button type="submit" name="submit" style="font-family:'Times New Roman', Times, serif;" class="btn btn-primary w-100">Modificar Reserva</button>
        </form>
    <?php else: ?>
        <p class="text-center">No se encontro la reserva o no tiene permiso para modificarla.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
