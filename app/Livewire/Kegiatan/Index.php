<?php

namespace App\Livewire\Kegiatan;

use App\Models\Kegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kegiatan UKM · SIP-UKM')]
class Index extends Component
{
    public bool $tampilkanForm = false;

    public bool $tampilkanDetail = false;

    public ?int $editKegiatanId = null;

    public ?int $detailKegiatanId = null;

    public string $nama = '';

    public string $deskripsi = '';

    public string $mulai_pada = '';

    public string $selesai_pada = '';

    public string $lokasi = '';

    public string $kapasitas = '';

    public string $audiens = 'mahasiswa_poliwangi';

    public string $pendaftaran_dibuka_pada = '';

    public string $pendaftaran_ditutup_pada = '';

    public string $status = 'draf';

    public function bukaForm(): void
    {
        $user = Auth::user();

        abort_unless(
            $user
                && $user->role === 'pengurus_ukm'
                && $user->ukm_id !== null,
            403
        );

        // Pastikan form kembali ke mode Tambah.
        $this->tutupForm();

        $this->editKegiatanId = null;
        $this->detailKegiatanId = null;

        $this->tampilkanForm = true;
    }

    public function tutupForm(): void
    {
        $this->reset([
            'nama',
            'deskripsi',
            'mulai_pada',
            'selesai_pada',
            'lokasi',
            'kapasitas',
            'pendaftaran_dibuka_pada',
            'pendaftaran_ditutup_pada',
        ]);

        $this->resetValidation();

        $this->audiens = 'mahasiswa_poliwangi';
        $this->status = 'draf';

        $this->editKegiatanId = null;
        $this->tampilkanForm = false;
    }

    public function editKegiatan(int $id): void
    {
        $user = Auth::user();

        abort_unless(
            $user
                && $user->role === 'pengurus_ukm'
                && $user->ukm_id !== null,
            403
        );

        // Hanya kegiatan dari UKM pengurus yang boleh diedit.
        $kegiatan = Kegiatan::query()
            ->where('ukm_id', $user->ukm_id)
            ->findOrFail($id);

        $this->resetValidation();

        $this->editKegiatanId = $kegiatan->id;

        $this->nama = $kegiatan->nama;
        $this->deskripsi = $kegiatan->deskripsi ?? '';

        $this->mulai_pada =
            $kegiatan->mulai_pada->format('Y-m-d\TH:i');

        $this->selesai_pada =
            $kegiatan->selesai_pada->format('Y-m-d\TH:i');

        $this->lokasi = $kegiatan->lokasi;
        $this->kapasitas = (string) $kegiatan->kapasitas;
        $this->audiens = $kegiatan->audiens;

        $this->pendaftaran_dibuka_pada =
            $kegiatan->pendaftaran_dibuka_pada
                ?->format('Y-m-d\TH:i') ?? '';

        $this->pendaftaran_ditutup_pada =
            $kegiatan->pendaftaran_ditutup_pada
                ?->format('Y-m-d\TH:i') ?? '';

        $this->status = $kegiatan->status;

        // Tutup detail jika sebelumnya sedang dibuka.
        $this->detailKegiatanId = null;

        $this->tampilkanForm = true;
    }

    public function simpan(): void
    {
        $user = Auth::user();

        abort_unless(
            $user
                && $user->role === 'pengurus_ukm'
                && $user->ukm_id !== null,
            403
        );

        // Jika edit, cari kegiatan milik UKM pengurus.
        $kegiatan = null;

        if ($this->editKegiatanId !== null) {
            $kegiatan = Kegiatan::query()
                ->where('ukm_id', $user->ukm_id)
                ->findOrFail($this->editKegiatanId);
        }

        $data = $this->validate([
            'nama' => [
                'required',
                'string',
                'max:150',
            ],
            'deskripsi' => [
                'required',
                'string',
            ],
            'mulai_pada' => [
                'required',
                'date',
            ],
            'selesai_pada' => [
                'required',
                'date',
                'after:mulai_pada',
            ],
            'lokasi' => [
                'required',
                'string',
                'max:255',
            ],
            'kapasitas' => [
                'required',
                'integer',
                'min:1',
            ],
            'audiens' => [
                'required',
                Rule::in(['mahasiswa_poliwangi', 'umum']),
            ],
            'pendaftaran_dibuka_pada' => [
                'nullable',
                'date',
                'before_or_equal:pendaftaran_ditutup_pada',
            ],
            'pendaftaran_ditutup_pada' => [
                'nullable',
                'date',
                'before_or_equal:mulai_pada',
            ],
            'status' => [
                'required',
                Rule::in(['draf', 'dipublikasikan']),
            ],
        ]);

        // Relasi UKM berasal dari user yang login.
        $data['ukm_id'] = $user->ukm_id;

        $data['pendaftaran_dibuka_pada'] =
            $data['pendaftaran_dibuka_pada'] ?: null;

        $data['pendaftaran_ditutup_pada'] =
            $data['pendaftaran_ditutup_pada'] ?: null;

        if ($kegiatan !== null) {

            // MODE EDIT
            $kegiatan->update($data);

            $pesan = 'Kegiatan berhasil diperbarui!';
        } else {

            // MODE TAMBAH
            Kegiatan::create($data);

            $pesan = 'Kegiatan berhasil ditambahkan!';
        }

        $this->tutupForm();

        session()->flash('success', $pesan);
    }

    public function lihatDetail(int $id): void
    {
        $user = Auth::user();

        $query = Kegiatan::query();

        // Pengurus hanya bisa melihat kegiatan UKM sendiri.
        if (
            $user?->role === 'pengurus_ukm'
            && $user->ukm_id !== null
        ) {
            $query->where('ukm_id', $user->ukm_id);
        } else {
            // Mahasiswa hanya melihat kegiatan yang dipublikasikan.
            $query->where('status', 'dipublikasikan');
        }

        $kegiatan = $query->findOrFail($id);

        $this->detailKegiatanId = $kegiatan->id;
        $this->tampilkanDetail = true;
    }

    public function tutupDetail(): void
    {
        $this->detailKegiatanId = null;
        $this->tampilkanDetail = false;
    }

    public function updatedTampilkanDetail(bool $value): void
    {
        if (! $value) {
            $this->detailKegiatanId = null;
        }
    }

    public function updatedTampilkanForm(bool $value): void
    {
        if (! $value) {
            $this->tutupForm();
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Menampilkan Kegiatan UKM
        $query = Kegiatan::query()
            ->with('ukm')
            ->withCount([
                'registrasiKegiatan as jumlah_peserta' => function ($query) {
                    $query->where('status', 'terdaftar');
                },
            ]);

        if (
            $user?->role === 'pengurus_ukm'
            && $user->ukm_id !== null
        ) {
            $query->where('ukm_id', $user->ukm_id);
        } else {
            $query->where('status', 'dipublikasikan');
        }

        $detail = null;

        if ($this->detailKegiatanId !== null) {
            $detailQuery = Kegiatan::query()->with('ukm');

            if (
                $user?->role === 'pengurus_ukm'
                && $user->ukm_id !== null
            ) {
                $detailQuery->where('ukm_id', $user->ukm_id);
            } else {
                $detailQuery->where('status', 'dipublikasikan');
            }

            $detail = $detailQuery
                ->find($this->detailKegiatanId);
        }

        return view('livewire.kegiatan.index', [
            'kegiatan' => $query
                ->orderByDesc('mulai_pada')
                ->get(),

            'bolehKelola' => $user?->role === 'pengurus_ukm'
                && $user->ukm_id !== null,

            'detail' => $detail,
        ]);
    }
}
