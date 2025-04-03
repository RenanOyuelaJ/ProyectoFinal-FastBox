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
        
        // Usamos .text() en lugar de .json() para recibir la respuesta cruda
        const data = await response.text(); 

        console.log("Respuesta cruda de la API:", data);  // Mostrar la respuesta cruda en la consola

        // Mostrar la respuesta cruda en el HTML
        tarifaRespuestaDiv.innerHTML = `<pre>${data}</pre>`;
    } catch (error) {
        console.error("Hubo un error al intentar obtener las tarifas.", error);
        tarifaRespuestaDiv.innerHTML = "Hubo un error al intentar obtener las tarifas.";
    }
}
