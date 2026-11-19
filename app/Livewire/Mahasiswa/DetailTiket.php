<?php

namespace App\Livewire\Mahasiswa;

use App\Models\TiketKegiatan;
use Livewire\Component;

class DetailTiket extends Component
{
    public TiketKegiatan $tiket;

    public function mount(string $kode_tiket): void
    {
        // 1. Cari tiket berdasarkan kode_tiket (bukan ID angka) beserta data relasinya
        $this->tiket = TiketKegiatan::with([
            'registrasiKegiatan.kegiatan.ukm',
            'registrasiKegiatan.user',
            'registrasiKegiatan.presensi',
        ])
            ->where('kode_tiket', $kode_tiket)
            ->firstOrFail();

        // 2. Jalankan pengecekan hak akses di TiketKegiatanPolicy::view()
        $this->authorize('view', $this->tiket);
    }

    public function render()
    {
        return view('livewire.mahasiswa.detail-tiket');
    }
}