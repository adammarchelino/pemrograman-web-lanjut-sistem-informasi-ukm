<x-ui.page>
    <x-ui.page-header
        eyebrow="Ruang pengurus"
        title="Kegiatan UKM"
        :description="$bolehKelola
            ? 'Kelola jadwal, kapasitas peserta, dan status publikasi kegiatan UKM.'
            : 'Daftar kegiatan UKM yang sudah dipublikasikan.'"
    >
        @if ($bolehKelola)
            <x-slot:actions>
                <flux:button variant="primary" icon="plus" wire:click="bukaForm">
                    Tambah Kegiatan
                </flux:button>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    @if (session()->has('success'))
        <div role="status" class="flex items-start gap-3 rounded-[3px] border border-moss-700/30 bg-moss-50 px-4 py-3 text-sm text-moss-700 dark:border-moss-700/50 dark:bg-moss-700/15 dark:text-moss-50">
            <flux:icon name="check-circle" class="mt-0.5 size-4 shrink-0" />
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <x-ui.panel flush>
        <div class="overflow-x-auto px-4">
            <table class="w-full min-w-[960px] table-fixed text-left text-sm">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        <th scope="col" class="w-[4%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">No</th>
                        <th scope="col" class="w-[18%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Kegiatan</th>
                        <th scope="col" class="w-[12%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">UKM</th>
                        <th scope="col" class="w-[13%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Jadwal</th>
                        <th scope="col" class="w-[11%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Lokasi</th>
                        <th scope="col" class="w-[6%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Peserta</th>
                        <th scope="col" class="w-[11%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Status</th>
                        <th scope="col" class="w-[25%] border-b border-slate-200 py-3 text-end font-semibold dark:border-white/10">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($kegiatan as $item)
                        <tr wire:key="kegiatan-{{ $item->id }}" class="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50/80 dark:border-white/5 dark:hover:bg-white/5">
                            <td class="py-3.5 pe-3 align-middle text-xs tabular-nums text-slate-500 dark:text-zinc-400">
                                {{ $loop->iteration }}
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                <div class="truncate text-sm font-medium text-slate-950 dark:text-white">{{ $item->nama }}</div>
                                @if ($item->deskripsi)
                                    <div class="truncate text-xs text-slate-500 dark:text-zinc-400">{{ Str::limit($item->deskripsi, 70) }}</div>
                                @endif
                            </td>

                            <td class="py-3.5 pe-3 align-middle text-xs text-slate-700 dark:text-zinc-300">
                                {{ $item->ukm?->nama ?? '-' }}
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                <div class="text-xs text-slate-700 dark:text-zinc-300">
                                    {{ $item->mulai_pada?->format('d M Y, H:i') ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-zinc-400">
                                    s.d. {{ $item->selesai_pada?->format('d M Y, H:i') ?? '-' }}
                                </div>
                            </td>

                            <td class="py-3.5 pe-3 align-middle text-xs text-slate-700 dark:text-zinc-300">
                                {{ $item->lokasi ?: '-' }}
                            </td>

                            <td class="py-3.5 pe-3 align-middle text-xs tabular-nums text-slate-700 dark:text-zinc-300">
                                {{ $item->jumlah_peserta }} / {{ $item->kapasitas }}
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                <x-ui.badge :tone="$item->status === 'dipublikasikan' ? 'positive' : 'neutral'">
                                    {{ $item->status === 'dipublikasikan' ? 'Dipublikasikan' : 'Draf' }}
                                </x-ui.badge>
                            </td>

                            <td class="py-3.5 align-middle text-end">
                                <div class="flex flex-wrap items-center justify-end gap-1">
                                    @can('kelola-presensi', $item)
                                        <flux:button
                                            size="xs"
                                            variant="subtle"
                                            :href="route('pengurus.kegiatan.presensi', $item)"
                                            icon="qr-code"
                                            title="Buka presensi kegiatan"
                                            wire:navigate
                                        >
                                            Presensi
                                        </flux:button>
                                    @endcan

                                    <flux:button size="xs" variant="subtle" wire:click="lihatDetail({{ $item->id }})" icon="eye" title="Lihat detail kegiatan">
                                        Detail
                                    </flux:button>

                                    @if ($bolehKelola)
                                        <flux:button size="xs" variant="subtle" wire:click="editKegiatan({{ $item->id }})" icon="pencil-square" title="Ubah kegiatan">
                                            Ubah
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-0">
                                <x-ui.empty
                                    icon="calendar-days"
                                    title="Belum ada kegiatan"
                                    description="Kegiatan yang dibuat pengurus UKM akan tampil di sini."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    <flux:modal wire:model="tampilkanForm" class="max-w-2xl">
        <form wire:submit="simpan" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editKegiatanId ? 'Ubah Kegiatan' : 'Tambah Kegiatan' }}</flux:heading>
                <flux:subheading>
                    {{ $editKegiatanId ? 'Perbarui jadwal dan status publikasi kegiatan.' : 'Lengkapi jadwal, lokasi, dan kapasitas kegiatan UKM.' }}
                </flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="nama" label="Nama Kegiatan" placeholder="Contoh: Workshop Robotika" required />
                <flux:textarea wire:model="deskripsi" label="Deskripsi" rows="3" placeholder="Gambaran singkat kegiatan" required />

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="mulai_pada" type="datetime-local" label="Mulai" required />
                    <flux:input wire:model="selesai_pada" type="datetime-local" label="Selesai" required />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="lokasi" label="Lokasi" placeholder="Contoh: Aula Poliwangi" required />
                    <flux:input wire:model="kapasitas" type="number" min="1" label="Kapasitas Peserta" required />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="pendaftaran_dibuka_pada" type="datetime-local" label="Pendaftaran Dibuka (opsional)" />
                    <flux:input wire:model="pendaftaran_ditutup_pada" type="datetime-local" label="Pendaftaran Ditutup (opsional)" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:select wire:model="audiens" label="Audiens">
                        <option value="mahasiswa_poliwangi">Mahasiswa Poliwangi</option>
                        <option value="umum">Umum</option>
                    </flux:select>

                    <flux:select wire:model="status" label="Status Publikasi">
                        <option value="draf">Draf</option>
                        <option value="dipublikasikan">Dipublikasikan</option>
                    </flux:select>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-white/10">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="simpan">{{ $editKegiatanId ? 'Simpan Perubahan' : 'Simpan Kegiatan' }}</span>
                    <span wire:loading wire:target="simpan">Menyimpan...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="tampilkanDetail" class="max-w-xl">
        @if ($detail)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Detail Kegiatan</flux:heading>
                    <flux:subheading>{{ $detail->nama }}</flux:subheading>
                </div>

                <dl class="grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2 dark:border-white/10 dark:bg-white/5">
                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">UKM</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $detail->ukm?->nama ?? '-' }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Lokasi</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $detail->lokasi ?: '-' }}</dd>
                    </div>

                    <div class="space-y-1 sm:col-span-2">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Deskripsi</dt>
                        <dd class="whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-zinc-300">{{ $detail->deskripsi ?: 'Belum ada deskripsi.' }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Mulai</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $detail->mulai_pada?->format('d M Y, H:i') ?? '-' }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Selesai</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $detail->selesai_pada?->format('d M Y, H:i') ?? '-' }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Kapasitas</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $detail->kapasitas }} orang</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Audiens</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $detail->audiens === 'umum' ? 'Umum' : 'Mahasiswa Poliwangi' }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Pendaftaran Dibuka</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $detail->pendaftaran_dibuka_pada?->format('d M Y, H:i') ?? '-' }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Pendaftaran Ditutup</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $detail->pendaftaran_ditutup_pada?->format('d M Y, H:i') ?? '-' }}</dd>
                    </div>

                    <div class="space-y-1 sm:col-span-2">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Status Publikasi</dt>
                        <dd>
                            <x-ui.badge :tone="$detail->status === 'dipublikasikan' ? 'positive' : 'neutral'">
                                {{ $detail->status === 'dipublikasikan' ? 'Dipublikasikan' : 'Draf' }}
                            </x-ui.badge>
                        </dd>
                    </div>
                </dl>

                <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-white/10">
                    <flux:modal.close>
                        <flux:button variant="filled">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>
</x-ui.page>
