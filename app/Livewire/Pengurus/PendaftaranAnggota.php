<?php

namespace App\Livewire\Pengurus;

use App\Models\PendaftaranAnggota as ModelPendaftaranAnggota;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pengajuan Anggota UKM — SIP-UKM')]
class PendaftaranAnggota extends Component
{
    use WithPagination;

    public string $filter = 'pending';

    public array $catatan = [];

    public function mount(): void
    {
        abort_unless(
            auth()->user()->isPengurusUkm() && auth()->user()->ukm_id !== null,
            403
        );
    }

    public function setFilter(string $filter): void
    {
        if (! in_array($filter, ['pending', 'diterima', 'ditolak', 'semua'], true)) {
            return;
        }

        $this->filter = $filter;
        $this->resetPage();
    }

    private function ensureCanProcess(ModelPendaftaranAnggota $application): void
    {
        abort_unless(auth()->user()->canManageUkm($application->ukm), 403);
    }

    public function terima(int $id): void
    {
        $application = ModelPendaftaranAnggota::query()->with('ukm')->findOrFail($id);
        $this->ensureCanProcess($application);

        $application->terima(auth()->user());

        session()->flash('status', 'Pengajuan berhasil diterima dan mahasiswa tercatat sebagai anggota aktif.');
    }

    public function tolak(int $id): void
    {
        $application = ModelPendaftaranAnggota::query()->with('ukm')->findOrFail($id);
        $this->ensureCanProcess($application);
        $note = trim((string) ($this->catatan[$id] ?? ''));

        if (mb_strlen($note) > 2000) {
            throw ValidationException::withMessages(["catatan.$id" => 'Catatan maksimal 2000 karakter.']);
        }

        DB::transaction(function () use ($application, $note) {
            $locked = ModelPendaftaranAnggota::query()->lockForUpdate()->findOrFail($application->id);
            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Pengajuan ini sudah diproses sebelumnya.']);
            }

            $locked->forceFill([
                'status' => 'ditolak',
                'catatan_pengurus' => $note !== '' ? $note : null,
                'diputuskan_oleh' => auth()->id(),
                'diputuskan_pada' => now(),
            ])->save();
        });

        unset($this->catatan[$id]);
        session()->flash('status', 'Pengajuan berhasil ditolak.');
    }

    public function render(): View
    {
        $user = auth()->user();
        $baseQuery = ModelPendaftaranAnggota::query()
            ->where('ukm_id', $user->ukm_id);

        $statistik = [
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'diterima' => (clone $baseQuery)->where('status', 'diterima')->count(),
            'ditolak' => (clone $baseQuery)->where('status', 'ditolak')->count(),
            'semua' => (clone $baseQuery)->count(),
        ];

        $query = ModelPendaftaranAnggota::query()
            ->with(['user', 'ukm', 'pemutus', 'keanggotaan'])
            ->where('ukm_id', $user->ukm_id)
            ->when(in_array($this->filter, ['pending', 'diterima', 'ditolak'], true), fn ($q) => $q->where('status', $this->filter))
            ->latest('diajukan_pada');

        return view('livewire.pengurus.pendaftaran-anggota', [
            'pengajuan' => $query->paginate(15),
            'statistik' => $statistik,
        ]);
    }
}
