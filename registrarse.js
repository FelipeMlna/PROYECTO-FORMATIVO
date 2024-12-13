document.getElementById('form').addEventListener('submit', function (event) {
    let warnings = '';
    let isValid = true;

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const placa = document.getElementById('placa').value; // Campo para la placa

    // Validación del Nombre (letras y espacios)
    const nameRegex = /^[a-zA-Z\s]+$/;
    if (!nameRegex.test(name)) {
        warnings += 'El nombre solo puede contener letras y espacios.<br>';
        isValid = false;
    }

    // Validación del Email (incluye @)
    if (!email.includes('@')) {
        warnings += 'El email debe contener el carácter "@".<br>';
        isValid = false;
    }

    // Validación de la Contraseña (mínimo 6 caracteres, al menos 1 mayúscula)
    const passwordRegex = /^(?=.*[A-Z]).{6,}$/;
    if (!passwordRegex.test(password)) {
        warnings += 'La contraseña debe tener al menos 6 caracteres y una letra mayúscula.<br>';
        isValid = false;
    }

    // Validación de la Placa (XXX-123)
    const placaRegex = /^[A-Z]{3}-\d{3}$/;
    if (!placaRegex.test(placa)) {
        warnings += 'La placa debe tener el formato XXX-123 (3 letras mayúsculas, un guion y 3 números).<br>';
        isValid = false;
    }

    // Mostrar mensajes de advertencia
    const warningsElement = document.getElementById('warnings');
    warningsElement.innerHTML = warnings;

    // Cancelar el envío si hay errores
    if (!isValid) {
        event.preventDefault();
    }
});
