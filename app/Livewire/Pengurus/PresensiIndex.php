<?php

namespace App\Livewire\Pengurus;

use App\Models\Kegiatan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Presensi Acara · SIP-UKM')]
class PresensiIndex extends Component
{
    public function mount(): void
    {
        $user = Auth::user();

        abort_unless(
            $user?->isPengurusUkm() && $user->ukm_id !== null,
            403
        );
    }

    public function render()
    {
        $user = Auth::user();

        $kegiatan = Kegiatan::query()
            ->where('ukm_id', $user->ukm_id)
            ->withCount([
                'registrasiKegiatan as jumlah_terdaftar' => function ($query) {
                    $query->where('status', 'terdaftar');
                },
                'registrasiKegiatan as jumlah_hadir' => function ($query) {
                    $query->where('status', 'terdaftar')->whereHas('presensi');
                },
            ])
            ->orderByDesc('mulai_pada')
            ->get();

        return view('livewire.pengurus.presensi-index', [
            'kegiatan' => $kegiatan,
        ]);
    }
}
