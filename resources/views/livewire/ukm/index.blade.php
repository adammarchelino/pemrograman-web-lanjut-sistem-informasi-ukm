<x-ui.page>
    <x-ui.page-header
        eyebrow="Administrasi akademik"
        title="Kelola Data UKM"
        description="Pusat pengelolaan unit kegiatan mahasiswa, status organisasi, dan pendaftaran anggota di Politeknik Negeri Banyuwangi."
    >
        @if ($canCreate)
            <x-slot:actions>
                <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
                    Tambah UKM Baru
                </flux:button>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan data UKM">
        <x-ui.stat label="Total UKM" :value="$ukmStats['total']" note="Seluruh organisasi terdaftar" icon="building-office-2" tone="navy" />
        <x-ui.stat label="UKM Aktif" :value="$ukmStats['aktif']" note="Organisasi berstatus aktif" icon="check-circle" tone="emerald" />
        <x-ui.stat label="UKM Nonaktif" :value="$ukmStats['nonaktif']" note="Organisasi berstatus nonaktif" icon="pause-circle" tone="slate" />
        <x-ui.stat label="Pendaftaran Dibuka" :value="$ukmStats['pendaftaran_dibuka']" note="UKM menerima calon anggota" icon="user-plus" tone="amber" />
    </section>

    <x-ui.panel flush>
        <div class="border-b border-slate-200 p-4 dark:border-white/10">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">
                <div class="md:col-span-2">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama UKM atau kontak..."
                        icon="magnifying-glass"
                        clearable
                    />
                </div>

                <flux:select wire:model.live="filterStatus" placeholder="Status Organisasi">
                    <option value="">Semua Status Organisasi</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </flux:select>

                <flux:select wire:model.live="filterPendaftaran" placeholder="Status Pendaftaran">
                    <option value="">Semua Status Pendaftaran</option>
                    <option value="dibuka">Pendaftaran Dibuka</option>
                    <option value="ditutup">Pendaftaran Ditutup</option>
                </flux:select>
            </div>

            <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3 dark:border-white/10">
                <p class="flex items-baseline gap-2 text-sm text-slate-600 dark:text-zinc-300">
                    <span class="text-xl font-semibold tabular-nums text-navy-950 dark:text-white">{{ $ukms->total() }}</span>
                    <span>UKM ditemukan</span>
                </p>

                @if ($search !== '' || $filterStatus !== '' || $filterPendaftaran !== '')
                    <flux:button variant="subtle" size="sm" wire:click="resetFilters" icon="arrow-path">
                        Reset Filter
                    </flux:button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto px-4">
            <table class="w-full min-w-[900px] table-fixed text-left text-sm">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        <th scope="col" class="w-[30%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Identitas UKM</th>
                        <th scope="col" class="w-[15%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Kontak Resmi</th>
                        <th scope="col" class="w-[13%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Status Organisasi</th>
                        <th scope="col" class="w-[17%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Pendaftaran Anggota</th>
                        <th scope="col" class="w-[25%] border-b border-slate-200 py-3 text-end font-semibold dark:border-white/10">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($ukms as $ukm)
                        <tr wire:key="ukm-{{ $ukm->id }}" class="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50/80 dark:border-white/5 dark:hover:bg-white/5">
                            <td class="py-3.5 pe-3 align-middle">
                                <div class="flex items-center gap-3">
                                    @if ($ukm->logoUrl())
                                        <img src="{{ $ukm->logoUrl() }}" alt="Logo {{ $ukm->nama }}" class="size-10 shrink-0 rounded-lg border border-slate-200 object-cover dark:border-white/10" loading="lazy">
                                    @else
                                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-navy-950 text-xs font-semibold text-white">
                                            {{ $ukm->inisial() }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="break-words text-sm font-semibold text-slate-950 dark:text-white">{{ $ukm->nama }}</div>
                                        @if ($ukm->deskripsi)
                                            <div class="line-clamp-2 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ Str::limit($ukm->deskripsi, 90) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                <div class="space-y-1 text-xs">
                                    @if ($ukm->email_kontak)
                                        <div class="font-mono text-slate-700 dark:text-zinc-300">{{ $ukm->email_kontak }}</div>
                                    @endif
                                    @if ($ukm->nomor_kontak)
                                        <div class="text-slate-500 dark:text-zinc-400">{{ $ukm->nomor_kontak }}</div>
                                    @endif
                                    @if (! $ukm->email_kontak && ! $ukm->nomor_kontak)
                                        <span class="italic text-slate-400">-</span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                <x-ui.badge :tone="$ukm->status === 'aktif' ? 'positive' : 'neutral'">
                                    {{ $ukm->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </x-ui.badge>
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                <div class="space-y-1">
                                    <x-ui.badge :tone="$ukm->status_pendaftaran_anggota === 'dibuka' ? 'positive' : 'negative'">
                                        Pendaftaran {{ $ukm->status_pendaftaran_anggota === 'dibuka' ? 'Dibuka' : 'Ditutup' }}
                                    </x-ui.badge>
                                    @if ($ukm->status_pendaftaran_anggota === 'dibuka' && $ukm->pendaftaran_anggota_ditutup_pada)
                                        <div class="text-[11px] text-slate-500 dark:text-zinc-400">s.d. {{ $ukm->pendaftaran_anggota_ditutup_pada->format('d M Y') }}</div>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3.5 align-middle text-end">
                                <div class="flex flex-wrap items-center justify-end gap-1">
                                    <flux:button size="xs" variant="subtle" wire:click="openDetailModal({{ $ukm->id }})" icon="eye" title="Lihat Detail UKM">Detail</flux:button>

                                    @can('update', $ukm)
                                        <flux:button size="xs" variant="subtle" wire:click="openEditModal({{ $ukm->id }})" icon="pencil-square" title="Ubah Data UKM">Ubah</flux:button>
                                    @endcan

                                    @can('toggleStatus', $ukm)
                                        @if ($ukm->status === 'aktif')
                                            <flux:button size="xs" variant="danger" wire:click="confirmDeactivate({{ $ukm->id }})" icon="no-symbol" title="Nonaktifkan UKM">Nonaktifkan</flux:button>
                                        @else
                                            <flux:button size="xs" variant="filled" wire:click="confirmActivate({{ $ukm->id }})" icon="check-circle" title="Aktifkan Kembali UKM">Aktifkan</flux:button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-0">
                                <x-ui.empty icon="building-library" title="Tidak ada data UKM ditemukan" description="Coba sesuaikan kata kunci pencarian atau bersihkan filter." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($ukms->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-white/10">
                {{ $ukms->links() }}
            </div>
        @endif
    </x-ui.panel>

    <flux:modal wire:model="showFormModal" class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Ubah Data UKM' : 'Tambah UKM Baru' }}</flux:heading>
                <flux:subheading>
                    {{ $editingId ? 'Perbarui informasi profil dan periode pendaftaran UKM.' : 'Lengkapi formulir untuk mendaftarkan organisasi UKM baru.' }}
                </flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="form.nama" label="Nama UKM" placeholder="Contoh: UKM Robotika Poliwangi" required />
                <flux:textarea wire:model="form.deskripsi" label="Deskripsi Organisasi" placeholder="Tuliskan gambaran umum aktivitas UKM..." rows="3" />

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:textarea wire:model="form.visi" label="Visi" placeholder="Visi organisasi..." rows="3" />
                    <flux:textarea wire:model="form.misi" label="Misi" placeholder="Misi organisasi..." rows="3" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="form.email_kontak" type="email" label="Email Resmi Kontak" placeholder="ukm@poliwangi.ac.id" />
                    <flux:input wire:model="form.nomor_kontak" label="Nomor Telepon / WhatsApp" placeholder="08123456789" />
                </div>

                <div class="space-y-2">
                    <label for="logoUpload" class="text-sm font-medium text-slate-800 dark:text-zinc-200">Logo UKM (Opsional)</label>
                    <div class="flex flex-col gap-4 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 sm:flex-row sm:items-center dark:border-white/15 dark:bg-white/5">
                        <div class="flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-white/10 dark:bg-zinc-900">
                            @if ($logoUpload && $logoUpload->isPreviewable())
                                <img src="{{ $logoUpload->temporaryUrl() }}" alt="Pratinjau logo UKM" class="size-full object-cover">
                            @elseif ($storedLogoPreviewUrl)
                                <img src="{{ $storedLogoPreviewUrl }}" alt="Logo UKM saat ini" class="size-full object-cover">
                            @else
                                <flux:icon name="photo" class="size-7 text-slate-300 dark:text-zinc-600" />
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <input
                                id="logoUpload"
                                type="file"
                                wire:model="logoUpload"
                                accept="image/png,image/jpeg,image/webp"
                                class="block w-full cursor-pointer text-sm text-slate-600 file:me-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:text-zinc-300 dark:file:bg-blue-950 dark:file:text-blue-300 dark:hover:file:bg-blue-900"
                            >
                            <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-zinc-400">Format PNG, JPG, atau WebP. Ukuran maksimal 2 MB.</p>
                            <div wire:loading wire:target="logoUpload" class="mt-2 text-xs font-medium text-blue-700 dark:text-blue-300">Memuat pratinjau...</div>
                            @error('logoUpload')
                                <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 border-t border-slate-100 pt-4 sm:grid-cols-2 dark:border-white/10">
                    @if (auth()->user()->isKemahasiswaan())
                        <flux:select wire:model="form.status" label="Status Organisasi">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </flux:select>
                    @endif

                    <flux:select wire:model="form.status_pendaftaran_anggota" label="Pendaftaran Anggota">
                        <option value="ditutup">Pendaftaran Ditutup</option>
                        <option value="dibuka">Pendaftaran Dibuka</option>
                    </flux:select>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="form.pendaftaran_anggota_dibuka_pada" type="datetime-local" label="Waktu Dibuka (Opsional)" />
                    <flux:input wire:model="form.pendaftaran_anggota_ditutup_pada" type="datetime-local" label="Waktu Ditutup (Opsional)" />
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-white/10">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan Perubahan' : 'Tambahkan UKM' }}</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="showDetailModal" class="max-w-2xl">
        @if ($selectedUkm)
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    @if ($selectedUkm->logoUrl())
                        <img src="{{ $selectedUkm->logoUrl() }}" alt="Logo {{ $selectedUkm->nama }}" class="size-14 shrink-0 rounded-xl border border-slate-200 object-cover dark:border-white/10">
                    @else
                        <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-navy-950 text-lg font-semibold text-white">{{ $selectedUkm->inisial() }}</div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ $selectedUkm->nama }}</h2>
                            <x-ui.badge :tone="$selectedUkm->status === 'aktif' ? 'positive' : 'neutral'">
                                {{ $selectedUkm->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                            </x-ui.badge>
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-zinc-400">
                            @if ($selectedUkm->email_kontak)<span class="font-mono">{{ $selectedUkm->email_kontak }}</span>@endif
                            @if ($selectedUkm->nomor_kontak)<span>{{ $selectedUkm->nomor_kontak }}</span>@endif
                        </div>
                    </div>
                </div>

                <div class="space-y-4 rounded-lg bg-slate-50 p-4 text-sm dark:bg-white/5">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Deskripsi</p>
                        <p class="mt-1 leading-relaxed text-slate-700 dark:text-zinc-300">{{ $selectedUkm->deskripsi ?: 'Belum ada deskripsi yang ditambahkan.' }}</p>
                    </div>

                    @if ($selectedUkm->visi)
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Visi</p>
                            <p class="mt-1 leading-relaxed text-slate-700 dark:text-zinc-300">{{ $selectedUkm->visi }}</p>
                        </div>
                    @endif

                    @if ($selectedUkm->misi)
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Misi</p>
                            <p class="mt-1 whitespace-pre-line leading-relaxed text-slate-700 dark:text-zinc-300">{{ $selectedUkm->misi }}</p>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 p-3 text-center dark:border-white/10">
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Pendaftaran Anggota</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $selectedUkm->status_pendaftaran_anggota === 'dibuka' ? 'Dibuka' : 'Ditutup' }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 p-3 text-center dark:border-white/10">
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Pengurus Terdaftar</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $selectedUkm->pengurus->count() }} orang</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 p-3 text-center dark:border-white/10">
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Anggota Aktif</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $selectedUkm->anggota_aktif_count ?? 0 }} orang</p>
                    </div>
                </div>

                @if ($selectedUkm->pengurus->isNotEmpty())
                    <div>
                        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Daftar Pengurus UKM</p>
                        <div class="divide-y divide-slate-100 rounded-lg border border-slate-200 dark:divide-white/10 dark:border-white/10">
                            @foreach ($selectedUkm->pengurus as $pengurus)
                                <div class="flex items-center justify-between gap-3 p-3 text-xs">
                                    <div class="min-w-0">
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $pengurus->name }} <span class="font-normal text-slate-500 dark:text-zinc-400">· {{ $pengurus->roleLabel() }}</span></p>
                                        <p class="text-slate-500 dark:text-zinc-400">{{ $pengurus->email }}</p>
                                        @if ($pengurus->divisiUkm)<p class="text-slate-500 dark:text-zinc-400">{{ $pengurus->divisiUkm->nama }}</p>@endif
                                    </div>
                                    @if ($pengurus->nim)
                                        <span class="shrink-0 font-mono text-slate-500 dark:text-zinc-400">NIM {{ $pengurus->nim }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-white/10">
                    <flux:modal.close>
                        <flux:button variant="filled">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>

    <flux:modal wire:model="showDeactivateModal" class="max-w-lg">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400">
                    <flux:icon name="exclamation-triangle" class="size-5" />
                </span>
                <flux:heading size="lg">Nonaktifkan Unit Kegiatan Mahasiswa?</flux:heading>
            </div>

            <div class="rounded-lg bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                Organisasi ini akan disembunyikan dari daftar publik dan akses pengurus akan dibatasi. Data histori, riwayat anggota, dan dokumen masa lalu tetap aman tersimpan.
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close><flux:button variant="filled">Batal</flux:button></flux:modal.close>
                <flux:button variant="danger" wire:click="deactivate" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="deactivate">Ya, Nonaktifkan</span>
                    <span wire:loading wire:target="deactivate">Memproses...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="showActivateModal" class="max-w-lg">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                    <flux:icon name="check-circle" class="size-5" />
                </span>
                <flux:heading size="lg">Aktifkan Kembali Unit Kegiatan Mahasiswa?</flux:heading>
            </div>

            <div class="rounded-lg bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                Organisasi ini akan kembali ditampilkan dalam daftar publik dan statusnya menjadi aktif.
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close><flux:button variant="filled">Batal</flux:button></flux:modal.close>
                <flux:button variant="primary" wire:click="activate" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="activate">Ya, Aktifkan Kembali</span>
                    <span wire:loading wire:target="activate">Memproses...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-ui.page>
