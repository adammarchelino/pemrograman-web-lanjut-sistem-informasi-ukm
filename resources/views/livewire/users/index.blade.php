<div class="space-y-6">
    {{-- Header & Action Bar --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-zinc-400">
                <span>SIP-UKM</span>
                <span>/</span>
                <span class="text-blue-600 dark:text-blue-400">Kelola User &amp; Role</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Kelola User &amp; Role
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">
                Manajemen akun pengguna, penugasan pengurus organisasi UKM, dan otorisasi hak akses kampus.
            </p>
        </div>

        <div>
            <flux:button variant="primary" icon="user-plus" wire:click="openCreateModal" class="w-full sm:w-auto">
                Tambah Akun User
            </flux:button>
        </div>
    </div>

    {{-- Account Summary --}}
    @php
        $userStatCards = [
            [
                'label' => 'Total Pengguna',
                'value' => $userStats['total'],
                'description' => 'Seluruh akun terdaftar',
                'icon' => 'users',
                'icon_class' => 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
            ],
            [
                'label' => 'Mahasiswa',
                'value' => $userStats['mahasiswa'],
                'description' => 'Akun mahasiswa',
                'icon' => 'academic-cap',
                'icon_class' => 'bg-sky-50 text-sky-700 dark:bg-sky-950 dark:text-sky-300',
            ],
            [
                'label' => 'Pengurus UKM',
                'value' => $userStats['pengurus_ukm'],
                'description' => 'Akun pengurus',
                'icon' => 'user-group',
                'icon_class' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300',
            ],
            [
                'label' => 'Kemahasiswaan',
                'value' => $userStats['kemahasiswaan'],
                'description' => 'Akun administrator',
                'icon' => 'building-library',
                'icon_class' => 'bg-violet-50 text-violet-700 dark:bg-violet-950 dark:text-violet-300',
            ],
        ];
    @endphp

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan akun pengguna">
        @foreach ($userStatCards as $card)
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
                    placeholder="Cari nama pengguna, email, NIM..."
                    icon="magnifying-glass"
                    clearable
                />
            </div>

            <div>
                <flux:select wire:model.live="filterRole" placeholder="Semua Role">
                    <option value="">Semua Role</option>
                    <option value="kemahasiswaan">Bagian Kemahasiswaan</option>
                    <option value="pengurus_ukm">Pengurus UKM</option>
                    <option value="mahasiswa">Mahasiswa</option>
                </flux:select>
            </div>

            <div>
                <flux:select wire:model.live="filterUkm" placeholder="Semua UKM">
                    <option value="">Semua Afiliasi UKM</option>
                    @foreach ($ukmList as $ukmItem)
                        <option value="{{ $ukmItem->id }}">{{ $ukmItem->nama }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        @if ($search !== '' || $filterRole !== '' || $filterUkm !== '')
            <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 text-xs text-slate-500 dark:border-zinc-700 dark:text-zinc-400">
                <span>Menampilkan pengguna terfilter</span>
                <flux:button variant="subtle" size="sm" wire:click="resetFilters" icon="arrow-path">
                    Reset Filter
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Users Data Table --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="uppercase tracking-wider text-xs">Pengguna</flux:table.column>
                    <flux:table.column class="uppercase tracking-wider text-xs">NIM</flux:table.column>
                    <flux:table.column class="uppercase tracking-wider text-xs">Role / Hak Akses</flux:table.column>
                    <flux:table.column class="uppercase tracking-wider text-xs">Afiliasi UKM</flux:table.column>
                    <flux:table.column align="end" class="uppercase tracking-wider text-xs">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($users as $user)
                        <flux:table.row :key="$user->id" class="hover:bg-slate-50/70 transition dark:hover:bg-zinc-700/50">
                            {{-- Profil Pengguna --}}
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-slate-100 font-semibold text-slate-700 text-sm dark:bg-zinc-700 dark:text-zinc-200">
                                        {{ $user->initials() }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-900 dark:text-white flex items-center gap-2">
                                            <span>{{ $user->name }}</span>
                                            @if ($user->id === auth()->id())
                                                <span class="rounded bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                                    Anda
                                                </span>
                                            @endif
                                        </div>
                                        <div class="font-mono text-xs text-slate-500 dark:text-zinc-400">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </flux:table.cell>

                            {{-- NIM --}}
                            <flux:table.cell>
                                @if ($user->nim)
                                    <span class="font-mono text-xs text-slate-700 dark:text-zinc-300">
                                        {{ $user->nim }}
                                    </span>
                                @else
                                    <span class="text-xs italic text-slate-400">-</span>
                                @endif
                            </flux:table.cell>

                            {{-- Role Badge --}}
                            <flux:table.cell>
                                <div class="flex flex-col items-start gap-1">
                                    <flux:badge :color="$user->isKemahasiswaan() ? 'purple' : ($user->isPengurusUkm() ? 'sky' : 'teal')" size="sm" variant="pill">
                                        {{ $user->roleLabel() }}
                                    </flux:badge>
                                    <flux:badge :color="$user->isActive() ? 'emerald' : 'zinc'" size="xs" variant="pill">
                                        {{ $user->isActive() ? 'Aktif' : 'Nonaktif' }}
                                    </flux:badge>
                                </div>
                            </flux:table.cell>

                            {{-- Afiliasi UKM --}}
                            <flux:table.cell>
                                @if ($user->ukm)
                                    <div class="text-xs font-medium text-slate-800 dark:text-zinc-200">
                                        {{ $user->ukm->nama }}
                                    </div>
                                    @if ($user->divisiUkm)
                                        <div class="text-xs text-slate-500 dark:text-zinc-400">{{ $user->divisiUkm->nama }}</div>
                                    @endif
                                @else
                                    <span class="text-xs italic text-slate-400">-</span>
                                @endif
                            </flux:table.cell>

                            {{-- Aksi --}}
                            <flux:table.cell align="end">
                                <div class="flex items-center justify-end gap-1.5">
                                    <flux:button
                                        size="xs"
                                        variant="subtle"
                                        wire:click="openDetailModal({{ $user->id }})"
                                        icon="eye"
                                        title="Lihat Detail Pengguna"
                                    >
                                        Detail
                                    </flux:button>

                                    @if ($user->id !== auth()->id())
                                        <flux:button
                                            size="xs"
                                            variant="subtle"
                                            wire:click="toggleAccountStatus({{ $user->id }})"
                                            wire:confirm="{{ $user->isActive() ? 'Nonaktifkan akun ini? Riwayat tetap tersimpan.' : 'Aktifkan kembali akun ini?' }}"
                                            icon="{{ $user->isActive() ? 'no-symbol' : 'check-circle' }}"
                                        >
                                            {{ $user->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </flux:button>
                                    @endif

                                    <flux:button
                                        size="xs"
                                        variant="subtle"
                                        wire:click="openEditModal({{ $user->id }})"
                                        icon="pencil-square"
                                        title="Ubah Data Pengguna"
                                    >
                                        Ubah
                                    </flux:button>

                                    @if ($user->id !== auth()->id())
                                        <flux:button
                                            size="xs"
                                            variant="danger"
                                            wire:click="confirmDelete({{ $user->id }})"
                                            icon="trash"
                                            title="Hapus Pengguna"
                                        >
                                            Hapus
                                        </flux:button>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <flux:icon name="users" class="size-8 text-slate-400 dark:text-zinc-500" />
                                    <div class="font-medium text-slate-800 dark:text-zinc-200">
                                        Tidak ada pengguna ditemukan
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-zinc-400">
                                        Coba sesuaikan filter pencarian atau role pengguna.
                                    </p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-zinc-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Form Modal (Tambah / Edit Pengguna) --}}
    <flux:modal wire:model="showFormModal" class="max-w-xl">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingUserId ? 'Ubah Akun Pengguna' : 'Tambah Akun Pengguna Baru' }}
                </flux:heading>
                <flux:subheading>
                    {{ $editingUserId ? 'Perbarui informasi profil dan penugasan peran pengguna.' : 'Lengkapi identitas pengguna dan tentukan hak akses peran sistem.' }}
                </flux:subheading>
            </div>

            <div class="space-y-4">
                {{-- Nama Lengkap --}}
                <flux:input
                    wire:model="form.name"
                    label="Nama Lengkap"
                    placeholder="Contoh: Muhammad Wildan"
                    required
                />

                {{-- Email --}}
                <flux:input
                    wire:model="form.email"
                    type="email"
                    label="Alamat Email"
                    placeholder="nama@poliwangi.ac.id"
                    required
                />

                {{-- NIM --}}
                <flux:input
                    wire:model="form.nim"
                    label="Nomor Induk Mahasiswa (NIM)"
                    placeholder="Contoh: 362558302044"
                />

                {{-- Role --}}
                <flux:select wire:model.live="form.role" label="Role / Hak Akses Sistem" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="pengurus_ukm">Pengurus UKM</option>
                    <option value="kemahasiswaan">Bagian Kemahasiswaan</option>
                </flux:select>

                {{-- Penempatan UKM ditetapkan Kemahasiswaan; role mahasiswa dapat merangkap pengurus. --}}
                @if ($form['role'] === 'pengurus_ukm' || filled($form['ukm_id'] ?? null) || filled($form['jabatan_ukm'] ?? null))
                    <flux:select wire:model="form.ukm_id" label="UKM Yang Dikelola" required>
                        <option value="">Pilih UKM Binaan...</option>
                        @foreach ($ukmList as $ukmOption)
                            <option value="{{ $ukmOption->id }}">{{ $ukmOption->nama }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model.live="form.jabatan_ukm" label="Jabatan dalam UKM" required>
                        <option value="ketua">Ketua UKM</option>
                        <option value="inti">Pengurus Inti</option>
                        <option value="divisi">Pengurus Divisi</option>
                    </flux:select>

                    @if ($form['jabatan_ukm'] === 'divisi')
                        <flux:select wire:model="form.divisi_ukm_id" label="Divisi" required>
                            <option value="">Pilih divisi yang telah dibuat Pengurus Inti...</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->nama }}</option>
                            @endforeach
                        </flux:select>
                        @if ($divisions->isEmpty())
                            <p class="text-xs text-amber-700 dark:text-amber-300">Belum ada divisi pada UKM ini. Pengurus Inti perlu menambahkannya terlebih dahulu.</p>
                        @endif
                    @endif
                @endif

                {{-- Password --}}
                <flux:input
                    wire:model="form.password"
                    type="password"
                    label="{{ $editingUserId ? 'Kata Sandi Baru (Kosongkan jika tidak diubah)' : 'Kata Sandi Awal' }}"
                    placeholder="{{ $editingUserId ? '••••••••' : 'Minimal 8 karakter' }}"
                    viewable
                />
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $editingUserId ? 'Simpan Perubahan' : 'Buat Akun' }}</span>
                    <span wire:loading>Menyimpan...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- User Detail Modal --}}
    <flux:modal wire:model="showDetailModal" class="max-w-xl">
        @if ($selectedUser)
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="flex size-14 shrink-0 items-center justify-center rounded-full bg-blue-100 text-lg font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                        {{ $selectedUser->initials() }}
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="lg">Detail Pengguna</flux:heading>
                        <flux:subheading>{{ $selectedUser->name }}</flux:subheading>
                    </div>
                </div>

                <dl class="grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="space-y-1">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-zinc-400">Email</dt>
                        <dd class="break-all text-sm text-slate-900 dark:text-white">{{ $selectedUser->email }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-zinc-400">NIM</dt>
                        <dd class="text-sm text-slate-900 dark:text-white">{{ $selectedUser->nim ?: 'Belum diisi' }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-zinc-400">Role</dt>
                        <dd class="text-sm text-slate-900 dark:text-white">{{ $selectedUser->roleLabel() }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-zinc-400">Afiliasi UKM</dt>
                        <dd class="text-sm text-slate-900 dark:text-white">{{ $selectedUser->ukm?->nama ?? 'Tidak ada' }}</dd>
                        @if ($selectedUser->divisiUkm)
                            <dd class="text-xs text-slate-500 dark:text-zinc-400">{{ $selectedUser->divisiUkm->nama }}</dd>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-zinc-400">Status akun</dt>
                        <dd class="text-sm text-slate-900 dark:text-white">{{ $selectedUser->isActive() ? 'Aktif' : 'Nonaktif' }}</dd>
                    </div>

                    <div class="space-y-1 sm:col-span-2">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-zinc-400">Terdaftar sejak</dt>
                        <dd class="text-sm text-slate-900 dark:text-white">{{ $selectedUser->created_at?->format('d M Y, H:i') ?? 'Tidak tersedia' }}</dd>
                    </div>
                </dl>

                <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-zinc-700">
                    <flux:modal.close>
                        <flux:button variant="filled">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- Delete Modal --}}
    <flux:modal wire:model="showDeleteModal" class="max-w-lg">
        <div class="space-y-4">
            <div class="flex items-center gap-3 text-rose-600 dark:text-rose-400">
                <div class="flex size-10 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-950/50">
                    <flux:icon name="trash" class="size-6 text-rose-600 dark:text-rose-400" />
                </div>
                <flux:heading size="lg">Hapus Akun Pengguna?</flux:heading>
            </div>

            <div class="rounded-lg bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 dark:bg-zinc-900 dark:text-zinc-300">
                Akun hanya dapat dihapus jika belum memiliki riwayat pengajuan anggota, keanggotaan, registrasi kegiatan, atau presensi. Riwayat yang sudah tercatat akan dipertahankan.
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="deleteUser" wire:loading.attr="disabled">
                    <span wire:loading.remove>Ya, Hapus Akun</span>
                    <span wire:loading>Memproses...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
