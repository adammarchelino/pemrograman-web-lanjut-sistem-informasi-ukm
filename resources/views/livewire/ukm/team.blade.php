<div class="mx-auto max-w-7xl space-y-6 p-6 lg:p-8">
    <header>
        <p class="text-sm font-semibold text-blue-700 dark:text-blue-300">Ruang Pengurus Inti · {{ $ukm->nama }}</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Divisi dan Pengurus</h1>
        <p class="mt-2 max-w-3xl text-sm text-slate-600 dark:text-zinc-300">Atur divisi UKM ini dan tempatkan mahasiswa sebagai Pengurus Divisi. Akun yang dibuat di sini hanya terikat ke UKM ini.</p>
    </header>

    <div class="grid gap-5 xl:grid-cols-2">
        <section class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div>
                <h2 class="font-semibold text-slate-900 dark:text-white">Struktur divisi</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Nama divisi mengikuti struktur UKM ini.</p>
            </div>
            <form wire:submit="saveDivision" class="flex flex-col gap-2 sm:flex-row">
                <flux:input wire:model="divisionName" label="Nama divisi" placeholder="Contoh: Divisi Humas" required />
                <div class="flex items-end gap-2">
                    <flux:button type="submit" variant="primary">{{ $editingDivisionId ? 'Simpan' : 'Tambah' }}</flux:button>
                    @if ($editingDivisionId)
                        <flux:button type="button" variant="ghost" wire:click="cancelDivisionEdit">Batal</flux:button>
                    @endif
                </div>
            </form>
            <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 dark:divide-zinc-700 dark:border-zinc-700">
                @forelse ($divisions as $division)
                    <div wire:key="division-{{ $division->id }}" class="flex items-center justify-between gap-3 p-3">
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $division->nama }}</p>
                            <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $division->pengurus_count }} pengurus</p>
                        </div>
                        <div class="flex gap-2">
                            <flux:button size="sm" variant="subtle" wire:click="editDivision({{ $division->id }})">Ubah nama</flux:button>
                            @if ($division->pengurus_count === 0)
                                <flux:button size="sm" variant="danger" wire:click="deleteDivision({{ $division->id }})" wire:confirm="Hapus divisi kosong ini?">Hapus</flux:button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="p-4 text-sm text-slate-500 dark:text-zinc-400">Belum ada divisi. Tambahkan divisi sebelum menetapkan pengurus.</p>
                @endforelse
            </div>
        </section>

        <section class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div>
                <h2 class="font-semibold text-slate-900 dark:text-white">Tempatkan mahasiswa yang sudah punya akun</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Cari dengan NIM atau email. Akun yang sudah terikat ke UKM lain tidak dapat dipindahkan lewat halaman ini.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <flux:input wire:model="lookupStudent" label="NIM atau email" placeholder="Masukkan identitas akun" />
                <div class="flex items-end"><flux:button type="button" variant="filled" wire:click="findStudent">Cari akun</flux:button></div>
            </div>
            @if ($candidate)
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/40">
                    <p class="font-medium text-slate-900 dark:text-white">{{ $candidate->name }} · {{ $candidate->nim }}</p>
                    <p class="text-sm text-slate-600 dark:text-zinc-300">{{ $candidate->email }}</p>
                    <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                        <flux:select wire:model="member.division_id" label="Tempatkan ke divisi" required>
                            <option value="">Pilih divisi</option>
                            @foreach ($divisions as $division)<option value="{{ $division->id }}">{{ $division->nama }}</option>@endforeach
                        </flux:select>
                        <div class="flex items-end"><flux:button type="button" variant="primary" wire:click="assignExistingStudent">Tetapkan</flux:button></div>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <section class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
        <div>
            <h2 class="font-semibold text-slate-900 dark:text-white">Buat akun Pengurus Divisi</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Gunakan jika mahasiswa belum memiliki akun. NIM dan email harus belum terdaftar.</p>
        </div>
        <form wire:submit="createDivisionMember" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <flux:input wire:model="member.name" label="Nama lengkap" required />
            <flux:input wire:model="member.nim" label="NIM" required />
            <flux:input wire:model="member.email" label="Email" type="email" required />
            <flux:select wire:model="member.division_id" label="Divisi" required>
                <option value="">Pilih divisi</option>
                @foreach ($divisions as $division)<option value="{{ $division->id }}">{{ $division->nama }}</option>@endforeach
            </flux:select>
            <flux:input wire:model="member.password" label="Kata sandi sementara" type="password" required viewable />
            <div class="flex items-end"><flux:button type="submit" variant="primary" wire:loading.attr="disabled">Buat akun dan tetapkan</flux:button></div>
        </form>
        @error('member')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
        <div class="border-b border-slate-200 p-5 dark:border-zinc-700">
            <h2 class="font-semibold text-slate-900 dark:text-white">Daftar Pengurus Divisi</h2>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-zinc-700">
            @forelse ($members as $memberRow)
                <div wire:key="member-{{ $memberRow->id }}" class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $memberRow->name }} <span class="text-sm font-normal text-slate-500">· {{ $memberRow->divisiUkm?->nama ?? 'Divisi belum ditentukan' }}</span></p>
                        <p class="text-sm text-slate-500 dark:text-zinc-400">{{ $memberRow->nim }} · {{ $memberRow->email }}</p>
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
                <p class="p-5 text-sm text-slate-500 dark:text-zinc-400">Belum ada Pengurus Divisi.</p>
            @endforelse
        </div>
    </section>
</div>
