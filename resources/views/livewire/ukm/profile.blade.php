<div class="mx-auto max-w-3xl space-y-6 p-6 lg:p-8">
    <header>
        <p class="text-sm font-semibold text-blue-700 dark:text-blue-300">Kelola profil publik</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Profil {{ $ukm->nama }}</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-zinc-300">Perubahan logo, nama, dan deskripsi langsung tampil pada katalog mahasiswa. Status organisasi tetap dikelola Kemahasiswaan.</p>
    </header>

    <form wire:submit="save" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
            @if ($logoUpload)
                <img src="{{ $logoUpload->temporaryUrl() }}" alt="Pratinjau logo" class="size-24 rounded-2xl border border-slate-200 object-cover dark:border-zinc-700">
            @elseif ($logoPreview)
                <img src="{{ $logoPreview }}" alt="Logo {{ $ukm->nama }}" class="size-24 rounded-2xl border border-slate-200 object-cover dark:border-zinc-700">
            @else
                <div class="flex size-24 items-center justify-center rounded-2xl bg-blue-50 text-2xl font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300">{{ $ukm->inisial() }}</div>
            @endif
            <div class="flex-1 space-y-2">
                <flux:input type="file" wire:model="logoUpload" label="Logo UKM" accept="image/png,image/jpeg,image/webp" />
                <p class="text-xs text-slate-500 dark:text-zinc-400">PNG, JPG, atau WEBP maksimal 2 MB. Pratinjau muncul sebelum disimpan.</p>
                <div wire:loading wire:target="logoUpload" class="text-xs text-blue-600">Mengunggah pratinjau...</div>
            </div>
        </div>

        <flux:input wire:model="form.nama" label="Nama UKM" required />
        <flux:textarea wire:model="form.deskripsi" label="Deskripsi" rows="5" required />
        <div class="flex justify-end">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Simpan profil</flux:button>
        </div>
    </form>
</div>
