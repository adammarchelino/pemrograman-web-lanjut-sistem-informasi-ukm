@php
    $registrasi = $tiket->registrasiKegiatan;
    $kegiatan = $registrasi->kegiatan;
    $presensi = $registrasi->presensi;
    $namaPeserta = $registrasi->user?->name ?? $registrasi->nama_peserta;
    $nim = $registrasi->user?->nim ?? $registrasi->nim;
@endphp

<div class="max-w-lg mx-auto p-6 space-y-6">
    <div class="flex items-center justify-between">
        <flux:button href="{{ route('tiket.index') }}" variant="ghost" wire:navigate>
            &larr; Kembali
        </flux:button>

        @if ($presensi)
            <flux:badge color="green">Hadir ({{ $presensi->hadir_pada->format('d M Y H:i') }})</flux:badge>
        @elseif ($tiket->status === 'aktif')
            <flux:badge color="blue">Tiket Aktif</flux:badge>
        @else
            <flux:badge color="red">Dibatalkan</flux:badge>
        @endif
    </div>

    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6 text-center shadow-sm">
        <div class="space-y-1">
            <span class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ $kegiatan->ukm->nama ?? 'UKM' }}</span>
            <flux:heading size="xl">{{ $kegiatan->nama }}</flux:heading>
            <flux:text>{{ $kegiatan->lokasi ?? '-' }}</flux:text>
        </div>

        {{-- Wadah QR Code yang dirender oleh JavaScript di app.js --}}
        <div class="flex justify-center py-4 bg-white rounded-xl border border-zinc-100 dark:border-zinc-800" wire:ignore>
            <canvas id="qr-tiket-canvas"></canvas>
            <p id="qr-tiket-canvas-gagal" role="alert" hidden
                class="mx-4 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-800">
                QR Code gagal ditampilkan. Tunjukkan atau salin kode tiket di bawah ini kepada pengurus.
            </p>
        </div>

        <div class="space-y-1">
            <flux:text class="text-xs">Kode Tiket</flux:text>
            <p id="kode-tiket" class="font-mono text-sm font-semibold break-all select-all">{{ $tiket->kode_tiket }}</p>
            <flux:button type="button" size="sm" variant="outline" data-salin="kode-tiket" wire:ignore>Salin Kode</flux:button>
        </div>

        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 text-left text-sm space-y-2">
            <div class="flex justify-between">
                <span class="text-zinc-500 dark:text-zinc-400">Nama Peserta</span>
                <span class="font-medium">{{ $namaPeserta }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-zinc-500 dark:text-zinc-400">NIM</span>
                <span class="font-medium">{{ $nim ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-zinc-500 dark:text-zinc-400">Diterbitkan Pada</span>
                <span class="font-medium">{{ $tiket->diterbitkan_pada }}</span>
            </div>
        </div>
    </div>
</div>

@script
<script>
    window.renderTiketQr('qr-tiket-canvas', @js($tiket->kode_tiket));
</script>
@endscript
