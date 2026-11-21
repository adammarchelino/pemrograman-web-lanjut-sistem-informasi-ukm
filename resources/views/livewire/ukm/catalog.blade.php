<x-ui.page>
    <x-ui.page-header
        eyebrow="Direktori organisasi"
        title="Katalog UKM"
        description="Lihat unit kegiatan mahasiswa yang sedang aktif beserta status pendaftaran anggotanya."
    />

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="w-full sm:max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau deskripsi UKM" icon="magnifying-glass" clearable />
        </div>

        <p class="flex items-baseline gap-2 text-sm text-slate-600 dark:text-zinc-300">
            <span class="text-xl font-semibold tabular-nums text-navy-950 dark:text-white">{{ $ukms->total() }}</span>
            <span>UKM aktif ditemukan</span>
        </p>
    </div>

    @if ($ukms->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-300 bg-white dark:border-white/15 dark:bg-zinc-900">
            <x-ui.empty
                icon="building-library"
                title="Belum ada UKM yang cocok"
                description="Coba kata kunci lain, atau kembali lagi saat pendaftaran UKM dibuka."
            />
        </div>
    @else
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($ukms as $ukm)
                <article wire:key="ukm-card-{{ $ukm->id }}" class="group flex h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs transition-colors hover:border-blue-300 dark:border-white/10 dark:bg-zinc-900 dark:hover:border-blue-800">
                    <div class="flex items-center gap-3 border-b border-slate-100 p-5 dark:border-white/10">
                        @if ($ukm->logoUrl())
                            <img src="{{ $ukm->logoUrl() }}" alt="Logo {{ $ukm->nama }}" class="size-12 shrink-0 rounded-lg border border-slate-200 object-cover dark:border-white/10" loading="lazy">
                        @else
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-navy-950 text-sm font-semibold text-white">{{ $ukm->inisial() }}</div>
                        @endif

                        <div class="min-w-0">
                            <h2 class="line-clamp-2 text-base font-semibold leading-snug text-slate-950 dark:text-white">{{ $ukm->nama }}</h2>
                            <x-ui.badge :tone="$ukm->isPendaftaranDibuka() ? 'positive' : 'negative'" class="mt-1.5">
                                Pendaftaran {{ $ukm->isPendaftaranDibuka() ? 'dibuka' : 'ditutup' }}
                            </x-ui.badge>
                        </div>
                    </div>

                    <p class="flex-1 p-5 text-sm leading-6 text-slate-600 dark:text-zinc-400">{{ $ukm->deskripsi ?: 'Deskripsi UKM belum tersedia.' }}</p>

                    @if (auth()->user()->role === \App\Models\User::ROLE_MAHASISWA)
                        <div class="border-t border-slate-100 p-5 pt-4 dark:border-white/10">
                            @if ($ukm->sudah_menjadi_anggota)
                                <div class="flex items-center justify-between gap-3">
                                    <x-ui.badge tone="positive">Anggota aktif</x-ui.badge>
                                    <span class="text-xs text-slate-500 dark:text-zinc-400">Kamu sudah terdaftar</span>
                                </div>
                            @elseif ($ukm->sudah_mengajukan)
                                <div class="flex items-center justify-between gap-3">
                                    <x-ui.badge tone="warning">Pengajuan terkirim</x-ui.badge>
                                    <flux:button size="sm" variant="subtle" :href="route('pendaftaran-anggota.create')" wire:navigate>Lihat status</flux:button>
                                </div>
                            @elseif ($ukm->isPendaftaranDibuka())
                                <flux:button size="sm" variant="primary" class="w-full" icon="user-plus" :href="route('pendaftaran-anggota.create', ['ukm' => $ukm->id])" wire:navigate>
                                    Daftar UKM
                                </flux:button>
                            @else
                                <p class="text-xs text-slate-500 dark:text-zinc-400">Pendaftaran anggota sedang ditutup.</p>
                            @endif
                        </div>
                    @endif
                </article>
            @endforeach
        </section>

        <div>{{ $ukms->links() }}</div>
    @endif
</x-ui.page>
