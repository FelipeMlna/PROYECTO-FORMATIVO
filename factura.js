document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form-parqueo");
  const facturaContainer = document.getElementById("factura");
  const btnDescargar = document.getElementById("btn-descargar");

  // Generar ID unico
  function generarIdPago() {
    const timestamp = Date.now().toString(36); // Basado en el tiempo actual
    const randomPart = Math.random().toString(36).substring(2, 8); // Parte aleatoria
    return `P-${timestamp}-${randomPart}`.toUpperCase(); // Formato: P-TIMESTAMP-RANDOM
  }

  form.addEventListener("submit", function (event) {
     event.preventDefault(); 

    // Obtener valores del formulario
    let placa = document.getElementById("placa").value.trim();

    // Validar el formato de la placa
    const placaRegex = /^[A-Z]{3}-\d{3}$/;
    if (!placaRegex.test(placa)) {
      alert("El formato de la placa debe ser: AAA-123 (tres letras mayusculas seguidas de un guion y tres numeros).");
      return;
    }

    // Convertir la placa a mayusculas
    placa = placa.toUpperCase();

    const vehiculo = document.getElementById("vehiculo").value;
    const entrada = new Date(document.getElementById("entrada").value);
    const salida = new Date(document.getElementById("salida").value);
    const metodo = document.getElementById("metodo").value;

    if (salida <= entrada) {
      alert("La hora de salida debe ser posterior a la hora de entrada.");
      return;
    }

    if (!metodo) {
      alert("Por favor seleccione un metodo de pago.");
      return;
    }

    let tiempoTotal = (salida - entrada) / (1000 * 60 * 60); // Convertir milisegundos a horas

    // Definir las tarifas segun el tipo de vehiculo
    let tarifaPorHora;
    if (vehiculo === "carro") {
      tarifaPorHora = 5300;
    } else if (vehiculo === "moto") {
      tarifaPorHora = 2400;
    } else {
      alert("Por favor seleccione un vehiculo valido.");
      return;
    }

    // Calcular el valor a pagar
    let valorTotal = tiempoTotal * tarifaPorHora;

    // Obtener el descuento si existe (desde la URL)
    const urlParams = new URLSearchParams(window.location.search);
    const descuentoTipo = urlParams.get("descuento");

    let descuento = 0;
    let mensajeDescuento = "";

    if (descuentoTipo === "80") {
      descuento = valorTotal * 0.8; // Descuento del 80%
      mensajeDescuento = "Descuento 80%";
    } else if (descuentoTipo === "15000") {
      descuento = 15000; // Descuento fijo de $15,000
      mensajeDescuento = "Descuento $15,000";
    }

    // Calcular el valor final asegurando que no sea menor al monto minimo
    const montoMinimo = 5000; // Definir el monto minimo permitido
    let valorFinal = valorTotal - descuento;
    if (valorFinal < montoMinimo) {
      valorFinal = montoMinimo; // Si es menor al minimo, ajustarlo
    }

    // Generar ID de pago
    const idPago = generarIdPago();

    // Mostrar la factura
    document.getElementById("factura-id-pago").textContent = idPago;
    document.getElementById("factura-placa").textContent = placa;
    document.getElementById("factura-vehiculo").textContent = vehiculo.charAt(0).toUpperCase() + vehiculo.slice(1);
    document.getElementById("factura-entrada").textContent = entrada.toLocaleString();
    document.getElementById("factura-salida").textContent = salida.toLocaleString();
    document.getElementById("factura-tiempo").textContent = tiempoTotal.toFixed(2);
    document.getElementById("factura-valor").textContent = valorTotal.toFixed(2);
    document.getElementById("factura-descuento").textContent = mensajeDescuento;
    document.getElementById("factura-valor-final").textContent = valorFinal.toFixed(2);
    document.getElementById("factura-metodo").textContent = metodo.charAt(0).toUpperCase() + metodo.slice(1);

    facturaContainer.style.display = "block";

    // Crear los datos para enviar al servidor
    const facturaData = {
      id_pago: idPago,
      placa: placa,
      vehiculo: vehiculo,
      fecha_entrada: entrada.toISOString(),
      fecha_salida: salida.toISOString(),
      valor_pagar: valorTotal.toFixed(2),
      metodo_pago: metodo,
      descuento_aplicado: mensajeDescuento,
      valor_final: valorFinal.toFixed(2)
    };

    // Enviar los datos al servidor con fetch
    fetch("guardar_factura.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: new URLSearchParams(facturaData)
    })
      .then(response => response.text())
      .then(data => {
        alert(data); // Mostrar respuesta del servidor
      })
      .catch(error => {
        console.error("Error:", error);
        alert("Hubo un error al guardar la factura.");
      });
  });

  // Funcion para descargar la factura como PDF
  btnDescargar.addEventListener("click", function () {
    const { jsPDF } = window.jspdf; // Asegurate de usar esto
    html2canvas(facturaContainer).then((canvas) => {
      const pdf = new jsPDF();
      const imgData = canvas.toDataURL("image/png");
      const imgWidth = 180;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;
      pdf.addImage(imgData, "PNG", 10, 10, imgWidth, imgHeight);
      pdf.save("factura.pdf");
    });
  });
});
