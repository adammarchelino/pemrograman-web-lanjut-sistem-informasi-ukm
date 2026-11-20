<x-layouts::auth :title="__('Verifikasi email')">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Periksa emailmu" description="Kami mengirim tautan verifikasi ke alamat email yang kamu daftarkan." />

        @if (session('status') == 'verification-link-sent')
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                Tautan verifikasi baru sudah dikirim ke emailmu.
            </div>
        @endif

        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
            Tautan berlaku terbatas. Jika email belum masuk, periksa folder spam atau kirim ulang.
        </div>

        <div class="flex flex-col gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full">
                    Kirim ulang email verifikasi
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="ghost" type="submit" class="w-full cursor-pointer text-sm" data-test="logout-button">
                    Keluar dari akun
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
