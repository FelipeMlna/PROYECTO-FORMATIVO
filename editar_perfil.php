<?php
include("conexion.php"); // Asegurate de que este archivo tenga la conexion a la base de datos

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos enviados por el formulario
    $nombre = trim($_POST['name']);
    $email = trim($_POST['email']);
    $placa = trim($_POST['placa']);
    $user_email_session = $_SESSION['email']; // Suponiendo que el correo se guarda en la sesion

    // Validar datos
    if (empty($nombre) || empty($email) || empty($placa)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit();
    }

    // Preparar la consulta SQL para actualizar los datos
    $stmt = $conn->prepare("UPDATE registros SET nombre = ?, email = ?, placa = ? WHERE email = ?");
    $stmt->bind_param("ssss", $nombre, $email, $placa, $user_email_session);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['email'] = $email; // Si el correo cambia, actualizar la sesion
        // Enviar respuesta exitosa despues de la actualizacion
        echo json_encode(['success' => true, 'message' => '¡Cambio exitoso!']);
    } else {
        // Enviar mensaje de error si la actualizacion falla
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la informacion']);
    }

    // Cerrar la declaracion
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="editar_perfil.css">
    <link rel="stylesheet" href="narvar.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="login.php">
                    <img src="Imagenes/Logo-removebg-preview.png" width="20" alt="Logo">
                    <span class="ms-2">PARKING-TECH</span>
                </a>
            </div>
        </nav>
    </header>

    <!-- Boton para abrir el modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
        Editar Perfil
    </button>

    <!-- Modal de edicion de perfil -->
<!-- Modal de edicion de perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-profile-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($Nombre); ?>" required>

                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email); ?>" required>

                    <label for="placa" class="form-label">Placa</label>
                    <input type="text" id="placa" name="placa" class="form-control" value="<?= htmlspecialchars($Placa); ?>" required>

                    <label for="rol" class="form-label">Rol</label>
                    <input type="text" id="rol" name="rol" class="form-control" value="<?= htmlspecialchars($rol); ?>" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para validar el formulario -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("edit-profile-form");
        
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

            // Obtener los valores de los campos
            const name = document.getElementById("name").value;
            const email = document.getElementById("email").value;
            const placa = document.getElementById("placa").value;

            // Validacion de nombre: solo letras
            const nameRegex = /^[a-zA-Z\s]+$/;
            if (!nameRegex.test(name)) {
                alert("El nombre solo puede contener letras.");
                return; // Detener el envio del formulario
            }

            // Validacion de placa: 3 letras mayusculas, un guion y 3 numeros
            const placaRegex = /^[A-Z]{3}-\d{3}$/;
            if (!placaRegex.test(placa)) {
                alert("La placa debe tener el formato: 3 letras mayusculas seguidas de un guion y 3 numeros (por ejemplo, ABC-123).");
                return; // Detener el envio del formulario
            }

            // Validacion de correo: debe contener al menos un "@"
            if (!email.includes("@")) {
                alert("El correo debe contener un '@'.");
                return; // Detener el envio del formulario
            }

            // Si todo esta bien, enviamos el formulario
            const formData = new FormData(form);
            fetch("", { // Enviar al mismo archivo PHP
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("¡Cambio exitoso!");
                    location.reload(); // Recargar la pagina si todo fue bien
                } else {
                    alert(data.message || "Error al guardar los cambios");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Ocurrio un error inesperado.");
            });
        });
    });
</script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("edit-profile-form");

            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

                const formData = new FormData(form);

                fetch(window.location.href, {
                    method: "POST",
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error en la red");
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("¡Cambio exitoso!");
                        location.reload(); // Recargar la pagina si todo fue bien
                    } else {
                        alert(data.message || "Error al guardar los cambios");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Ocurrio un error inesperado.");
                });
            });
        });

        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
