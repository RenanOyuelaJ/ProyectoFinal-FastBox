async function obtenerToken() {
    try {
        // Realizar la solicitud al archivo PHP que actúa como backend
        const response = await fetch('fedex.php', {
            method: 'GET',
        });

        const data = await response.json();

        if (data.access_token) {
            return data.access_token; // Devuelve el token de acceso
        } else {
            console.error("No se pudo obtener el token.");
            return null;
        }
    } catch (error) {
        console.error("Error al hacer la solicitud al backend:", error);
        return null;
    }
}

// Función para rastrear un envío
async function rastrearEnvio(trackingNumber) {
    const token = await obtenerToken();
    if (!token) {
        console.error("No se pudo obtener el token.");
        return;
    }

    const url = "https://apis-sandbox.fedex.com/track/v1/trackingnumbers";
    const payload = { trackingInfo: [{ trackingNumberInfo: { trackingNumber } }] };

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        return data;
    } catch (error) {
        console.error("Error en el rastreo:", error);
        return null;
    }
}

// Función que se ejecuta cuando el usuario hace clic en "Rastrear"
async function buscarTracking() {
    const trackingNumber = document.getElementById("trackingInput").value;
    if (!trackingNumber) {
        alert("Ingresa un número de guía.");
        return;
    }

    const trackingData = await rastrearEnvio(trackingNumber);
    if (trackingData) {
        document.getElementById("resultado").innerText = `Estado: ${trackingData.output.completeTrackResults[0].trackResults[0].latestStatusDetail.description}`;
    } else {
        document.getElementById("resultado").innerText = "Error al obtener datos.";
    }
}
