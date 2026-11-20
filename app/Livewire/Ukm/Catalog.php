<?php

namespace App\Livewire\Ukm;

use App\Models\Ukm;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Katalog UKM — SIP-UKM')]
class Catalog extends Component
{
    #[Url]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewCatalog', Ukm::class);
    }

    public function render(): View
    {
        $userId = auth()->id();

        $ukms = Ukm::query()
            ->withExists([
                'pendaftaranAnggota as sudah_mengajukan' => fn ($query) => $query
                    ->where('user_id', $userId)
                    ->whereIn('status', ['pending', 'diterima']),
                'keanggotaan as sudah_menjadi_anggota' => fn ($query) => $query
                    ->where('user_id', $userId)
                    ->where('status', 'aktif'),
            ])
            ->where('status', Ukm::STATUS_AKTIF)
            ->when(trim($this->search) !== '', function ($query) {
                $term = '%'.trim($this->search).'%';
                $query->where(fn ($q) => $q->where('nama', 'like', $term)->orWhere('deskripsi', 'like', $term));
            })
            ->orderBy('nama')
            ->paginate(12);

        return view('livewire.ukm.catalog', compact('ukms'));
    }
}
