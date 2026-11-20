<div class="max-w-5xl mx-auto p-6 space-y-6">
    <div>
        <flux:heading size="xl">Tiket Acara Saya</flux:heading>
        <flux:text>Daftar e-tiket acara UKM yang Anda ikuti.</flux:text>
    </div>

    @if ($daftarTiket->isEmpty())
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center">
            <flux:text>Belum ada tiket acara yang diterbitkan untuk akun Anda.</flux:text>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($daftarTiket as $tiket)
                @php
                    $registrasi = $tiket->registrasiKegiatan;
                    $kegiatan = $registrasi?->kegiatan;
                    $sudahHadir = $registrasi?->presensi !== null;
                @endphp

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                {{ $kegiatan?->ukm?->nama ?? 'UKM' }}
                            </span>

                            @if ($sudahHadir)
                                <flux:badge color="green">Sudah Hadir</flux:badge>
                            @elseif ($tiket->status === 'aktif')
                                <flux:badge color="blue">Aktif</flux:badge>
                            @else
                                <flux:badge color="red">Dibatalkan</flux:badge>
                            @endif
                        </div>

                        <flux:heading size="lg">{{ $kegiatan?->nama ?? 'Acara UKM' }}</flux:heading>
                        <flux:text class="font-mono text-xs">{{ $tiket->kode_tiket }}</flux:text>
                    </div>

                    <flux:button href="{{ route('tiket.show', $tiket->kode_tiket) }}" variant="primary" class="w-full" wire:navigate>
                        Lihat Detail E-Tiket
                    </flux:button>
                </div>
            @endforeach
        </div>
    @endif
</div>
