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

        console.log("Respuesta completa de la API:", data);

        // Verificamos si la respuesta contiene un error
        if (data.error) {
            responseDiv.innerHTML = data.error;
            return;
        }

        // Verificamos si tenemos resultados completos del rastreo
        if (data.output && data.output.completeTrackResults && data.output.completeTrackResults.length > 0) {
            const trackResults = data.output.completeTrackResults[0].trackResults[0];

            // Información del remitente y destinatario
            const senderCity = trackResults.originLocation.locationContactAndAddress.address.city || "No disponible";
            const recipientCity = trackResults.lastUpdatedDestinationAddress.city || "No disponible";

            // Mensaje de servicio
            const serviceMessage = trackResults.serviceCommitMessage ? trackResults.serviceCommitMessage.message : "No disponible";

            // Ubicación de retención
            const holdLocation = trackResults.holdAtLocation ? trackResults.holdAtLocation.locationContactAndAddress.address.city + ", " + trackResults.holdAtLocation.locationContactAndAddress.address.stateOrProvinceCode : "No disponible";

            // Extraer información del evento de rastreo
            const status = trackResults.statusDescription || "Estado no disponible";
            const latestEvent = trackResults.scanEvents ? trackResults.scanEvents[0] : null;
            const eventDescription = latestEvent ? latestEvent.eventDescription : "No hay eventos recientes";
            const eventLocation = latestEvent ? latestEvent.scanLocation.city || "Ubicación no disponible" : "Ubicación no disponible";
            const eventState = latestEvent ? latestEvent.scanLocation.stateOrProvinceCode || "Estado no disponible" : "Estado no disponible";

            // Crear un contenido HTML dinámico para mostrar los resultados
            const resultsHTML = `
                <h3>Resultados de Rastreo para ${trackingNumber}</h3>
                <p><strong>Estado: </strong>${status}</p>
                <p><strong>Último evento: </strong>${eventDescription}</p>
                <p><strong>Ubicación del evento: </strong>${eventLocation}, ${eventState}</p>
                <p><strong>Detalles del paquete:</strong></p>
                <ul>
                    <li><strong>Tipo de embalaje:</strong> ${trackResults.packageDetails.packagingDescription.description}</li>
                    <li><strong>Peso:</strong> ${trackResults.packageDetails.weightAndDimensions.weight[0].value} ${trackResults.packageDetails.weightAndDimensions.weight[0].unit}</li>
                    <li><strong>Dimensiones:</strong> ${trackResults.packageDetails.weightAndDimensions.dimensions[0].length}x${trackResults.packageDetails.weightAndDimensions.dimensions[0].width}x${trackResults.packageDetails.weightAndDimensions.dimensions[0].height} ${trackResults.packageDetails.weightAndDimensions.dimensions[0].units}</li>
                </ul>

                <p><strong>Remitente: </strong>${senderCity}</p>
                <p><strong>Destinatario: </strong>${recipientCity}</p>

                <p><strong>Mensaje de servicio: </strong>${serviceMessage}</p>

                <p><strong>Información de ubicación de retención: </strong>${holdLocation}</p>

                <h4>Línea de tiempo de eventos:</h4>
                <ul>
                    ${trackResults.scanEvents.map(event => {
                        const eventLocation = event.scanLocation.city || "Ubicación no disponible";
                        const eventState = event.scanLocation.stateOrProvinceCode || "Estado no disponible";
                        return `
                            <li><strong>${event.date}</strong> - ${event.eventDescription} en ${eventLocation}, ${eventState}</li>
                        `;
                    }).join('')}
                </ul>
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
