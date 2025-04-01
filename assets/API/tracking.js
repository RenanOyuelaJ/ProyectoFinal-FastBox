const apiUrl = "https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/fedex.php";

async function trackPackage() {
    const trackingNumber = document.getElementById("trackingNumber").value;
    const responseDiv = document.getElementById("response");

    if (!trackingNumber) {
        responseDiv.innerHTML = "Por favor ingresa un número de seguimiento.";
        return;
    }

    // Realizar la solicitud a PHP para obtener el token y los detalles del rastreo
    try {
        const response = await fetch(`${apiUrl}?trackingNumber=${trackingNumber}`);
        const data = await response.json();

        // Verificamos si la respuesta contiene un error
        if (data.error) {
            responseDiv.innerHTML = data.error;
            return;
        }

        // Verificamos si tenemos resultados completos del rastreo
        if (data.output && data.output.completeTrackResults && data.output.completeTrackResults.length > 0) {
            const trackResults = data.output.completeTrackResults[0].trackResults[0];

            // Extraer información importante
            const trackingNumber = trackResults.trackingNumber;
            const status = trackResults.statusDescription || "Estado no disponible";

            // Crear un contenido HTML dinámico para mostrar los resultados
            const resultsHTML = `
                <h3>Resultados de Rastreo para ${trackingNumber}</h3>
                <p><strong>Estado: </strong>${status}</p>
                <p><strong>Detalles adicionales:</strong></p>
                <pre>${JSON.stringify(trackResults, null, 2)}</pre>
            `;

            responseDiv.innerHTML = resultsHTML;
        } else {
            responseDiv.innerHTML = "No se encontraron resultados para este número de seguimiento.";
        }
    } catch (error) {
        console.error("Hubo un error al intentar obtener la información del paquete.", error);
        responseDiv.innerHTML = "Hubo un error al intentar obtener la información del paquete.";
    }
}
