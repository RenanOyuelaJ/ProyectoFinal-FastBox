const API_URL = "https://crispy-guacamole-qg9x54x6wxj29jj4-8000.app.github.dev/fedex.php"; // URL de tu PHP

function trackPackage() {
    const trackingNumber = document.getElementById("trackingNumber").value;
    const resultDiv = document.getElementById("result");

    if (!trackingNumber) {
        resultDiv.innerHTML = "<p style='color: red;'>Por favor, ingresa un número de rastreo.</p>";
        return;
    }

    // Enviar solicitud al servidor PHP
    fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `tracking_number=${trackingNumber}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            resultDiv.innerHTML = `<p style='color: red;'>Error: ${data.error}</p>`;
        } else {
            resultDiv.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<p style='color: red;'>Error en la solicitud: ${error.message}</p>`;
    });
}
