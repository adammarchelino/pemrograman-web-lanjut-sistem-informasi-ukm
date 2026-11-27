<?php

namespace App\Livewire\Mahasiswa;

use App\Models\TiketKegiatan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detail Tiket Acara')]
class DetailTiket extends Component
{
    public TiketKegiatan $tiket;

    public function mount(string $kode_tiket): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->isActive() && $user->role === User::ROLE_MAHASISWA && ! $user->isPengurusUkm(),
            403
        );

        $this->tiket = TiketKegiatan::with([
            'registrasiKegiatan.kegiatan.ukm',
            'registrasiKegiatan.user',
            'registrasiKegiatan.presensi',
        ])
            ->where('kode_tiket', $kode_tiket)
            ->firstOrFail();

        $this->authorize('view', $this->tiket);
    }

    public function render()
    {
        return view('livewire.mahasiswa.detail-tiket');
    }
}
