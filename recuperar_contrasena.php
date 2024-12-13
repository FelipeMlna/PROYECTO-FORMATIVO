<?php
session_start();
require 'vendor/autoload.php'; // Incluye PHPMailer automaticamente si usas Composer
include("conexion.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar si se envio el formulario
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Buscar el correo en la base de datos
    $stmt = $conn->prepare("SELECT * FROM registros WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Generar un token unico para el enlace de recuperacion
        $token = bin2hex(random_bytes(50));
        $resetLink = "http://localhost/PARKING-TECH/reset_password.php?token=" . $token;

        // Guardar el token en la base de datos
        $stmt = $conn->prepare("UPDATE registros SET token = ? WHERE Email = ?");
        $stmt->bind_param("ss", $token, $email);

        if ($stmt->execute()) {
            // Configurar PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Configuracion del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
                $mail->SMTPAuth = true;
                $mail->Username = 'sebaastnm@gmail.com'; // Tu direccion de correo
                $mail->Password = 'lmhc tubl wtln frdx'; // Usa la contrasena de aplicacion aqui
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Configuracion del correo
                $mail->setFrom('tu_correo@gmail.com', 'Parking Tech'); // Remitente
                $mail->addAddress($email);                            // Destinatario

                $mail->isHTML(true);
                $mail->Subject = 'Recuperacion de contrasena - Parking Tech';
                $mail->Body = "
                    <h3>Hola {$usuario['nombre']},</h3>
                    <p>Hemos recibido una solicitud para restablecer tu contrasena.</p>
                    <p>Haz clic en el siguiente enlace para cambiar tu contrasena:</p>
                    <a href='$resetLink'>$resetLink</a>
                    <p>Si no solicitaste este cambio, ignora este correo.</p>
                ";

                $mail->send();
                echo "El correo de recuperacion ha sido enviado. Revisa tu bandeja de entrada.";
            } catch (Exception $e) {
                echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error al generar el enlace de recuperacion.";
        }
    } else {
        echo "El correo electronico no esta registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="recuperar_contrasena.css">
    <link rel="stylesheet" href="narvar.css">
</head>

<body>
<header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container-fluid">
                <a href="login.php" class="navbar-brand d-flex align-items-center">
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
    <h2 class="letra">Recuperar Contraseña</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="email" style="font-family:'Times New Roman', Times, serif;" class="form-label"></label>
            <input type="email" style="font-family:'Times New Roman', Times, serif;" class="form-control" id="email" name="email" placeholder="Ingresa tu correo" required>
        </div>
        <button type="submit" class="btn btn-primary" style="font-family:'Times New Roman', Times, serif;">Enviar</button>
    </form>
    <?php if (isset($mensaje)): ?>
        <div class="alert alert-info mt-3"><?= $mensaje; ?></div>
    <?php endif; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
