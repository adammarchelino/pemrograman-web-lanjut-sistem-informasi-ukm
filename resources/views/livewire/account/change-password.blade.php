<div class="mx-auto max-w-xl space-y-6 p-6">
    <div>
        <p class="text-sm font-semibold text-blue-700 dark:text-blue-300">Langkah pertama</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Buat kata sandi pribadi</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-zinc-300">Akun ini dibuat dengan kata sandi sementara. Ganti sekarang sebelum melanjutkan.</p>
    </div>

    <form wire:submit="save" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
        <flux:input wire:model="password" label="Kata sandi baru" type="password" required viewable autocomplete="new-password" />
        <flux:input wire:model="password_confirmation" label="Ulangi kata sandi baru" type="password" required viewable autocomplete="new-password" />
        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">Simpan kata sandi</flux:button>
        </div>
    </form>
</div>
