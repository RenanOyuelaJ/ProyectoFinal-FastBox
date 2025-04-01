function obtenerTracking() {
    const trackingNumber = document.getElementById("trackingInput").value;
    if (!trackingNumber) {
        console.error("Ingrese un número de rastreo.");
        return;
    }

    const url = `https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/assets/API/fedex.php?tracking_number=${trackingNumber}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Error:", data.error);
            } else {
                // Aquí procesas el resultado y lo muestras en el frontend
                console.log("Datos de rastreo:", data);
                mostrarResultado(data);
            }
        })
        .catch(error => console.error("Error al hacer la solicitud:", error));
}

function mostrarResultado(data) {
    // Mostrar el resultado en el frontend (puedes hacerlo en el HTML como prefieras)
    const resultadoDiv = document.getElementById("resultado");
    if (data && data.output) {
        resultadoDiv.innerHTML = `
            <h3>Resultado de rastreo:</h3>
            <pre>${JSON.stringify(data, null, 2)}</pre>
        `;
    } else {
        resultadoDiv.innerHTML = "<p>No se encontraron datos de rastreo.</p>";
    }
}
