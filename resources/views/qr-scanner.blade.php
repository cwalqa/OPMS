<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.min.js"></script>
    <style>
        #reader {
            width: 100%;
            max-width: 400px;
            margin: auto;
        }
    </style>
</head>
<body>
    <h2>QR Code Scanner</h2>
    <div id="reader"></div>
    <p><strong>Scanned QR Code:</strong> <span id="qr-result">None</span></p>
    
    
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('qr-result').innerText = decodedText;
            console.log(`Code scanned = ${decodedText}`, decodedResult);
            
            fetch("{{ route('qr.scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ qr_code: decodedText })
            })
            .then(response => response.json())
            .then(data => console.log("Server Response:", data))
            .catch(error => console.error("Error:", error));
        }

        function onScanError(errorMessage) {
            console.warn(`QR scan error: ${errorMessage}`);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: { width: 250, height: 250 } },
            false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanError);

        document.getElementById('file-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.getElementById('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0, img.width, img.height);

                    const imageData = ctx.getImageData(0, 0, img.width, img.height);
                    const qrCode = jsQR(imageData.data, img.width, img.height);

                    if (qrCode) {
                        document.getElementById('qr-result').innerText = qrCode.data;
                        console.log("QR Code detected:", qrCode.data);
                    } else {
                        document.getElementById('qr-result').innerText = "No QR code found.";
                        console.log("No QR code detected.");
                    }
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>
</html>
