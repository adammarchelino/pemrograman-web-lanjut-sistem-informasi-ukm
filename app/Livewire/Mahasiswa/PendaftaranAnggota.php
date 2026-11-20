<?php

namespace App\Livewire\Mahasiswa;

use App\Models\Keanggotaan;
use App\Models\PendaftaranAnggota as ModelPendaftaranAnggota;
use App\Models\Ukm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pendaftaran Anggota UKM — SIP-UKM')]
class PendaftaranAnggota extends Component
{
    #[Url(as: 'ukm')]
    public ?int $ukmId = null;
    public string $alasan = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->role === 'mahasiswa', 403);
    }

    public function ajukan(): void
    {
        $user = auth()->user();
        abort_unless($user->role === 'mahasiswa', 403);

        $data = $this->validate([
            'ukmId' => ['required', 'integer', 'exists:ukm,id'],
            'alasan' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'ukmId.required' => 'Pilih UKM terlebih dahulu.',
            'alasan.required' => 'Alasan bergabung wajib diisi.',
            'alasan.min' => 'Alasan bergabung minimal 10 karakter.',
        ]);

        DB::transaction(function () use ($user, $data) {
            $ukm = Ukm::query()->lockForUpdate()->findOrFail($data['ukmId']);

            if (! $ukm->isAktif() || ! $ukm->isPendaftaranDibuka()) {
                throw ValidationException::withMessages(['ukmId' => 'Pendaftaran UKM ini sedang tidak tersedia.']);
            }

            $now = now();
            if (($ukm->pendaftaran_anggota_dibuka_pada && $now->lt($ukm->pendaftaran_anggota_dibuka_pada))
                || ($ukm->pendaftaran_anggota_ditutup_pada && $now->gt($ukm->pendaftaran_anggota_ditutup_pada))) {
                throw ValidationException::withMessages(['ukmId' => 'Pendaftaran UKM ini berada di luar periode penerimaan.']);
            }

            if (Keanggotaan::query()->where('user_id', $user->id)->where('ukm_id', $ukm->id)->where('status', 'aktif')->exists()) {
                throw ValidationException::withMessages(['ukmId' => 'Anda sudah menjadi anggota aktif UKM ini.']);
            }

            if (ModelPendaftaranAnggota::query()
                ->where('user_id', $user->id)
                ->where('ukm_id', $ukm->id)
                ->whereIn('status', ['pending', 'diterima'])
                ->exists()) {
                throw ValidationException::withMessages(['ukmId' => 'Anda sudah memiliki pengajuan aktif untuk UKM ini.']);
            }

            ModelPendaftaranAnggota::query()->create([
                'user_id' => $user->id,
                'ukm_id' => $ukm->id,
                'jawaban_formulir' => ['alasan' => trim($data['alasan'])],
                'status' => 'pending',
                'diajukan_pada' => $now,
            ]);
        });

        $this->reset(['ukmId', 'alasan']);
        session()->flash('status', 'Pengajuan keanggotaan berhasil dikirim.');
    }

    public function render(): View
    {
        $user = auth()->user();
        $ukms = Ukm::query()
            ->where('status', Ukm::STATUS_AKTIF)
            ->where('status_pendaftaran_anggota', Ukm::OPREC_DIBUKA)
            ->whereDoesntHave('pendaftaranAnggota', fn ($q) => $q->where('user_id', $user->id)->whereIn('status', ['pending', 'diterima']))
            ->whereDoesntHave('keanggotaan', fn ($q) => $q->where('user_id', $user->id)->where('status', 'aktif'))
            ->orderBy('nama')
            ->get();

        $riwayat = ModelPendaftaranAnggota::query()
            ->with('ukm')
            ->where('user_id', $user->id)
            ->latest('diajukan_pada')
            ->get();

        return view('livewire.mahasiswa.pendaftaran-anggota', compact('ukms', 'riwayat'));
    }
}
