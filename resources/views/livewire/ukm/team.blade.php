<x-ui.page>
    <x-ui.page-header
        eyebrow="Ruang Pengurus Inti · {{ $ukm->nama }}"
        title="Divisi dan Pengurus"
        description="Atur divisi UKM ini dan tempatkan mahasiswa sebagai Pengurus Divisi. Akun yang dibuat di sini hanya terikat ke UKM ini."
    />

    <div class="grid gap-5 xl:grid-cols-2">
        <x-ui.panel title="Struktur divisi" description="Nama divisi mengikuti struktur UKM ini." icon="squares-2x2">
            <form wire:submit="saveDivision" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <flux:input wire:model="divisionName" label="Nama divisi" placeholder="Contoh: Divisi Humas" required />
                </div>
                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ $editingDivisionId ? 'Simpan' : 'Tambah' }}</flux:button>
                    @if ($editingDivisionId)
                        <flux:button type="button" variant="ghost" wire:click="cancelDivisionEdit">Batal</flux:button>
                    @endif
                </div>
            </form>

            @error('divisionName')
                <p class="mt-3 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror

            <div class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-200 dark:divide-white/10 dark:border-white/10">
                @forelse ($divisions as $division)
                    <div wire:key="division-{{ $division->id }}" class="flex items-center justify-between gap-3 p-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-950 dark:text-white">{{ $division->nama }}</p>
                            <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $division->pengurus_count }} pengurus</p>
                        </div>
                        <div class="flex shrink-0 gap-1.5">
                            <flux:button size="xs" variant="subtle" icon="pencil-square" wire:click="editDivision({{ $division->id }})">Ubah</flux:button>
                            @if ($division->pengurus_count === 0)
                                <flux:button size="xs" variant="danger" icon="trash" wire:click="deleteDivision({{ $division->id }})" wire:confirm="Hapus divisi kosong ini?">Hapus</flux:button>
                            @endif
                        </div>
                    </div>
                @empty
                    <x-ui.empty icon="squares-2x2" title="Belum ada divisi" description="Tambahkan divisi sebelum menetapkan pengurus." />
                @endforelse
            </div>
        </x-ui.panel>

        <x-ui.panel title="Tempatkan mahasiswa yang sudah punya akun" description="Cari dengan NIM atau email. Akun yang sudah terikat ke UKM lain tidak dapat dipindahkan lewat halaman ini." icon="magnifying-glass">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <flux:input wire:model="lookupStudent" label="NIM atau email" placeholder="Masukkan identitas akun" />
                </div>
                <flux:button type="button" variant="filled" wire:click="findStudent" wire:loading.attr="disabled">Cari akun</flux:button>
            </div>

            @error('lookupStudent')
                <p class="mt-3 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror

            @if ($candidate)
                <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/40">
                    <p class="text-sm font-semibold text-slate-950 dark:text-white">{{ $candidate->name }} · <span class="font-mono text-xs font-normal">{{ $candidate->nim }}</span></p>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-zinc-300">{{ $candidate->email }}</p>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <flux:select wire:model="member.division_id" label="Tempatkan ke divisi" required>
                                <option value="">Pilih divisi</option>
                                @foreach ($divisions as $division)<option value="{{ $division->id }}">{{ $division->nama }}</option>@endforeach
                            </flux:select>
                        </div>
                        <flux:button type="button" variant="primary" wire:click="assignExistingStudent" wire:loading.attr="disabled">Tetapkan</flux:button>
                    </div>
                    @error('member.division_id')
                        <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </x-ui.panel>
    </div>

    <x-ui.panel title="Buat akun Pengurus Divisi" description="Gunakan jika mahasiswa belum memiliki akun. NIM dan email harus belum terdaftar." icon="user-plus">
        <form wire:submit="createDivisionMember" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <flux:input wire:model="member.name" label="Nama lengkap" required />
            <flux:input wire:model="member.nim" label="NIM" required />
            <flux:input wire:model="member.email" label="Email" type="email" required />
            <flux:select wire:model="member.division_id" label="Divisi" required>
                <option value="">Pilih divisi</option>
                @foreach ($divisions as $division)<option value="{{ $division->id }}">{{ $division->nama }}</option>@endforeach
            </flux:select>
            <flux:input wire:model="member.password" label="Kata sandi sementara" type="password" required viewable />
            <div class="flex items-end">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="createDivisionMember">Buat akun dan tetapkan</span>
                    <span wire:loading wire:target="createDivisionMember">Menyimpan...</span>
                </flux:button>
            </div>
        </form>

        @error('member')
            <p class="mt-3 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </x-ui.panel>

    <x-ui.panel title="Daftar Pengurus Divisi" :description="$members->count().' pengurus divisi terdaftar'" icon="user-group" flush>
        <div class="divide-y divide-slate-100 dark:divide-white/10">
            @forelse ($members as $memberRow)
                <div wire:key="member-{{ $memberRow->id }}" class="flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-950 dark:text-white">
                            {{ $memberRow->name }}
                            <span class="font-normal text-slate-500 dark:text-zinc-400">· {{ $memberRow->divisiUkm?->nama ?? 'Divisi belum ditentukan' }}</span>
                        </p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-zinc-400">{{ $memberRow->nim }} · {{ $memberRow->email }}</p>
                    </div>

                    <div class="flex flex-wrap items-end gap-2">
                        @if ($memberRow->isActive())
                            <flux:select wire:model="divisionAssignments.{{ $memberRow->id }}" label="Divisi">
                                @foreach ($divisions as $division)<option value="{{ $division->id }}">{{ $division->nama }}</option>@endforeach
                            </flux:select>
                            <flux:button size="sm" variant="filled" wire:click="moveMemberToDivision({{ $memberRow->id }})">Simpan divisi</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="deactivateMember({{ $memberRow->id }})" wire:confirm="Nonaktifkan akun ini? Riwayat tetap tersimpan.">Nonaktifkan akun</flux:button>
                        @else
                            <flux:badge color="zinc" size="sm">Akun nonaktif</flux:badge>
                        @endif
                    </div>
                </div>
            @empty
                <x-ui.empty icon="user-group" title="Belum ada Pengurus Divisi" description="Tetapkan mahasiswa ke divisi untuk mulai menyusun struktur pengurus." />
            @endforelse
        </div>
    </x-ui.panel>
</x-ui.page>
