<?php

namespace App\Livewire\Mahasiswa;

use App\Models\TiketKegiatan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tiket Acara Saya')]
class DaftarTiket extends Component
{
    public function mount(): void
    {
        // Tiket peserta hanya untuk mahasiswa murni. isMahasiswa() juga true untuk
        // pengurus UKM (mereka panitia acara, bukan peserta), jadi cek kolom role.
        abort_unless(Auth::user()?->role === User::ROLE_MAHASISWA, 403);
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
