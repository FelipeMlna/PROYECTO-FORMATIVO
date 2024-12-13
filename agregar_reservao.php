<?php
session_start();
include("conexion.php"); // Incluir la conexion a la base de datos

// Verificar si el usuario esta autenticado y es administrador
if (!isset($_SESSION['email']) || $_SESSION['rol'] !== 'Operador') {
    header("Location: IniciarSesion.html");
    exit();
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $fecha_hora_entrada = trim($_POST['fecha_hora_entrada']);
    $fecha_hora_salida = trim($_POST['fecha_hora_salida']);
    $espacio = strtoupper(trim($_POST['espacio']));  // Convertir el espacio a mayusculas
    $email = trim($_POST['email']);

    // Validar campos vacios
    if (empty($nombre) || empty($telefono) || empty($fecha_hora_entrada) || empty($fecha_hora_salida) || empty($espacio) || empty($email)) {
        $mensaje = "Todos los campos son obligatorios.";
    } 
    // Validar formato del nombre
    elseif (!preg_match("/^[A-Za-z\s]+$/", $nombre)) {
        $mensaje = "El nombre solo puede contener letras y espacios.";
    } 
    // Validar formato del telefono
    elseif (!preg_match("/^\d{10}$/", $telefono)) {
        $mensaje = "El numero de telefono debe tener exactamente 10 digitos.";
    } 
    // Validar fechas
    elseif (strtotime($fecha_hora_salida) <= strtotime($fecha_hora_entrada)) {
        $mensaje = "La fecha y hora de salida debe ser posterior a la de entrada.";
    } 
    // Validar formato del correo
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo electronico no es valido.";
    } 
    // Validar formato del espacio (ejemplo: C-1)
    elseif (!preg_match("/^[A-Z]{1}-[0-9]{1,2}$/", $espacio)) {
        $mensaje = "El espacio debe tener el formato 'Letra-Mayuscula-Numero', por ejemplo: C-1.";
    } else {
        // Verificar si el espacio ya esta reservado en el rango de fechas
        $stmt = $conn->prepare("SELECT * FROM reservas WHERE espacio = ? AND (
            (fecha_hora_entrada BETWEEN ? AND ?) OR 
            (fecha_hora_salida BETWEEN ? AND ?)
        )");

        // Revisamos cuantos parametros estamos pasando en bind_param() para asegurarnos que coincidan con los signos de interrogacion en la consulta
        if (!$stmt) {
            die("Error en la preparacion de la consulta: " . $conn->error);
        }

        $stmt->bind_param("sssss", $espacio, $fecha_hora_entrada, $fecha_hora_salida, $fecha_hora_entrada, $fecha_hora_salida);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "El espacio '$espacio' ya esta reservado en el rango de fechas seleccionado.";
        } else {
            // Insertar en la base de datos si todas las validaciones pasan
            $stmt = $conn->prepare("INSERT INTO reservas (nombre, telefono, fecha_hora_entrada, fecha_hora_salida, espacio, email) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Error en la preparacion de la consulta: " . $conn->error);
            }

            $stmt->bind_param("ssssss", $nombre, $telefono, $fecha_hora_entrada, $fecha_hora_salida, $espacio, $email);
            if ($stmt->execute()) {
                $mensaje = "Reserva agregada exitosamente.";
            } else {
                $mensaje = "Error al agregar la reserva: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="narvar.css">
    <link rel="stylesheet" href="agregar_reserva.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a href="operador.php" class="navbar-brand d-flex align-items-center">
                <img src="Imagenes/Logo-removebg-preview.png" width="10" alt="Logo">
                <span class="ms-2">ADMINISTRACION-RESERVAS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
</header>

<div class="container mt-4">    
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST" action="" id="reservaForm">
        <div class="mb-3">
            <div class="col-lg-12 d-flex flex-column justify-content-center align-items-center">
                <img src="icon.html/Logo-removebg-preview.png" alt="Logo" style="width: 5rem;">
                <h1 class="sesion">Agregar Reservas</h1>
            </div>
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= isset($nombre) ? $nombre : ''; ?>" required minlength="3" maxlength="50" pattern="[A-Za-z\s]+" title="Solo se permiten letras y espacios">
        </div>
        <div class="mb-3">
            <label for="telefono" style="font-family:'Times New Roman', Times, serif;" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= isset($telefono) ? $telefono : ''; ?>" required pattern="\d{10}" title="El numero de telefono debe tener 10 digitos">
        </div>
        <div class="mb-3">
            <label for="fecha_hora_entrada" style="font-family:'Times New Roman', Times, serif;" class="form-label">Fecha y Hora de Entrada</label>
            <input type="datetime-local" class="form-control" id="fecha_hora_entrada" name="fecha_hora_entrada" value="<?= isset($fecha_hora_entrada) ? $fecha_hora_entrada : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="fecha_hora_salida" style="font-family:'Times New Roman', Times, serif;" class="form-label">Fecha y Hora de Salida</label>
            <input type="datetime-local" class="form-control" id="fecha_hora_salida" name="fecha_hora_salida" value="<?= isset($fecha_hora_salida) ? $fecha_hora_salida : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="espacio" style="font-family:'Times New Roman', Times, serif;" class="form-label">Espácio</label>
            <input type="text" class="form-control" id="espacio" name="espacio" value="<?= isset($espacio) ? $espacio : ''; ?>" required minlength="3" maxlength="5" pattern="[A-Z]{1}-[0-9]{1,2}" title="Formato del espacio: Letra-Mayuscula-Numero, ejemplo: C-1">
        </div>
        <div class="mb-3">
            <label for="email" style="font-family:'Times New Roman', Times, serif;" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= isset($email) ? $email : ''; ?>" required>
        </div>
        <button type="submit" style="font-family:'Times New Roman', Times, serif;" class="btn btn-primary">Agregar Reserva</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="agregar_reservao.js"></script>
</body>
</html>
