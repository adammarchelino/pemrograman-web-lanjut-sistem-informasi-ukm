<div class="mx-auto max-w-7xl space-y-6 p-6 lg:p-8">
    <header>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pengajuan Anggota UKM</h1>
        <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">Periksa pengajuan mahasiswa dan proses sesuai kewenangan UKM Anda.</p>
    </header>

    @if (session('status'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach (['pending' => 'Menunggu diproses', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'semua' => 'Total pengajuan'] as $key => $label)
            <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-slate-500 dark:text-zinc-400">{{ $label }}</p>
                <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">{{ $statistik[$key] }}</p>
            </div>
        @endforeach
    </section>

    <div class="flex flex-wrap gap-2">
        @foreach (['pending' => 'Menunggu', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'semua' => 'Semua'] as $value => $label)
            <button type="button" wire:click="$set('filter', '{{ $value }}')"
                class="rounded-lg border px-3 py-2 text-sm {{ $filter === $value ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white text-slate-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600 dark:bg-zinc-800 dark:text-zinc-300">
                <tr><th class="p-3">Mahasiswa</th><th class="p-3">Alasan</th><th class="p-3">Diajukan</th><th class="p-3">Status</th><th class="p-3">Tindakan / Catatan</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                @forelse ($pengajuan as $item)
                    <tr wire:key="application-{{ $item->id }}" class="align-top">
                        <td class="p-3">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $item->user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $item->user->nim ?: $item->user->email }}</div>
                        </td>
                        <td class="max-w-xs whitespace-pre-line p-3 text-slate-600 dark:text-zinc-300">{{ $item->jawaban_formulir['alasan'] ?? '—' }}</td>
                        <td class="whitespace-nowrap p-3">{{ $item->diajukan_pada?->format('d-m-Y H:i') }}</td>
                        <td class="p-3"><flux:badge :color="match($item->status) {'pending' => 'amber', 'diterima' => 'green', default => 'red'}">{{ ucfirst($item->status) }}</flux:badge></td>
                        <td class="min-w-56 p-3">
                            @if ($item->status === 'pending')
                                <div class="space-y-2">
                                    <flux:textarea wire:model="catatan.{{ $item->id }}" placeholder="Catatan penolakan (opsional)" rows="2" />
                                    <div class="flex flex-wrap gap-2">
                                        <flux:button size="sm" variant="primary" wire:click="terima({{ $item->id }})" wire:confirm="Terima pengajuan mahasiswa ini?">Terima</flux:button>
                                        <flux:button size="sm" variant="danger" wire:click="tolak({{ $item->id }})" wire:confirm="Tolak pengajuan mahasiswa ini?">Tolak</flux:button>
                                    </div>
                                    @error("catatan.{$item->id}") <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @else
                                <p class="text-slate-600 dark:text-zinc-300">{{ $item->catatan_pengurus ?: '—' }}</p>
                                <p class="mt-1 text-xs text-slate-500">Diproses {{ $item->diputuskan_pada?->format('d-m-Y H:i') }}</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Tidak ada pengajuan pada kategori ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $pengajuan->links() }}
</div>
