<x-ui.page>
    @if ($stats)
        <section class="relative overflow-hidden rounded-2xl bg-navy-950 text-white">
            <div class="brand-grid absolute inset-0" aria-hidden="true"></div>
            <div class="brand-glow absolute inset-0" aria-hidden="true"></div>

            <div class="relative flex flex-col gap-6 p-6 sm:p-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-blue-300">PANEL KEMAHASISWAAN</p>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Selamat datang, {{ $user->name }}</h1>
                    <p class="mt-3 text-sm leading-6 text-blue-100/80">
                        Pantau ringkasan UKM dan akun pengguna, lalu lanjutkan pengelolaan dari pintasan berikut.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <flux:button variant="primary" :href="route('ukm.index')" icon="building-office-2" wire:navigate>
                        Data UKM
                    </flux:button>
                    <a href="{{ route('users.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg border border-white/20 px-3.5 py-2 text-sm font-medium text-white transition-colors hover:bg-white/10">
                        <flux:icon name="users" class="size-4" />
                        User &amp; Role
                    </a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan administratif">
            <x-ui.stat label="Total UKM" :value="$stats['ukm_total']" note="Organisasi terdaftar" icon="building-office-2" tone="navy" />
            <x-ui.stat label="UKM Aktif" :value="$stats['ukm_aktif']" note="Berstatus aktif" icon="check-circle" tone="emerald" />
            <x-ui.stat label="Pendaftaran Dibuka" :value="$stats['ukm_oprec']" note="UKM menerima anggota" icon="user-plus" tone="amber" />
            <x-ui.stat label="Total Pengguna" :value="$stats['users_total']" note="Semua role" icon="users" tone="blue" />
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <x-ui.panel title="Komposisi pengguna" description="Akun berdasarkan role yang terdaftar." icon="user-group">
                <dl class="divide-y divide-slate-100 dark:divide-white/10">
                    <div class="flex items-center justify-between py-3 text-sm">
                        <dt class="text-slate-600 dark:text-zinc-300">Mahasiswa</dt>
                        <dd class="font-semibold tabular-nums text-slate-950 dark:text-white">{{ number_format($stats['users_mahasiswa']) }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3 text-sm">
                        <dt class="text-slate-600 dark:text-zinc-300">Pengurus UKM</dt>
                        <dd class="font-semibold tabular-nums text-slate-950 dark:text-white">{{ number_format($stats['users_pengurus']) }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3 text-sm">
                        <dt class="text-slate-600 dark:text-zinc-300">Kemahasiswaan</dt>
                        <dd class="font-semibold tabular-nums text-slate-950 dark:text-white">{{ number_format($stats['users_kemahasiswaan']) }}</dd>
                    </div>
                </dl>
            </x-ui.panel>

            <x-ui.panel title="Akses cepat" description="Buka halaman pengelolaan yang sering digunakan." icon="arrow-top-right-on-square">
                <div class="space-y-3">
                    <a href="{{ route('ukm.index') }}" wire:navigate class="group flex items-center justify-between gap-3 rounded-lg border border-slate-200 p-4 transition-colors hover:border-blue-300 hover:bg-blue-50/60 dark:border-white/10 dark:hover:border-blue-800 dark:hover:bg-blue-950/30">
                        <span class="flex items-center gap-3">
                            <span class="flex size-9 items-center justify-center rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                <flux:icon name="building-office-2" class="size-4" />
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-slate-950 dark:text-white">Kelola Data UKM</span>
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-zinc-400">Profil, status, dan pendaftaran anggota</span>
                            </span>
                        </span>
                        <flux:icon name="arrow-right" class="size-4 shrink-0 text-slate-400 transition-transform group-hover:translate-x-0.5 group-hover:text-blue-700" />
                    </a>

                    <a href="{{ route('users.index') }}" wire:navigate class="group flex items-center justify-between gap-3 rounded-lg border border-slate-200 p-4 transition-colors hover:border-blue-300 hover:bg-blue-50/60 dark:border-white/10 dark:hover:border-blue-800 dark:hover:bg-blue-950/30">
                        <span class="flex items-center gap-3">
                            <span class="flex size-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                                <flux:icon name="users" class="size-4" />
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-slate-950 dark:text-white">Kelola User &amp; Role</span>
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-zinc-400">Akun dan penugasan hak akses</span>
                            </span>
                        </span>
                        <flux:icon name="arrow-right" class="size-4 shrink-0 text-slate-400 transition-transform group-hover:translate-x-0.5 group-hover:text-blue-700" />
                    </a>
                </div>
            </x-ui.panel>
        </section>
    @elseif ($user->isPengurusUkm() && $assignedUkm)
        <section class="relative overflow-hidden rounded-2xl bg-navy-950 text-white">
            <div class="brand-grid absolute inset-0" aria-hidden="true"></div>
            <div class="brand-glow absolute inset-0" aria-hidden="true"></div>

            <div class="relative flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:p-8">
                @if ($assignedUkm->logoUrl())
                    <img src="{{ $assignedUkm->logoUrl() }}" alt="Logo {{ $assignedUkm->nama }}" class="size-16 shrink-0 rounded-xl border border-white/20 object-cover">
                @else
                    <div class="flex size-16 shrink-0 items-center justify-center rounded-xl border border-white/15 bg-white/10 text-xl font-semibold">{{ $assignedUkm->inisial() }}</div>
                @endif

                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-blue-300">Ruang Pengurus · {{ $user->roleLabel() }}</p>
                    <h1 class="mt-1 text-2xl font-semibold tracking-tight sm:text-3xl">{{ $assignedUkm->nama }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex h-6 items-center gap-1.5 rounded-[3px] border border-white/25 px-2 text-[11px] font-medium whitespace-nowrap text-blue-50">
                            <span @class(['size-1.5 rounded-[1px]', $assignedUkm->status === 'aktif' ? 'bg-moss-400' : 'bg-white/40']) aria-hidden="true"></span>
                            Status {{ ucfirst($assignedUkm->status) }}
                        </span>
                        <span class="inline-flex h-6 items-center gap-1.5 rounded-[3px] border border-white/25 px-2 text-[11px] font-medium whitespace-nowrap text-blue-50">
                            <span @class(['size-1.5 rounded-[1px]', $assignedUkm->isPendaftaranDibuka() ? 'bg-moss-400' : 'bg-rust-400']) aria-hidden="true"></span>
                            Pendaftaran {{ $assignedUkm->isPendaftaranDibuka() ? 'dibuka' : 'ditutup' }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if ($user->isKetuaUkm())
                        <flux:button variant="primary" :href="route('ukm.profile')" icon="pencil-square" wire:navigate>Kelola profil</flux:button>
                    @endif
                    @if ($user->canManageTeam())
                        <a href="{{ route('ukm.team') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg border border-white/20 px-3.5 py-2 text-sm font-medium text-white transition-colors hover:bg-white/10">
                            <flux:icon name="user-group" class="size-4" />
                            Kelola divisi
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <x-ui.panel title="Jabatan" icon="identification">
                <p class="text-lg font-semibold text-slate-950 dark:text-white">{{ $user->roleLabel() }}</p>
                @if ($user->divisiUkm)
                    <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $user->divisiUkm->nama }}</p>
                @endif
            </x-ui.panel>

            <x-ui.panel title="Status akun" icon="shield-check">
                <p class="text-lg font-semibold text-moss-700 dark:text-moss-400">Aktif</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Akses terbatas pada {{ $assignedUkm->nama }}</p>
            </x-ui.panel>

            <x-ui.panel title="Pintasan ruang UKM" icon="arrow-top-right-on-square">
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                    <a href="{{ route('kegiatan.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">
                        Kelola kegiatan
                        <flux:icon name="arrow-right" class="size-4" />
                    </a>

                    <a href="{{ route('ukm.catalog') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">
                        Lihat katalog
                        <flux:icon name="arrow-right" class="size-4" />
                    </a>
                </div>
            </x-ui.panel>
        </section>
    @else
        <section class="relative overflow-hidden rounded-2xl bg-navy-950 text-white">
            <div class="brand-grid absolute inset-0" aria-hidden="true"></div>
            <div class="brand-glow absolute inset-0" aria-hidden="true"></div>

            <div class="relative grid gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-end">
                <div class="max-w-2xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-blue-300">DIREKTORI UKM MAHASISWA</p>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Selamat datang, {{ $user->name }}</h1>
                    <p class="mt-3 text-sm leading-6 text-blue-100/80">
                        Jelajahi {{ number_format($activeUkmCount) }} UKM aktif, lihat deskripsi dan status pendaftaran anggota, lalu pilih komunitas yang sesuai minatmu.
                    </p>
                    <div class="mt-6">
                        <flux:button variant="primary" :href="route('ukm.catalog')" icon="building-office-2" wire:navigate>
                            Jelajahi Katalog UKM
                        </flux:button>
                    </div>
                </div>

                <div class="rounded-xl border border-white/15 bg-white/5 px-5 py-4 text-sm">
                    <p class="text-xs uppercase tracking-[0.16em] text-blue-200/70">UKM aktif</p>
                    <p class="mt-1 text-3xl font-semibold tabular-nums">{{ number_format($activeUkmCount) }}</p>
                    <p class="mt-1 text-xs text-blue-100/70">Terdaftar di kampus</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2">
            <x-ui.panel title="Katalog UKM" description="Temukan komunitas dan status pendaftarannya." icon="building-library">
                <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    Setiap UKM aktif menampilkan deskripsi singkat dan status pendaftaran anggota. Gunakan pencarian untuk menyaring nama atau deskripsi.
                </p>
                <a href="{{ route('ukm.catalog') }}" wire:navigate class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">
                    Buka katalog
                    <flux:icon name="arrow-right" class="size-4" />
                </a>
            </x-ui.panel>

            <x-ui.panel title="Tiket acara" description="Akses tiket acara yang sudah kamu miliki." icon="ticket">
                <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    Tiket acara UKM tersimpan di halaman Tiket Saya bersama kode tiket dan status validasinya.
                </p>
                <a href="{{ route('tiket.index') }}" wire:navigate class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">
                    Lihat tiket saya
                    <flux:icon name="arrow-right" class="size-4" />
                </a>
            </x-ui.panel>
        </section>
    @endif
</x-ui.page>
