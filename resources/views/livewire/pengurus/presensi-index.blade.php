<x-ui.page>
    <x-ui.page-header
        eyebrow="Ruang pengurus"
        title="Presensi Acara"
        description="Pilih acara UKM yang kamu kelola untuk memvalidasi tiket dan merekap kehadiran peserta."
    >
        <x-slot:actions>
            <flux:button variant="subtle" icon="calendar-days" :href="route('kegiatan.index')" wire:navigate>
                Kelola kegiatan
            </flux:button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel flush>
        <div class="overflow-x-auto px-4">
            <table class="w-full min-w-[820px] table-fixed text-left text-sm">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        <th scope="col" class="w-[4%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">No</th>
                        <th scope="col" class="w-[36%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Acara</th>
                        <th scope="col" class="w-[20%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Jadwal</th>
                        <th scope="col" class="w-[10%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Terdaftar</th>
                        <th scope="col" class="w-[10%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Hadir</th>
                        <th scope="col" class="w-[20%] border-b border-slate-200 py-3 text-end font-semibold dark:border-white/10">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($kegiatan as $item)
                        <tr wire:key="presensi-{{ $item->id }}" class="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50/80 dark:border-white/5 dark:hover:bg-white/5">
                            <td class="py-3.5 pe-3 align-middle text-xs tabular-nums text-slate-500 dark:text-zinc-400">
                                {{ $loop->iteration }}
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                <div class="truncate text-sm font-medium text-slate-950 dark:text-white">{{ $item->nama }}</div>
                                <div class="mt-1">
                                    <x-ui.badge :tone="$item->status === 'dipublikasikan' ? 'positive' : 'neutral'">
                                        {{ $item->status === 'dipublikasikan' ? 'Dipublikasikan' : 'Draf' }}
                                    </x-ui.badge>
                                </div>
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                <div class="text-xs text-slate-700 dark:text-zinc-300">
                                    {{ $item->mulai_pada?->format('d M Y, H:i') ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-zinc-400">
                                    {{ $item->lokasi ?: '-' }}
                                </div>
                            </td>

                            <td class="py-3.5 pe-3 align-middle text-xs tabular-nums text-slate-700 dark:text-zinc-300">
                                {{ $item->jumlah_terdaftar }}
                            </td>

                            <td class="py-3.5 pe-3 align-middle text-xs tabular-nums text-slate-700 dark:text-zinc-300">
                                {{ $item->jumlah_hadir }}
                            </td>

                            <td class="py-3.5 align-middle text-end">
                                <flux:button
                                    size="xs"
                                    variant="subtle"
                                    :href="route('pengurus.kegiatan.presensi', $item)"
                                    icon="qr-code"
                                    title="Buka presensi {{ $item->nama }}"
                                    wire:navigate
                                >
                                    Buka presensi
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-0">
                                <x-ui.empty
                                    icon="qr-code"
                                    title="Belum ada acara"
                                    description="Acara yang dibuat UKM kamu akan muncul di sini untuk presensi peserta."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-ui.page>
