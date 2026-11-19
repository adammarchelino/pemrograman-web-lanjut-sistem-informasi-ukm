import QRCode from 'qrcode';

window.renderTiketQr = async function (canvasId, kodeTiket) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !kodeTiket) return;

    try {
        await QRCode.toCanvas(canvas, kodeTiket, {
            width: 200,
            margin: 2,
        });
    } catch (error) {
        console.error('Gagal merender QR Code tiket:', error);
    }
};