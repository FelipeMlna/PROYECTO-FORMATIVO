// Seleccionar los elementos del formulario
const form = document.getElementById('form');
const passwordInput = document.getElementById('password');
const warnings = document.getElementById('warnings');

// Escuchar el evento submit del formulario
form.addEventListener('submit', function (e) {
    let message = '';

    // Validar la longitud de la contrasena
    if (passwordInput.value.length < 6) {
        message += 'La contrasena debe tener al menos 6 caracteres.<br>';
    }

    // Validar que contenga al menos una letra mayuscula
    if (!/[A-Z]/.test(passwordInput.value)) {
        message += 'La contrasena debe contener al menos una letra mayuscula.<br>';
    }

    // Mostrar mensajes de error o permitir el envio
    if (message !== '') {
        e.preventDefault(); // Detener el envio del formulario
        warnings.innerHTML = message; // Mostrar mensajes de advertencia
    } else {
        warnings.innerHTML = ''; // Limpiar mensajes si todo esta bien
    }
});
