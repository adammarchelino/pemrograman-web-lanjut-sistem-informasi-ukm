@php
    $tautanProfil = auth()->check() ? route('ukm.catalog') : route('login');
    $tahun = now()->year;
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
    <head>
        @include('partials.head', ['title' => 'Direktori Unit Kegiatan Mahasiswa'])

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap"
            rel="stylesheet"
        >
    </head>
    <body class="min-h-svh bg-white font-display text-slate-900 antialiased dark:bg-charcoal dark:text-zinc-100">
        <a
            href="#konten"
            class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-navy-950 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white focus:outline-2 focus:outline-offset-2 focus:outline-blue-600 dark:focus:bg-white dark:focus:text-navy-950 dark:focus:outline-white"
        >
            Lewati ke konten
        </a>

        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur-md dark:border-charcoal-line dark:bg-charcoal/95">
            <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-12">
                <div class="flex min-w-0 items-center gap-3">
                    <x-app-logo :on-paper="true" :href="route('home')" :hide-institution="true" />

                    <span class="hidden h-5 w-px bg-slate-200 xl:block dark:bg-charcoal-line" aria-hidden="true"></span>
                    <span class="hidden rounded border border-slate-200 bg-slate-100 px-2 py-0.5 font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-600 xl:inline-block dark:border-charcoal-line dark:bg-charcoal-soft dark:text-zinc-400">
                        Portal resmi
                    </span>
                </div>

                <nav class="hidden items-center gap-1 md:flex" aria-label="Navigasi halaman">
                    <a
                        href="#konten"
                        aria-current="page"
                        class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-950 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-charcoal-soft dark:text-zinc-100 dark:focus-visible:outline-blue-400"
                    >
                        Beranda
                    </a>
                    <a href="#direktori" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-100 dark:focus-visible:outline-blue-400">Direktori UKM</a>
                    <a href="#fitur" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-100 dark:focus-visible:outline-blue-400">Fitur Layanan</a>
                    <a href="#alur" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-100 dark:focus-visible:outline-blue-400">Alur Mahasiswa</a>
                    <a href="#faq" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-100 dark:focus-visible:outline-blue-400">FAQ</a>
                </nav>

                <div class="flex shrink-0 items-center gap-2">
                    <x-theme-toggle />

                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex min-h-11 items-center bg-blue-600 px-4 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="hidden min-h-11 items-center rounded-lg px-3 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:inline-flex dark:text-zinc-300 dark:hover:bg-white/5 dark:focus-visible:outline-blue-400"
                        >
                            Masuk
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex min-h-11 items-center bg-blue-600 px-4 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400"
                            >
                                Buat akun
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <nav
                class="flex items-center gap-1 overflow-x-auto border-t border-slate-200 px-4 py-2 md:hidden dark:border-charcoal-line"
                aria-label="Navigasi bagian halaman"
            >
                <a href="#direktori" class="inline-flex min-h-11 shrink-0 items-center rounded-lg px-3 text-sm font-medium text-slate-600 dark:text-zinc-400">Direktori</a>
                <a href="#fitur" class="inline-flex min-h-11 shrink-0 items-center rounded-lg px-3 text-sm font-medium text-slate-600 dark:text-zinc-400">Fitur</a>
                <a href="#alur" class="inline-flex min-h-11 shrink-0 items-center rounded-lg px-3 text-sm font-medium text-slate-600 dark:text-zinc-400">Alur</a>
                <a href="#faq" class="inline-flex min-h-11 shrink-0 items-center rounded-lg px-3 text-sm font-medium text-slate-600 dark:text-zinc-400">FAQ</a>
                @guest
                    <a href="{{ route('login') }}" class="inline-flex min-h-11 shrink-0 items-center rounded-lg px-3 text-sm font-semibold text-slate-950 dark:text-zinc-100">Masuk</a>
                @endguest
            </nav>
        </header>

        <main id="konten">
            <section class="relative overflow-hidden border-b border-slate-100 dark:border-charcoal-line">
                <div class="mx-auto grid w-full max-w-7xl grid-cols-1 items-center gap-12 px-4 pt-14 pb-20 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:px-12 lg:pt-36 lg:pb-24">
                    <div class="lg:col-span-7">
                        <p class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 font-label text-[10px] font-semibold uppercase tracking-[0.14em] text-blue-700 shadow-xs dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300">
                            <span class="size-2 shrink-0 rounded-full bg-blue-600" aria-hidden="true"></span>
                            Portal resmi kemahasiswaan // Poliwangi {{ $tahun }}
                        </p>

                        <h1 class="mt-5 max-w-2xl text-[clamp(2.1rem,4.6vw,3rem)] font-extrabold leading-[1.08] tracking-[-0.03em] text-slate-950 dark:text-zinc-100">
                            Temukan UKM yang cocok di Poliwangi.
                        </h1>

                        <p class="mt-5 max-w-xl text-base leading-[1.625] text-slate-600 dark:text-zinc-400">
                            Satu pintu untuk seluruh unit kegiatan mahasiswa Politeknik Negeri Banyuwangi. Lihat status
                            penerimaan, baca profil dan divisi tiap unit, ajukan keanggotaan, lalu ikuti kegiatan dan
                            presensinya dari satu akun.
                        </p>

                        <div class="mt-7 flex flex-wrap items-center gap-3">
                            @auth
                                <a
                                    href="{{ route('dashboard') }}"
                                    class="inline-flex min-h-12 items-center gap-2 bg-blue-600 px-6 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400"
                                >
                                    Buka dashboard
                                    <flux:icon.arrow-right class="size-4" aria-hidden="true" />
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex min-h-12 items-center gap-2 bg-blue-600 px-6 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400"
                                >
                                    Masuk ke akun SIP-UKM
                                    <flux:icon.arrow-right class="size-4" aria-hidden="true" />
                                </a>
                            @endauth

                            <a
                                href="#direktori"
                                class="inline-flex min-h-12 items-center gap-2 border border-slate-300 bg-white px-6 text-sm font-semibold text-slate-800 shadow-xs transition-colors hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-charcoal-line dark:bg-charcoal-soft dark:text-zinc-200 dark:hover:bg-white/5 dark:focus-visible:outline-blue-400"
                            >
                                Lihat direktori UKM
                                <flux:icon.magnifying-glass class="size-4" aria-hidden="true" />
                            </a>
                        </div>

                        <dl class="mt-8 grid max-w-lg grid-cols-3 gap-2 sm:gap-3">
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 shadow-xs dark:border-charcoal-line dark:bg-charcoal-soft">
                                <dt class="font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:text-zinc-400">UKM aktif</dt>
                                <dd class="mt-1 text-xl font-bold tabular-nums text-slate-950 dark:text-zinc-100">
                                    {{ number_format($totalAktif) }}
                                    <span class="font-label text-[10px] font-medium text-slate-500 dark:text-zinc-400">dari {{ number_format($totalUkm) }}</span>
                                </dd>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 shadow-xs dark:border-charcoal-line dark:bg-charcoal-soft">
                                <dt class="font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:text-zinc-400">Penerimaan dibuka</dt>
                                <dd class="mt-1 text-xl font-bold tabular-nums text-blue-700 dark:text-blue-400">
                                    {{ number_format($pendaftaranDibuka) }}
                                    <span class="font-label text-[10px] font-medium text-slate-500 dark:text-zinc-400">UKM</span>
                                </dd>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 shadow-xs dark:border-charcoal-line dark:bg-charcoal-soft">
                                <dt class="font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:text-zinc-400">Anggota aktif</dt>
                                <dd class="mt-1 text-xl font-bold tabular-nums text-slate-950 dark:text-zinc-100">
                                    {{ number_format($anggotaAktif) }}
                                    <span class="font-label text-[10px] font-medium text-slate-500 dark:text-zinc-400">orang</span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="relative lg:col-span-5">
                        <div class="pointer-events-none absolute -right-6 -top-6 size-72 rounded-full bg-blue-500/10 blur-3xl" aria-hidden="true"></div>

                        @if ($ukmSorotan)
                            <article class="relative z-20 w-full max-w-md rounded-xl border border-slate-800 bg-navy-950 p-6 text-white shadow-xl dark:border-charcoal-line">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="flex min-w-0 flex-1 basis-48 items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-sm font-bold text-white" aria-hidden="true">
                                            {{ $ukmSorotan->inisial() }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-base font-bold leading-tight">{{ $ukmSorotan->nama }}</p>
                                            <p class="mt-1 font-label text-[10px] uppercase tracking-[0.14em] text-slate-300">Unit kegiatan mahasiswa</p>
                                        </div>
                                    </div>

                                    <x-ui.badge :tone="$ukmSorotan->isPendaftaranDibuka() ? 'positive' : 'negative'" :pill="true">
                                        {{ $ukmSorotan->isPendaftaranDibuka() ? 'Penerimaan dibuka' : 'Penerimaan ditutup' }}
                                    </x-ui.badge>
                                </div>

                                <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-slate-300">
                                    {{ $ukmSorotan->deskripsi ?: 'Deskripsi unit ini belum diisi pengurus.' }}
                                </p>

                                @if ($ukmSorotan->divisi->isNotEmpty())
                                    <ul class="mt-4 flex flex-wrap gap-1.5">
                                        @foreach ($ukmSorotan->divisi->take(3) as $divisi)
                                            <li class="rounded border border-slate-700 bg-slate-900 px-2 py-0.5 font-label text-[10px] uppercase tracking-[0.06em] text-slate-200">
                                                {{ $divisi->nama }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-800 pt-4">
                                    <p class="font-label text-[10px] uppercase tracking-[0.06em] text-slate-300">
                                        {{ $ukmSorotan->anggota_aktif_count > 0 ? number_format($ukmSorotan->anggota_aktif_count).' anggota aktif' : 'Belum ada anggota aktif' }}
                                    </p>
                                    <a
                                        href="{{ $tautanProfil }}"
                                        class="inline-flex min-h-11 items-center rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white transition-colors hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                                    >
                                        {{ $ukmSorotan->isPendaftaranDibuka() ? 'Ajukan formulir' : 'Lihat katalog' }}
                                    </a>
                                </div>
                            </article>

                            <div class="absolute -top-28 right-0 z-30 hidden w-56 rotate-2 flex-col gap-2 rounded-lg border border-slate-200 bg-white p-3 text-slate-900 shadow-lg transition-transform hover:rotate-0 lg:flex dark:border-charcoal-line dark:bg-charcoal-raised dark:text-zinc-100">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="rounded border border-slate-200 bg-slate-100 px-2 py-0.5 font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:border-charcoal-line dark:bg-white/5 dark:text-zinc-300">E-presensi</span>
                                    <flux:icon.qr-code class="size-4 text-blue-700 dark:text-blue-400" aria-hidden="true" />
                                </div>
                                <div>
                                    <p class="text-[13px] font-bold leading-tight">Tiket kegiatan UKM</p>
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400">Satu kode unik per peserta</p>
                                </div>
                                <div class="flex items-center justify-between gap-2 border-t border-dashed border-slate-200 pt-1.5 font-label text-[10px] uppercase tracking-[0.06em] text-slate-500 dark:border-charcoal-line dark:text-zinc-400">
                                    <span>Check-in di lokasi</span>
                                    <span class="font-semibold text-blue-700 dark:text-blue-400">Valid</span>
                                </div>
                            </div>

                            <div class="absolute -bottom-7 left-0 z-30 hidden -rotate-2 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-slate-900 shadow-md lg:flex dark:border-charcoal-line dark:bg-charcoal-raised dark:text-zinc-100">
                                <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-moss-50 text-moss-700 dark:bg-moss-700/25 dark:text-moss-50" aria-hidden="true">
                                    <flux:icon.check-badge class="size-4" />
                                </span>
                                <div>
                                    <p class="text-[11px] font-semibold leading-tight">Pengajuan disetujui</p>
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400">Status anggota tampil di dashboard</p>
                                </div>
                            </div>
                        @else
                            <div class="w-full max-w-md rounded-xl border border-dashed border-slate-300 bg-white p-6 dark:border-charcoal-line dark:bg-charcoal-raised">
                                <p class="text-sm font-semibold text-slate-950 dark:text-zinc-100">Belum ada UKM aktif untuk ditampilkan.</p>
                                <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                    Data unit dikelola Bagian Kemahasiswaan. Muat ulang halaman ini beberapa saat lagi.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section id="direktori" class="scroll-mt-32 md:scroll-mt-20">
                <div class="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-12 lg:py-24">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                        <div class="max-w-xl">
                            <p class="flex items-center gap-2 font-label text-[10px] font-semibold uppercase tracking-[0.18em] text-blue-700 dark:text-blue-400">
                                <span class="size-2 shrink-0 rounded-full bg-blue-600" aria-hidden="true"></span>
                                Direktori resmi organisasi
                            </p>
                            <h2 class="mt-3 text-[1.6rem] font-bold leading-tight tracking-[-0.02em] text-slate-950 sm:text-[2rem] dark:text-zinc-100">
                                Katalog dan status penerimaan UKM
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                Data unit kegiatan yang dikelola pengurus UKM dan dipantau Bagian Kemahasiswaan.
                                Label penerimaan diperbarui pengurus masing-masing unit.
                            </p>
                        </div>

                        <p class="flex shrink-0 items-center gap-2 self-start rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 font-label text-[10px] uppercase tracking-[0.06em] text-slate-600 shadow-xs sm:self-auto dark:border-charcoal-line dark:bg-charcoal-soft dark:text-zinc-400">
                            <flux:icon.arrow-path class="size-3.5 shrink-0 text-blue-600 dark:text-blue-400" aria-hidden="true" />
                            <span data-ukm-count>{{ $ukm->count() }}</span> organisasi aktif
                        </p>
                    </div>

                    @if ($ukm->isEmpty())
                        <p class="mt-8 max-w-xl rounded-lg border border-dashed border-slate-300 bg-white px-5 py-6 text-sm leading-6 text-slate-600 dark:border-charcoal-line dark:bg-charcoal-raised dark:text-zinc-400">
                            Belum ada UKM berstatus aktif yang bisa ditampilkan. Hubungi Bagian Kemahasiswaan bila
                            daftar ini seharusnya sudah terisi.
                        </p>
                    @else
                        <div data-ukm-filter>
                            <div class="mt-6 flex flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    data-ukm-filter-button="semua"
                                    aria-pressed="true"
                                    class="inline-flex min-h-11 items-center rounded-full border border-slate-300 bg-white px-4 font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 shadow-xs transition-colors hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-charcoal-line dark:bg-charcoal-soft dark:text-zinc-300 dark:hover:bg-white/5 dark:focus-visible:outline-blue-400 aria-pressed:border-blue-600 aria-pressed:bg-blue-600 aria-pressed:text-white aria-pressed:hover:bg-blue-600"
                                >
                                    Semua UKM
                                </button>
                                <button
                                    type="button"
                                    data-ukm-filter-button="dibuka"
                                    aria-pressed="false"
                                    class="inline-flex min-h-11 items-center rounded-full border border-slate-300 bg-white px-4 font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 shadow-xs transition-colors hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-charcoal-line dark:bg-charcoal-soft dark:text-zinc-300 dark:hover:bg-white/5 dark:focus-visible:outline-blue-400 aria-pressed:border-blue-600 aria-pressed:bg-blue-600 aria-pressed:text-white aria-pressed:hover:bg-blue-600"
                                >
                                    Penerimaan dibuka
                                </button>
                                <button
                                    type="button"
                                    data-ukm-filter-button="ditutup"
                                    aria-pressed="false"
                                    class="inline-flex min-h-11 items-center rounded-full border border-slate-300 bg-white px-4 font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 shadow-xs transition-colors hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-charcoal-line dark:bg-charcoal-soft dark:text-zinc-300 dark:hover:bg-white/5 dark:focus-visible:outline-blue-400 aria-pressed:border-blue-600 aria-pressed:bg-blue-600 aria-pressed:text-white aria-pressed:hover:bg-blue-600"
                                >
                                    Penerimaan ditutup
                                </button>
                            </div>

                            <p class="mt-6 rounded-lg border border-dashed border-slate-300 bg-white px-5 py-6 text-sm leading-6 text-slate-600 dark:border-charcoal-line dark:bg-charcoal-raised dark:text-zinc-400" data-ukm-empty hidden>
                                Tidak ada UKM dengan status penerimaan itu saat ini.
                            </p>

                            <ol class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                @foreach ($ukm as $item)
                                    <li
                                        data-ukm-status="{{ $item->isPendaftaranDibuka() ? 'dibuka' : 'ditutup' }}"
                                        class="group rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-slate-300 hover:shadow-md dark:border-charcoal-line dark:bg-charcoal-raised dark:hover:border-white/20"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:text-zinc-400">
                                                UKM-{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                            </span>

                                            <x-ui.badge :tone="$item->isPendaftaranDibuka() ? 'positive' : 'negative'" :pill="true">
                                                {{ $item->isPendaftaranDibuka() ? 'Penerimaan dibuka' : 'Penerimaan ditutup' }}
                                            </x-ui.badge>
                                        </div>

                                        <div class="mt-4 flex items-start gap-3">
                                            @if ($item->logoUrl())
                                                <img
                                                    src="{{ $item->logoUrl() }}"
                                                    alt="Logo {{ $item->nama }}"
                                                    width="44"
                                                    height="44"
                                                    loading="lazy"
                                                    class="size-11 shrink-0 rounded-lg border border-slate-200 bg-white object-cover dark:border-charcoal-line"
                                                >
                                            @else
                                                <span class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-navy-950 text-sm font-bold text-sky-200" aria-hidden="true">
                                                    {{ $item->inisial() }}
                                                </span>
                                            @endif

                                            <div class="min-w-0">
                                                <h3 class="text-base font-bold leading-6 text-slate-950 transition-colors group-hover:text-blue-700 dark:text-zinc-100 dark:group-hover:text-blue-400">{{ $item->nama }}</h3>
                                                <p class="mt-0.5 font-label text-[10px] uppercase tracking-[0.06em] text-slate-500 dark:text-zinc-400">
                                                    {{ $item->divisi->isEmpty() ? 'Profil unit' : $item->divisi->count().' divisi' }}
                                                </p>
                                            </div>
                                        </div>

                                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                            {{ $item->deskripsi ?: 'Deskripsi unit ini belum diisi pengurus.' }}
                                        </p>

                                        @if ($item->divisi->isNotEmpty())
                                            <ul class="mt-3 flex flex-wrap gap-1.5">
                                                @foreach ($item->divisi->take(3) as $divisi)
                                                    <li class="rounded border border-slate-200 bg-slate-100 px-2 py-0.5 font-label text-[10px] uppercase tracking-[0.06em] text-slate-700 dark:border-charcoal-line dark:bg-white/5 dark:text-zinc-300">
                                                        {{ $divisi->nama }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-2 dark:border-charcoal-line">
                                            <span class="font-label text-[10px] uppercase tracking-[0.06em] text-slate-500 dark:text-zinc-400">
                                                {{ $item->anggota_aktif_count > 0 ? number_format($item->anggota_aktif_count).' anggota aktif' : 'Belum ada anggota aktif' }}
                                            </span>
                                            <a
                                                href="{{ $tautanProfil }}"
                                                class="inline-flex min-h-11 items-center gap-1 text-xs font-semibold text-blue-700 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-blue-400 dark:focus-visible:outline-blue-400"
                                            >
                                                Detail profil
                                                <flux:icon.chevron-right class="size-3.5" aria-hidden="true" />
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                </div>
            </section>

            <section id="fitur" class="scroll-mt-32 border-y border-slate-200/80 bg-slate-50/70 md:scroll-mt-20 dark:border-charcoal-line dark:bg-charcoal-900">
                <div class="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-12 lg:py-24">
                    <div class="max-w-2xl">
                        <p class="font-label text-[10px] font-semibold uppercase tracking-[0.18em] text-blue-700 dark:text-blue-400">
                            Fitur utama sistem
                        </p>
                        <h2 class="mt-3 text-[1.6rem] font-bold leading-tight tracking-[-0.02em] text-slate-950 sm:text-[2rem] dark:text-zinc-100">
                            Kelola keanggotaan dan kegiatan kampus tanpa birokrasi manual.
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                            Semua interaksi antara mahasiswa, pengurus UKM, dan Bagian Kemahasiswaan berada dalam satu
                            sistem, dari pengajuan sampai presensi acara.
                        </p>
                    </div>

                    <div class="mt-8 grid gap-4 md:grid-cols-12">
                        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs md:col-span-8 dark:border-charcoal-line dark:bg-charcoal-raised">
                            <span class="flex size-10 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-slate-800 dark:border-charcoal-line dark:bg-charcoal-soft dark:text-zinc-200" aria-hidden="true">
                                <flux:icon.check-badge class="size-5" />
                            </span>
                            <h3 class="mt-4 text-xl font-bold leading-snug text-slate-950 dark:text-zinc-100">
                                Profil unit terbuka untuk semua mahasiswa
                            </h3>
                            <p class="mt-2 max-w-md text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                Setiap UKM mencantumkan deskripsi, visi, misi, dan daftar divisinya. Kamu bisa membaca
                                isi organisasinya lebih dulu sebelum memutuskan mengajukan keanggotaan.
                            </p>

                            @if ($contohDivisi->isNotEmpty())
                                <div class="mt-5 max-w-md rounded-lg border border-slate-200 bg-slate-50 p-4 shadow-inner dark:border-charcoal-line dark:bg-charcoal-soft">
                                    <p class="font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:text-zinc-400">
                                        Divisi pada profil {{ $ukmSorotan->nama }}
                                    </p>
                                    <ul class="mt-2 space-y-1.5">
                                        @foreach ($contohDivisi as $divisi)
                                            <li class="flex items-center gap-2 text-sm text-slate-900 dark:text-zinc-200">
                                                <flux:icon.check class="size-3.5 shrink-0 text-moss-700 dark:text-moss-400" aria-hidden="true" />
                                                {{ $divisi->nama }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <p class="mt-5 max-w-md rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 text-sm leading-6 text-slate-600 dark:border-charcoal-line dark:bg-charcoal-soft dark:text-zinc-400">
                                    Daftar divisi tiap unit tampil di halaman profil UKM setelah kamu masuk dengan akun kampus.
                                </p>
                            @endif
                        </article>

                        <article class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-xs md:col-span-4 dark:border-charcoal-line dark:bg-charcoal-raised">
                            <div>
                                <span class="flex size-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300" aria-hidden="true">
                                    <flux:icon.clipboard-document-check class="size-5" />
                                </span>
                                <h3 class="mt-4 text-lg font-bold leading-6 text-slate-950 dark:text-zinc-100">
                                    Status pengajuan terlacak
                                </h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                    Pantau pengajuanmu dari dashboard: menunggu, diterima, atau ditolak, tanpa perlu
                                    bertanya ke sekretariat.
                                </p>
                            </div>
                            <a href="#alur" class="mt-5 inline-flex min-h-11 items-center gap-1 font-label text-[10px] font-semibold uppercase tracking-[0.06em] text-blue-700 hover:underline dark:text-blue-400">
                                Lihat alur pengajuan
                                <flux:icon.arrow-right class="size-3.5" aria-hidden="true" />
                            </a>
                        </article>

                        <article class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-xs md:col-span-4 dark:border-charcoal-line dark:bg-charcoal-raised">
                            <div>
                                <span class="flex size-10 items-center justify-center rounded-lg bg-navy-950 text-sky-200" aria-hidden="true">
                                    <flux:icon.qr-code class="size-5" />
                                </span>
                                <h3 class="mt-4 text-lg font-bold leading-6 text-slate-950 dark:text-zinc-100">
                                    E-tiket dan presensi
                                </h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                    Kegiatan yang kamu ikuti punya tiket dengan kode unik dan QR. Pengurus memindai
                                    atau mencatat kode itu saat acara berlangsung.
                                </p>
                            </div>
                            <p class="mt-5 font-label text-[10px] uppercase tracking-[0.06em] text-slate-500 dark:text-zinc-400">
                                Tersimpan di menu Tiket Saya
                            </p>
                        </article>

                        <article class="rounded-xl border border-slate-800 bg-navy-950 p-6 text-white shadow-md md:col-span-8 dark:border-charcoal-line">
                            <span class="flex size-10 items-center justify-center rounded-lg bg-blue-600 text-white" aria-hidden="true">
                                <flux:icon.chart-bar class="size-5" />
                            </span>
                            <h3 class="mt-4 text-xl font-bold leading-snug">
                                Dashboard untuk pengurus dan Kemahasiswaan
                            </h3>
                            <p class="mt-2 max-w-lg text-sm leading-6 text-slate-300">
                                Pengurus mengelola anggota, divisi, kegiatan, peserta, dan presensi unitnya. Bagian
                                Kemahasiswaan memantau pengajuan anggota lintas UKM dalam satu halaman hanya-baca.
                            </p>
                            <ul class="mt-5 flex flex-wrap gap-2">
                                <li class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 font-label text-[10px] uppercase tracking-[0.06em] text-sky-200">Kelola anggota</li>
                                <li class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 font-label text-[10px] uppercase tracking-[0.06em] text-sky-200">Publikasi kegiatan</li>
                                <li class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 font-label text-[10px] uppercase tracking-[0.06em] text-sky-200">Presensi tiket</li>
                                <li class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 font-label text-[10px] uppercase tracking-[0.06em] text-sky-200">Pantau pengajuan</li>
                            </ul>
                        </article>
                    </div>
                </div>
            </section>

            <section id="alur" class="scroll-mt-32 md:scroll-mt-20">
                <div class="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-12 lg:py-24">
                    <div class="max-w-xl">
                        <p class="font-label text-[10px] font-semibold uppercase tracking-[0.18em] text-blue-700 dark:text-blue-400">
                            Panduan singkat
                        </p>
                        <h2 class="mt-3 text-[1.6rem] font-bold leading-tight tracking-[-0.02em] text-slate-950 sm:text-[2rem] dark:text-zinc-100">
                            Dari akun baru sampai resmi jadi anggota.
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                            Empat langkah yang sama untuk mahasiswa baru maupun semester lanjut.
                        </p>
                    </div>

                    <ol class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <li class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-xs transition-colors hover:border-slate-300 dark:border-charcoal-line dark:bg-charcoal-raised dark:hover:border-white/20">
                            <div>
                                <span class="font-label text-2xl font-extrabold tabular-nums text-blue-700 dark:text-blue-400" aria-hidden="true">01</span>
                                <h3 class="mt-2 text-base font-bold text-slate-950 dark:text-zinc-100">Registrasi akun</h3>
                                <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                    Daftar dengan email aktif, verifikasi lewat tautan yang dikirim sistem, lalu masuk ke
                                    dashboard.
                                </p>
                            </div>
                            <p class="mt-4 border-t border-slate-100 pt-2 font-label text-[10px] uppercase tracking-[0.06em] text-slate-500 dark:border-charcoal-line dark:text-zinc-400">Sekali daftar</p>
                        </li>
                        <li class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-xs transition-colors hover:border-slate-300 dark:border-charcoal-line dark:bg-charcoal-raised dark:hover:border-white/20">
                            <div>
                                <span class="font-label text-2xl font-extrabold tabular-nums text-blue-700 dark:text-blue-400" aria-hidden="true">02</span>
                                <h3 class="mt-2 text-base font-bold text-slate-950 dark:text-zinc-100">Lengkapi data diri</h3>
                                <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                    Isi NIM dan data mahasiswa pada halaman pengaturan akun supaya pengurus bisa
                                    mencocokkan pengajuanmu.
                                </p>
                            </div>
                            <p class="mt-4 border-t border-slate-100 pt-2 font-label text-[10px] uppercase tracking-[0.06em] text-slate-500 dark:border-charcoal-line dark:text-zinc-400">Kapan saja</p>
                        </li>
                        <li class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-xs transition-colors hover:border-slate-300 dark:border-charcoal-line dark:bg-charcoal-raised dark:hover:border-white/20">
                            <div>
                                <span class="font-label text-2xl font-extrabold tabular-nums text-blue-700 dark:text-blue-400" aria-hidden="true">03</span>
                                <h3 class="mt-2 text-base font-bold text-slate-950 dark:text-zinc-100">Pilih UKM dan ajukan</h3>
                                <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                    Buka katalog, baca profil unit, lalu kirim pengajuan saat status penerimaannya dibuka.
                                </p>
                            </div>
                            <p class="mt-4 border-t border-slate-100 pt-2 font-label text-[10px] uppercase tracking-[0.06em] text-slate-500 dark:border-charcoal-line dark:text-zinc-400">Saat penerimaan dibuka</p>
                        </li>
                        <li class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-xs transition-colors hover:border-slate-300 dark:border-charcoal-line dark:bg-charcoal-raised dark:hover:border-white/20">
                            <div>
                                <span class="font-label text-2xl font-extrabold tabular-nums text-blue-700 dark:text-blue-400" aria-hidden="true">04</span>
                                <h3 class="mt-2 text-base font-bold text-slate-950 dark:text-zinc-100">Ikuti kegiatan</h3>
                                <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                    Setelah pengurus meninjau, status keanggotaanmu muncul di dashboard. Daftar kegiatan
                                    yang dibuka, lalu simpan tiketnya.
                                </p>
                            </div>
                            <p class="mt-4 border-t border-slate-100 pt-2 font-label text-[10px] uppercase tracking-[0.06em] text-moss-700 dark:border-charcoal-line dark:text-moss-400">Dipantau dari dashboard</p>
                        </li>
                    </ol>
                </div>
            </section>

            <section id="faq" class="scroll-mt-32 border-t border-slate-200/80 md:scroll-mt-20 dark:border-charcoal-line">
                <div class="mx-auto grid w-full max-w-7xl gap-8 px-4 py-20 sm:px-6 lg:grid-cols-12 lg:gap-12 lg:px-12 lg:py-24">
                    <div class="lg:col-span-5">
                        <p class="font-label text-[10px] font-semibold uppercase tracking-[0.18em] text-blue-700 dark:text-blue-400">
                            Pusat bantuan // FAQ
                        </p>
                        <h2 class="mt-3 text-[1.6rem] font-bold leading-tight tracking-[-0.02em] text-slate-950 sm:text-[2rem] dark:text-zinc-100">
                            Pertanyaan seputar SIP-UKM.
                        </h2>
                        <p class="mt-2 max-w-md text-sm leading-6 text-slate-600 dark:text-zinc-400">
                            Pertanyaan yang paling sering muncul soal pendaftaran, status pengajuan, dan akses
                            pengurus.
                        </p>

                        <div class="mt-6 flex max-w-md items-start gap-3 rounded-lg border border-slate-200 bg-slate-100 p-4 shadow-xs dark:border-charcoal-line dark:bg-charcoal-soft">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-white text-slate-800 shadow-xs dark:bg-white/10 dark:text-zinc-200" aria-hidden="true">
                                <flux:icon.lifebuoy class="size-4" />
                            </span>
                            <p class="text-sm leading-6 text-slate-700 dark:text-zinc-300">
                                Butuh bantuan lain? Hubungi pengurus UKM lewat email atau nomor kontak pada profil
                                unitnya.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2 lg:col-span-7">
                        <details class="group rounded-xl border border-slate-200 bg-white shadow-xs transition-colors open:bg-slate-50/70 dark:border-charcoal-line dark:bg-charcoal-raised dark:open:bg-charcoal-soft" open>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-4 text-base font-bold text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 [&::-webkit-details-marker]:hidden dark:text-zinc-100 dark:focus-visible:outline-blue-400">
                                <span>Apakah pendaftaran UKM di SIP-UKM dipungut biaya?</span>
                                <flux:icon.chevron-down class="size-4 shrink-0 text-blue-700 transition-transform group-open:rotate-180 dark:text-blue-400" aria-hidden="true" />
                            </summary>
                            <div class="px-4 pb-4 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                Tidak ada pungutan biaya. SIP-UKM adalah sistem internal Politeknik Negeri Banyuwangi
                                dan tidak memproses pembayaran apa pun.
                            </div>
                        </details>

                        <details class="group rounded-xl border border-slate-200 bg-white shadow-xs transition-colors open:bg-slate-50/70 dark:border-charcoal-line dark:bg-charcoal-raised dark:open:bg-charcoal-soft">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-4 text-base font-bold text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 [&::-webkit-details-marker]:hidden dark:text-zinc-100 dark:focus-visible:outline-blue-400">
                                <span>Berapa UKM yang boleh saya ajukan dalam satu semester?</span>
                                <flux:icon.chevron-down class="size-4 shrink-0 text-blue-700 transition-transform group-open:rotate-180 dark:text-blue-400" aria-hidden="true" />
                            </summary>
                            <div class="px-4 pb-4 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                Sistem tidak membatasi jumlah unit yang bisa kamu ajukan, selama penerimaan tiap unit
                                sedang dibuka dan kamu belum punya pengajuan aktif di unit yang sama. Keputusan akhir
                                tetap di pengurus masing-masing UKM.
                            </div>
                        </details>

                        <details class="group rounded-xl border border-slate-200 bg-white shadow-xs transition-colors open:bg-slate-50/70 dark:border-charcoal-line dark:bg-charcoal-raised dark:open:bg-charcoal-soft">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-4 text-base font-bold text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 [&::-webkit-details-marker]:hidden dark:text-zinc-100 dark:focus-visible:outline-blue-400">
                                <span>UKM tujuan saya sedang menutup penerimaan. Apa yang bisa saya lakukan?</span>
                                <flux:icon.chevron-down class="size-4 shrink-0 text-blue-700 transition-transform group-open:rotate-180 dark:text-blue-400" aria-hidden="true" />
                            </summary>
                            <div class="px-4 pb-4 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                Pantau label penerimaan di direktori halaman ini. Pengurus unit yang membuka dan
                                menutup periode penerimaan, dan belum ada notifikasi otomatis saat periode baru dibuka.
                            </div>
                        </details>

                        <details class="group rounded-xl border border-slate-200 bg-white shadow-xs transition-colors open:bg-slate-50/70 dark:border-charcoal-line dark:bg-charcoal-raised dark:open:bg-charcoal-soft">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-4 text-base font-bold text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 [&::-webkit-details-marker]:hidden dark:text-zinc-100 dark:focus-visible:outline-blue-400">
                                <span>Bagaimana pengurus UKM mendapat akses dashboard?</span>
                                <flux:icon.chevron-down class="size-4 shrink-0 text-blue-700 transition-transform group-open:rotate-180 dark:text-blue-400" aria-hidden="true" />
                            </summary>
                            <div class="px-4 pb-4 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                Akun dengan peran pengurus UKM disiapkan oleh Bagian Kemahasiswaan. Setelah akun aktif,
                                dashboard pengurus langsung terbuka untuk unit yang menjadi tanggung jawabnya.
                            </div>
                        </details>
                    </div>
                </div>
            </section>

            <section>
                <div class="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-12 lg:py-24">
                    <div class="relative flex flex-col justify-between gap-8 overflow-hidden rounded-xl border border-slate-800 bg-navy-950 p-8 text-white shadow-xl sm:p-12 lg:flex-row lg:items-center lg:p-16 dark:border-charcoal-line">
                        <div class="pointer-events-none absolute -bottom-16 -left-16 size-96 rounded-full bg-blue-500/10 blur-3xl" aria-hidden="true"></div>

                        <div class="z-10 max-w-xl">
                            <p class="font-label text-[10px] font-semibold uppercase tracking-[0.14em] text-blue-400">
                                Mahasiswa, pengurus, dan pembina kemahasiswaan
                            </p>
                            <h2 class="mt-3 text-2xl font-bold leading-tight tracking-[-0.02em] sm:text-3xl">
                                Masuk untuk memantau pendaftaran dan kegiatan yang kamu ikuti.
                            </h2>
                            <p class="mt-3 text-sm leading-6 text-slate-300">
                                Pastikan data NIM dan kontak di akunmu sudah benar sebelum mengirim pengajuan.
                            </p>
                        </div>

                        <div class="z-10 flex w-full shrink-0 flex-col gap-3 sm:w-auto sm:flex-row">
                            @auth
                                <a
                                    href="{{ route('dashboard') }}"
                                    class="inline-flex min-h-12 items-center justify-center bg-blue-600 px-6 text-sm font-semibold text-white shadow-md transition-colors hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                                >
                                    Buka dashboard
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex min-h-12 items-center justify-center bg-blue-600 px-6 text-sm font-semibold text-white shadow-md transition-colors hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                                >
                                    Masuk ke akun
                                </a>

                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="inline-flex min-h-12 items-center justify-center border border-slate-700 bg-slate-800/80 px-6 text-sm font-semibold text-slate-100 transition-colors hover:bg-slate-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                                    >
                                        Buat akun baru
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-slate-50 dark:border-charcoal-line dark:bg-charcoal-900">
            <div class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-12">
                <div class="grid gap-8 md:grid-cols-12">
                    <div class="md:col-span-5">
                        <x-app-logo :on-paper="true" :href="route('home')" />

                        <p class="mt-4 max-w-sm text-sm leading-6 text-slate-600 dark:text-zinc-400">
                            Sistem Informasi Pengelolaan Unit Kegiatan Mahasiswa (SIP-UKM) Politeknik Negeri
                            Banyuwangi. Direktori, pengajuan keanggotaan, kegiatan, tiket, dan presensi dalam satu akun.
                        </p>

                        <p class="mt-4 flex items-start gap-2 font-label text-[10px] uppercase tracking-[0.06em] text-slate-500 dark:text-zinc-400">
                            <flux:icon.map-pin class="mt-0.5 size-3.5 shrink-0" aria-hidden="true" />
                            Jl. Raya Jember KM 13 Labanasem, Kabat, Banyuwangi
                        </p>
                    </div>

                    <nav class="md:col-span-3" aria-label="Layanan portal">
                        <h2 class="text-base font-bold text-slate-950 dark:text-zinc-100">Layanan portal</h2>
                        <ul class="mt-2 text-sm text-slate-600 dark:text-zinc-400">
                            <li><a href="#direktori" class="inline-flex min-h-11 items-center transition-colors hover:text-slate-950 hover:underline dark:hover:text-zinc-100">Direktori UKM</a></li>
                            <li><a href="#fitur" class="inline-flex min-h-11 items-center transition-colors hover:text-slate-950 hover:underline dark:hover:text-zinc-100">Fitur layanan</a></li>
                            <li><a href="#alur" class="inline-flex min-h-11 items-center transition-colors hover:text-slate-950 hover:underline dark:hover:text-zinc-100">Alur pendaftaran</a></li>
                            <li><a href="#faq" class="inline-flex min-h-11 items-center transition-colors hover:text-slate-950 hover:underline dark:hover:text-zinc-100">Pusat pertanyaan</a></li>
                        </ul>
                    </nav>

                    <nav class="md:col-span-4" aria-label="Akun">
                        <h2 class="text-base font-bold text-slate-950 dark:text-zinc-100">Akun</h2>
                        <ul class="mt-2 text-sm text-slate-600 dark:text-zinc-400">
                            @auth
                                <li><a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center transition-colors hover:text-slate-950 hover:underline dark:hover:text-zinc-100">Dashboard</a></li>
                                <li><a href="{{ route('ukm.catalog') }}" class="inline-flex min-h-11 items-center transition-colors hover:text-slate-950 hover:underline dark:hover:text-zinc-100">Katalog UKM</a></li>
                                <li><a href="{{ route('tiket.index') }}" class="inline-flex min-h-11 items-center transition-colors hover:text-slate-950 hover:underline dark:hover:text-zinc-100">Tiket saya</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="inline-flex min-h-11 items-center transition-colors hover:text-slate-950 hover:underline dark:hover:text-zinc-100">Masuk</a></li>
                                @if (Route::has('register'))
                                    <li><a href="{{ route('register') }}" class="inline-flex min-h-11 items-center transition-colors hover:text-slate-950 hover:underline dark:hover:text-zinc-100">Buat akun</a></li>
                                @endif
                            @endauth
                        </ul>
                    </nav>
                </div>

                <div class="mt-8 flex flex-col justify-between gap-2 border-t border-slate-200 pt-4 font-label text-[10px] uppercase tracking-[0.06em] text-slate-500 sm:flex-row sm:items-center dark:border-charcoal-line dark:text-zinc-400">
                    <p>&copy; {{ $tahun }} Politeknik Negeri Banyuwangi.</p>
                    <p>SIP-UKM // Sistem Informasi Pengelolaan UKM</p>
                </div>
            </div>
        </footer>
    </body>
</html>
