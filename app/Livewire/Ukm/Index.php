<?php

namespace App\Livewire\Ukm;

use App\Models\Ukm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Data UKM — SIP-UKM')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterPendaftaran = '';

    // Modal Visibility Flags
    public bool $showFormModal = false;

    public bool $showDetailModal = false;

    public bool $showDeactivateModal = false;

    public bool $showActivateModal = false;

    // Active UKM / Edit ID
    public ?int $editingId = null;

    public ?int $selectedUkmId = null;

    public ?TemporaryUploadedFile $logoUpload = null;

    public ?string $storedLogoPreviewUrl = null;

    // Form Data
    public array $form = [
        'nama' => '',
        'logo' => '',
        'deskripsi' => '',
        'visi' => '',
        'misi' => '',
        'email_kontak' => '',
        'nomor_kontak' => '',
        'status' => Ukm::STATUS_AKTIF,
        'status_pendaftaran_anggota' => Ukm::OPREC_DITUTUP,
        'pendaftaran_anggota_dibuka_pada' => '',
        'pendaftaran_anggota_ditutup_pada' => '',
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Ukm::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPendaftaran(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterStatus', 'filterPendaftaran']);
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'form.nama' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ukm', 'nama')->ignore($this->editingId),
            ],
            'logoUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'form.deskripsi' => ['required', 'string'],
            'form.visi' => ['nullable', 'string'],
            'form.misi' => ['nullable', 'string'],
            'form.email_kontak' => ['nullable', 'email', 'max:255'],
            'form.nomor_kontak' => ['nullable', 'string', 'max:30'],
            'form.status' => ['required', Rule::in([Ukm::STATUS_AKTIF, Ukm::STATUS_NONAKTIF])],
            'form.status_pendaftaran_anggota' => ['required', Rule::in([Ukm::OPREC_DIBUKA, Ukm::OPREC_DITUTUP])],
            'form.pendaftaran_anggota_dibuka_pada' => ['nullable', 'date'],
            'form.pendaftaran_anggota_ditutup_pada' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    $buka = $this->form['pendaftaran_anggota_dibuka_pada'] ?? null;
                    if ($buka && $value && strtotime($value) <= strtotime($buka)) {
                        $fail('Waktu penutupan pendaftaran harus setelah waktu pembukaan.');
                    }
                },
            ],
        ];
    }

    protected array $messages = [
        'form.nama.required' => 'Nama UKM wajib diisi.',
        'form.nama.unique' => 'Nama UKM ini sudah terdaftar.',
        'form.email_kontak.email' => 'Format email kontak tidak valid.',
        'form.status.required' => 'Status organisasi wajib dipilih.',
        'form.status_pendaftaran_anggota.required' => 'Status pendaftaran wajib dipilih.',
    ];

    public function openCreateModal(): void
    {
        $this->authorize('create', Ukm::class);

        $this->resetValidation();
        $this->reset('logoUpload', 'storedLogoPreviewUrl');
        $this->editingId = null;
        $this->form = [
            'nama' => '',
            'logo' => '',
            'deskripsi' => '',
            'visi' => '',
            'misi' => '',
            'email_kontak' => '',
            'nomor_kontak' => '',
            'status' => Ukm::STATUS_AKTIF,
            'status_pendaftaran_anggota' => Ukm::OPREC_DITUTUP,
            'pendaftaran_anggota_dibuka_pada' => '',
            'pendaftaran_anggota_ditutup_pada' => '',
        ];

        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $ukm = Ukm::findOrFail($id);
        $this->authorize('update', $ukm);

        $this->resetValidation();
        $this->reset('logoUpload');
        $this->editingId = $ukm->id;
        $this->storedLogoPreviewUrl = $ukm->logoUrl();
        $this->form = [
            'nama' => $ukm->nama,
            'logo' => (string) ($ukm->logo ?? ''),
            'deskripsi' => (string) ($ukm->deskripsi ?? ''),
            'visi' => (string) ($ukm->visi ?? ''),
            'misi' => (string) ($ukm->misi ?? ''),
            'email_kontak' => (string) ($ukm->email_kontak ?? ''),
            'nomor_kontak' => (string) ($ukm->nomor_kontak ?? ''),
            'status' => $ukm->status,
            'status_pendaftaran_anggota' => $ukm->status_pendaftaran_anggota,
            'pendaftaran_anggota_dibuka_pada' => $ukm->pendaftaran_anggota_dibuka_pada ? $ukm->pendaftaran_anggota_dibuka_pada->format('Y-m-d\TH:i') : '',
            'pendaftaran_anggota_ditutup_pada' => $ukm->pendaftaran_anggota_ditutup_pada ? $ukm->pendaftaran_anggota_ditutup_pada->format('Y-m-d\TH:i') : '',
        ];

        $this->showFormModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $ukm = Ukm::findOrFail($this->editingId);
            $this->authorize('update', $ukm);

            // Nama dan status UKM adalah kewenangan Kemahasiswaan. Nilai dari
            // request non-Kemahasiswaan selalu dikembalikan ke data tersimpan.
            if (! Auth::user()->isKemahasiswaan()) {
                $this->form['nama'] = $ukm->nama;
                $this->form['status'] = $ukm->status;
            }

            $this->validate();

            $newLogo = $this->storeUploadedLogo();
            if ($this->logoUpload && ! $newLogo) {
                return;
            }

            $previousLogo = $ukm->logo;

            $ukm->update([
                'nama' => $this->form['nama'],
                'logo' => $newLogo ?? $ukm->logo,
                'deskripsi' => $this->form['deskripsi'] ?: null,
                'visi' => $this->form['visi'] ?: null,
                'misi' => $this->form['misi'] ?: null,
                'email_kontak' => $this->form['email_kontak'] ?: null,
                'nomor_kontak' => $this->form['nomor_kontak'] ?: null,
                'status' => $this->form['status'],
                'status_pendaftaran_anggota' => $this->form['status_pendaftaran_anggota'],
                'pendaftaran_anggota_dibuka_pada' => $this->form['pendaftaran_anggota_dibuka_pada'] ?: null,
                'pendaftaran_anggota_ditutup_pada' => $this->form['pendaftaran_anggota_ditutup_pada'] ?: null,
            ]);

            $this->deleteReplacedLogo($previousLogo, $ukm->logo);

            $this->dispatch('toast-show', [
                'heading' => 'Perubahan Tersimpan',
                'text' => "Data UKM {$ukm->nama} berhasil diperbarui.",
                'variant' => 'success',
            ]);
        } else {
            $this->authorize('create', Ukm::class);
            $this->validate();

            $newLogo = $this->storeUploadedLogo();
            if ($this->logoUpload && ! $newLogo) {
                return;
            }

            $ukm = Ukm::create([
                'nama' => $this->form['nama'],
                'logo' => $newLogo,
                'deskripsi' => $this->form['deskripsi'] ?: null,
                'visi' => $this->form['visi'] ?: null,
                'misi' => $this->form['misi'] ?: null,
                'email_kontak' => $this->form['email_kontak'] ?: null,
                'nomor_kontak' => $this->form['nomor_kontak'] ?: null,
                'status' => $this->form['status'],
                'status_pendaftaran_anggota' => $this->form['status_pendaftaran_anggota'],
                'pendaftaran_anggota_dibuka_pada' => $this->form['pendaftaran_anggota_dibuka_pada'] ?: null,
                'pendaftaran_anggota_ditutup_pada' => $this->form['pendaftaran_anggota_ditutup_pada'] ?: null,
            ]);

            $this->dispatch('toast-show', [
                'heading' => 'UKM Ditambahkan',
                'text' => "Data UKM {$ukm->nama} berhasil ditambahkan.",
                'variant' => 'success',
            ]);
        }

        $this->showFormModal = false;
        $this->editingId = null;
        $this->reset('logoUpload', 'storedLogoPreviewUrl');
    }

    private function storeUploadedLogo(): ?string
    {
        if (! $this->logoUpload) {
            return null;
        }

        $path = $this->logoUpload->storePublicly('ukm-logos', 'public');

        if (! $path) {
            $this->addError('logoUpload', 'Logo gagal disimpan. Silakan coba unggah kembali.');

            return null;
        }

        return $path;
    }

    private function deleteReplacedLogo(?string $previousLogo, ?string $currentLogo): void
    {
        if ($previousLogo && $previousLogo !== $currentLogo && Str::startsWith($previousLogo, 'ukm-logos/')) {
            Storage::disk('public')->delete($previousLogo);
        }
    }

    public function openDetailModal(int $id): void
    {
        $this->selectedUkmId = $id;
        $this->showDetailModal = true;
    }

    public function confirmDeactivate(int $id): void
    {
        $ukm = Ukm::findOrFail($id);
        $this->authorize('toggleStatus', $ukm);

        $this->selectedUkmId = $id;
        $this->showDeactivateModal = true;
    }

    public function deactivate(): void
    {
        if (! $this->selectedUkmId) {
            return;
        }

        $ukm = Ukm::findOrFail($this->selectedUkmId);
        $this->authorize('toggleStatus', $ukm);

        $ukm->update(['status' => Ukm::STATUS_NONAKTIF]);

        $this->dispatch('toast-show', [
            'heading' => 'UKM Dinonaktifkan',
            'text' => "Status UKM {$ukm->nama} telah diubah menjadi nonaktif.",
            'variant' => 'info',
        ]);

        $this->showDeactivateModal = false;
        $this->selectedUkmId = null;
    }

    public function confirmActivate(int $id): void
    {
        $ukm = Ukm::findOrFail($id);
        $this->authorize('toggleStatus', $ukm);

        $this->selectedUkmId = $id;
        $this->showActivateModal = true;
    }

    public function activate(): void
    {
        if (! $this->selectedUkmId) {
            return;
        }

        $ukm = Ukm::findOrFail($this->selectedUkmId);
        $this->authorize('toggleStatus', $ukm);

        $ukm->update(['status' => Ukm::STATUS_AKTIF]);

        $this->dispatch('toast-show', [
            'heading' => 'UKM Diaktifkan Kembali',
            'text' => "Status UKM {$ukm->nama} berhasil diaktifkan kembali.",
            'variant' => 'success',
        ]);

        $this->showActivateModal = false;
        $this->selectedUkmId = null;
    }

    public function render(): View
    {
        $ukmCounts = Ukm::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as aktif', [Ukm::STATUS_AKTIF])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as nonaktif', [Ukm::STATUS_NONAKTIF])
            ->selectRaw('SUM(CASE WHEN status_pendaftaran_anggota = ? THEN 1 ELSE 0 END) as pendaftaran_dibuka', [Ukm::OPREC_DIBUKA])
            ->first();

        $query = Ukm::query()
            ->withCount(['pengurusAktif as pengurus_count', 'keanggotaan'])
            ->when($this->search !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('nama', 'like', $term)
                        ->orWhere('email_kontak', 'like', $term)
                        ->orWhere('nomor_kontak', 'like', $term);
                });
            })
            ->when($this->filterStatus !== '', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterPendaftaran !== '', function ($q) {
                $q->where('status_pendaftaran_anggota', $this->filterPendaftaran);
            })
            ->orderBy('nama', 'asc');

        $ukms = $query->paginate(10);

        $selectedUkm = $this->selectedUkmId
            ? Ukm::with(['pengurus' => fn ($query) => $query->where('akun_aktif', true)])->withCount(['pengurusAktif as pengurus_count', 'keanggotaan', 'kegiatan'])->find($this->selectedUkmId)
            : null;

        return view('livewire.ukm.index', [
            'ukms' => $ukms,
            'selectedUkm' => $selectedUkm,
            'canCreate' => Auth::user()->can('create', Ukm::class),
            'ukmStats' => [
                'total' => (int) $ukmCounts->total,
                'aktif' => (int) $ukmCounts->aktif,
                'nonaktif' => (int) $ukmCounts->nonaktif,
                'pendaftaran_dibuka' => (int) $ukmCounts->pendaftaran_dibuka,
            ],
        ]);
    }
}
