const apiUrlTarifas = "https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/fedex_tarifas.php";

async function calcularTarifa() {
    const origenPostal = document.getElementById("origenPostal").value;
    const destinoPostal = document.getElementById("destinoPostal").value;
    const peso = document.getElementById("peso").value;
    const respuestaDiv = document.getElementById("tarifaRespuesta");

    if (!origenPostal || !destinoPostal || !peso) {
        respuestaDiv.innerHTML = "Por favor, completa todos los campos.";
        return;
    }

    const requestData = {
        accountNumber: { value: "740561073" },
        rateRequestControlParameters: { returnTransitTimes: true },
        requestedShipment: {
            shipper: { address: { postalCode: origenPostal, countryCode: "US" } },
            recipient: { address: { postalCode: destinoPostal, countryCode: "US" } },
            pickupType: "DROPOFF_AT_FEDEX_LOCATION",
            shippingChargesPayment: { paymentType: "SENDER", payor: {} },
            requestedPackageLineItems: [ { weight: { units: "LB", value: peso } } ]
        }
    };

    try {
        const response = await fetch(apiUrlTarifas, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(requestData)
        });

        const data = await response.json();
        console.log("Respuesta de FedEx Tarifa:", data);

        if (data.error) {
            respuestaDiv.innerHTML = `<p style='color:red;'>Error: ${data.error}</p>`;
        } else {
            const rateDetail = data.output.rateReplyDetails?.[0]?.ratedShipmentDetails?.[0]?.totalNetCharge?.amount || "No disponible";
            respuestaDiv.innerHTML = `<p>Tarifa estimada: $${rateDetail} USD</p>`;
        }
    } catch (error) {
        console.error("Error al calcular tarifa:", error);
        respuestaDiv.innerHTML = "Hubo un error al calcular la tarifa.";
    }
}
