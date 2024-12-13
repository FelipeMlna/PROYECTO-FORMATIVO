<?php
// Configuracion para la conexion a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parking-tech1";

// Crear conexion
$conn = new mysqli($servername, $username, $password, $dbname);
session_start();  // Iniciar la sesión
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
        if (!preg_match("/^[a-zA-ZaeiouAEIOU-nN\s]+$/", $nombre)) {
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

           
// Verificar si el usuario está logueado
if (!isset($_SESSION['email']) || !isset($_SESSION['nombre'])) {
    header("Location: login.php"); // Redirigir si no está logueado
    exit();
}
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="Reservas.css">
  <link rel="stylesheet" href="narvar.css">
    <meta charset="UTF-8">
    <title>RESERVAS</title>
</head>
<body>

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
                  <img class="iconos-navbar" src="icon.html/nota-unscreen.gif" alt="" width="30" height="30">
                  <a href="Historial.php" class="nav-link ms-1">Historial</a>
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
</head>

<P></P>
<div class="container mt-5">
<style>
  .mb-3{
    color: aliceblue;
    font-weight: bold;
  }
</style>

<form id="reservaForm" method="POST" action="Reservas.php">
  <div class="mb-3" style="font-family:'Times New Roman', Times, serif;">
    <label for="nombre" class="form-label">Nombre:</label>
    <input type="text" id="nombre" name="nombre" class="form-control" 
           value="<?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : ''; ?>" 
           readonly required>
  </div>

  <div class="mb-3" style="font-family:'Times New Roman', Times, serif;">
    <label for="email" class="form-label">Email:</label>
    <input type="email" id="email" name="email" class="form-control" 
           value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
           readonly required>
  </div>

  <div class="mb-3" style="font-family:'Times New Roman', Times, serif;">
    <label for="telefono" class="form-label">Teléfono:</label>
    <input type="tel" id="telefono" name="telefono" class="form-control" required>
  </div>

  <div class="mb-3" style="font-family:'Times New Roman', Times, serif;">
    <label for="entrada" class="form-label">Fecha y Hora de Entrada:</label>
    <input type="datetime-local" id="entrada" name="fecha_hora_entrada" class="form-control" required>
  </div>

  <div class="mb-3" style="font-family:'Times New Roman', Times, serif;">
    <label for="salida" class="form-label">Fecha y Hora de Salida:</label>
    <input type="datetime-local" id="salida" name="fecha_hora_salida" class="form-control" required>
  </div>

  <div class="mb-3" style="font-family:'Times New Roman', Times, serif;">
    <label for="espacio" class="form-label">Espacio:</label>
    <input type="text" id="espacio" name="espacio" class="form-control" readonly required>
  </div>
</form>
  
<!-- Disponibilidad -->

      <div class="cuadro">
        <div class="form-container p-4 bg-white shadow rounded">
              <h1 class="mb-4">Disponibilidad</h1>
              <form action="Reservas.php" method="post">
                <div class="parking-space" id="A1"  onclick="selectParking('A-1')">
                  <a><label for="email" class="form-label">A-1</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton"></a>
                </div>

                <div class="parking-space" id="A2"   onclick="selectParking('A-2')">
                  <a><label for="email" class="form-label">A-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton2"></a>
                </div>

                <div class="parking-space" id="A3"   onclick="selectParking('A-3')">
                  <a><label for="email" class="form-label">A-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton3"></a>
                </div>

                <div class="parking-space" id="B1"   onclick="selectParking('B-1')">
                  <a><label for="email" class="form-label">B-1</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton4"></a>
                </div>

                <div class="parking-space" id="B2"   onclick="selectParking('B-2')">
                  <a><label for="email" class="form-label">B-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton5"></a>
                </div>

                <div class="parking-space" id="B3"   onclick="selectParking('B-3')">
                  <a><label for="email" class="form-label">B-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton6"></a>
                </div>

                <div class="parking-space" id="C1"   onclick="selectParking('C-1')">
                  <a><label for="email" class="form-label">C-1</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton7"></a>
                </div>

                <div class="parking-space" id="C2"   onclick="selectParking('C-2')">
                  <a><label for="email" class="form-label">C-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton8"></a>
                </div>

                <div class="parking-space" id="C3"   onclick="selectParking('C-3')">
                  <a><label for="email" class="form-label">C-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton9"></a>
                </div>

                <div class="parking-space" id="D1"   onclick="selectParking('D-1')">
                  <a><label for="email" class="form-label">D-1</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton10"></a>
                </div>

                <div class="parking-space" id="D2"   onclick="selectParking('D-2')">
                  <a><label for="email" class="form-label">D-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton11"></a>
                </div>

                <div class="parking-space" id="D3"   onclick="selectParking('D-3')">
                  <a><label for="email" class="form-label">D-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton12"></a>
                </div>

                <div class="parking-space" id="E1"   onclick="selectParking('E-1')">
                  <a><label for="email" class="form-label">E-1</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton13"></a>
                </div>

                <div class="parking-space" id="E2"   onclick="selectParking('E-2')">
                  <a><label for="email" class="form-label">E-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton14"></a>
                </div>

                <div class="parking-space" id="E3"   onclick="selectParking('E-3')">
                  <a><label for="email" class="form-label">E-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton15"></a>
                </div>

                <div class="parking-space" id="F1"   onclick="selectParking('F-1')">
                  <a><label for="email" class="form-label">F-1</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton16"></a>
                </div>

                <div class="parking-space" id="F2"   onclick="selectParking('F-2')">
                  <a><label for="email" class="form-label">F-2</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton17"></a>
                </div>

                <div class="parking-space" id="F3"   onclick="selectParking('F-3')">
                  <a><label for="email" class="form-label">F-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton18"></a>
                </div>

                <div class="parking-space-block1" id="G1"   onclick="selectParking('G-1')">
                  <a><label for="email" class="form-label">G-1</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton19"></a>
                </div>

                <div class="parking-space-block1" id="G2"   onclick="selectParking('G-2')">
                  <a><label for="email" class="form-label">G-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton20"></a>
                </div>

                <div class="parking-space-block1" id="G3"   onclick="selectParking('G-3')">
                  <a><label for="email" class="form-label">G-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton21"></a>
                </div>

                <div class="parking-space-block1" id="H1"   onclick="selectParking('H-1')">
                  <a><label for="email" class="form-label">H-1</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton22"></a>
                </div>

                <div class="parking-space-block1" id="H2"   onclick="selectParking('H-2')">
                  <a><label for="email" class="form-label">H-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton23"></a>
                </div>

                <div class="parking-space-block1" id="H3"   onclick="selectParking('H-3')">
                  <a><label for="email" class="form-label">H-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton24"></a>
                </div>

                <div class="parking-space-block1" id="I1"   onclick="selectParking('I-1')">
                  <a><label for="email" class="form-label">I-1</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton25"></a>
                </div>

                <div class="parking-space-block1" id="I2"   onclick="selectParking('I-2')">
                  <a><label for="email" class="form-label">I-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton26"></a>
                </div>

                <div class="parking-space-block1" id="I3"   onclick="selectParking('I-3')">
                  <a><label for="email" class="form-label">I-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton27"></a>
                </div>

                <div class="parking-space-block1" id="J1"   onclick="selectParking('J-1')">
                  <a><label for="email" class="form-label">J-1</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton28"></a>
                </div>

                <div class="parking-space-block1" id="J2"   onclick="selectParking('J-1')">
                  <a><label for="email" class="form-label">J-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton29"></a>
                </div>

                <div class="parking-space-block1" id="J3"   onclick="selectParking('J-3')">
                  <a><label for="email" class="form-label">J-3</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton30"></a>
                </div>

                <div class="parking-space-block1" id="K1"   onclick="selectParking('K-1')">
                  <a><label for="email" class="form-label">K-1</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton31"></a>

                </div>  <div class="parking-space-block1" id="K2"   onclick="selectParking('K-2')">
                  <a><label for="email" class="form-label">K-2</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton32"></a>
                </div>

                <div class="parking-space-block1" id="K3"   onclick="selectParking('K-3')">
                  <a><label for="email" class="form-label">K-3</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton33"></a>

                </div>  <div class="parking-space-block1" id="L1"   onclick="selectParking('L-1')">
                  <a><label for="email" class="form-label">L-1</label>
                    <img src="Imagenes/CARRO.jpg" alt="Descripcion de la imagen" class="imagen-boton34"></a>
                </div>

                <div class="parking-space-block1" id="L2"   onclick="selectParking('L-2')">
                  <a><label for="email" class="form-label">L-2</label>
                    <img src="Imagenes/MOTO.jpg" alt="Descripcion de la imagen" class="imagen-boton35"></a>
                </div>


    <!-- Contenedor para mostrar el mensaje -->
<p id="status-message"></p>
              
              </form>
                      </div>
                  </div>
                </div>
              </div>
              </div>
              </div>
              
              
              <!-- Contenedor para los estados de los espacios -->
              <div class="status-container">
                <div class="status-item" style="font-family:'Times New Roman', Times, serif;">
                  <label for="available"><strong>Espacios disponibles</strong></label>
                  <div class="status-box" id="available" style="background-color: green;"></div>
                </div>
              
                <div class="status-item" style="font-family:'Times New Roman', Times, serif;">
                  <label for="in-person"><strong>Espacios presenciales</strong></label>
                  <div class="status-box" id="in-person" style="background-color: rgb(8, 248, 236);"></div>
                </div>
              </div>
              
              
              <!-- Boton de reserva -->
              <div class="boton">
                <center>
                  <button type="submit" form="reservaForm">Reservar</button>
                </center>
              </div>
              
              <script>
                function selectParking(spaceId) {
                  // Actualiza el campo de texto del espacio con el valor del espacio seleccionado
                  const espacioInput = document.getElementById('espacio');
                  espacioInput.value = spaceId;
              
                  // Muestra un mensaje para confirmar la seleccion
                  const statusMessage = document.getElementById('status-message');
                }
              </script>
              
              <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
              <!-- Cards -->
              <section id="oferta">
              
              <p class="edit">
                <h1 class="selecciona">¡SELECCIONA TU OFERTA!</h1>
              </p>
              
              <div class="container2" style="margin-bottom: 50px;">
                <div class="row d-flex justify-content-center align-items-center">
                  <div class="col-lg-4 d-flex justify-content-center align-items-center">
                    <div class="card" style="width: 18rem;">
                      <div class="card d-flex justify-content-center align-items-center" id="descuento-80" onclick="redirigir('80')">
                        <img class="card-img-top" src="Imagenes/800.png" width="100%" alt="Descuento 80%">     
                        <div style="font-family:'Times New Roman', Times, serif;" class="card-title">Descuento 80%</div>
                        <div style="font-family:'Times New Roman', Times, serif;" class="card-description">Obtén un descuento del 80% en tu factura.</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 d-flex justify-content-center align-items-center">
                    <div class="card" style="width: 18rem;">
                      <div class="card d-flex justify-content-center align-items-center" id="descuento-80" onclick="redirigir('15000')">
                        <img class="card-img-top" src="Imagenes/oferta.png" width="100%" alt="Descuento $15,000">
                        <div style="font-family:'Times New Roman', Times, serif;" class="card-title">Descuento $15,000</div>
                        <div style="font-family:'Times New Roman', Times, serif;" class="card-description">Aplica un descuento fijo de $15,000 en tu factura.</div>
                      </div>
                    </div>
                  </div>
                </div>  
              </div>
              
              </section>
              <script>
                function redirigir(tipoDescuento) {
                  // Redirigir con parametro del descuento seleccionado
                  window.location.href = `factura.html?descuento=${tipoDescuento}`;
                }
              
                // Esperar a que el documento cargue completamente antes de anadir el evento
                document.addEventListener('DOMContentLoaded', function () {
                    // Agregar evento al formulario de reserva
                    document.getElementById('reservaForm').addEventListener('submit', function (e) {
                        e.preventDefault(); // Prevenir el envio estandar del formulario
              
                        // Crear el FormData a partir del formulario
                        const form = e.target;
                        const formData = new FormData(form);
              
                        // Realizar la solicitud al servidor para verificar disponibilidad
                        fetch('/php/verificar_disponibilidad.php', { // Ruta al archivo PHP
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())  // Procesar respuesta JSON
                        .then(result => {
                            if (result.success) {
                                // Si el espacio esta disponible, proceder con el envio del formulario
                                alert(result.message); // Mostrar mensaje de exito
                                form.submit(); // Enviar el formulario
                            } else {
                                alert(result.message); // Mostrar mensaje de error si el espacio no esta disponible
                            }
                        })
                        .catch(error => {
                            console.error('Error al verificar la disponibilidad:', error);
                            alert('Ocurrio un error al procesar la solicitud. Intenta nuevamente.');
                        });
                    });
                });
              </script>
              <script src="/mapa.js"></script>
              <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
              
              </body>
              
              </html>
              