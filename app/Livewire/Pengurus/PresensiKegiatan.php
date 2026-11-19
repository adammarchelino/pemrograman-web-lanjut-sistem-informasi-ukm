<?php

namespace App\Livewire\Pengurus;

use App\Models\Kegiatan;
use App\Models\RegistrasiKegiatan;
use App\Services\TiketPresensiService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PresensiKegiatan extends Component
{
    public Kegiatan $kegiatan;
    public string $kodeTiket = '';
    public string $filterStatus = 'semua';

    public function mount(Kegiatan $kegiatan): void
    {
        // 1. Cek izin saat halaman pertama kali dibuka (Gate dari AppServiceProvider)
        $this->authorize('kelola-presensi', $kegiatan);
        $this->kegiatan = $kegiatan;
    }

    public function prosesCheckIn(TiketPresensiService $service): void
    {
        // 2. Ulangi cek izin saat tombol submit ditekan
        $this->authorize('kelola-presensi', $this->kegiatan);

        $this->validate([
            'kodeTiket' => ['required', 'string'],
        ], [
            'kodeTiket.required' => 'Masukkan atau pindai kode tiket terlebih dahulu.',
        ]);

        // 3. Panggil Service untuk validasi tiket & catat kehadiran ke database
        $presensi = $service->validasiDanCatatPresensi(
            $this->kodeTiket,
            $this->kegiatan,
            Auth::user()
        );

        // 4. Ambil nama peserta (cek akun User dulu; jika peserta umum/null, ambil dari kolom nama_peserta)
        $registrasi = $presensi->registrasiKegiatan;
        $namaPeserta = $registrasi->user?->name ?? $registrasi->nama_peserta ?? 'Peserta';

        // 5. Tampilkan pop-up notifikasi sukses bawaan Flux UI
        Flux::toast(
            variant: 'success',
            heading: 'Check-in Berhasil',
            text: "Presensi tercatat untuk {$namaPeserta}."
        );

        // 6. Kosongkan kotak input agar siap untuk tiket berikutnya
        $this->reset('kodeTiket');
    }

    public function render()
    {
        // Ambil seluruh peserta kegiatan ini yang status pendaftarannya 'terdaftar'
        $semuaPeserta = RegistrasiKegiatan::with(['user', 'tiketKegiatan', 'presensi'])
            ->where('kegiatan_id', $this->kegiatan->id)
            ->where('status', 'terdaftar')
            ->get();

        $totalTerdaftar = $semuaPeserta->count();
        $totalHadir = $semuaPeserta->filter(fn ($item) => $item->presensi !== null)->count();
        $persentaseKehadiran = $totalTerdaftar > 0
            ? round(($totalHadir / $totalTerdaftar) * 100)
            : 0;

        // Saring baris tabel sesuai tombol filter yang dipilih pengurus
        $pesertaTampil = match ($this->filterStatus) {
            'hadir'       => $semuaPeserta->filter(fn ($item) => $item->presensi !== null),
            'tidak_hadir' => $semuaPeserta->filter(fn ($item) => $item->presensi === null),
            default       => $semuaPeserta,
        };

        return view('livewire.pengurus.presensi-kegiatan', [
            'pesertaTampil'       => $pesertaTampil,
            'totalTerdaftar'      => $totalTerdaftar,
            'totalHadir'          => $totalHadir,
            'persentaseKehadiran' => $persentaseKehadiran,
        ]);
    }
}