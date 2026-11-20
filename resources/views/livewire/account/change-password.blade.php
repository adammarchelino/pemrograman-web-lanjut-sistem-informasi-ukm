<x-ui.page width="2xl">
    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-zinc-400">
        <span class="flex size-5 items-center justify-center rounded-full bg-blue-700 text-[10px] font-semibold text-white">1</span>
        <span>Langkah pertama sebelum masuk dashboard</span>
    </div>

    <x-ui.page-header
        eyebrow="Keamanan akun"
        title="Buat kata sandi pribadi"
        description="Akun ini dibuat dengan kata sandi sementara. Ganti sekarang sebelum melanjutkan."
    />

    <x-ui.panel>
        <form wire:submit="save" class="space-y-5">
            <flux:input wire:model="password" label="Kata sandi baru" type="password" required viewable autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <flux:input wire:model="password_confirmation" label="Ulangi kata sandi baru" type="password" required viewable autocomplete="new-password" placeholder="Ketik ulang kata sandi" />

            <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Gunakan kombinasi huruf dan angka yang mudah kamu ingat.</p>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan kata sandi</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </flux:button>
            </div>
        </form>
    </x-ui.panel>
</x-ui.page>
