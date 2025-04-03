const apiUrl = "https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/fedex_tarifas.php";

async function calcularTarifa() {
    const origenPostal = document.getElementById("origenPostal").value;
    const destinoPostal = document.getElementById("destinoPostal").value;
    const peso = document.getElementById("peso").value;
    const tarifaRespuestaDiv = document.getElementById("tarifaRespuesta");

    if (!origenPostal || !destinoPostal || !peso) {
        tarifaRespuestaDiv.innerHTML = "Por favor, complete todos los campos.";
        return;
    }

    console.log(`Datos enviados: Origen ${origenPostal}, Destino ${destinoPostal}, Peso ${peso} lbs`);

    try {
        const response = await fetch(`${apiUrl}?origenPostal=${origenPostal}&destinoPostal=${destinoPostal}&peso=${peso}`);
        const data = await response.json();

        console.log("Respuesta cruda de la API:", data);  // Aquí mostramos la respuesta cruda de la API

        // Muestra la respuesta cruda para que puedas ver la estructura completa
        tarifaRespuestaDiv.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;

        if (data.error) {
            tarifaRespuestaDiv.innerHTML = data.error;
            return;
        }

        // Aquí debes procesar la respuesta de la API y mostrar las tarifas
        if (data.output && data.output.rateReplyDetails) {
            const tarifas = data.output.rateReplyDetails;
            tarifaRespuestaDiv.innerHTML = "<h3>Tarifas disponibles:</h3><table class='table table-sm'><thead><tr><th>Servicio</th><th>Tarifa</th></tr></thead><tbody>";
            tarifas.forEach(tarifa => {
                tarifaRespuestaDiv.innerHTML += `
                    <tr>
                        <td>${tarifa.serviceType}</td>
                        <td>${tarifa.totalNetCharge.amount} ${tarifa.totalNetCharge.currency}</td>
                    </tr>
                `;
            });
            tarifaRespuestaDiv.innerHTML += "</tbody></table>";
        } else {
            tarifaRespuestaDiv.innerHTML = "No se encontraron tarifas para esta solicitud.";
        }
    } catch (error) {
        console.error("Hubo un error al intentar obtener las tarifas.", error);
        tarifaRespuestaDiv.innerHTML = "Hubo un error al intentar obtener las tarifas.";
    }
}
