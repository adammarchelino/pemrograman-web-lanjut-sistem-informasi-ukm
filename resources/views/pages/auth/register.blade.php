<x-layouts::auth :title="__('Daftar')">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Buat akun mahasiswa" description="Isi data diri sesuai kartu mahasiswa. Akun pengurus UKM dibuat oleh Bagian Kemahasiswaan." />

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <flux:input
                name="name"
                label="Nama lengkap"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama sesuai kartu mahasiswa"
            />

            <flux:input
                name="nim"
                label="NIM"
                :value="old('nim')"
                type="text"
                required
                inputmode="numeric"
                autocomplete="username"
                placeholder="Nomor Induk Mahasiswa"
            />

            <flux:input
                name="email"
                label="Email"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="nama@poliwangi.ac.id"
            />

            <flux:input
                name="password"
                label="Kata sandi"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <flux:input
                name="password_confirmation"
                label="Ulangi kata sandi"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Ketik ulang kata sandi"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                Buat akun
            </flux:button>
        </form>

        <div class="border-t border-slate-200 pt-5 text-center text-sm text-slate-600 dark:border-white/10 dark:text-zinc-400">
            <span>Sudah punya akun?</span>
            <flux:link :href="route('login')" wire:navigate class="font-medium">Masuk</flux:link>
        </div>
    </div>
</x-layouts::auth>
