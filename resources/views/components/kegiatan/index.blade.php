<?php

namespace App\Livewire\Kegiatan;

use App\Models\Kegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manajemen Kegiatan')]
class Index extends Component
{
    public $search = '';

    public $tampilkanForm = false;
    public $tampilkanDetail = false;

    public $kegiatanId = null;

    public $nama = '';
    public $deskripsi = '';
    public $mulai_pada = '';
    public $selesai_pada = '';
    public $lokasi = '';
    public $kapasitas = 50;
    public $audiens = 'mahasiswa_poliwangi';
    public $pendaftaran_dibuka_pada = '';
    public $pendaftaran_ditutup_pada = '';
    public $status = 'draf';

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'mulai_pada' => ['required', 'date'],
            'selesai_pada' => ['required', 'date', 'after:mulai_pada'],
            'lokasi' => ['required', 'string', 'max:255'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'audiens' => [
                'required',
                Rule::in(['mahasiswa_poliwangi', 'umum']),
            ],
            'pendaftaran_dibuka_pada' => ['nullable', 'date'],
            'pendaftaran_ditutup_pada' => [
                'nullable',
                'date',
                'after:pendaftaran_dibuka_pada',
            ],
            'status' => [
                'required',
                Rule::in(['draf', 'dipublikasikan', 'dibatalkan', 'selesai']),
            ],
        ];
    }

    private function pengurus()
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->role === 'pengurus_ukm' && $user->ukm_id,
            403,
            'Hanya pengurus UKM yang dapat mengelola kegiatan.'
        );

        return $user;
    }


    public function render()
    {
        $user = $this->pengurus();

        $kegiatan = Kegiatan::query()
            ->where('ukm_id', $user->ukm_id)
            ->withCount([
                'registrasiKegiatan as peserta_terdaftar_count' => function ($query) {
                    $query->where('status', 'terdaftar');
                },
            ])
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->get();

        $detailKegiatan = $this->tampilkanDetail && $this->kegiatanId
            ? $this->ambilKegiatan($this->kegiatanId)->loadCount([
                'registrasiKegiatan as peserta_terdaftar_count' => function ($query) {
                    $query->where('status', 'terdaftar');
                },
            ])
            : null;

        return view('livewire.kegiatan.index', [
            'kegiatanList' => $kegiatan,
            'detailKegiatan' => $detailKegiatan,
        ]);
    }


    public function tambah()
    {
        $this->resetForm();

        $this->tampilkanForm = true;
        $this->tampilkanDetail = false;
    }

    public function edit($id)
    {
        $this->resetValidation();

        $kegiatan = $this->ambilKegiatan($id);

        $this->kegiatanId = $kegiatan->id;
        $this->nama = $kegiatan->nama;
        $this->deskripsi = $kegiatan->deskripsi;
        $this->mulai_pada = $kegiatan->mulai_pada->format('Y-m-d\TH:i');
        $this->selesai_pada = $kegiatan->selesai_pada->format('Y-m-d\TH:i');
        $this->lokasi = $kegiatan->lokasi;
        $this->kapasitas = $kegiatan->kapasitas;
        $this->audiens = $kegiatan->audiens;
        $this->pendaftaran_dibuka_pada = $kegiatan->pendaftaran_dibuka_pada?->format('Y-m-d\TH:i') ?? '';
        $this->pendaftaran_ditutup_pada = $kegiatan->pendaftaran_ditutup_pada?->format('Y-m-d\TH:i') ?? '';
        $this->status = $kegiatan->status;

        $this->tampilkanForm = true;
        $this->tampilkanDetail = false;
    }

    public function simpan()
    {
        $user = $this->pengurus();

        $data = $this->validate();

        $data['ukm_id'] = $user->ukm_id;
        $data['pendaftaran_dibuka_pada'] = $data['pendaftaran_dibuka_pada'] ?: null;
        $data['pendaftaran_ditutup_pada'] = $data['pendaftaran_ditutup_pada'] ?: null;

        DB::transaction(function () use ($data) {
            if ($this->kegiatanId) {
                $kegiatan = $this->ambilKegiatan($this->kegiatanId);

                $jumlahPeserta = $kegiatan->registrasiKegiatan()
                    ->where('status', 'terdaftar')
                    ->count();

                if ($data['kapasitas'] < $jumlahPeserta) {
                    $this->addError(
                        'kapasitas',
                        "Kapasitas tidak boleh kurang dari jumlah peserta terdaftar ({$jumlahPeserta})."
                    );

                    return;
                }

                $kegiatan->update($data);
            } else {
                Kegiatan::create($data);
            }
        });

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        session()->flash(
            'success',
            $this->kegiatanId
                ? 'Kegiatan berhasil diperbarui.'
                : 'Kegiatan berhasil ditambahkan.'
        );

        $this->resetForm();
    }

    public function detail($id)
    {
        $this->resetValidation();

        $this->kegiatanId = $id;
        $this->tampilkanDetail = true;
        $this->tampilkanForm = false;
    }

    public function hapus($id)
    {
        $kegiatan = $this->ambilKegiatan($id);

        if ($kegiatan->registrasiKegiatan()->exists()) {
            session()->flash(
                'error',
                'Kegiatan tidak dapat dihapus karena sudah memiliki data registrasi.'
            );

            return;
        }

        $kegiatan->delete();

        session()->flash('success', 'Kegiatan berhasil dihapus.');
    }

    public function tutup()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'kegiatanId',
            'nama',
            'deskripsi',
            'mulai_pada',
            'selesai_pada',
            'lokasi',
            'kapasitas',
            'pendaftaran_dibuka_pada',
            'pendaftaran_ditutup_pada',
        ]);

        $this->audiens = 'mahasiswa_poliwangi';
        $this->status = 'draf';
        $this->kapasitas = 50;

        $this->tampilkanForm = false;
        $this->tampilkanDetail = false;

        $this->resetValidation();
    }

    private function ambilKegiatan($id): Kegiatan
    {
        $user = $this->pengurus();

        return Kegiatan::query()
            ->where('ukm_id', $user->ukm_id)
            ->findOrFail($id);
    }
}
