const apiUrl = "https://verbose-space-journey-7vrwvpv7qjg72rwqp-8000.app.github.dev/fedex_tracking.php";

async function trackPackage() {
    const trackingNumber = document.getElementById("trackingNumber").value;
    const responseDiv = document.getElementById("response");

    if (!trackingNumber) {
        responseDiv.innerHTML = "Por favor ingresa un número de seguimiento.";
        return;
    }

    console.log(`Número de seguimiento enviado: ${trackingNumber}`);

    try {
        const response = await fetch(`${apiUrl}?trackingNumber=${trackingNumber}`);
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
            const serviceMessage = trackResults.serviceCommitMessage?.message || "No disponible";
            const holdLocation = trackResults.holdAtLocation?.locationContactAndAddress?.address?.city 
                ? `${trackResults.holdAtLocation.locationContactAndAddress.address.city}, ${trackResults.holdAtLocation.locationContactAndAddress.address.stateOrProvinceCode}`
                : "No disponible";

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
            <table class="table table-sm">
                <thead>
                    <tr><th colspan="2" style="background-color: #464646; color: white;">Detalles del Paquete</th></tr>
                </thead>
                <tbody>
                    <tr><td><strong>Tipo de embalaje:</strong></td><td>${trackResults.packageDetails?.packagingDescription?.description || "No disponible"}</td></tr>
                    <tr><td><strong>Peso:</strong></td><td>${trackResults.packageDetails?.weightAndDimensions?.weight?.[0]?.value || "N/A"} ${trackResults.packageDetails?.weightAndDimensions?.weight?.[0]?.unit || ""}</td></tr>
                    <tr><td><strong>Dimensiones:</strong></td><td>${trackResults.packageDetails?.weightAndDimensions?.dimensions?.[0]?.length || "N/A"}x${trackResults.packageDetails?.weightAndDimensions?.dimensions?.[0]?.width || "N/A"}x${trackResults.packageDetails?.weightAndDimensions?.dimensions?.[0]?.height || "N/A"} ${trackResults.packageDetails?.weightAndDimensions?.dimensions?.[0]?.units || ""}</td></tr>
                </tbody>
            </table>
            <table class="table table-sm">
                <thead>
                    <tr><th colspan="2" style="background-color: #464646; color: white;">Información del Envío</th></tr>
                </thead>
                <tbody>
                    <tr><td><strong>Remitente:</strong></td><td>${senderCity}</td></tr>
                    <tr><td><strong>Destinatario:</strong></td><td>${recipientCity}</td></tr>
                    <tr><td><strong>Detalles del servicio:</strong></td><td>${serviceDescription}</td></tr>
                    <tr><td><strong>Mensaje de servicio:</strong></td><td>${serviceMessage}</td></tr>
                    <tr><td><strong>Información de ubicación de retención:</strong></td><td>${holdLocation}</td></tr>
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