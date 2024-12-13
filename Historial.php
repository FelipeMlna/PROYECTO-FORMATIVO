<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Historial de Reservas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="historial.css">
</head>
<body>
  <!-- Encabezado -->
  <header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
      <div class="container-fluid">
        <a href="login.php" class="navbar-brand d-flex align-items-center">
          <img src="imagenes/Logo-removebg-preview.png" width="100" alt="Logo de Parking-Tech">
          <span class="ms-2">PARKING-TECH</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item d-flex align-items-center">
              <img class="iconos-navbar me-2" src="icon.html/apoyo-unscreen.gif">
              <a href="Ayuda.html" class="nav-link">Contáctanos</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Contenido principal -->
  <main class="container mt-4">
    <section>
      <div class="text-center mb-4">
        <img src="Imagenes/Logo-removebg-preview.png" alt="Logo de Parking-Tech" class="logo-centered img-fluid" width="100">
      </div>
      <h2 class="titulo1 text-center mb-4">Historial de Reservas</h2>
      
      <!-- Tabla de reservas con scroll horizontal en móviles -->
      <div class="table-responsive">
        <table class="table table-striped table-bordered text-center">
          <thead class="table-dark">
            <tr>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Fecha Entrada</th>
              <th>Fecha Salida</th>
              <th>Espacio</th>
              <th>Email</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>

          
            <?php
            session_start();
            include("conexion.php");

            date_default_timezone_set('America/Bogota');

            if (!isset($_SESSION['email'])) {
                echo "<tr><td colspan='7' class='text-center text-danger'>No has iniciado sesión.</td></tr>";
                exit();
            }

            $email = $_SESSION['email'];
            $stmt = $conn->prepare("SELECT id_reserva, nombre, email, telefono, fecha_hora_entrada, fecha_hora_salida, espacio FROM reservas WHERE email = ?");

            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['telefono']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['fecha_hora_entrada']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['fecha_hora_salida']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['espacio']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['email']) . "</td>";

                        if (!empty($fila['fecha_hora_salida'])) {
                            try {
                                $fecha_actual = new DateTime();
                                $fecha_salida = new DateTime($fila['fecha_hora_salida']);

                                if ($fecha_actual > $fecha_salida) {
                                    echo "<td><span class='badge bg-danger'>Finalizado</span></td>";
                                } else {
                                    echo "<td><span class='badge bg-success'>Activo</span></td>";
                                }
                            } catch (Exception $e) {
                                echo "<td><span class='badge bg-warning'>Error en la fecha</span></td>";
                            }
                        } else {
                            echo "<td><span class='badge bg-secondary'>Sin fecha de salida</span></td>";
                        }

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center text-warning'>No se encontraron reservas.</td></tr>";
                }

                $stmt->close();
            } else {
                echo "<tr><td colspan='7' class='text-center text-danger'>Error en la consulta: " . htmlspecialchars($conn->error) . "</td></tr>";
            }
            $conn->close();
            ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <!-- Scripts de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    font-size: 27px;
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