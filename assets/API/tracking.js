const apiUrl = "https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/fedex_tracking.php";

async function trackPackage() {
    const trackingNumber = document.getElementById("trackingNumber").value;
    const responseDiv = document.getElementById("response");

    if (!trackingNumber) {
        responseDiv.innerHTML = "Por favor ingresa un número de seguimiento.";
        return;
    }

    console.log(`Número de seguimiento enviado: ${trackingNumber}`);

    try {
        const response = await fetch(`${apiUrl}?trackingNumber=${trackingNumber}`, { redirect: "manual" });

        console.log("Código de respuesta:", response.status);

        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.statusText}`);
        }

        const data = await response.json();

        console.log("Respuesta completa de la API:", data);

        if (data.error) {
            responseDiv.innerHTML = data.error;
            return;
        }

        if (data.output?.completeTrackResults?.length > 0) {
            const trackResults = data.output.completeTrackResults[0].trackResults[0];

            const senderCity = trackResults.originLocation?.locationContactAndAddress?.address?.city || "No disponible";
            const recipientCity = trackResults.lastUpdatedDestinationAddress?.city || "No disponible";
            const status = trackResults.statusDescription || "Estado no disponible";
            const latestEvent = trackResults.scanEvents?.[0] || null;
            const eventDescription = latestEvent?.eventDescription || "No hay eventos recientes";
            const eventLocation = latestEvent?.scanLocation?.city || "Ubicación no disponible";
            const eventState = latestEvent?.scanLocation?.stateOrProvinceCode || "Estado no disponible";
            const serviceDescription = trackResults.serviceDetail?.description || "No disponible";

            trackingResults.style.display = 'block';
            responseDiv.innerHTML = `
            <h3>Resultados de Rastreo para ${trackingNumber}</h3>
            <table class="table table-sm">
                <thead>
                    <tr><th colspan="2" style="background-color:#464646; color: white;">Estado del Paquete</th></tr>
                </thead>
                <tbody>
                    <tr><td><strong>Estado:</strong></td><td>${status}</td></tr>
                    <tr><td><strong>Último evento:</strong></td><td>${eventDescription}</td></tr>
                    <tr><td><strong>Ubicación del evento:</strong></td><td>${eventLocation}, ${eventState}</td></tr>
                </tbody>
            </table>
            <h4>Línea de tiempo de eventos:</h4>
            <table class="table table-sm">
                <thead>
                    <tr><th style="background-color: #464646; color: white;">Fecha</th><th style="background-color: #464646; color: white;">Evento</th><th style="background-color: #464646; color: white;">Ubicación</th></tr>
                </thead>
                <tbody>
                    ${trackResults.scanEvents.map(event => {
                        const eventLocation = event.scanLocation?.city || "Ubicación no disponible";
                        const eventState = event.scanLocation?.stateOrProvinceCode || "Estado no disponible";
                        return `<tr class="table-light"><td>${event.date}</td><td>${event.eventDescription}</td><td>${eventLocation}, ${eventState}</td></tr>`;
                    }).join('')}
                </tbody>
            </table>
        `;

        } else {
            responseDiv.innerHTML = "No se encontraron resultados para este número de seguimiento.";
        }
    } catch (error) {
        console.error("Hubo un error al intentar obtener la información del paquete.", error);
        responseDiv.innerHTML = "Hubo un error al intentar obtener la información del paquete.";
    }
}
