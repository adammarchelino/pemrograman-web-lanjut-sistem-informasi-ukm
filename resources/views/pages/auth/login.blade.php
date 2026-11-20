<x-layouts::auth :title="__('Masuk')">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Masuk ke SIP-UKM" description="Gunakan akun kampusmu untuk mengakses dashboard, katalog UKM, dan tiket acara." />

        <x-auth-session-status :status="session('status')" />

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <flux:input
                name="email"
                label="Email"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="nama@poliwangi.ac.id"
            />

            <div class="relative">
                <flux:input
                    name="password"
                    label="Kata sandi"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute end-0 top-0 text-xs font-medium" :href="route('password.request')" wire:navigate>
                        Lupa kata sandi?
                    </flux:link>
                @endif
            </div>

            <flux:checkbox name="remember" label="Ingat saya" :checked="old('remember')" />

            <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                Masuk
            </flux:button>
        </form>

        <div class="border-t border-slate-200 pt-5 text-center text-sm text-slate-600 dark:border-white/10 dark:text-zinc-400">
            <span>Belum punya akun?</span>
            <flux:link :href="route('register')" wire:navigate class="font-medium">Daftar sebagai mahasiswa</flux:link>
        </div>
    </div>
</x-layouts::auth>
