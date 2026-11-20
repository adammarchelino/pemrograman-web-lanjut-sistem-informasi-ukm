<x-ui.page>
    <x-ui.page-header
        eyebrow="Keanggotaan UKM"
        title="Pendaftaran Anggota"
        description="Ajukan diri ke UKM yang sedang membuka penerimaan anggota, lalu pantau status setiap pengajuanmu di halaman ini."
    />

    @if (session('status'))
        <div role="status" class="flex items-start gap-3 rounded-xl border border-moss-700/25 bg-moss-50 px-4 py-3 text-sm text-moss-700 dark:border-moss-700/50 dark:bg-moss-700/20 dark:text-moss-50">
            <flux:icon name="check-circle" class="mt-0.5 size-4 shrink-0" />
            <p class="font-medium">{{ session('status') }}</p>
        </div>
    @endif

    <x-ui.panel
        title="Formulir pengajuan"
        description="Satu pengajuan aktif per UKM. Pengajuan diperiksa oleh pengurus UKM tujuan."
        icon="user-plus"
    >
        @if ($ukms->isEmpty())
            <x-ui.empty
                icon="building-library"
                title="Belum ada UKM yang membuka pendaftaran"
                description="Semua pengajuan aktifmu sedang diproses, atau belum ada UKM yang membuka penerimaan anggota saat ini."
            />
        @else
            <form wire:submit="ajukan" class="space-y-5">
                <flux:select wire:model="ukmId" label="UKM tujuan" placeholder="Pilih UKM">
                    @foreach ($ukms as $ukm)
                        <flux:select.option :value="$ukm->id">{{ $ukm->nama }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:textarea
                    wire:model="alasan"
                    label="Alasan bergabung"
                    description="Minimal 10 karakter. Jelaskan minat dan kontribusi yang ingin kamu berikan."
                    placeholder="Ceritakan alasan dan minatmu bergabung dengan UKM ini..."
                    rows="5"
                />

                <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                    <p class="text-xs leading-5 text-slate-500 dark:text-zinc-400">
                        Status pengajuan dapat dipantau pada tabel riwayat di bawah.
                    </p>

                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="ajukan">
                        <span wire:loading.remove wire:target="ajukan">Kirim pengajuan</span>
                        <span wire:loading wire:target="ajukan">Mengirim...</span>
                    </flux:button>
                </div>
            </form>
        @endif
    </x-ui.panel>

    <x-ui.panel flush>
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <h2 class="text-sm font-semibold tracking-tight text-slate-950 dark:text-white">Riwayat pengajuan</h2>

            <p class="flex items-baseline gap-2 text-sm text-slate-600 dark:text-zinc-300">
                <span class="text-xl font-semibold tabular-nums text-navy-950 dark:text-white">{{ $riwayat->count() }}</span>
                <span>pengajuan</span>
            </p>
        </div>

        @if ($riwayat->isEmpty())
            <x-ui.empty
                icon="clipboard-document-list"
                title="Belum ada pengajuan"
                description="Pengajuan yang kamu kirim akan tercatat di sini beserta catatan dari pengurus UKM."
            />
        @else
            <div class="overflow-x-auto px-4">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                            <th scope="col" class="border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">UKM</th>
                            <th scope="col" class="border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Diajukan</th>
                            <th scope="col" class="border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Status</th>
                            <th scope="col" class="border-b border-slate-200 py-3 text-start font-semibold dark:border-white/10">Catatan pengurus</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($riwayat as $item)
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

                            <tr wire:key="pengajuan-{{ $item->id }}" class="border-b border-slate-100 last:border-0 dark:border-white/5">
                                <td class="py-3.5 pe-3 align-middle">
                                    <div class="font-medium text-slate-950 dark:text-white">{{ $item->ukm->nama }}</div>
                                </td>
                                <td class="py-3.5 pe-3 align-middle text-xs tabular-nums text-slate-500 dark:text-zinc-400">
                                    {{ $item->diajukan_pada?->format('d M Y, H:i') ?? '-' }}
                                </td>
                                <td class="py-3.5 pe-3 align-middle">
                                    <x-ui.badge :tone="$statusTone">{{ $statusLabel }}</x-ui.badge>
                                </td>
                                <td class="py-3.5 align-middle text-xs leading-5 text-slate-600 dark:text-zinc-300">
                                    {{ $item->catatan_pengurus ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.panel>
</x-ui.page>
