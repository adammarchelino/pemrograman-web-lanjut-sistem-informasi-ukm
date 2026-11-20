<x-layouts::auth :title="__('Autentikasi dua faktor')">
    <div class="flex flex-col gap-6">
        <div
            class="relative h-auto w-full"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                focusOtp() {
                    this.$nextTick(() => this.$refs.otp?.querySelector('input')?.focus());
                },
                init() {
                    if (! this.showRecoveryInput) {
                        this.focusOtp();
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : this.focusOtp();
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    title="Kode autentikasi"
                    description="Masukkan kode 6 digit dari aplikasi authenticator-mu."
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    title="Kode pemulihan"
                    description="Masukkan salah satu kode pemulihan darurat yang kamu simpan."
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="mt-5 space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="my-5 flex items-center justify-center" x-ref="otp">
                            <flux:otp
                                x-model="code"
                                length="6"
                                name="code"
                                label="OTP Code"
                                label:sr-only
                                class="mx-auto"
                             />
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <flux:input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                                placeholder="Kode pemulihan"
                            />
                        </div>

                        @error('recovery_code')
                            <flux:text color="red">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full"
                    >
                        Lanjutkan
                    </flux:button>
                </div>

                <div class="mt-5 text-center text-sm leading-5">
                    <span class="text-slate-500 dark:text-zinc-400">atau</span>
                    <div class="mt-1 inline cursor-pointer font-medium text-blue-700 underline decoration-blue-300 underline-offset-4 dark:text-blue-300">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">masuk dengan kode pemulihan</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">masuk dengan kode autentikasi</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
