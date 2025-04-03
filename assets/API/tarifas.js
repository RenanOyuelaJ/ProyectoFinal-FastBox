const apiUrl = "https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/fedex_tarifas.php";

async function calcularTarifa() {
    const origenPostal = document.getElementById("origenPostal").value;
    const destinoPostal = document.getElementById("destinoPostal").value;
    const peso = document.getElementById("peso").value;
    const tarifaRespuesta = document.getElementById("tarifaRespuesta");

    if (!origenPostal || !destinoPostal || !peso) {
        tarifaRespuesta.innerHTML = "Por favor ingresa todos los datos requeridos.";
        return;
    }

    console.log(`Datos enviados: Origen ${origenPostal}, Destino ${destinoPostal}, Peso ${peso} lbs`);

    try {
        const response = await fetch(apiUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                origin: origenPostal,
                destination: destinoPostal,
                weight: parseFloat(peso)
            })
        });

        const data = await response.json();
        console.log("Respuesta completa de la API:", data);

        if (data.error) {
            tarifaRespuesta.innerHTML = `<p style="color: red;">${data.error}</p>`;
            return;
        }

        if (data.output?.rateReplyDetails?.length > 0) {
            let tarifasHTML = "<h3>Opciones de Tarifas:</h3><ul>";
            data.output.rateReplyDetails.forEach(rate => {
                tarifasHTML += `<li><strong>${rate.serviceType}:</strong> ${rate.ratedShipmentDetails[0].totalNetCharge} USD</li>`;
            });
            tarifasHTML += "</ul>";

            tarifaRespuesta.innerHTML = tarifasHTML;
        } else {
            tarifaRespuesta.innerHTML = "<p>No se encontraron tarifas disponibles.</p>";
        }

    } catch (error) {
        console.error("Hubo un error al intentar obtener las tarifas.", error);
        tarifaRespuesta.innerHTML = "<p style='color: red;'>Hubo un error al intentar obtener las tarifas.</p>";
    }
}
