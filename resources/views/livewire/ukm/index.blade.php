<div class="space-y-6">
    {{-- Header & Action Bar --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-zinc-400">
                <span>SIP-UKM</span>
                <span>/</span>
                <span class="text-blue-600 dark:text-blue-400">Kelola Data UKM</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Kelola Data UKM
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">
                Pusat pengelolaan unit kegiatan mahasiswa, status organisasi, dan pendaftaran anggota di Politeknik Negeri Banyuwangi.
            </p>
        </div>

        @if ($canCreate)
            <div>
                <flux:button variant="primary" icon="plus" wire:click="openCreateModal" class="w-full sm:w-auto">
                    Tambah UKM Baru
                </flux:button>
            </div>
        @endif
    </div>

    {{-- UKM Summary --}}
    @php
        $ukmStatCards = [
            [
                'label' => 'Total UKM',
                'value' => $ukmStats['total'],
                'description' => 'Seluruh organisasi terdaftar',
                'icon' => 'building-office-2',
                'icon_class' => 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
            ],
            [
                'label' => 'UKM Aktif',
                'value' => $ukmStats['aktif'],
                'description' => 'Organisasi berstatus aktif',
                'icon' => 'check-circle',
                'icon_class' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
            ],
            [
                'label' => 'UKM Nonaktif',
                'value' => $ukmStats['nonaktif'],
                'description' => 'Organisasi berstatus nonaktif',
                'icon' => 'pause-circle',
                'icon_class' => 'bg-slate-100 text-slate-700 dark:bg-zinc-700 dark:text-zinc-300',
            ],
            [
                'label' => 'Pendaftaran Dibuka',
                'value' => $ukmStats['pendaftaran_dibuka'],
                'description' => 'UKM menerima calon anggota',
                'icon' => 'user-plus',
                'icon_class' => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
            ],
        ];
    @endphp

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan data UKM">
        @foreach ($ukmStatCards as $card)
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-zinc-700 dark:bg-zinc-800">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-zinc-300">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ number_format($card['value']) }}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $card['description'] }}</p>
                    </div>
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg {{ $card['icon_class'] }}">
                        <flux:icon :name="$card['icon']" class="size-5" />
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    {{-- Filter & Search Toolbar --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-zinc-700 dark:bg-zinc-800">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">
            <div class="md:col-span-2">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama UKM, kontak..."
                    icon="magnifying-glass"
                    clearable
                />
            </div>

            <div>
                <flux:select wire:model.live="filterStatus" placeholder="Status Organisasi">
                    <option value="">Semua Status Organisasi</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </flux:select>
            </div>

            <div>
                <flux:select wire:model.live="filterPendaftaran" placeholder="Status Pendaftaran">
                    <option value="">Semua Status Pendaftaran</option>
                    <option value="dibuka">Pendaftaran Dibuka</option>
                    <option value="ditutup">Pendaftaran Ditutup</option>
                </flux:select>
            </div>
        </div>

        @if ($search !== '' || $filterStatus !== '' || $filterPendaftaran !== '')
            <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 text-xs text-slate-500 dark:border-zinc-700 dark:text-zinc-400">
                <span>Menampilkan hasil terfilter</span>
                <flux:button variant="subtle" size="sm" wire:click="resetFilters" icon="arrow-path">
                    Reset Filter
                </flux:button>
            </div>
        @endif
    </div>

    {{-- UKM Data Table --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="uppercase tracking-wider text-xs">Identitas UKM</flux:table.column>
                    <flux:table.column class="uppercase tracking-wider text-xs">Kontak Resmi</flux:table.column>
                    <flux:table.column class="uppercase tracking-wider text-xs">Status Organisasi</flux:table.column>
                    <flux:table.column class="uppercase tracking-wider text-xs">Pendaftaran Anggota</flux:table.column>
                    <flux:table.column align="end" class="uppercase tracking-wider text-xs">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($ukms as $ukm)
                        <flux:table.row :key="$ukm->id" class="hover:bg-slate-50/70 transition dark:hover:bg-zinc-700/50">
                            {{-- Identitas UKM --}}
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    @if ($ukm->logoUrl())
                                        <img src="{{ $ukm->logoUrl() }}" alt="Logo {{ $ukm->nama }}" class="size-10 shrink-0 rounded-lg border border-slate-200 object-cover dark:border-zinc-700" loading="lazy">
                                    @else
                                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                            {{ $ukm->inisial() }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 dark:text-white">
                                            {{ $ukm->nama }}
                                        </div>
                                        @if ($ukm->deskripsi)
                                            <div class="truncate text-xs text-slate-500 max-w-xs dark:text-zinc-400">
                                                {{ Str::limit($ukm->deskripsi, 60) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>

                            {{-- Kontak Resmi --}}
                            <flux:table.cell>
                                <div class="space-y-1">
                                    @if ($ukm->email_kontak)
                                        <div class="font-mono text-xs text-slate-700 dark:text-zinc-300">
                                            {{ $ukm->email_kontak }}
                                        </div>
                                    @endif
                                    @if ($ukm->nomor_kontak)
                                        <div class="text-xs text-slate-500 dark:text-zinc-400">
                                            {{ $ukm->nomor_kontak }}
                                        </div>
                                    @endif
                                    @if (! $ukm->email_kontak && ! $ukm->nomor_kontak)
                                        <span class="text-xs italic text-slate-400">-</span>
                                    @endif
                                </div>
                            </flux:table.cell>

                            {{-- Status Organisasi --}}
                            <flux:table.cell>
                                @if ($ukm->status === 'aktif')
                                    <flux:badge color="emerald" size="sm" variant="pill">
                                        Aktif
                                    </flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" variant="pill">
                                        Nonaktif
                                    </flux:badge>
                                @endif
                            </flux:table.cell>

                            {{-- Status Pendaftaran --}}
                            <flux:table.cell>
                                <div class="space-y-1">
                                    @if ($ukm->status_pendaftaran_anggota === 'dibuka')
                                        <flux:badge color="emerald" size="sm" variant="pill">
                                            Pendaftaran Dibuka
                                        </flux:badge>
                                        @if ($ukm->pendaftaran_anggota_ditutup_pada)
                                            <div class="text-[11px] text-slate-500 dark:text-zinc-400">
                                                s.d. {{ $ukm->pendaftaran_anggota_ditutup_pada->format('d M Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <flux:badge color="zinc" size="sm" variant="pill">
                                            Pendaftaran Ditutup
                                        </flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>

                            {{-- Aksi --}}
                            <flux:table.cell align="end">
                                <div class="flex items-center justify-end gap-1.5">
                                    <flux:button
                                        size="xs"
                                        variant="subtle"
                                        wire:click="openDetailModal({{ $ukm->id }})"
                                        icon="eye"
                                        title="Lihat Detail UKM"
                                    >
                                        Detail
                                    </flux:button>

                                    @can('update', $ukm)
                                        <flux:button
                                            size="xs"
                                            variant="subtle"
                                            wire:click="openEditModal({{ $ukm->id }})"
                                            icon="pencil-square"
                                            title="Ubah Data UKM"
                                        >
                                            Ubah
                                        </flux:button>
                                    @endcan

                                    @can('toggleStatus', $ukm)
                                        @if ($ukm->status === 'aktif')
                                            <flux:button
                                                size="xs"
                                                variant="danger"
                                                wire:click="confirmDeactivate({{ $ukm->id }})"
                                                icon="no-symbol"
                                                title="Nonaktifkan UKM"
                                            >
                                                Nonaktifkan
                                            </flux:button>
                                        @else
                                            <flux:button
                                                size="xs"
                                                variant="filled"
                                                wire:click="confirmActivate({{ $ukm->id }})"
                                                icon="check-circle"
                                                title="Aktifkan Kembali UKM"
                                            >
                                                Aktifkan
                                            </flux:button>
                                        @endif
                                    @endcan
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <flux:icon name="building-library" class="size-8 text-slate-400 dark:text-zinc-500" />
                                    <div class="font-medium text-slate-800 dark:text-zinc-200">
                                        Tidak ada data UKM ditemukan
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-zinc-400">
                                        Coba sesuaikan kata kunci pencarian atau bersihkan filter.
                                    </p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($ukms->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-zinc-700">
                {{ $ukms->links() }}
            </div>
        @endif
    </div>

    {{-- Form Modal (Tambah / Edit UKM) --}}
    <flux:modal wire:model="showFormModal" class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingId ? 'Ubah Data UKM' : 'Tambah UKM Baru' }}
                </flux:heading>
                <flux:subheading>
                    {{ $editingId ? 'Perbarui informasi profil dan periode pendaftaran UKM.' : 'Lengkapi formulir di bawah ini untuk mendaftarkan organisasi UKM baru.' }}
                </flux:subheading>
            </div>

            <div class="space-y-4">
                {{-- Nama UKM --}}
                <flux:input
                    wire:model="form.nama"
                    label="Nama UKM"
                    placeholder="Contoh: UKM Robotika Poliwangi"
                    required
                />

                {{-- Deskripsi --}}
                <flux:textarea
                    wire:model="form.deskripsi"
                    label="Deskripsi Organisasi"
                    placeholder="Tuliskan gambaran umum aktivitas UKM..."
                    rows="3"
                />

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Visi --}}
                    <flux:textarea
                        wire:model="form.visi"
                        label="Visi"
                        placeholder="Visi organisasi..."
                        rows="3"
                    />

                    {{-- Misi --}}
                    <flux:textarea
                        wire:model="form.misi"
                        label="Misi"
                        placeholder="Misi organisasi..."
                        rows="3"
                    />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Email Kontak --}}
                    <flux:input
                        wire:model="form.email_kontak"
                        type="email"
                        label="Email Resmi Kontak"
                        placeholder="ukm@poliwangi.ac.id"
                    />

                    {{-- Nomor Kontak --}}
                    <flux:input
                        wire:model="form.nomor_kontak"
                        label="Nomor Telepon / WhatsApp"
                        placeholder="08123456789"
                    />
                </div>

                {{-- Logo Upload and Preview --}}
                <div class="space-y-2">
                    <label for="logoUpload" class="text-sm font-medium text-slate-800 dark:text-zinc-200">Logo UKM (Opsional)</label>
                    <div class="flex flex-col gap-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 sm:flex-row sm:items-center dark:border-zinc-600 dark:bg-zinc-900">
                        <div class="flex size-24 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
                            @if ($logoUpload && $logoUpload->isPreviewable())
                                <img src="{{ $logoUpload->temporaryUrl() }}" alt="Pratinjau logo UKM" class="size-full object-cover">
                            @elseif ($storedLogoPreviewUrl)
                                <img src="{{ $storedLogoPreviewUrl }}" alt="Logo UKM saat ini" class="size-full object-cover">
                            @else
                                <flux:icon name="photo" class="size-8 text-slate-300 dark:text-zinc-500" />
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
                            <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-zinc-400">Format PNG, JPG, atau WebP. Ukuran maksimal 2 MB. Pratinjau tampil sebelum logo disimpan.</p>
                            <div wire:loading wire:target="logoUpload" class="mt-2 text-xs font-medium text-blue-700 dark:text-blue-300">Memuat pratinjau...</div>
                            @error('logoUpload')
                                <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Status & Pendaftaran --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 border-t border-slate-100 pt-4 dark:border-zinc-700">
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
                    <flux:input
                        wire:model="form.pendaftaran_anggota_dibuka_pada"
                        type="datetime-local"
                        label="Waktu Dibuka (Opsional)"
                    />

                    <flux:input
                        wire:model="form.pendaftaran_anggota_ditutup_pada"
                        type="datetime-local"
                        label="Waktu Ditutup (Opsional)"
                    />
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $editingId ? 'Simpan Perubahan' : 'Tambahkan UKM' }}</span>
                    <span wire:loading>Menyimpan...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Detail Modal --}}
    <flux:modal wire:model="showDetailModal" class="max-w-2xl">
        @if ($selectedUkm)
            <div class="space-y-6">
                {{-- Modal Header --}}
                <div class="flex items-start gap-4">
                    @if ($selectedUkm->logoUrl())
                        <img src="{{ $selectedUkm->logoUrl() }}" alt="Logo {{ $selectedUkm->nama }}" class="size-14 shrink-0 rounded-xl border border-slate-200 object-cover dark:border-zinc-700">
                    @else
                        <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-lg font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                            {{ $selectedUkm->inisial() }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                                {{ $selectedUkm->nama }}
                            </h2>
                            @if ($selectedUkm->status === 'aktif')
                                <flux:badge color="emerald" size="sm" variant="pill">Aktif</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" variant="pill">Nonaktif</flux:badge>
                            @endif
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-zinc-400">
                            @if ($selectedUkm->email_kontak)
                                <span class="font-mono">{{ $selectedUkm->email_kontak }}</span>
                            @endif
                            @if ($selectedUkm->nomor_kontak)
                                <span>{{ $selectedUkm->nomor_kontak }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Deskripsi, Visi, Misi --}}
                <div class="space-y-4 rounded-xl bg-slate-50 p-4 text-sm dark:bg-zinc-900">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                            Deskripsi
                        </div>
                        <p class="mt-1 text-slate-700 leading-relaxed dark:text-zinc-300">
                            {{ $selectedUkm->deskripsi ?: 'Belum ada deskripsi yang ditambahkan.' }}
                        </p>
                    </div>

                    @if ($selectedUkm->visi)
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                                Visi
                            </div>
                            <p class="mt-1 text-slate-700 leading-relaxed dark:text-zinc-300">
                                {{ $selectedUkm->visi }}
                            </p>
                        </div>
                    @endif

                    @if ($selectedUkm->misi)
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                                Misi
                            </div>
                            <p class="mt-1 text-slate-700 leading-relaxed dark:text-zinc-300 whitespace-pre-line">
                                {{ $selectedUkm->misi }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Info Pendaftaran & Statistik --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 p-3 text-center dark:border-zinc-700">
                        <div class="text-xs text-slate-500 dark:text-zinc-400">Pendaftaran Anggota</div>
                        <div class="mt-1 font-semibold text-slate-800 dark:text-zinc-200">
                            {{ $selectedUkm->status_pendaftaran_anggota === 'dibuka' ? 'Dibuka' : 'Ditutup' }}
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 p-3 text-center dark:border-zinc-700">
                        <div class="text-xs text-slate-500 dark:text-zinc-400">Pengurus Terdaftar</div>
                        <div class="mt-1 font-semibold text-slate-800 dark:text-zinc-200">
                            {{ $selectedUkm->pengurus->count() }} orang
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 p-3 text-center dark:border-zinc-700">
                        <div class="text-xs text-slate-500 dark:text-zinc-400">Total Anggota</div>
                        <div class="mt-1 font-semibold text-slate-800 dark:text-zinc-200">
                            {{ $selectedUkm->keanggotaan_count ?? 0 }} orang
                        </div>
                    </div>
                </div>

                {{-- Pengurus List --}}
                @if ($selectedUkm->pengurus->isNotEmpty())
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-2">
                            Daftar Pengurus UKM
                        </div>
                        <div class="divide-y divide-slate-100 rounded-lg border border-slate-200 dark:divide-zinc-700 dark:border-zinc-700">
                            @foreach ($selectedUkm->pengurus as $pengurus)
                                <div class="flex items-center justify-between p-2.5 text-xs">
                                    <div>
                                        <div class="font-medium text-slate-800 dark:text-zinc-200">{{ $pengurus->name }} <span class="font-normal text-slate-500">· {{ $pengurus->roleLabel() }}</span></div>
                                        <div class="text-slate-500 dark:text-zinc-400">{{ $pengurus->email }}</div>
                                        @if ($pengurus->divisiUkm)
                                            <div class="text-slate-500 dark:text-zinc-400">{{ $pengurus->divisiUkm->nama }}</div>
                                        @endif
                                    </div>
                                    @if ($pengurus->nim)
                                        <div class="font-mono text-slate-500 dark:text-zinc-400">NIM: {{ $pengurus->nim }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-zinc-700">
                    <flux:modal.close>
                        <flux:button variant="filled">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- Educational Deactivation Modal (DESIGN.md 5.3) --}}
    <flux:modal wire:model="showDeactivateModal" class="max-w-lg">
        <div class="space-y-4">
            <div class="flex items-center gap-3 text-rose-600 dark:text-rose-400">
                <div class="flex size-10 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-950/50">
                    <flux:icon name="exclamation-triangle" class="size-6 text-rose-600 dark:text-rose-400" />
                </div>
                <flux:heading size="lg">Nonaktifkan Unit Kegiatan Mahasiswa?</flux:heading>
            </div>

            <div class="rounded-lg bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 dark:bg-zinc-900 dark:text-zinc-300">
                Organisasi ini akan disembunyikan dari daftar publik dan akses pengurus akan dibatasi. Data histori, riwayat anggota, dan dokumen masa lalu tetap aman tersimpan di sistem.
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="deactivate" wire:loading.attr="disabled">
                    <span wire:loading.remove>Ya, Nonaktifkan</span>
                    <span wire:loading>Memproses...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Re-activation Modal --}}
    <flux:modal wire:model="showActivateModal" class="max-w-lg">
        <div class="space-y-4">
            <div class="flex items-center gap-3 text-blue-600 dark:text-blue-400">
                <div class="flex size-10 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-950/50">
                    <flux:icon name="check-circle" class="size-6 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:heading size="lg">Aktifkan Kembali Unit Kegiatan Mahasiswa?</flux:heading>
            </div>

            <div class="rounded-lg bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 dark:bg-zinc-900 dark:text-zinc-300">
                Organisasi ini akan kembali ditampilkan dalam daftar publik dan statusnya menjadi aktif.
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" wire:click="activate" wire:loading.attr="disabled">
                    <span wire:loading.remove>Ya, Aktifkan Kembali</span>
                    <span wire:loading>Memproses...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
