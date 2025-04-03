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

        console.log("Respuesta de la API:", data);  // Mostrar la respuesta procesada

        if (data.error) {
            tarifaRespuestaDiv.innerHTML = data.error;
            return;
        }

        // Verificar que se recibieron tarifas
        if (data.length > 0) {
            tarifaRespuestaDiv.innerHTML = "<h3>Tarifas disponibles:</h3><table class='table table-sm'><thead><tr><th>Servicio</th><th>Tarifa</th></tr></thead><tbody>";

            data.forEach(tarifa => {
                // Verificar si 'ratedShipmentDetails' existe y tiene detalles
                if (tarifa.ratedShipmentDetails && tarifa.ratedShipmentDetails.length > 0) {
                    // Acceder al detalle de la tarifa de forma segura
                    const ratedDetail = tarifa.ratedShipmentDetails[0];

                    if (ratedDetail && ratedDetail.totalNetCharge && ratedDetail.totalNetCharge.amount) {
                        tarifaRespuestaDiv.innerHTML += `
                            <tr>
                                <td>${tarifa.serviceName}</td>
                                <td>${ratedDetail.totalNetCharge.amount} ${ratedDetail.totalNetCharge.currency}</td>
                            </tr>
                        `;
                    } else {
                        tarifaRespuestaDiv.innerHTML += `
                            <tr>
                                <td>${tarifa.serviceName}</td>
                                <td>No disponible</td>
                            </tr>
                        `;
                    }
                } else {
                    tarifaRespuestaDiv.innerHTML += `
                        <tr>
                            <td>${tarifa.serviceName}</td>
                            <td>Detalles no disponibles</td>
                        </tr>
                    `;
                }
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
