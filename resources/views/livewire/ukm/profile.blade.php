<x-ui.page width="4xl">
    <x-ui.page-header
        eyebrow="Kelola profil publik"
        title="Profil {{ $ukm->nama }}"
        description="Perubahan logo dan deskripsi langsung tampil pada katalog mahasiswa."
    >
        <x-slot:actions>
            <x-ui.badge :tone="$ukm->status === 'aktif' ? 'positive' : 'negative'">
                {{ $ukm->status === 'aktif' ? 'UKM Aktif' : 'UKM Nonaktif' }}
            </x-ui.badge>
        </x-slot:actions>
    </x-ui.page-header>

    <form wire:submit="save" class="space-y-5">
        <x-ui.panel title="Identitas UKM" description="Nama dan status organisasi ditetapkan oleh Bagian Kemahasiswaan." icon="building-library">
            <div class="space-y-4">
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3.5 py-2.5 dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs font-medium text-slate-500 dark:text-zinc-400">Nama UKM</p>
                    <p class="mt-0.5 text-sm font-semibold text-slate-950 dark:text-white">{{ $ukm->nama }}</p>
                </div>

                <p class="text-xs leading-5 text-slate-500 dark:text-zinc-400">
                    Untuk perubahan nama atau status organisasi, ajukan ke Bagian Kemahasiswaan. Ketua UKM hanya dapat memperbarui logo dan deskripsi.
                </p>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Logo UKM" description="PNG, JPG, atau WEBP maksimal 2 MB. Pratinjau muncul sebelum disimpan." icon="photo">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                @if ($logoUpload)
                    <img src="{{ $logoUpload->temporaryUrl() }}" alt="Pratinjau logo" class="size-20 shrink-0 rounded-xl border border-slate-200 object-cover dark:border-white/10">
                @elseif ($logoPreview)
                    <img src="{{ $logoPreview }}" alt="Logo {{ $ukm->nama }}" class="size-20 shrink-0 rounded-xl border border-slate-200 object-cover dark:border-white/10">
                @else
                    <div class="flex size-20 shrink-0 items-center justify-center rounded-xl bg-navy-950 text-xl font-semibold text-white">{{ $ukm->inisial() }}</div>
                @endif

                <div class="min-w-0 flex-1 space-y-2">
                    <flux:input type="file" wire:model="logoUpload" label="Unggah logo baru" accept="image/png,image/jpeg,image/webp" />
                    <div wire:loading wire:target="logoUpload" class="text-xs font-medium text-blue-700 dark:text-blue-300">Mengunggah pratinjau...</div>
                    @error('logoUpload')
                        <p class="text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Deskripsi" description="Tulis ringkasan aktivitas UKM yang akan dibaca mahasiswa di katalog." icon="document-text">
            <flux:textarea wire:model="form.deskripsi" label="Deskripsi UKM" rows="6" required />
        </x-ui.panel>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-500 dark:text-zinc-400">Perubahan berlaku langsung setelah disimpan.</p>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Simpan profil</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </flux:button>
        </div>
    </form>
</x-ui.page>
