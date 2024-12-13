<?php
// Inicia o continúa la sesion
session_start();

// Destruye todas las variables de sesion
session_unset();

// Destruye la sesion completamente
session_destroy();

// Redirige a la pagina principal (index.html) con un parametro 'logout=success' en la URL
header("Location: index.html?logout=success");
exit();
?>
