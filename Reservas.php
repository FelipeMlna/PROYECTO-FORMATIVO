<?php
// Configuracion para la conexion a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parking-tech1";

// Crear conexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexion
if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}

$mensaje = ""; // Variable para guardar el mensaje

// Validar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $telefono = filter_var($_POST['telefono'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $fecha_hora_entrada = $_POST['fecha_hora_entrada'] ?? '';
    $fecha_hora_salida = $_POST['fecha_hora_salida'] ?? '';
    $espacio = filter_var($_POST['espacio'] ?? '', FILTER_SANITIZE_STRING);

    // Validar que los campos no esten vacios
    if (empty($nombre) || empty($email) || empty($telefono) || empty($fecha_hora_entrada) || empty($fecha_hora_salida) || empty($espacio)) {
        $mensaje = "Por favor, completa todos los campos.";
    } else {
        // Validar Nombre (solo letras)
        if (!preg_match("/^[a-zA-ZaeiouAEIOUáéíóúÁÉÍÓÚnN\s]+$/", $nombre)) {
            $mensaje = "El nombre debe contener solo letras.";
        }
        // Validar Correo Electronico
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "El correo electronico debe ser valido.";
        }
        // Validar Telefono (solo numeros, minimo 10 caracteres)
        elseif (!preg_match("/^\d{10,}$/", $telefono)) {
            $mensaje = "El telefono debe contener al menos 10 digitos.";
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
                $mensaje = "El espacio ya esta reservado en el horario seleccionado.";
            } else {
                // Preparar la consulta para insertar la reserva
                $query_insert = "INSERT INTO reservas (nombre, email, telefono, fecha_hora_entrada, fecha_hora_salida, espacio) 
                                 VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query_insert);

                if ($stmt) {
                    $stmt->bind_param("ssssss", $nombre, $email, $telefono, $fecha_hora_entrada, $fecha_hora_salida, $espacio);

                    if ($stmt->execute()) {
                        $mensaje = "Reserva registrada exitosamente.";
                    } else {
                        $mensaje = "Error al registrar la reserva.";
                    }
                    $stmt->close();
                } else {
                    $mensaje = "Error en la preparacion de la consulta.";
                }
            }
            $stmt_check->close();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="factura.css">
    <link rel="stylesheet" href="narvar.css">

    <!-- Metadatos -->
    <meta charset="UTF-8">
    <title>FACTURA</title>
</head>

<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container-fluid">
                <a href="login.php" class="navbar-brand d-flex align-items-center">
                    <img src="Imagenes/Logo-removebg-preview.png" width="50" alt="Logo">
                    <span class="ms-2">PARKING-TECH</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item d-flex align-items-center">
                            <img class="iconos-navbar" src="icon.html/bolsa-de-dinero-unscreen.gif" alt="" width="30" height="30">
                            <a href="factura.html" class="nav-link ms-1">Pagos</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <img class="iconos-navbar" src="icon.html/apoyo-unscreen.gif" alt="" width="30" height="30">
                            <a href="Ayuda.html" class="nav-link ms-1">Contáctanos</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenido Principal -->
    <div class="container">
        <img src="icon.html/Logo-removebg-preview.png" alt="" width="40">
        <h1 class="Generar-fac">Generar Factura</h1>
        <form id="form-parqueo">
            <div class="mb-3">
                <label for="placa" class="form-label">Placa del Vehículo:</label>
                <input type="text" class="form-control" id="placa" required>
            </div>
            <div class="mb-3">
                <label for="vehiculo" class="form-label">Vehículo:</label>
                <select id="vehiculo" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    <option value="carro">Carro</option>
                    <option value="moto">Moto</option>
                </select>
            </div>
            <div class="mb-3">
    <label for="entrada" class="form-label">Fecha y Hora de Entrada:</label>
    <input 
        type="datetime-local" 
        class="form-control" 
        id="entrada" 
        name="fecha_hora_entrada" 
        value="<?= htmlspecialchars($_POST['fecha_hora_entrada'] ?? '') ?>" 
        required>
</div>
<div class="mb-3">
    <label for="salida" class="form-label">Fecha y Hora de Salida:</label>
    <input 
        type="datetime-local" 
        class="form-control" 
        id="salida" 
        name="fecha_hora_salida" 
        value="<?= htmlspecialchars($_POST['fecha_hora_salida'] ?? '') ?>" 
        required>
</div>
            <div class="mb-3">
                <label for="metodo" class="form-label">Metodo de Pago:</label>
                <select id="metodo" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    <option value="nequi">Nequi</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="bancolombia">Bancolombia</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Calcular Factura</button>
        </form>

        <!-- Seccion de Factura -->
        <div class="factura mt-5" id="factura" style="display: none;">
            <h2>Factura de Pago</h2>
            <img src="icon.html/Logo-removebg-preview.png" alt="" width="40">
            <p><strong>ID de Pago:</strong> <span id="factura-id-pago"></span></p>
            <p><strong>Placa:</strong> <span id="factura-placa"></span></p>
            <p><strong>Vehiculo:</strong> <span id="factura-vehiculo"></span></p>
            <p><strong>Fecha y Hora de Entrada:</strong> <span id="factura-entrada"></span></p>
            <p><strong>Fecha y Hora de Salida:</strong> <span id="factura-salida"></span></p>
            <p><strong>Tiempo Total:</strong> <span id="factura-tiempo"></span> horas</p>
            <p><strong>Valor a Pagar:</strong> $<span id="factura-valor"></span></p>
            <p><strong>Descuento Aplicado:</strong> <span id="factura-descuento"></span></p>
            <p><strong>Valor Final a Pagar:</strong> $<span id="factura-valor-final"></span></p>
            <p><strong>Metodo de Pago:</strong> <span id="factura-metodo"></span></p>
            <button id="btn-descargar" class="btn btn-success">Descargar Factura</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="factura.js"></script>
</body>

</html>
