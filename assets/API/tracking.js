// Definir la URL base manualmente
const BASE_URL = 'https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev';

// Función para rastrear el paquete
function trackPackage() {
    const trackingNumber = document.getElementById('trackingNumber').value;

    if (!trackingNumber) {
        alert('Por favor, ingresa un número de rastreo');
        return;
    }

    fetch(`${BASE_URL}/fedex.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ trackingNumber: trackingNumber })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('result').innerHTML = JSON.stringify(data, null, 2);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
