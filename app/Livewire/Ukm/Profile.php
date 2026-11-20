<?php

namespace App\Livewire\Ukm;

use App\Models\Ukm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Profil UKM — SIP-UKM')]
class Profile extends Component
{
    use WithFileUploads;

    /**
     * Nama dan status UKM adalah kewenangan Kemahasiswaan. Ketua UKM hanya
     * mengelola deskripsi dan logo, sehingga nama tidak pernah masuk state form.
     */
    public array $form = ['deskripsi' => ''];

    public ?TemporaryUploadedFile $logoUpload = null;

    public ?string $logoPreview = null;

    public function mount(): void
    {
        $ukm = $this->managedUkm();
        $this->authorize('update', $ukm);
        $this->form = ['deskripsi' => (string) $ukm->deskripsi];
        $this->logoPreview = $ukm->logoUrl();
    }

    public function updatedLogoUpload(): void
    {
        $this->validate(['logoUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);
    }

    public function save(): void
    {
        $ukm = $this->managedUkm();
        $this->authorize('update', $ukm);
        $this->validate([
            'form.deskripsi' => ['required', 'string'],
            'logoUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $previousLogo = $ukm->logo;
        $payload = ['deskripsi' => $this->form['deskripsi']];
        if ($this->logoUpload) {
            $path = $this->logoUpload->storePublicly('ukm-logos', 'public');
            if (! $path) {
                $this->addError('logoUpload', 'Logo gagal disimpan. Silakan coba kembali.');

                return;
            }

            $payload['logo'] = $path;
        }
        $ukm->update($payload);

        if ($previousLogo && $previousLogo !== $ukm->logo && Str::startsWith($previousLogo, 'ukm-logos/')) {
            Storage::disk('public')->delete($previousLogo);
        }

        $this->logoUpload = null;
        $this->logoPreview = $ukm->fresh()->logoUrl();
        $this->dispatch('toast-show', ['heading' => 'Profil diperbarui', 'text' => 'Perubahan profil UKM langsung tampil di katalog.', 'variant' => 'success']);
    }

    private function managedUkm(): Ukm
    {
        return Ukm::findOrFail(Auth::user()->ukm_id);
    }

    public function render(): View
    {
        return view('livewire.ukm.profile', ['ukm' => $this->managedUkm()]);
    }
}
