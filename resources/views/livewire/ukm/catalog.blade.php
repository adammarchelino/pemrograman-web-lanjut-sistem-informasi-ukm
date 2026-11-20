<div class="mx-auto max-w-7xl space-y-6 p-6 lg:p-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-blue-700 dark:text-blue-300">Temukan komunitasmu</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Katalog UKM</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-600 dark:text-zinc-300">Lihat unit kegiatan mahasiswa yang sedang aktif beserta status pendaftaran anggotanya.</p>
        </div>
        <div class="w-full sm:max-w-sm">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau deskripsi UKM" icon="magnifying-glass" clearable />
        </div>
    </header>

    @if ($ukms->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 p-12 text-center dark:border-zinc-700">
            <flux:icon name="building-library" class="mx-auto size-10 text-slate-400" />
            <h2 class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">Belum ada UKM aktif</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">UKM aktif akan muncul di sini.</p>
        </div>
    @else
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($ukms as $ukm)
                <article wire:key="ukm-card-{{ $ukm->id }}" class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="flex items-center gap-3">
                        @if ($ukm->logoUrl())
                            <img src="{{ $ukm->logoUrl() }}" alt="Logo {{ $ukm->nama }}" class="size-14 rounded-xl border border-slate-200 object-cover dark:border-zinc-700">
                        @else
                            <div class="flex size-14 items-center justify-center rounded-xl bg-blue-50 text-lg font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300">{{ $ukm->inisial() }}</div>
                        @endif
                        <div class="min-w-0">
                            <h2 class="font-semibold text-slate-900 dark:text-white">{{ $ukm->nama }}</h2>
                            <flux:badge size="sm" variant="pill" :color="$ukm->isPendaftaranDibuka() ? 'emerald' : 'zinc'">
                                Pendaftaran {{ $ukm->isPendaftaranDibuka() ? 'dibuka' : 'ditutup' }}
                            </flux:badge>
                        </div>
                    </div>
                    <p class="mt-4 flex-1 text-sm leading-6 text-slate-600 dark:text-zinc-300">{{ $ukm->deskripsi ?: 'Deskripsi UKM belum tersedia.' }}</p>

                    @if (auth()->user()->role === 'mahasiswa')
                        <div class="mt-5 border-t border-slate-100 pt-4 dark:border-zinc-700">
                            @if ($ukm->sudah_menjadi_anggota)
                                <flux:badge color="green" icon="check-circle">Anda sudah menjadi anggota</flux:badge>
                            @elseif ($ukm->sudah_mengajukan)
                                <flux:button class="w-full" variant="filled" :href="route('pendaftaran-anggota.create')" wire:navigate>
                                    Lihat Status Pengajuan
                                </flux:button>
                            @elseif ($ukm->isPendaftaranDibuka())
                                <flux:button class="w-full" variant="primary" :href="route('pendaftaran-anggota.create', ['ukm' => $ukm->id])" wire:navigate>
                                    Daftar UKM
                                </flux:button>
                            @else
                                <flux:button class="w-full" variant="filled" disabled>
                                    Pendaftaran Ditutup
                                </flux:button>
                            @endif
                        </div>
                    @endif
                </article>
            @endforeach
        </section>
        <div>{{ $ukms->links() }}</div>
    @endif
</div>
