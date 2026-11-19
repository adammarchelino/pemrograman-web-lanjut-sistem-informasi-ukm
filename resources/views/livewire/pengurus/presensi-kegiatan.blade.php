<div class="max-w-6xl mx-auto p-6 space-y-6">
    <div>
        <flux:heading size="xl">Presensi Kegiatan: {{ $kegiatan->nama }}</flux:heading>
        <flux:text>Validasi kode tiket peserta dan pantau rekapitulasi kehadiran.</flux:text>
    </div>

    {{-- Form Validasi / Input Scan Tiket --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-4">
        <flux:heading size="lg">Validasi Tiket Peserta</flux:heading>

        <form wire:submit="prosesCheckIn" class="flex flex-col sm:flex-row gap-3 items-start">
            <div class="w-full sm:flex-1">
                <flux:input
                    wire:model="kodeTiket"
                    placeholder="Masukkan atau pindai kode tiket (contoh: TKT-...)"
                    autofocus
                />
                <flux:error name="kodeTiket" />
            </div>

            <flux:button type="submit" variant="primary">
                Validasi & Catat Hadir
            </flux:button>
        </form>
    </div>

    {{-- Ringkasan Kehadiran --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
            <flux:text class="text-xs">Total Peserta Terdaftar</flux:text>
            <p class="text-2xl font-bold mt-1">{{ $totalTerdaftar }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
            <flux:text class="text-xs">Hadir</flux:text>
            <p class="text-2xl font-bold mt-1 text-green-600">{{ $totalHadir }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
            <flux:text class="text-xs">Persentase Kehadiran</flux:text>
            <p class="text-2xl font-bold mt-1">{{ $persentaseKehadiran }}%</p>
        </div>
    </div>

    {{-- Filter Status --}}
    <div class="flex gap-2">
        <flux:button
            size="sm"
            :variant="$filterStatus === 'semua' ? 'primary' : 'outline'"
            wire:click="$set('filterStatus', 'semua')"
        >
            Semua
        </flux:button>
        <flux:button
            size="sm"
            :variant="$filterStatus === 'hadir' ? 'primary' : 'outline'"
            wire:click="$set('filterStatus', 'hadir')"
        >
            Hadir
        </flux:button>
        <flux:button
            size="sm"
            :variant="$filterStatus === 'tidak_hadir' ? 'primary' : 'outline'"
            wire:click="$set('filterStatus', 'tidak_hadir')"
        >
            Tidak Hadir
        </flux:button>
    </div>

    {{-- Tabel Rekap Presensi --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>Nama</flux:table.column>
                <flux:table.column>NIM</flux:table.column>
                <flux:table.column>Waktu Presensi</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Tiket</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($pesertaTampil as $item)
                    <flux:table.row :key="$item->id">
                        <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                        <flux:table.cell class="font-medium">
                            {{ $item->user?->name ?? $item->nama_peserta ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $item->user?->nim ?? $item->nim ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $item->presensi ? $item->presensi->hadir_pada->format('H:i') : '—' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($item->presensi)
                                <flux:badge color="green">Hadir</flux:badge>
                            @else
                                <flux:badge color="red">Tidak Hadir</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($item->tiketKegiatan)
                                <flux:badge color="blue">Terbit</flux:badge>
                            @else
                                <flux:button size="sm" variant="outline" wire:click="terbitkanTiket({{ $item->id }})">
                                    Terbitkan
                                </flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-zinc-500 py-6">
                            Tidak ada data peserta untuk filter ini.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
