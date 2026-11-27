// Pemindai QR via kamera untuk halaman presensi pengurus (komponen Alpine).
// qr-scanner dimuat saat dibutuhkan saja, tetap dibundel lokal oleh Vite (tanpa CDN).

const JEDA_KODE_SAMA_MS = 2500;

const pesanErrorKamera = async () => {
    // qr-scanner menyamarkan semua kegagalan kamera menjadi "Camera not found.",
    // jadi penyebab aslinya ditanyakan langsung ke browser.
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        stream.getTracks().forEach((track) => track.stop());
    } catch (error) {
        if (error.name === 'NotAllowedError' || error.name === 'SecurityError') {
            return 'Izin kamera ditolak. Izinkan akses kamera di pengaturan browser, atau gunakan input manual.';
        }

        if (error.name === 'NotFoundError') {
            return 'Kamera tidak ditemukan di perangkat ini. Gunakan input manual.';
        }

        if (error.name === 'NotReadableError') {
            return 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi tersebut lalu coba lagi.';
        }
    }

    return 'Kamera tidak dapat dibuka. Coba lagi atau gunakan input manual.';
};

export const buatPemindaiQr = (muatScanner = () => import('qr-scanner')) => () => {
    // Disimpan di luar state Alpine agar instance scanner tidak dibungkus Proxy.
    let scanner = null;
    let sesi = 0;
    let kodeTerakhir = '';
    let terakhirTerlihat = 0;
    let matikan = null;

    return {
        aktif: false,
        memproses: false,
        pesanError: '',

        init() {
            matikan = () => this.hentikan();
            document.addEventListener('livewire:navigating', matikan);
            window.addEventListener('pagehide', matikan);
        },

        destroy() {
            this.hentikan();
            document.removeEventListener('livewire:navigating', matikan);
            window.removeEventListener('pagehide', matikan);
        },

        toggle() {
            if (this.aktif) {
                this.hentikan();

                return;
            }

            return this.mulai();
        },

        async mulai() {
            this.pesanError = '';

            if (! window.isSecureContext) {
                this.pesanError = 'Kamera hanya dapat digunakan melalui HTTPS atau localhost. Gunakan input manual.';

                return;
            }

            if (! navigator.mediaDevices?.getUserMedia) {
                this.pesanError = 'Browser ini tidak mendukung akses kamera. Gunakan input manual.';

                return;
            }

            // Nomor sesi membatalkan proses mulai yang keburu ditutup pengguna.
            const sesiIni = ++sesi;
            this.aktif = true;
            kodeTerakhir = '';

            let instance = null;

            try {
                const { default: QrScanner } = await muatScanner();

                // Tunggu video benar-benar tampil sebelum kamera dipasang.
                await this.$nextTick();

                if (sesiIni !== sesi) return;

                instance = new QrScanner(this.$refs.video, (hasil) => this.terbaca(hasil.data), {
                    returnDetailedScanResult: true,
                    preferredCamera: 'environment',
                    highlightScanRegion: true,
                    highlightCodeOutline: true,
                    maxScansPerSecond: 5,
                });
                scanner = instance;

                await instance.start();

                if (sesiIni !== sesi) instance.destroy();
            } catch (error) {
                if (sesiIni !== sesi) return;

                console.error('Gagal membuka kamera:', error);
                this.hentikan();
                this.pesanError = await pesanErrorKamera();
            }
        },

        hentikan() {
            sesi++;
            scanner?.destroy(); // menghentikan stream kamera sekaligus overlay
            scanner = null;
            this.aktif = false;
            this.memproses = false;
        },

        async terbaca(data) {
            const kode = String(data ?? '').trim();

            if (! kode || ! this.aktif) return;

            const sekarang = Date.now();
            const masihQrSama = kode === kodeTerakhir && sekarang - terakhirTerlihat < JEDA_KODE_SAMA_MS;

            // Selama QR yang sama masih disorot, jeda terus diperpanjang.
            if (masihQrSama) {
                terakhirTerlihat = sekarang;

                return;
            }

            if (this.memproses) return;

            kodeTerakhir = kode;
            terakhirTerlihat = sekarang;
            this.memproses = true;

            try {
                // false: nilai dikirim bersama pemanggilan prosesCheckIn (satu request).
                this.$wire.set('kodeTiket', kode, false);
                await this.$wire.prosesCheckIn();
            } catch (error) {
                console.error('Gagal mengirim hasil pindai:', error);
                kodeTerakhir = ''; // boleh dipindai ulang
            } finally {
                terakhirTerlihat = Date.now();
                this.memproses = false;
            }
        },
    };
};
