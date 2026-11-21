<?php

namespace App\Livewire\Kemahasiswaan;

use App\Models\PendaftaranAnggota;
use App\Models\Ukm;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pemantauan Pengajuan Anggota — SIP-UKM')]
class PengajuanAnggota extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterUkm = '';

    public string $filterStatus = 'semua';

    public function mount(): void
    {
        abort_unless(auth()->user()->isKemahasiswaan(), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterUkm(): void
    {
        $this->resetPage();
    }

    public function setFilterStatus(string $status): void
    {
        if (! in_array($status, ['semua', 'pending', 'diterima', 'ditolak'], true)) {
            return;
        }

        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'filterUkm');
        $this->filterStatus = 'semua';
        $this->resetPage();
    }

    public function render(): View
    {
        $baseQuery = PendaftaranAnggota::query();

        $statistik = [
            'semua' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'diterima' => (clone $baseQuery)->where('status', 'diterima')->count(),
            'ditolak' => (clone $baseQuery)->where('status', 'ditolak')->count(),
        ];

        $pengajuan = PendaftaranAnggota::query()
            ->with(['user', 'ukm', 'pemutus'])
            ->when($this->filterUkm !== '', fn ($query) => $query->where('ukm_id', (int) $this->filterUkm))
            ->when(
                in_array($this->filterStatus, ['pending', 'diterima', 'ditolak'], true),
                fn ($query) => $query->where('status', $this->filterStatus)
            )
            ->when(trim($this->search) !== '', function ($query) {
                $term = '%'.trim($this->search).'%';

                $query->whereHas('user', fn ($user) => $user
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('nim', 'like', $term));
            })
            ->latest('diajukan_pada')
            ->paginate(15);

        $ukmList = Ukm::query()->orderBy('nama')->get(['id', 'nama']);

        $rekap = Ukm::query()
            ->withCount([
                'keanggotaan as anggota_aktif_count' => fn ($query) => $query->where('status', 'aktif'),
                'pendaftaranAnggota as pengajuan_count',
                'pendaftaranAnggota as pengajuan_pending_count' => fn ($query) => $query->where('status', 'pending'),
                'pendaftaranAnggota as pengajuan_diterima_count' => fn ($query) => $query->where('status', 'diterima'),
                'pendaftaranAnggota as pengajuan_ditolak_count' => fn ($query) => $query->where('status', 'ditolak'),
            ])
            ->orderBy('nama')
            ->get();

        return view('livewire.kemahasiswaan.pengajuan-anggota', [
            'pengajuan' => $pengajuan,
            'statistik' => $statistik,
            'ukmList' => $ukmList,
            'rekap' => $rekap,
        ]);
    }
}
