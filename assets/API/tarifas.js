const apiUrl = "https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/fedex_tarifas.php";

async function calcularTarifa() {
    const origenPostal = document.getElementById("origenPostal").value;
    const destinoPostal = document.getElementById("destinoPostal").value;
    const peso = document.getElementById("peso").value;
    const responseDiv = document.getElementById("response");

    // Validar que todos los campos están llenos
    if (!origenPostal || !destinoPostal || !peso) {
        responseDiv.innerHTML = "Por favor, complete todos los campos.";
        return;
    }

    console.log(`Datos enviados: Origen ${origenPostal}, Destino ${destinoPostal}, Peso ${peso} lbs`);

    try {
        const response = await fetch(`${apiUrl}?origenPostal=${origenPostal}&destinoPostal=${destinoPostal}&peso=${peso}`);
        
        if (!response.ok) {  // Verificar si la respuesta es exitosa
            throw new Error("Error al hacer la solicitud: " + response.statusText);
        }

        const data = await response.json();
        console.log("Respuesta de la API:", data);  // Mostrar la respuesta procesada

        // Llamar a la función para mostrar las tarifas en HTML
        mostrarTarifas(data);

    } catch (error) {
        console.error("Hubo un error al intentar obtener las tarifas.", error);
        responseDiv.innerHTML = `Hubo un error al intentar obtener las tarifas: ${error.message}`;
    }
}

function mostrarTarifas(response) {
    const tarifasDiv = document.getElementById("tarifas_result");
    
    // Verificar si hay datos de tarifas en la respuesta
    if (!response.output || !response.output.rateReplyDetails) {
        tarifasDiv.innerHTML = "<p>No se encontraron tarifas disponibles.</p>";
        return;
    }

    const tarifas = response.output.rateReplyDetails;
    let htmlContent = "<h3>Opciones de Envío:</h3><ul>";

    // Recorrer los resultados y extraer datos clave
    tarifas.forEach((tarifa) => {
        const servicio = tarifa.serviceName || "Servicio desconocido";
        const precio = tarifa.ratedShipmentDetails?.[0]?.totalNetCharge?.amount || "No disponible";
        htmlContent += `<li><strong>${servicio}:</strong> $${precio} USD</li>`;
    });

    htmlContent += "</ul>";
    tarifasDiv.innerHTML = htmlContent;
}
