document.getElementById("reservaForm").addEventListener("submit", function (e) {
    const fechaEntrada = new Date(document.getElementById("fecha_hora_entrada").value);
    const fechaSalida = new Date(document.getElementById("fecha_hora_salida").value);

    // Verificar que la fecha de salida sea posterior a la de entrada
    if (fechaSalida <= fechaEntrada) {
        alert("La fecha y hora de salida debe ser posterior a la de entrada.");
        e.preventDefault(); // Evitar el envío del formulario
    }
});
