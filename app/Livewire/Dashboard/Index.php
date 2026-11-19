<?php

namespace App\Livewire\Dashboard;

use App\Models\Ukm;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard — SIP-UKM')]
class Index extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        $stats = null;
        $assignedUkm = $user->isPengurusUkm() ? $user->ukm()->first() : null;
        $activeUkmCount = Ukm::query()->where('status', Ukm::STATUS_AKTIF)->count();

        if ($user->isKemahasiswaan()) {
            $ukmCounts = Ukm::query()
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as aktif', [Ukm::STATUS_AKTIF])
                ->selectRaw('SUM(CASE WHEN status_pendaftaran_anggota = ? THEN 1 ELSE 0 END) as pendaftaran_dibuka', [Ukm::OPREC_DIBUKA])
                ->first();

            $userCounts = User::query()
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as mahasiswa', [User::ROLE_MAHASISWA])
                ->selectRaw('SUM(CASE WHEN role = ? OR jabatan_ukm IS NOT NULL THEN 1 ELSE 0 END) as pengurus_ukm', [User::ROLE_PENGURUS_UKM])
                ->selectRaw('SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as kemahasiswaan', [User::ROLE_KEMAHASISWAAN])
                ->first();

            $stats = [
                'ukm_total' => (int) $ukmCounts->total,
                'ukm_aktif' => (int) $ukmCounts->aktif,
                'ukm_oprec' => (int) $ukmCounts->pendaftaran_dibuka,
                'users_total' => (int) $userCounts->total,
                'users_mahasiswa' => (int) $userCounts->mahasiswa,
                'users_pengurus' => (int) $userCounts->pengurus_ukm,
                'users_kemahasiswaan' => (int) $userCounts->kemahasiswaan,
            ];
        }

        return view('livewire.dashboard.index', [
            'stats' => $stats,
            'user' => $user,
            'assignedUkm' => $assignedUkm,
            'activeUkmCount' => $activeUkmCount,
        ]);
    }
}
