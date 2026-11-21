<x-ui.page>
    <x-ui.page-header
        eyebrow="Administrasi keanggotaan"
        title="Pemantauan Pengajuan Anggota"
        description="Pantau pengajuan anggota dari seluruh UKM beserta rekap keanggotaan aktifnya. Halaman ini hanya-baca; keputusan tetap dilakukan pengurus UKM masing-masing."
    >
        <x-slot:actions>
            <x-ui.badge tone="neutral">Hanya baca</x-ui.badge>
        </x-slot:actions>
    </x-ui.page-header>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan pengajuan anggota lintas UKM">
        <x-ui.stat label="Menunggu" :value="$statistik['pending']" note="Belum diputuskan pengurus" icon="clock" tone="amber" />
        <x-ui.stat label="Diterima" :value="$statistik['diterima']" note="Tercatat sebagai anggota" icon="check-circle" tone="emerald" />
        <x-ui.stat label="Ditolak" :value="$statistik['ditolak']" note="Tidak lolos seleksi" icon="x-circle" tone="slate" />
        <x-ui.stat label="Total Pengajuan" :value="$statistik['semua']" note="Seluruh UKM" icon="clipboard-document-list" tone="navy" />
    </section>

    <x-ui.panel flush>
        <div class="space-y-3 border-b border-slate-200 p-4 dark:border-white/10">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <div class="w-full lg:max-w-md">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama, NIM, atau email mahasiswa"
                        icon="magnifying-glass"
                        clearable
                    />
                </div>

                <div class="w-full lg:max-w-xs">
                    <flux:select wire:model.live="filterUkm" aria-label="Filter UKM">
                        <option value="">Semua UKM</option>
                        @foreach ($ukmList as $ukm)
                            <option value="{{ $ukm->id }}">{{ $ukm->nama }}</option>
                        @endforeach
                    </flux:select>
                </div>

                @if ($search !== '' || $filterUkm !== '' || $filterStatus !== 'semua')
                    <flux:button variant="subtle" size="sm" wire:click="resetFilters" icon="arrow-path">
                        Reset filter
                    </flux:button>
                @endif
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap gap-1.5" role="group" aria-label="Filter status pengajuan">
                    @foreach (['semua' => 'Semua', 'pending' => 'Menunggu', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $value => $label)
                        <button
                            type="button"
                            wire:click="setFilterStatus('{{ $value }}')"
                            aria-pressed="{{ $filterStatus === $value ? 'true' : 'false' }}"
                            @class([
                                'inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-medium transition-colors',
                                'border-navy-950 bg-navy-950 text-white dark:border-white dark:bg-white dark:text-navy-950' => $filterStatus === $value,
                                'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900 dark:border-white/10 dark:bg-transparent dark:text-zinc-300 dark:hover:border-white/25 dark:hover:text-white' => $filterStatus !== $value,
                            ])
                        >
                            {{ $label }}
                            <span class="tabular-nums opacity-70">{{ $statistik[$value] }}</span>
                        </button>
                    @endforeach
                </div>

                <p class="flex items-baseline gap-2 text-sm text-slate-600 dark:text-zinc-300">
                    <span class="text-xl font-semibold tabular-nums text-navy-950 dark:text-white">{{ $pengajuan->total() }}</span>
                    <span>pengajuan ditemukan</span>
                </p>
            </div>
        </div>

        @if ($pengajuan->isEmpty())
            <x-ui.empty
                icon="clipboard-document-list"
                title="Tidak ada pengajuan"
                description="Belum ada pengajuan yang cocok dengan pencarian atau filter ini."
            />
        @else
            <div class="overflow-x-auto px-4">
                <table class="w-full min-w-[720px] table-fixed text-left text-sm">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                            <th scope="col" class="w-[24%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Mahasiswa</th>
                            <th scope="col" class="w-[18%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">UKM</th>
                            <th scope="col" class="w-[22%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Alasan bergabung</th>
                            <th scope="col" class="w-[11%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Diajukan</th>
                            <th scope="col" class="w-[10%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Status</th>
                            <th scope="col" class="w-[15%] border-b border-slate-200 py-3 text-start font-semibold dark:border-white/10">Keputusan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pengajuan as $item)
                            @php
                                $statusTone = match ($item->status) {
                                    'pending' => 'accent',
                                    'diterima' => 'positive',
                                    default => 'negative',
                                };
                                $statusLabel = match ($item->status) {
                                    'pending' => 'Menunggu',
                                    'diterima' => 'Diterima',
                                    default => 'Ditolak',
                                };
                            @endphp

                            <tr wire:key="pemantauan-{{ $item->id }}" class="border-b border-slate-100 align-top transition-colors last:border-0 hover:bg-slate-50/80 dark:border-white/5 dark:hover:bg-white/5">
                                <td class="py-3.5 pe-3 align-top">
                                    <div class="flex items-start gap-3">
                                        <x-user-avatar :user="$item->user" size="size-9" font="text-xs" />

                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-slate-950 dark:text-white">{{ $item->user->name }}</p>
                                            <p class="truncate font-mono text-xs text-slate-500 dark:text-zinc-400">
                                                {{ $item->user->nim ?: $item->user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 pe-3 align-top">
                                    <p class="text-xs font-medium text-slate-800 dark:text-zinc-200">{{ $item->ukm->nama }}</p>
                                </td>

                                <td class="py-3.5 pe-3 align-top">
                                    <p class="line-clamp-2 text-xs leading-5 text-slate-600 dark:text-zinc-300">{{ $item->jawaban_formulir['alasan'] ?? '—' }}</p>
                                </td>

                                <td class="py-3.5 pe-3 align-top text-xs tabular-nums text-slate-500 dark:text-zinc-400">
                                    {{ $item->diajukan_pada?->format('d M Y') ?? '-' }}
                                </td>

                                <td class="py-3.5 pe-3 align-top">
                                    <x-ui.badge :tone="$statusTone">{{ $statusLabel }}</x-ui.badge>
                                </td>

                                <td class="py-3.5 align-top text-xs leading-5 text-slate-600 dark:text-zinc-300">
                                    @if ($item->diputuskan_pada)
                                        <p>{{ $item->pemutus?->name ?? 'Pengurus UKM' }}</p>
                                        <p class="text-[11px] text-slate-500 dark:text-zinc-400">{{ $item->diputuskan_pada->format('d M Y') }}</p>
                                    @else
                                        <span class="text-slate-400 dark:text-zinc-500">Belum diputuskan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($pengajuan->hasPages())
                <div class="border-t border-slate-200 p-4 dark:border-white/10">
                    {{ $pengajuan->links() }}
                </div>
            @endif
        @endif
    </x-ui.panel>

    <x-ui.panel flush>
        <div class="flex flex-col gap-1 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
            <div>
                <h2 class="text-sm font-semibold tracking-tight text-slate-950 dark:text-white">Rekap lintas UKM</h2>
                <p class="mt-0.5 text-xs leading-5 text-slate-500 dark:text-zinc-400">
                    Anggota aktif dihitung dari data keanggotaan berstatus aktif, bukan dari pengajuan yang diterima.
                </p>
            </div>

            <p class="flex items-baseline gap-2 text-sm text-slate-600 dark:text-zinc-300">
                <span class="text-xl font-semibold tabular-nums text-navy-950 dark:text-white">{{ number_format($rekap->sum('anggota_aktif_count')) }}</span>
                <span>anggota aktif</span>
            </p>
        </div>

        <div class="divide-y divide-slate-100 md:hidden dark:divide-white/5">
            @foreach ($rekap as $ukm)
                <div class="px-5 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-medium text-slate-950 dark:text-white">{{ $ukm->nama }}</p>
                        @if (! $ukm->isAktif())
                            <x-ui.badge tone="neutral" :marker="false">Nonaktif</x-ui.badge>
                        @endif
                    </div>

                    <dl class="mt-3 grid grid-cols-2 gap-x-6 gap-y-1.5 text-xs">
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="text-slate-500 dark:text-zinc-400">Anggota aktif</dt>
                            <dd class="font-semibold tabular-nums text-slate-950 dark:text-white">{{ number_format($ukm->anggota_aktif_count) }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="text-slate-500 dark:text-zinc-400">Total pengajuan</dt>
                            <dd class="font-medium tabular-nums text-slate-950 dark:text-white">{{ number_format($ukm->pengajuan_count) }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="text-slate-500 dark:text-zinc-400">Menunggu</dt>
                            <dd class="tabular-nums text-slate-600 dark:text-zinc-300">{{ number_format($ukm->pengajuan_pending_count) }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="text-slate-500 dark:text-zinc-400">Diterima</dt>
                            <dd class="tabular-nums text-slate-600 dark:text-zinc-300">{{ number_format($ukm->pengajuan_diterima_count) }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="text-slate-500 dark:text-zinc-400">Ditolak</dt>
                            <dd class="tabular-nums text-slate-600 dark:text-zinc-300">{{ number_format($ukm->pengajuan_ditolak_count) }}</dd>
                        </div>
                    </dl>
                </div>
            @endforeach
        </div>

        <div class="hidden overflow-x-auto px-4 md:block">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        <th scope="col" class="border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">UKM</th>
                        <th scope="col" class="border-b border-slate-200 py-3 pe-3 text-end font-semibold dark:border-white/10">Anggota aktif</th>
                        <th scope="col" class="border-b border-slate-200 py-3 pe-3 text-end font-semibold dark:border-white/10">Menunggu</th>
                        <th scope="col" class="border-b border-slate-200 py-3 pe-3 text-end font-semibold dark:border-white/10">Diterima</th>
                        <th scope="col" class="border-b border-slate-200 py-3 pe-3 text-end font-semibold dark:border-white/10">Ditolak</th>
                        <th scope="col" class="border-b border-slate-200 py-3 text-end font-semibold dark:border-white/10">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($rekap as $ukm)
                        <tr class="border-b border-slate-100 last:border-0 dark:border-white/5">
                            <td class="py-3 pe-3 align-middle">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium text-slate-950 dark:text-white">{{ $ukm->nama }}</span>
                                    @if (! $ukm->isAktif())
                                        <x-ui.badge tone="neutral" :marker="false">Nonaktif</x-ui.badge>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 pe-3 text-end align-middle font-semibold tabular-nums text-slate-950 dark:text-white">{{ number_format($ukm->anggota_aktif_count) }}</td>
                            <td class="py-3 pe-3 text-end align-middle tabular-nums text-slate-600 dark:text-zinc-300">{{ number_format($ukm->pengajuan_pending_count) }}</td>
                            <td class="py-3 pe-3 text-end align-middle tabular-nums text-slate-600 dark:text-zinc-300">{{ number_format($ukm->pengajuan_diterima_count) }}</td>
                            <td class="py-3 pe-3 text-end align-middle tabular-nums text-slate-600 dark:text-zinc-300">{{ number_format($ukm->pengajuan_ditolak_count) }}</td>
                            <td class="py-3 text-end align-middle font-medium tabular-nums text-slate-950 dark:text-white">{{ number_format($ukm->pengajuan_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-ui.page>
