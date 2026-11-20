<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-svh bg-slate-100 antialiased dark:bg-zinc-950">
        <div class="grid min-h-svh lg:grid-cols-[1.05fr_1fr]">
            <aside class="relative hidden flex-col justify-between overflow-hidden bg-navy-950 p-10 text-white lg:flex xl:p-14">
                <div class="brand-grid absolute inset-0" aria-hidden="true"></div>
                <div class="brand-glow absolute inset-0" aria-hidden="true"></div>

                <div class="relative">
                    <x-app-logo :sidebar="true" :href="route('home')" wire:navigate />
                </div>

                <div class="relative max-w-lg space-y-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-blue-300">Portal organisasi mahasiswa</p>

                    <h1 class="text-3xl font-semibold leading-tight tracking-tight xl:text-4xl">
                        Satu ruang untuk mengelola UKM, acara, dan tiket kemahasiswaan.
                    </h1>

                    <p class="text-sm leading-6 text-blue-100/80">
                        SIP-UKM menghubungkan Bagian Kemahasiswaan, pengurus UKM, dan mahasiswa Politeknik Negeri Banyuwangi dalam satu alur kerja yang tertib.
                    </p>

                    <ul class="space-y-3 text-sm text-blue-50/90">
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-md bg-white/10">
                                <flux:icon name="building-library" class="size-3.5" />
                            </span>
                            Data UKM, pengurus, dan divisi tersimpan rapi.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-md bg-white/10">
                                <flux:icon name="calendar-days" class="size-3.5" />
                            </span>
                            Acara dan registrasi peserta terpusat.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-md bg-white/10">
                                <flux:icon name="ticket" class="size-3.5" />
                            </span>
                            Tiket dan presensi acara mudah diverifikasi.
                        </li>
                    </ul>
                </div>

                <p class="relative text-xs text-blue-200/60">
                    Politeknik Negeri Banyuwangi · Sistem Informasi Pengelolaan UKM
                </p>
            </aside>

            <main class="relative flex items-center justify-center px-5 py-10 sm:px-8">
                <div class="absolute end-4 top-4 sm:end-8 sm:top-6">
                    <x-theme-toggle />
                </div>

                <div class="w-full max-w-[26rem]">
                    <div class="mb-8 lg:hidden">
                        <x-app-logo :href="route('home')" wire:navigate />
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 dark:border-white/10 dark:bg-zinc-900">
                        {{ $slot }}
                    </div>

                    <p class="mt-6 text-center text-xs leading-5 text-slate-500 dark:text-zinc-400">
                        Kendala masuk? Hubungi Bagian Kemahasiswaan Politeknik Negeri Banyuwangi.
                    </p>
                </div>
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
