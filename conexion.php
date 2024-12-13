<?php
$servername = "localhost";  // Cambia esto si tu base de datos esta en otro servidor
$username = "root";         // Tu nombre de usuario
$password = "";             // Tu contrasena
$dbname = "parking-tech1";  // Nombre de la base de datos

// Crear la conexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexion
if ($conn->connect_error) {
    die("Conexion fallida: " . $conn->connect_error);
}
?>
