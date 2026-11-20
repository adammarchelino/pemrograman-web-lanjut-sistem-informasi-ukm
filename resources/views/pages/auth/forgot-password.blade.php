<x-layouts::auth :title="__('Lupa kata sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Atur ulang kata sandi" description="Masukkan email akunmu. Kami akan mengirim tautan untuk membuat kata sandi baru." />

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
            @csrf

            <flux:input
                name="email"
                label="Email"
                type="email"
                required
                autofocus
                placeholder="nama@poliwangi.ac.id"
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
                Kirim tautan atur ulang
            </flux:button>
        </form>

        <div class="border-t border-slate-200 pt-5 text-center text-sm text-slate-600 dark:border-white/10 dark:text-zinc-400">
            <span>Ingat kata sandimu?</span>
            <flux:link :href="route('login')" wire:navigate class="font-medium">Kembali masuk</flux:link>
        </div>
    </div>
</x-layouts::auth>
