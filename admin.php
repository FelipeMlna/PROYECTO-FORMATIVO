<?php
session_start();
include("conexion.php"); // Incluir la conexión a la base de datos

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['email']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: IniciarSesion.html");
    exit();
}

$mensaje = "";

// Obtener el nombre y rol del administrador autenticado
$email_usuario = $_SESSION['email'];
$stmt = $conn->prepare("SELECT nombre, roles FROM registros WHERE email = ?");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param("s", $email_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    $nombre_usuario = $usuario['nombre'];
    $rol_usuario = $usuario['roles'];
} else {
    session_destroy();
    header("Location: IniciarSesion.html");
    exit();
}
$stmt->close();

// Manejar eliminación de reserva
if (isset($_GET['action']) && $_GET['action'] === 'delete_reserva' && isset($_GET['id_reserva'])) {
    $id_reserva = $_GET['id_reserva'];
    $stmt = $conn->prepare("DELETE FROM reservas WHERE id_reserva = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id_reserva);
    if ($stmt->execute()) {
        $mensaje = "Reserva eliminada exitosamente.";
    } else {
        $mensaje = "Error al eliminar la reserva: " . $conn->error;
    }
    $stmt->close();
}

// Manejar eliminación de usuario
if (isset($_GET['action']) && $_GET['action'] === 'delete_user' && isset($_GET['email'])) {
    $email_usuario_eliminar = $_GET['email'];
    $stmt = $conn->prepare("DELETE FROM registros WHERE email = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $email_usuario_eliminar);
    if ($stmt->execute()) {
        $mensaje = "Usuario eliminado exitosamente.";
    } else {
        $mensaje = "Error al eliminar el usuario: " . $conn->error;
    }
    $stmt->close();
}

// Obtener usuarios
$usuarios = [];
$busqueda_usuario = isset($_GET['search_usuario']) ? $_GET['search_usuario'] : '';
$stmt = $conn->prepare("SELECT nombre, email, roles FROM registros WHERE nombre LIKE ? OR email LIKE ?");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$search_term = "%" . $busqueda_usuario . "%";
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}
$stmt->close();

// Obtener reservas con búsqueda
$reservas = [];
$busqueda_reserva = isset($_GET['search_reserva']) ? $_GET['search_reserva'] : '';
$query_reserva = "SELECT id_reserva, nombre, telefono, fecha_hora_entrada, fecha_hora_salida, espacio, email FROM reservas";
if (!empty($busqueda_reserva)) {
    $query_reserva .= " WHERE nombre LIKE ? OR telefono LIKE ? OR email LIKE ? ORDER BY fecha_hora_entrada DESC";
    $stmt = $conn->prepare($query_reserva);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $search_term_reserva = "%" . $busqueda_reserva . "%";
    $stmt->bind_param("sss", $search_term_reserva, $search_term_reserva, $search_term_reserva);
} else {
    $query_reserva .= " ORDER BY fecha_hora_entrada DESC";
    $stmt = $conn->prepare($query_reserva);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
}

$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $reservas[] = $row;
}
$stmt->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador - Gestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="narvar.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <img src="Imagenes/Logo-removebg-preview.png" width="70" alt="Logo">
                <span class="ms-2">ADMINISTRADOR</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Información del Perfil
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="font-family:'Times New Roman', Times, serif;" aria-labelledby="userDropdown">
                            <li><span class="dropdown-item-text"><strong>Nombre:</strong> <?= htmlspecialchars($nombre_usuario); ?></span></li>
                            <li><span class="dropdown-item-text"><strong>Rol:</strong> <?= htmlspecialchars($rol_usuario); ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="cerrar-sesion" href="logout.php">Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="container mt-4">
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje; ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs" id="gestionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" href="#usuarios" role="tab" aria-controls="usuarios" aria-selected="true">Gestionar Usuarios</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="reservas-tab" data-bs-toggle="tab" href="#reservas" role="tab" aria-controls="reservas" aria-selected="false">Gestionar Reservas</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="historial-tab" data-bs-toggle="tab" href="#historial" role="tab" aria-controls="historial" aria-selected="false">Historial General</a>
        </li>
    </ul>    

    <div class="tab-content mt-3" id="gestionTabsContent">

        <!-- Gestionar Usuarios -->
        <div class="tab-pane fade show active" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
            <h2>Usuarios Registrados</h2>
            <form method="get" action="" class="mb-3 d-flex">
                <input type="text" name="search_usuario" class="form-control" placeholder="Buscar por nombre o email" value="<?= htmlspecialchars($busqueda_usuario); ?>">
                <button type="submit" class="btn btn-primary mt-2 ms-2">Buscar</button>
            </form>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?= htmlspecialchars($usuario['email']); ?></td>
                            <td><?= htmlspecialchars($usuario['roles']); ?></td>
                            <td>
                                <a href="modificar_usuario.php?email=<?= urlencode($usuario['email']); ?>" class="btn btn-warning btn-sm">Modificar</a>
                                <a href="?action=delete_user&email=<?= urlencode($usuario['email']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estas seguro de que deseas eliminar este usuario?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Gestionar Reservas -->
<div class="tab-pane fade" id="reservas" role="tabpanel" aria-labelledby="reservas-tab">
    <h2>Reservas Registradas</h2>
    
    <!-- Barra de búsqueda -->
    <form method="get" action="" class="mb-3 d-flex">
        <input type="text" name="search_reserva" class="form-control" placeholder="Buscar por nombre, teléfono o email" value="<?= htmlspecialchars($busqueda_reserva); ?>">
        <button type="submit" class="btn btn-primary mt-2 ms-2">Buscar</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Fecha y Hora de Entrada</th>
                <th>Espacio</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['nombre']); ?></td>
                    <td><?= htmlspecialchars($reserva['telefono']); ?></td>
                    <td><?= htmlspecialchars($reserva['fecha_hora_entrada']); ?></td>
                    <td><?= htmlspecialchars($reserva['espacio']); ?></td>
                    <td><?= htmlspecialchars($reserva['email']); ?></td>
                    <td>
                        <a href="modificar_reserva.php?id_reserva=<?= urlencode($reserva['id_reserva']); ?>" class="btn btn-warning btn-sm">Modificar</a>
                        <a href="?action=delete_reserva&id_reserva=<?= urlencode($reserva['id_reserva']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta reserva?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


        <!-- Historial de Reservas -->
        <div class="tab-pane fade" id="historial" role="tabpanel" aria-labelledby="historial-tab">
            <h2>Historial General</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Telefono</th>
                        <th>Fecha y Hora de Entrada</th>
                        <th>Fecha y Hora de Salida</th>
                        <th>Espacio</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['nombre']); ?></td>
                            <td><?= htmlspecialchars($reserva['telefono']); ?></td>
                            <td><?= htmlspecialchars($reserva['fecha_hora_entrada']); ?></td>
                            <td><?= htmlspecialchars($reserva['fecha_hora_salida']); ?></td>
                            <td><?= htmlspecialchars($reserva['espacio']); ?></td>
                            <td><?= htmlspecialchars($reserva['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
