function actualizarEspacios() {
    fetch('actualizar_espacios.php')  // Llamada al archivo PHP que obtiene los estados
        .then(response => response.json()) // Convierte la respuesta en JSON
        .then(data => {
            data.forEach(espacio => {
                const elemento = document.querySelector(`#${espacio.espacio}`);
                if (espacio.estado === 'ocupado') {
                    // Si el espacio esta ocupado, lo bloqueamos visualmente
                    elemento.classList.add('parking-space-block');
                    elemento.classList.remove('parking-space');
                } else {
                    // Si el espacio esta libre, lo habilitamos
                    elemento.classList.add('parking-space');
                    elemento.classList.remove('parking-space-block');
                }
            });
        })
        .catch(error => console.error('Error al actualizar los espacios:', error));
}
