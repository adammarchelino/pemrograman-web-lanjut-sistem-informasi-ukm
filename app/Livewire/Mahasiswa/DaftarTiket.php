<?php

namespace App\Livewire\Mahasiswa;

use App\Models\TiketKegiatan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DaftarTiket extends Component
{
    public function mount(): void
    {
        // Pastikan hanya pengguna dengan role mahasiswa yang dapat membuka halaman ini
        abort_unless(Auth::user()?->isMahasiswa(), 403);
    }

    public function render()
    {
        // Ambil tiket yang terhubung ke data registrasi milik mahasiswa yang sedang login
        $daftarTiket = TiketKegiatan::with([
            'registrasiKegiatan.kegiatan.ukm',
            'registrasiKegiatan.presensi',
        ])
            ->whereHas('registrasiKegiatan', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest('diterbitkan_pada')
            ->get();

        return view('livewire.mahasiswa.daftar-tiket', [
            'daftarTiket' => $daftarTiket,
        ]);
    }
}
