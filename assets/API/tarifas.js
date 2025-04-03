const apiUrl = "https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/fedex_tarifas.php";

async function calcularTarifa() {
    const origenPostal = document.getElementById("origenPostal").value;
    const destinoPostal = document.getElementById("destinoPostal").value;
    const peso = document.getElementById("peso").value;
    const tarifaRespuestaDiv = document.getElementById("tarifaRespuesta");

    // Validar que todos los campos están llenos
    if (!origenPostal || !destinoPostal || !peso) {
        tarifaRespuestaDiv.innerHTML = "Por favor, complete todos los campos.";
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

        console.log("Asignando contenido a tarifaRespuestaDiv");
        tarifaRespuestaDiv.innerHTML = "<pre>{ \"respuesta\": \"esto es una prueba\" }</pre>";
        console.log("Contenido asignado:", tarifaRespuestaDiv.innerHTML);

        // Mostrar la respuesta completa en formato JSON en el HTML
        //tarifaRespuestaDiv.innerHTML = "<pre>" + JSON.stringify(data, null, 2) + "</pre>";
        
    } catch (error) {
        console.error("Hubo un error al intentar obtener las tarifas.", error);
        tarifaRespuestaDiv.innerHTML = `Hubo un error al intentar obtener las tarifas: ${error.message}`;
    }
}
