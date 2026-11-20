<x-ui.page>
    <x-ui.page-header
        eyebrow="Administrasi akses"
        title="Kelola User & Role"
        description="Manajemen akun pengguna, penugasan pengurus organisasi UKM, dan otorisasi hak akses kampus."
    >
        <x-slot:actions>
            <flux:button variant="primary" icon="user-plus" wire:click="openCreateModal">
                Tambah Akun User
            </flux:button>
        </x-slot:actions>
    </x-ui.page-header>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan akun pengguna">
        <x-ui.stat label="Total Pengguna" :value="$userStats['total']" note="Seluruh akun terdaftar" icon="users" tone="navy" />
        <x-ui.stat label="Mahasiswa" :value="$userStats['mahasiswa']" note="Akun mahasiswa" icon="academic-cap" tone="blue" />
        <x-ui.stat label="Pengurus UKM" :value="$userStats['pengurus_ukm']" note="Akun pengurus" icon="user-group" tone="emerald" />
        <x-ui.stat label="Kemahasiswaan" :value="$userStats['kemahasiswaan']" note="Akun administrator" icon="building-library" tone="slate" />
    </section>

    <x-ui.panel flush>
        <div class="border-b border-slate-200 p-4 dark:border-white/10">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">
                <div class="md:col-span-2">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama pengguna, email, atau NIM..."
                        icon="magnifying-glass"
                        clearable
                    />
                </div>

                <flux:select wire:model.live="filterRole" placeholder="Semua Role">
                    <option value="">Semua Role</option>
                    <option value="kemahasiswaan">Bagian Kemahasiswaan</option>
                    <option value="pengurus_ukm">Pengurus UKM</option>
                    <option value="mahasiswa">Mahasiswa</option>
                </flux:select>

                <flux:select wire:model.live="filterUkm" placeholder="Semua UKM">
                    <option value="">Semua Afiliasi UKM</option>
                    @foreach ($ukmList as $ukmItem)
                        <option value="{{ $ukmItem->id }}">{{ $ukmItem->nama }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3 dark:border-white/10">
                <p class="flex items-baseline gap-2 text-sm text-slate-600 dark:text-zinc-300">
                    <span class="text-xl font-semibold tabular-nums text-navy-950 dark:text-white">{{ $users->total() }}</span>
                    <span>pengguna ditemukan</span>
                </p>

                @if ($search !== '' || $filterRole !== '' || $filterUkm !== '')
                    <flux:button variant="subtle" size="sm" wire:click="resetFilters" icon="arrow-path">Reset Filter</flux:button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto px-4">
            <table class="w-full min-w-[860px] table-fixed text-left text-sm">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        <th scope="col" class="w-[28%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Pengguna</th>
                        <th scope="col" class="w-[11%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">NIM</th>
                        <th scope="col" class="w-[19%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Role / Hak Akses</th>
                        <th scope="col" class="w-[19%] border-b border-slate-200 py-3 pe-3 text-start font-semibold dark:border-white/10">Afiliasi UKM</th>
                        <th scope="col" class="w-[23%] border-b border-slate-200 py-3 text-end font-semibold dark:border-white/10">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50/80 dark:border-white/5 dark:hover:bg-white/5">
                            <td class="py-3.5 pe-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-navy-950 text-xs font-semibold text-white">
                                        {{ $user->initials() }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 text-sm font-medium text-slate-950 dark:text-white">
                                            <span class="truncate">{{ $user->name }}</span>
                                            @if ($user->id === auth()->id())
                                                <x-ui.badge tone="accent" :marker="false">Anda</x-ui.badge>
                                            @endif
                                        </div>
                                        <div class="truncate font-mono text-xs text-slate-500 dark:text-zinc-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                @if ($user->nim)
                                    <span class="font-mono text-xs text-slate-700 dark:text-zinc-300">{{ $user->nim }}</span>
                                @else
                                    <span class="text-xs italic text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <x-ui.badge>{{ $user->roleLabel() }}</x-ui.badge>
                                    <x-ui.badge :tone="$user->isActive() ? 'positive' : 'negative'">
                                        {{ $user->isActive() ? 'Aktif' : 'Nonaktif' }}
                                    </x-ui.badge>
                                </div>
                            </td>

                            <td class="py-3.5 pe-3 align-middle">
                                @if ($user->ukm)
                                    <div class="text-xs font-medium text-slate-800 dark:text-zinc-200">{{ $user->ukm->nama }}</div>
                                    @if ($user->divisiUkm)
                                        <div class="text-xs text-slate-500 dark:text-zinc-400">{{ $user->divisiUkm->nama }}</div>
                                    @endif
                                @else
                                    <span class="text-xs italic text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="py-3.5 align-middle text-end">
                                <div class="flex flex-wrap items-center justify-end gap-1">
                                    <flux:button size="xs" variant="subtle" wire:click="openDetailModal({{ $user->id }})" icon="eye" title="Lihat Detail Pengguna">Detail</flux:button>

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

                                    <flux:button size="xs" variant="subtle" wire:click="openEditModal({{ $user->id }})" icon="pencil-square" title="Ubah Data Pengguna">Ubah</flux:button>

                                    @if ($user->id !== auth()->id())
                                        <flux:button size="xs" variant="danger" wire:click="confirmDelete({{ $user->id }})" icon="trash" title="Hapus Pengguna">Hapus</flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-0">
                                <x-ui.empty icon="users" title="Tidak ada pengguna ditemukan" description="Coba sesuaikan filter pencarian atau role pengguna." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-white/10">
                {{ $users->links() }}
            </div>
        @endif
    </x-ui.panel>

    <flux:modal wire:model="showFormModal" class="max-w-xl">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingUserId ? 'Ubah Akun Pengguna' : 'Tambah Akun Pengguna Baru' }}</flux:heading>
                <flux:subheading>
                    {{ $editingUserId ? 'Perbarui informasi profil dan penugasan peran pengguna.' : 'Lengkapi identitas pengguna dan tentukan hak akses peran sistem.' }}
                </flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="form.name" label="Nama Lengkap" placeholder="Contoh: Muhammad Wildan" required />
                <flux:input wire:model="form.email" type="email" label="Alamat Email" placeholder="nama@poliwangi.ac.id" required />
                <flux:input wire:model="form.nim" label="Nomor Induk Mahasiswa (NIM)" placeholder="Contoh: 362558302044" />

                <flux:select wire:model.live="form.role" label="Role / Hak Akses Sistem" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="pengurus_ukm">Pengurus UKM</option>
                    <option value="kemahasiswaan">Bagian Kemahasiswaan</option>
                </flux:select>

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

                <flux:input
                    wire:model="form.password"
                    type="password"
                    label="{{ $editingUserId ? 'Kata Sandi Baru (Kosongkan jika tidak diubah)' : 'Kata Sandi Awal' }}"
                    placeholder="{{ $editingUserId ? '••••••••' : 'Minimal 8 karakter' }}"
                    viewable
                />
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-white/10">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ $editingUserId ? 'Simpan Perubahan' : 'Buat Akun' }}</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="showDetailModal" class="max-w-xl">
        @if ($selectedUser)
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-navy-950 text-sm font-semibold text-white">
                        {{ $selectedUser->initials() }}
                    </span>
                    <div class="min-w-0">
                        <flux:heading size="lg">Detail Pengguna</flux:heading>
                        <flux:subheading>{{ $selectedUser->name }}</flux:subheading>
                    </div>
                </div>

                <dl class="grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2 dark:border-white/10 dark:bg-white/5">
                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Email</dt>
                        <dd class="break-all text-sm text-slate-950 dark:text-white">{{ $selectedUser->email }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">NIM</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $selectedUser->nim ?: 'Belum diisi' }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Role</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $selectedUser->roleLabel() }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Afiliasi UKM</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $selectedUser->ukm?->nama ?? 'Tidak ada' }}</dd>
                        @if ($selectedUser->divisiUkm)
                            <dd class="text-xs text-slate-500 dark:text-zinc-400">{{ $selectedUser->divisiUkm->nama }}</dd>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Status akun</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $selectedUser->isActive() ? 'Aktif' : 'Nonaktif' }}</dd>
                    </div>

                    <div class="space-y-1 sm:col-span-2">
                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Terdaftar sejak</dt>
                        <dd class="text-sm text-slate-950 dark:text-white">{{ $selectedUser->created_at?->format('d M Y, H:i') ?? 'Tidak tersedia' }}</dd>
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

    <flux:modal wire:model="showDeleteModal" class="max-w-lg">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400">
                    <flux:icon name="trash" class="size-5" />
                </span>
                <flux:heading size="lg">Hapus Akun Pengguna?</flux:heading>
            </div>

            <div class="rounded-lg bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                Akun hanya dapat dihapus jika belum memiliki riwayat pengajuan anggota, keanggotaan, registrasi kegiatan, atau presensi. Riwayat yang sudah tercatat akan dipertahankan.
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close><flux:button variant="filled">Batal</flux:button></flux:modal.close>
                <flux:button variant="danger" wire:click="deleteUser" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="deleteUser">Ya, Hapus Akun</span>
                    <span wire:loading wire:target="deleteUser">Memproses...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-ui.page>
