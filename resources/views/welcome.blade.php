<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
    <head>
        @include('partials.head', ['title' => 'Direktori Unit Kegiatan Mahasiswa'])
    </head>
    <body class="min-h-svh bg-paper text-navy-900 antialiased dark:bg-navy-950 dark:text-slate-300">
        <a
            href="#konten"
            class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-navy-950 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white focus:outline-2 focus:outline-offset-2 focus:outline-sunrise-700 dark:focus:bg-paper dark:focus:text-navy-950 dark:focus:outline-white"
        >
            Lewati ke konten
        </a>

        <header class="sticky top-0 z-30 border-b border-navy-950/10 bg-paper dark:border-white/10 dark:bg-navy-950/95 dark:backdrop-blur">
            <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <x-app-logo :on-paper="true" :href="route('home')" />

                <nav class="flex items-center gap-1 sm:gap-2" aria-label="Navigasi utama">
                    <x-theme-toggle tone="paper" />

                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex min-h-11 items-center bg-navy-950 px-4 text-sm font-semibold text-white transition-colors hover:bg-navy-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sunrise-700 dark:bg-paper dark:text-navy-950 dark:hover:bg-white dark:focus-visible:outline-white"
                        >
                            Buka dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex min-h-11 items-center px-3 text-sm font-medium text-navy-900 underline-offset-4 transition-colors hover:text-sunrise-700 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sunrise-700 dark:text-slate-200 dark:hover:text-sunrise-300 dark:focus-visible:outline-white"
                        >
                            Masuk
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex min-h-11 items-center bg-navy-950 px-4 text-sm font-semibold text-white transition-colors hover:bg-navy-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sunrise-700 dark:bg-paper dark:text-navy-950 dark:hover:bg-white dark:focus-visible:outline-white"
                            >
                                Buat akun
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main id="konten">
                        {{-- Hero: satu fokus saja. Daftar UKM hidup di direktori, tidak diulang di sini. --}}
            <section class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="border-b border-navy-950/10 py-14 lg:py-20 dark:border-white/10">
                    <div class="max-w-3xl">
                        <h1 class="text-[clamp(2.2rem,5vw,3.6rem)] font-semibold leading-[1.02] tracking-[-0.035em] text-navy-950 dark:text-paper">
                            Temukan UKM yang cocok di Poliwangi.
                        </h1>

                        <p class="mt-6 max-w-xl text-base leading-7 text-slate-600 dark:text-slate-300">
                            Setiap unit kegiatan mahasiswa mencantumkan profil dan status penerimaan anggotanya, jadi kamu
                            tahu kapan bisa mengajukan keanggotaan. Pengurus mengelola anggota dan divisi dari akun masing-masing.
                        </p>

                        <div class="mt-9 flex flex-wrap items-center gap-3">
                            @auth
                                <a
                                    href="{{ route('dashboard') }}"
                                    class="inline-flex min-h-11 items-center bg-sunrise-700 px-5 text-sm font-semibold text-white transition-colors hover:bg-sunrise-700/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy-950 dark:focus-visible:outline-white"
                                >
                                    Buka dashboard
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex min-h-11 items-center bg-sunrise-700 px-5 text-sm font-semibold text-white transition-colors hover:bg-sunrise-700/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy-950 dark:focus-visible:outline-white"
                                >
                                    Masuk ke akun SIP-UKM
                                </a>
                            @endauth

                            <a
                                href="#direktori"
                                class="inline-flex min-h-11 items-center border border-navy-950/20 px-5 text-sm font-semibold text-navy-950 transition-colors hover:border-navy-950 hover:bg-paper-deep focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sunrise-700 dark:border-white/25 dark:text-paper dark:hover:border-white dark:hover:bg-white/10 dark:focus-visible:outline-white"
                            >
                                Lihat direktori UKM
                            </a>
                        </div>

                        <p class="mt-10 border-t border-navy-950/10 pt-4 text-sm tabular-nums text-slate-600 dark:border-white/10 dark:text-slate-400">
                            {{ number_format($totalAktif) }} UKM aktif dari {{ number_format($totalUkm) }} terdaftar,
                            <span class="font-semibold text-moss-700 dark:text-moss-400">{{ number_format($pendaftaranDibuka) }} sedang membuka penerimaan anggota.</span>
                        </p>
                    </div>
                </div>
            </section>

            {{-- Direktori: baris bergaris, bukan grid kartu seragam. --}}
            <section id="direktori" class="border-b border-navy-950/10 bg-paper-deep dark:border-white/10 dark:bg-navy-900/50">
                <div class="mx-auto w-full max-w-6xl px-4 py-14 sm:px-6 sm:py-18 lg:px-8">
                    <div class="flex flex-col gap-4 border-b border-navy-950/15 pb-6 sm:flex-row sm:items-end sm:justify-between dark:border-white/15">
                        <div>
                            <h2 class="text-2xl font-semibold tracking-[-0.02em] text-navy-950 sm:text-3xl dark:text-paper">Direktori UKM</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                                Hanya unit berstatus aktif yang ditampilkan, diurutkan menurut nama. Label penerimaan anggota
                                diperbarui oleh pengurus masing-masing unit.
                            </p>
                        </div>

                        <p class="shrink-0 text-sm tabular-nums text-slate-600 dark:text-slate-400">
                            {{ number_format($ukm->count()) }} organisasi
                        </p>
                    </div>

                    @if ($ukm->isEmpty())
                        <p class="max-w-xl py-12 text-sm leading-6 text-slate-600 dark:text-slate-300">
                            Belum ada UKM berstatus aktif yang bisa ditampilkan. Hubungi Bagian Kemahasiswaan bila daftar ini
                            seharusnya sudah terisi.
                        </p>
                    @else
                        <ol class="grid gap-x-12 md:grid-cols-2">
                            @foreach ($ukm as $item)
                                <li class="border-b border-navy-950/10 py-6 dark:border-white/10">
                                    <div class="flex items-start gap-4">
                                        <span class="w-6 shrink-0 pt-1 text-xs tabular-nums text-slate-500 dark:text-slate-400" aria-hidden="true">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </span>

                                        @if ($item->logoUrl())
                                            <img
                                                src="{{ $item->logoUrl() }}"
                                                alt="Logo {{ $item->nama }}"
                                                width="40"
                                                height="40"
                                                loading="lazy"
                                                class="size-10 shrink-0 border border-navy-950/10 bg-white object-cover dark:border-white/15"
                                            >
                                        @else
                                            <span class="flex size-10 shrink-0 items-center justify-center bg-navy-950 text-xs font-semibold text-white dark:bg-paper dark:text-navy-950" aria-hidden="true">
                                                {{ $item->inisial() }}
                                            </span>
                                        @endif

                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                                <h3 class="text-base font-semibold leading-6 text-navy-950 dark:text-paper">{{ $item->nama }}</h3>

                                                {{-- Status adalah keputusan utama pengunjung, jadi diberi warna semantik sendiri. --}}
                                                @if ($item->isPendaftaranDibuka())
                                                    <span class="inline-flex items-center gap-1.5 bg-moss-50 px-2 py-1 text-xs font-semibold text-moss-700 dark:bg-moss-700/25 dark:text-moss-50">
                                                        <span class="size-1.5 shrink-0 bg-moss-700 dark:bg-moss-400" aria-hidden="true"></span>
                                                        Penerimaan dibuka
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 bg-rust-50 px-2 py-1 text-xs font-semibold text-rust-700 dark:bg-rust-700/25 dark:text-rust-50">
                                                        <span class="size-1.5 shrink-0 border border-rust-700 dark:border-rust-400" aria-hidden="true"></span>
                                                        Penerimaan ditutup
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-slate-400">
                                                {{ \Illuminate\Support\Str::limit($item->deskripsi ?: 'Deskripsi UKM ini belum diisi pengurus.', 150) }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>

            {{-- Alur: daftar bernomor vertikal, komposisi berbeda dari dua section di atas. --}}
            <section class="border-b border-navy-950/10 dark:border-white/10">
                <div class="mx-auto grid w-full max-w-6xl gap-10 px-4 py-14 sm:px-6 sm:py-18 lg:grid-cols-[0.85fr_1.15fr] lg:gap-16 lg:px-8">
                    <div>
                        <h2 class="text-2xl font-semibold tracking-[-0.02em] text-navy-950 sm:text-3xl dark:text-paper">
                            Dari akun baru sampai resmi jadi anggota.
                        </h2>

                        <p class="mt-4 max-w-md text-sm leading-6 text-slate-600 dark:text-slate-300">
                            Kamu perlu akun dulu sebelum bisa melihat direktori dari dalam sistem dan mengirim pengajuan.
                        </p>

                        <p class="mt-8 max-w-md border-t border-navy-950/10 pt-4 text-sm leading-6 text-slate-600 dark:border-white/10 dark:text-slate-300">
                            Pengajuan hanya bisa dikirim selama UKM membuka penerimaan anggota. Di luar masa itu, tombol
                            pengajuan tidak tersedia.
                        </p>
                    </div>

                    <ol class="border-t border-navy-950/15 dark:border-white/15">
                        <li class="grid grid-cols-[2.5rem_1fr] gap-5 border-b border-navy-950/15 py-6 dark:border-white/15">
                            <span class="text-xl font-semibold tabular-nums text-sunrise-700 dark:text-sunrise-300" aria-hidden="true">01</span>
                            <div>
                                <h3 class="text-base font-semibold text-navy-950 dark:text-paper">Buat akun dan lengkapi data diri</h3>
                                <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-slate-400">
                                    Daftar dengan email aktif, lalu isi NIM dan data mahasiswa pada halaman pengaturan akun.
                                </p>
                            </div>
                        </li>

                        <li class="grid grid-cols-[2.5rem_1fr] gap-5 border-b border-navy-950/15 py-6 dark:border-white/15">
                            <span class="text-xl font-semibold tabular-nums text-sunrise-700 dark:text-sunrise-300" aria-hidden="true">02</span>
                            <div>
                                <h3 class="text-base font-semibold text-navy-950 dark:text-paper">Baca profil UKM yang kamu minati</h3>
                                <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-slate-400">
                                    Katalog memuat deskripsi, visi, misi, dan daftar divisi tiap UKM, jadi keputusanmu
                                    berangkat dari isi organisasinya.
                                </p>
                            </div>
                        </li>

                        <li class="grid grid-cols-[2.5rem_1fr] gap-5 py-6">
                            <span class="text-xl font-semibold tabular-nums text-sunrise-700 dark:text-sunrise-300" aria-hidden="true">03</span>
                            <div>
                                <h3 class="text-base font-semibold text-navy-950 dark:text-paper">Kirim pengajuan keanggotaan</h3>
                                <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-slate-400">
                                    Ajukan saat penerimaan dibuka, lalu pantau statusnya dari dashboard dan ikuti kegiatan
                                    yang diselenggarakan UKM tersebut.
                                </p>
                            </div>
                        </li>
                    </ol>
                </div>
            </section>

            {{-- Penutup: blok tinta penuh, satu aksi utama. --}}
            <section class="bg-navy-950 text-white">
                <div class="mx-auto grid w-full max-w-6xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-[1.3fr_1fr] lg:items-end lg:px-8">
                    <div>
                        <p class="text-sm text-navy-200">Untuk mahasiswa, pengurus UKM, dan Bagian Kemahasiswaan.</p>
                        <h2 class="mt-3 max-w-xl text-[clamp(1.6rem,3.2vw,2.4rem)] font-semibold leading-tight tracking-[-0.02em]">
                            Masuk untuk memantau pendaftaran dan kegiatan yang kamu ikuti.
                        </h2>
                    </div>

                    <div class="flex flex-wrap gap-3 lg:justify-end">
                        @auth
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex min-h-11 items-center bg-sunrise-300 px-5 text-sm font-semibold text-navy-950 transition-colors hover:bg-sunrise-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                            >
                                Buka dashboard
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex min-h-11 items-center bg-sunrise-300 px-5 text-sm font-semibold text-navy-950 transition-colors hover:bg-sunrise-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                            >
                                Masuk
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex min-h-11 items-center border border-white/30 px-5 text-sm font-semibold text-white transition-colors hover:border-white hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                                >
                                    Buat akun
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-navy-950 text-navy-200">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-2 border-t border-white/10 px-4 py-6 text-sm sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>SIP-UKM, Sistem Informasi Pengelolaan UKM</p>
                <p class="tabular-nums">Politeknik Negeri Banyuwangi, {{ now()->year }}</p>
            </div>
        </footer>
    </body>
</html>
