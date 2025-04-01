// URL de la API de FedEx PHP
const fedexUrl = "https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/fedex.php";

// Función para hacer la llamada a la API de seguimiento
function trackPackage() {
  const trackingNumber = document.getElementById('trackingNumber').value;
  
  // Validamos que el número de seguimiento no esté vacío
  if (!trackingNumber) {
    alert('Por favor ingresa un número de seguimiento.');
    return;
  }

  // Llamada a la API de FedEx para obtener el token y los resultados del seguimiento
  fetch(fedexUrl + '?trackingNumber=' + trackingNumber)
    .then(response => response.json())
    .then(data => {
      // Llamamos a la función para mostrar los resultados del seguimiento
      displayTrackingInfo(data);
    })
    .catch(error => {
      console.error('Error al obtener los datos de la API:', error);
      alert('Hubo un error al intentar obtener la información del paquete.');
    });
}

// Función para mostrar la información de seguimiento
function displayTrackingInfo(response) {
  // Comprobamos si la respuesta contiene los datos esperados
  if (response && response.completeTrackResults && response.completeTrackResults.length > 0) {
    // Extraemos el resultado del seguimiento
    const trackingResult = response.completeTrackResults[0];
    
    // Creamos el HTML para mostrar la información de forma organizada
    let outputHTML = `
      <h2>Información de Seguimiento para ${trackingResult.trackingNumber}</h2>
      <p><strong>Estado del paquete:</strong> ${trackingResult.trackResults[0].status.description || "Información no disponible"}</p>
      <p><strong>Fecha estimada de entrega:</strong> ${trackingResult.trackResults[0].estimatedDeliveryDate || "Información no disponible"}</p>
      <p><strong>Última actualización:</strong> ${trackingResult.latestStatusDetail.description || "Información no disponible"}</p>
      
      <h3>Detalles del Origen y Destino:</h3>
      <p><strong>Origen:</strong> ${trackingResult.originLocation.city || "Información no disponible"}, ${trackingResult.originLocation.countryCode || "Información no disponible"}</p>
      <p><strong>Destino:</strong> ${trackingResult.destinationLocation.city || "Información no disponible"}, ${trackingResult.destinationLocation.countryCode || "Información no disponible"}</p>

      <h3>Información Adicional:</h3>
      <ul>`;

    // Recorremos los identificadores adicionales del paquete
    if (trackingResult.additionalTrackingInfo.packageIdentifiers.length > 0) {
      trackingResult.additionalTrackingInfo.packageIdentifiers.forEach(function(identifier) {
        outputHTML += `
          <li><strong>${identifier.type}:</strong> ${identifier.values.join(", ")}</li>
        `;
      });
    }

    // Si hay detalles de entrega, los mostramos
    if (trackingResult.deliveryDetails) {
      outputHTML += `
        <li><strong>Detalles de entrega:</strong> ${trackingResult.deliveryDetails.deliveryDate || "Información no disponible"}</li>
      `;
    }

    // Mostramos los eventos de escaneo, si existen
    if (trackingResult.scanEvents.length > 0) {
      outputHTML += `<h3>Eventos de Escaneo:</h3><ul>`;
      trackingResult.scanEvents.forEach(function(event) {
        outputHTML += `<li><strong>Evento:</strong> ${event.status.description}, <strong>Fecha:</strong> ${event.dateAndTime}</li>`;
      });
      outputHTML += `</ul>`;
    }

    // Mostramos los resultados en el HTML
    document.getElementById('trackingResult').innerHTML = outputHTML;
  } else {
    // Si no se encuentran resultados, mostramos un mensaje de error
    document.getElementById('trackingResult').innerHTML = "<p>No se encontraron resultados para este número de seguimiento.</p>";
  }
}
