<x-layouts::auth :title="__('Konfirmasi kata sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Konfirmasi kata sandi" description="Area ini memuat data sensitif. Konfirmasi identitasmu sebelum melanjutkan." />

        <x-auth-session-status :status="session('status')" />

        <x-passkey-verify
            options-route="passkey.confirm-options"
            submit-route="passkey.confirm"
            :label="__('Konfirmasi dengan passkey')"
            :loading-label="__('Mengonfirmasi...')"
            :separator="__('Atau konfirmasi dengan kata sandi')"
        />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-5">
            @csrf

            <flux:input
                name="password"
                label="Kata sandi"
                type="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan kata sandi"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                Konfirmasi
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
