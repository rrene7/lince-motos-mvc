(function () {
    'use strict';

    var container = document.getElementById('moto-qr');
    var status = document.getElementById('qr-status');
    var downloadButton = document.querySelector('[data-download-qr]');
    var copyButton = document.querySelector('[data-copy-qr]');
    var printButton = document.querySelector('[data-print-ficha]');

    if (!container) return;

    var qrUrl = container.dataset.qrUrl || window.location.href;
    var qrName = container.dataset.qrName || 'motocicleta';

    function setStatus(message, isError) {
        if (!status) return;
        status.textContent = message;
        status.classList.toggle('text-danger', Boolean(isError));
    }

    function renderQr() {
        if (typeof window.QRCode !== 'function') {
            setStatus('No se pudo cargar el generador de QR.', true);
            return;
        }

        container.innerHTML = '';
        new window.QRCode(container, {
            text: qrUrl,
            width: 190,
            height: 190,
            colorDark: '#111111',
            colorLight: '#ffffff',
            correctLevel: window.QRCode.CorrectLevel.M
        });

        setStatus('QR listo para escanear.', false);
        if (downloadButton) downloadButton.disabled = false;
    }

    function loadFallbackLibrary() {
        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
        script.async = true;
        script.onload = renderQr;
        script.onerror = function () {
            setStatus('No se pudo generar el QR. Ejecute nuevamente bash install.sh con conexión a Internet.', true);
        };
        document.head.appendChild(script);
    }

    if (typeof window.QRCode === 'function') renderQr();
    else loadFallbackLibrary();

    if (copyButton) {
        copyButton.addEventListener('click', function () {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(qrUrl).then(function () {
                    copyButton.textContent = 'Enlace copiado';
                });
                return;
            }

            var helper = document.createElement('textarea');
            helper.value = qrUrl;
            helper.setAttribute('readonly', '');
            helper.style.position = 'fixed';
            helper.style.opacity = '0';
            document.body.appendChild(helper);
            helper.select();
            document.execCommand('copy');
            helper.remove();
            copyButton.textContent = 'Enlace copiado';
        });
    }

    if (downloadButton) {
        downloadButton.addEventListener('click', function () {
            var canvas = container.querySelector('canvas');
            var image = container.querySelector('img');
            var source = canvas ? canvas.toDataURL('image/png') : (image ? image.src : '');
            if (!source) return;

            var link = document.createElement('a');
            link.href = source;
            link.download = 'QR-' + qrName.replace(/[^a-z0-9_-]/gi, '-') + '.png';
            document.body.appendChild(link);
            link.click();
            link.remove();
        });
    }

    if (printButton) {
        printButton.addEventListener('click', function () {
            window.print();
        });
    }
}());
