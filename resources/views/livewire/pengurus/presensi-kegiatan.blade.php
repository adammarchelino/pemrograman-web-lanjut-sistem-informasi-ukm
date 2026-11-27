<div class="max-w-6xl mx-auto p-6 space-y-6">
    <div>
        <flux:heading size="xl">Presensi Acara: {{ $kegiatan->nama }}</flux:heading>
        <flux:text>Validasi tiket peserta acara UKM dan pantau rekapitulasi kehadiran.</flux:text>
    </div>

    {{-- Form Validasi / Input Scan Tiket --}}
    <div x-data="pemindaiQr" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <flux:heading size="lg">Validasi Tiket Acara</flux:heading>

            <flux:button type="button" variant="outline" icon="qr-code" x-on:click="toggle()">
                <span x-text="aktif ? 'Tutup Kamera' : 'Scan QR Kamera'">Scan QR Kamera</span>
            </flux:button>
        </div>

        {{-- Area kamera: wire:ignore agar video tidak diganggu saat Livewire render ulang --}}
        <div wire:ignore>
            <div x-show="aktif" x-cloak class="space-y-2">
                <div class="relative mx-auto max-w-sm overflow-hidden rounded-lg bg-black">
                    <video x-ref="video" class="block w-full" muted playsinline></video>
                </div>
                <p class="text-center text-sm text-zinc-500 dark:text-zinc-400" role="status"
                    x-text="memproses ? 'Memproses tiket...' : 'Arahkan kamera ke QR tiket peserta.'"></p>
            </div>

            <p x-show="pesanError" x-cloak x-text="pesanError" role="alert"
                class="rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-200"></p>
        </div>

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
            <p class="text-2xl font-bold mt-1 text-green-600 dark:text-green-400">{{ $totalHadir }}</p>
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
                            {{ $item->presensi ? $item->presensi->hadir_pada->format('H:i') : '-' }}
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
                        <flux:table.cell colspan="6" class="text-center text-zinc-500 dark:text-zinc-400 py-6">
                            Tidak ada data peserta untuk filter ini.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
