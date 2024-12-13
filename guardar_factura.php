<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parking-tech1";

// Crear conexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexion
if ($conn->connect_error) {
    die("Conexion fallida: " . $conn->connect_error);
}

// Recibir datos enviados desde el cliente
$id_pago = $_POST['id_pago'];
$placa = $_POST['placa'];
$vehiculo = $_POST['vehiculo'];
$fecha_entrada = $_POST['fecha_entrada'];
$fecha_salida = $_POST['fecha_salida'];
$valor_pagar = $_POST['valor_pagar'];
$metodo_pago = $_POST['metodo_pago'];
$descuento_aplicado = $_POST['descuento_aplicado'];
$valor_final = $_POST['valor_final'];

// Preparar y ejecutar consulta SQL
$sql = "INSERT INTO facturas (id_pago, placa, vehiculo, fecha_entrada, fecha_salida, valor_pagar, metodo_pago, descuento_aplicado, valor_final) 
VALUES ('$id_pago', '$placa', '$vehiculo', '$fecha_entrada', '$fecha_salida', '$valor_pagar', '$metodo_pago', '$descuento_aplicado', '$valor_final')";

if ($conn->query($sql) === TRUE) {
    echo "Datos guardados correctamente";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>



