<div class="mx-auto max-w-7xl space-y-6 p-6 lg:p-8">
    <header>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pendaftaran Anggota UKM</h1>
        <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">Pilih UKM yang membuka penerimaan anggota dan pantau status pengajuan Anda.</p>
    </header>

    @if (session('status'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif

    <section class="rounded-xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="font-semibold text-slate-900 dark:text-white">Formulir Pengajuan</h2>
        @if ($ukms->isEmpty())
            <p class="mt-3 text-sm text-slate-500">Belum ada UKM aktif yang membuka pendaftaran anggota.</p>
        @else
            <form wire:submit="ajukan" class="mt-4 space-y-4">
                <div>
                    <flux:select wire:model="ukmId" label="Pilih UKM" placeholder="Pilih UKM">
                        @foreach ($ukms as $ukm)
                            <flux:select.option :value="$ukm->id">{{ $ukm->nama }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('ukmId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <flux:textarea wire:model="alasan" label="Alasan bergabung" placeholder="Ceritakan alasan dan minat Anda bergabung dengan UKM ini..." rows="4" />
                    @error('alasan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="ajukan">Kirim Pengajuan</flux:button>
            </form>
        @endif
    </section>

    <section class="space-y-3">
        <h2 class="font-semibold text-slate-900 dark:text-white">Riwayat Pengajuan</h2>
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600 dark:bg-zinc-800 dark:text-zinc-300">
                    <tr><th class="p-3">UKM</th><th class="p-3">Tanggal</th><th class="p-3">Status</th><th class="p-3">Catatan</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @forelse ($riwayat as $item)
                        <tr wire:key="application-{{ $item->id }}">
                            <td class="p-3 font-medium text-slate-900 dark:text-white">{{ $item->ukm->nama }}</td>
                            <td class="p-3">{{ $item->diajukan_pada?->format('d-m-Y H:i') }}</td>
                            <td class="p-3">
                                <flux:badge :color="match($item->status) {'pending' => 'amber', 'diterima' => 'green', default => 'red'}">
                                    {{ ucfirst($item->status) }}
                                </flux:badge>
                            </td>
                            <td class="p-3 text-slate-600 dark:text-zinc-300">{{ $item->catatan_pengurus ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-5 text-center text-slate-500">Anda belum memiliki pengajuan keanggotaan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
