<x-ui.page>
    <x-ui.page-header
        eyebrow="Keanggotaan UKM"
        title="Pengajuan Anggota"
        description="Periksa pengajuan mahasiswa untuk {{ auth()->user()->ukm?->nama ?? 'UKM kamu' }}, lalu terima atau tolak sesuai hasil seleksi."
    />

    @if (session('status'))
        <div role="status" class="flex items-start gap-3 rounded-xl border border-moss-700/25 bg-moss-50 px-4 py-3 text-sm text-moss-700 dark:border-moss-700/50 dark:bg-moss-700/20 dark:text-moss-50">
            <flux:icon name="check-circle" class="mt-0.5 size-4 shrink-0" />
            <p class="font-medium">{{ session('status') }}</p>
        </div>
    @endif

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan pengajuan anggota">
        <x-ui.stat label="Menunggu" :value="$statistik['pending']" note="Belum diproses" icon="clock" tone="amber" />
        <x-ui.stat label="Diterima" :value="$statistik['diterima']" note="Menjadi anggota aktif" icon="check-circle" tone="emerald" />
        <x-ui.stat label="Ditolak" :value="$statistik['ditolak']" note="Tidak lolos seleksi" icon="x-circle" tone="slate" />
        <x-ui.stat label="Total Pengajuan" :value="$statistik['semua']" note="Seluruh pengajuan UKM" icon="clipboard-document-list" tone="navy" />
    </section>

    <x-ui.panel flush>
        <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
            <div class="flex flex-wrap gap-1.5" role="group" aria-label="Filter status pengajuan">
                @foreach (['pending' => 'Menunggu', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'semua' => 'Semua'] as $value => $label)
                    <button
                        type="button"
                        wire:click="setFilter('{{ $value }}')"
                        aria-pressed="{{ $filter === $value ? 'true' : 'false' }}"
                        @class([
                            'inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-medium transition-colors',
                            'border-navy-950 bg-navy-950 text-white dark:border-white dark:bg-white dark:text-navy-950' => $filter === $value,
                            'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900 dark:border-white/10 dark:bg-transparent dark:text-zinc-300 dark:hover:border-white/25 dark:hover:text-white' => $filter !== $value,
                        ])
                    >
                        {{ $label }}
                        <span class="tabular-nums opacity-70">{{ $statistik[$value] }}</span>
                    </button>
                @endforeach
            </div>

            <p class="flex items-baseline gap-2 text-sm text-slate-600 dark:text-zinc-300">
                <span class="text-xl font-semibold tabular-nums text-navy-950 dark:text-white">{{ $pengajuan->total() }}</span>
                <span>pengajuan</span>
            </p>
        </div>

        @if ($pengajuan->isEmpty())
            <x-ui.empty
                icon="clipboard-document-list"
                title="Tidak ada pengajuan"
                description="Belum ada pengajuan pada kategori ini. Coba pilih status lain."
            />
        @else
            <div class="overflow-x-auto px-4">
                <table class="w-full min-w-[720px] table-fixed text-left text-sm">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                            <th scope="col" class="w-[24%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Mahasiswa</th>
                            <th scope="col" class="w-[26%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Alasan bergabung</th>
                            <th scope="col" class="w-[13%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Diajukan</th>
                            <th scope="col" class="w-[12%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Status</th>
                            <th scope="col" class="w-[25%] border-b border-slate-200 py-3 text-start font-semibold dark:border-white/10">Tindakan</th>
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

                            <tr wire:key="pengajuan-{{ $item->id }}" class="border-b border-slate-100 align-top transition-colors last:border-0 hover:bg-slate-50/80 dark:border-white/5 dark:hover:bg-white/5">
                                <td class="py-3.5 pe-3 align-top">
                                    <div class="flex items-start gap-3">
                                        <x-user-avatar :user="$item->user" size="size-9" font="text-xs" />

                                        <div class="min-w-0">
                                            <p class="font-medium text-slate-950 dark:text-white">{{ $item->user->name }}</p>
                                            <p class="truncate font-mono text-xs text-slate-500 dark:text-zinc-400">
                                                {{ $item->user->nim ?: $item->user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 pe-3 align-top">
                                    <p class="whitespace-pre-line text-xs leading-5 text-slate-600 dark:text-zinc-300">{{ $item->jawaban_formulir['alasan'] ?? '—' }}</p>
                                </td>

                                <td class="py-3.5 pe-3 align-top text-xs tabular-nums text-slate-500 dark:text-zinc-400">
                                    {{ $item->diajukan_pada?->format('d M Y, H:i') ?? '-' }}
                                </td>

                                <td class="py-3.5 pe-3 align-top">
                                    <x-ui.badge :tone="$statusTone">{{ $statusLabel }}</x-ui.badge>

                                    @if ($item->diputuskan_pada)
                                        <p class="mt-1 text-[11px] text-slate-500 dark:text-zinc-400">
                                            {{ $item->diputuskan_pada->format('d M Y') }}
                                        </p>
                                    @endif
                                </td>

                                <td class="py-3.5 align-top">
                                    @if ($item->status === 'pending')
                                        <div class="space-y-2">
                                            <flux:textarea wire:model="catatan.{{ $item->id }}" placeholder="Catatan penolakan (opsional)" rows="2" />

                                            @error("catatan.{$item->id}")
                                                <p class="text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                            @enderror

                                            <div class="flex flex-wrap gap-2">
                                                <flux:button size="sm" variant="primary" wire:click="terima({{ $item->id }})" wire:confirm="Terima pengajuan mahasiswa ini?">
                                                    Terima
                                                </flux:button>
                                                <flux:button size="sm" variant="danger" wire:click="tolak({{ $item->id }})" wire:confirm="Tolak pengajuan mahasiswa ini?">
                                                    Tolak
                                                </flux:button>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-xs leading-5 text-slate-600 dark:text-zinc-300">{{ $item->catatan_pengurus ?: '—' }}</p>
                                        @if ($item->pemutus)
                                            <p class="mt-1 text-[11px] text-slate-500 dark:text-zinc-400">oleh {{ $item->pemutus->name }}</p>
                                        @endif
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
</x-ui.page>
