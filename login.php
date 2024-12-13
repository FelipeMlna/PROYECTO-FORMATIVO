<?php
session_start();
include("conexion.php"); // Incluye la conexion a la base de datos

// Inicializar variables
$Nombre = '';
$Placa = '';
$rol = '';

// Verificar si el usuario ha iniciado sesion
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Consultar datos del usuario en la base de datos
    $stmt = $conn->prepare("SELECT nombre, placa, roles FROM registros WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $Nombre = $usuario['nombre'] ?? '';
        $Placa = $usuario['placa'] ?? '';
        $rol = $usuario['roles'] ?? '';
    } else {
        // Si no encuentra el usuario, redirigir al inicio de sesion
        header("Location: IniciarSesion.html");
        exit();
    }
    $stmt->close();
} else {
    // Si no esta autenticado, redirigir al inicio de sesion
    header("Location: IniciarSesion.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PARKING-TECH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="narvar.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Logo -->
           <a class="navbar-brand d-flex align-items-center">
                    <img src="Imagenes/Logo-removebg-preview.png" 
                         alt="Logo" 
                         style="width: 70px; height: 70px; margin-right: 10px;">
                         <span class="ms-2">PARKING-TECH</span>
                </a>

            <!-- Botón para desplegar menú en dispositivos pequeños -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenido del navbar -->
            <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Contactanos -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item d-flex align-items-center">
                    <img class="iconos-navbar" src="icon.html/apoyo-unscreen.gif" alt="" width="30" height="30">
                    <a href="Ayuda.html" class="nav-link ms-1">Contáctanos</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <img class="iconos-navbar" src="icon.html/nota-unscreen.gif" alt="" width="30" height="30">
                    <a href="Historial.php" class="nav-link ms-1">Historial</a>
                </li>
            </ul>
            </ul>

<!-- Informacion del usuario -->
<ul class="navbar-nav ms-auto">
    <li class="nav-item dropdown" style="font-family:'Times New Roman', Times, serif;">
        <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Información del Perfil
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
            <li>
                <span class="dropdown-item" style="font-family:'Times New Roman', Times, serif;">
                    <strong>Nombre:</strong> <?= htmlspecialchars($Nombre); ?>
                </span>
            </li>
            <li>
                <span class="dropdown-item" style="font-family:'Times New Roman', Times, serif;">
                    <strong>Placa:</strong> <?= htmlspecialchars($Placa); ?>
                </span>
            </li>
            <li>
                <span class="dropdown-item" style="font-family:'Times New Roman', Times, serif;">
                    <strong>Rol:</strong> <?= htmlspecialchars($rol); ?>
                </span>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
            
            <!-- Boton para abrir el modal de edicion -->
            <li>
                <button class="editar" data-bs-toggle="modal" data-bs-target="#editProfileModal">Editar perfil</button>
            </li>
            <li>
                <a class="cerrar-sesion" href="logout.php">Cerrar sesión</a>
            </li>
        </ul>
    </li>
</ul>
<!-- Modal de edicion de perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-profile-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel" style="font-family:'Times New Roman', Times, serif;">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="name" class="form-label" style="font-family:'Times New Roman', Times, serif;">Nombre</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($Nombre); ?>" required>

                    <label for="email" class="form-label" style="font-family:'Times New Roman', Times, serif;">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email); ?>" required>

                    <label for="placa" class="form-label" style="font-family:'Times New Roman', Times, serif;">Placa</label>
                    <input type="text" id="placa" name="placa" class="form-control" value="<?= htmlspecialchars($Placa); ?>" required>

                    <label for="rol" class="form-label" style="font-family:'Times New Roman', Times, serif;">Rol</label>
                    <input type="text" id="rol" name="rol" class="form-control" value="<?= htmlspecialchars($rol); ?>" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-family:'Times New Roman', Times, serif;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="font-family:'Times New Roman', Times, serif;">Guardar Cambios</button>
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
            return; // Detener el envío del formulario
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
        fetch("editar_perfil.php", { // Enviar los datos al archivo PHP que los procesara
            method: "POST",
            body: formData
        })
        .then(response => response.json())  // Parsear la respuesta a JSON
        .then(data => {
            // Verificar si la respuesta es valida y contiene los datos esperados
            if (data && data.success) {
                alert(data.message || "¡Cambio exitoso!"); // Mostrar el mensaje de exito
                location.reload(); // Recargar la pagina si todo fue bien
            } else {
                alert(data.message || "Error al guardar los cambios"); // Mostrar el mensaje de error si ocurre algo
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("¡Cambio exitoso!"); // Mostrar un mensaje si ocurre un error en la comunicacion
        });
    });
});
</script>

</header>
<div class="container">
      <div class="row">
        <div class="col d-flex justify-content-center align-items-center">
          <button type="button" class="btn7">
            <a href="Reserva.php">RESERVAS</a>
          </button>
        </div>
      </div>
   </div>
</div>

<script>
    function actualizarFechaHora() {
        const fechaHoraElemento = document.getElementById("fecha-hora");
        const ahora = new Date();

        // Formatear fecha y hora
        const opcionesFecha = { year: 'numeric', month: 'long', day: 'numeric' };
        const fecha = ahora.toLocaleDateString('es-ES', opcionesFecha);
        const hora = ahora.toLocaleTimeString('es-ES');

        // Mostrar en el contenedor con estilo
        fechaHoraElemento.innerHTML = `
            <p style="font-family:'Times New Roman', Times, serif; font-weight: bold; font-size: 24px; margin: 0;">${fecha}</p>
            <p style="font-family:'Times New Roman', Times, serif; font-weight: bold; font-size: 24px; margin: 0;">${hora}</p>
        `;
    }

    // Actualizar cada segundo
    setInterval(actualizarFechaHora, 1000);

    // Llamada inicial
    actualizarFechaHora();
</script>

<center>
    <div id="fecha-hora"></div>
</center>



   <!-- Contenedor de la fecha y hora -->
   <span id="fecha-hora" class="fecha"></span>
                    
<p></p>

<!--Carrusel-->
<section>
  <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel" >
    <div class="carousel-inner">
      <div class="carousel-item active">

        <img width="200" height="600" autoplay loop muted
        src="Imagenes/carru1.png" class="d-block w-100" alt="...">
      </div>

      <div class="carousel-item">
        <img width="200" height="600" autoplay loop muted
        src="Imagenes/carru2.png" class="d-block w-100" alt="...">
      </div>

      <div class="carousel-item">
        <img width="200" height="600" autoplay loop muted
        src="Imagenes/carru3.png" class="d-block w-100" alt="...">
      </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  <div class="container d-flex justify-content-center">

    <button type="button" class="btn1">
      <a href="Reserva.php#oferta">¡OFERTAS!</a>
    </button>
    
  </div>

<p>
  <H1 class="nosotros">-¿Por qué Nosotros?-</H1>
</p>


<div class="container text-center">
  <div class="row">
    <div class="col">
      En Parking Tech, nos dedicamos a ofrecerte un servicio de estacionamiento seguro, confiable y conveniente, las 24 horas del dia, los 365 dias del ano. Entendemos lo importante que es tu vehiculo para ti, por eso te brindamos un espacio seguro y protegido donde podras dejarlo con total tranquilidad.
    </div>

    <div class="col">
      Ya que contamos con sistemas de seguridad de ultima generacion y vigilancia constante. Ademas, nuestra ubicacion en te ofrece facil acceso y excelentes conexiones.
      Comodidad y facilidad a tu alcance. Realiza tu reserva de manera rapida y sencilla a traves de nuestra pagina web. 
    </div>

    <div class="col">
      Y como miembro de nuestra comunidad, disfruta de ofertas exclusivas y descuentos especiales. Nuestro compromiso es brindarte una experiencia unica. Nuestro equipo esta siempre dispuesto a atender tus consultas y ofrecerte la mejor asistencia.
    </div>
  </div>
</div>

<p>
  <h1 class="visita">¡Visitanos y descubre por que somos tu mejor opción!</h1>
</p>

<h1 class="servi">-Servicios-</h1>
<div class="container text-center">
  <div class="row">
    <div class="col">
      "Olvidate de la preocupacion por encontrar estacionamiento. En Parking-Tech, te ofrecemos un servicio completo y personalizado. Reserva tu lugar de forma rapida y sencilla a traves de nuestra plataforma online, relajate sabiendo que tu vehiculo esta protegido por nuestro sistema de vigilancia las 24 horas del dia,
      los 365 dias del ano, y elige la forma de pago que mejor se adapte a ti: efectivo, tarjeta o aplicaciones moviles"
    </div>
  </div>
</div>


<P></P>

<div class="contenedor">
  <div class="caja">
    <h1>Horarios de Servicio</h1>
    <div class="dia text-center">
      <img src="icon.html/reloj-unscreen.gif" alt="" width="40">
      <h2>Lunes a Viernes</h2>
      <p class="abierto">Abierto de 6 am a 8 pm</p>
      <h2>Sábados, domingos y festivos</h2>
      <p class="abierto">Abierto de 8 am a 6 pm</p>
    </div>
  </div>
</div>

<p></p>


<div class="container-fluid bg-dark text-white">

  <div class="row">

<div class="col-xa-12 col-md-6 col-lg-3">
  <img src="Imagenes/Logo-removebg-preview.png" width="90" height="90" alt="">
</div>

<div class="col-xa-12 col-md-6 col-lg-3">
  <p class="h4">Redes Sociles</p>
  <div>
    <img src="icon.html/icons8-facebook-nuevo-16.png" alt="" width="30">
    <a href="#">Facebook</a>
  </div>
  <p></p>
  <div>
    <img src="icon.html/icons8-insta-16.png" alt="" width="30">
    <a href="#">Instagram</a>
  </div>
</div>

<div class="col-xa-12 col-md-6 col-lg-3">
  <p class="h4">Contactanos</p>
  <div>
    <img src="icon.html/ubicacion-unscreen.gif" alt="" width="40">
    <a href="https://www.google.com/maps/place/Cra.+8+%2357-109+a+57+1,+Ibagu%C3%A9,+Tolima/@4.4394968,-75.2064745,17z/data=!3m1!4b1!4m5!3m4!1s0x8e38c4e303aeeb8b:0x19f3a75d3839c9c4!8m2!3d4.4394915!4d-75.2038996?entry=ttu&g_ep=EgoyMDI0MDkwMi4xIKXMDSoASAFQAw%3D%3D">Cra. 8 #57-109 a 57 1</a>
  </div>

  <div>
    <img src="icon.html/perfil-unscreen.gif" alt="" width="40">
    <a href="parkingtech7@gmail.com">parkingtech7@gmail.com</a>
  </div>
  
   </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="barradatos.js"></script>

</body>
</html>

<style>
      /* Navbar */
/* Ajustes adicionales para una mejor responsividad */
@media (max-width: 992px) {
  /* Para pantallas medianas y pequeñas */
  .navbar-nav .nav-link {
    padding: 0.5rem 1rem; /* Ajusta el padding de los enlaces */
  }
  .iconos-navbar {
    width: 25px; /* Reduce el tamaño de los iconos */
  }
}

@media (max-width: 768px) {
  /* Para pantallas pequeñas (móviles) */
  .navbar-brand {
    font-size: 20px; /* Reduce el tamaño de la fuente del logo */
  }
  .navbar-nav {
    flex-direction: column; /* Apila los elementos de la navbar en columna */
  }
}
    
</style>