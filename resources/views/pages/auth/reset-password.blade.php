<x-layouts::auth :title="__('Atur ulang kata sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Buat kata sandi baru" description="Gunakan minimal 8 karakter yang tidak dipakai di layanan lain." />

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <flux:input
                name="email"
                value="{{ request('email') }}"
                label="Email"
                type="email"
                required
                autocomplete="email"
            />

            <flux:input
                name="password"
                label="Kata sandi baru"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <flux:input
                name="password_confirmation"
                label="Ulangi kata sandi baru"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Ketik ulang kata sandi"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <flux:button type="submit" variant="primary" class="w-full" data-test="reset-password-button">
                Simpan kata sandi baru
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
