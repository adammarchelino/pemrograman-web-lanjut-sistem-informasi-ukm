<div class="space-y-6">
    @if ($stats)
        @php
            $summaryCards = [
                [
                    'label' => 'Total UKM',
                    'value' => $stats['ukm_total'],
                    'note' => 'Organisasi terdaftar',
                    'icon' => 'building-office-2',
                    'icon_class' => 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
                ],
                [
                    'label' => 'UKM Aktif',
                    'value' => $stats['ukm_aktif'],
                    'note' => 'Berstatus aktif',
                    'icon' => 'check-circle',
                    'icon_class' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
                ],
                [
                    'label' => 'Pendaftaran Dibuka',
                    'value' => $stats['ukm_oprec'],
                    'note' => 'UKM menerima anggota',
                    'icon' => 'user-plus',
                    'icon_class' => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
                ],
                [
                    'label' => 'Total Pengguna',
                    'value' => $stats['users_total'],
                    'note' => 'Semua role',
                    'icon' => 'users',
                    'icon_class' => 'bg-violet-50 text-violet-700 dark:bg-violet-950 dark:text-violet-300',
                ],
            ];
        @endphp

        <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-blue-950 via-blue-900 to-indigo-900 p-6 text-white shadow-sm sm:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold tracking-wide text-blue-200">PANEL KEMAHASISWAAN</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Selamat datang, {{ $user->name }}</h1>
                    <p class="mt-2 text-sm leading-6 text-blue-100 sm:text-base">
                        Pantau ringkasan UKM dan akun pengguna, lalu lanjutkan pengelolaan dari pintasan berikut.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <flux:button variant="primary" :href="route('ukm.index')" icon="building-office-2" wire:navigate>
                        Data UKM
                    </flux:button>
                    <flux:button variant="filled" :href="route('users.index')" icon="users" wire:navigate>
                        User &amp; Role
                    </flux:button>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan administratif">
            @foreach ($summaryCards as $card)
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-600 dark:text-zinc-300">{{ $card['label'] }}</p>
                            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ number_format($card['value']) }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $card['note'] }}</p>
                        </div>
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg {{ $card['icon_class'] }}">
                            <flux:icon :name="$card['icon']" class="size-5" />
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <article class="rounded-xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-slate-900 dark:text-white">Komposisi pengguna</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Akun berdasarkan role yang terdaftar.</p>
                    </div>
                    <flux:icon name="user-group" class="size-5 text-slate-400" />
                </div>
                <dl class="mt-5 divide-y divide-slate-100 dark:divide-zinc-700">
                    <div class="flex items-center justify-between py-3 text-sm">
                        <dt class="text-slate-600 dark:text-zinc-300">Mahasiswa</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white">{{ number_format($stats['users_mahasiswa']) }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3 text-sm">
                        <dt class="text-slate-600 dark:text-zinc-300">Pengurus UKM</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white">{{ number_format($stats['users_pengurus']) }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3 text-sm">
                        <dt class="text-slate-600 dark:text-zinc-300">Kemahasiswaan</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white">{{ number_format($stats['users_kemahasiswaan']) }}</dd>
                    </div>
                </dl>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-slate-900 dark:text-white">Akses cepat</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Buka halaman pengelolaan yang sering digunakan.</p>
                    </div>
                    <flux:icon name="arrow-top-right-on-square" class="size-5 text-slate-400" />
                </div>
                <div class="mt-5 space-y-3">
                    <a href="{{ route('ukm.index') }}" wire:navigate class="group flex items-center justify-between rounded-lg border border-slate-200 p-4 transition hover:border-blue-300 hover:bg-blue-50/60 dark:border-zinc-700 dark:hover:border-blue-800 dark:hover:bg-blue-950/30">
                        <span class="flex items-center gap-3">
                            <span class="flex size-9 items-center justify-center rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                <flux:icon name="building-office-2" class="size-5" />
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-slate-900 dark:text-white">Kelola Data UKM</span>
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-zinc-400">Profil, status, dan pendaftaran anggota</span>
                            </span>
                        </span>
                        <flux:icon name="arrow-right" class="size-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-blue-600" />
                    </a>

                    <a href="{{ route('users.index') }}" wire:navigate class="group flex items-center justify-between rounded-lg border border-slate-200 p-4 transition hover:border-violet-300 hover:bg-violet-50/60 dark:border-zinc-700 dark:hover:border-violet-800 dark:hover:bg-violet-950/30">
                        <span class="flex items-center gap-3">
                            <span class="flex size-9 items-center justify-center rounded-lg bg-violet-50 text-violet-700 dark:bg-violet-950 dark:text-violet-300">
                                <flux:icon name="users" class="size-5" />
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-slate-900 dark:text-white">Kelola User &amp; Role</span>
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-zinc-400">Akun dan penugasan hak akses</span>
                            </span>
                        </span>
                        <flux:icon name="arrow-right" class="size-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-violet-600" />
                    </a>
                </div>
            </article>
        </section>
    @elseif ($user->isPengurusUkm() && $assignedUkm)
        <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-blue-950 via-blue-900 to-indigo-900 p-6 text-white shadow-sm sm:p-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                @if ($assignedUkm->logoUrl())
                    <img src="{{ $assignedUkm->logoUrl() }}" alt="Logo {{ $assignedUkm->nama }}" class="size-20 rounded-2xl border border-white/20 object-cover">
                @else
                    <div class="flex size-20 items-center justify-center rounded-2xl bg-white/10 text-2xl font-bold">{{ $assignedUkm->inisial() }}</div>
                @endif
                <div class="flex-1">
                    <p class="text-sm font-semibold tracking-wide text-blue-200">RUANG PENGURUS · {{ $user->roleLabel() }}</p>
                    <h1 class="mt-1 text-2xl font-bold sm:text-3xl">{{ $assignedUkm->nama }}</h1>
                    <p class="mt-2 text-sm text-blue-100">Status UKM: {{ ucfirst($assignedUkm->status) }} · Pendaftaran anggota {{ $assignedUkm->isPendaftaranDibuka() ? 'dibuka' : 'ditutup' }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($user->isKetuaUkm())
                        <flux:button variant="primary" :href="route('ukm.profile')" icon="pencil-square" wire:navigate>Kelola profil</flux:button>
                    @endif
                    @if ($user->canManageTeam())
                        <flux:button variant="filled" :href="route('ukm.team')" icon="users" wire:navigate>Kelola divisi</flux:button>
                    @endif
                </div>
            </div>
        </section>
        <section class="grid gap-4 md:grid-cols-3">
            <article class="rounded-xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-sm text-slate-500 dark:text-zinc-400">Jabatan</p>
                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $user->roleLabel() }}</p>
                @if ($user->divisiUkm)<p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $user->divisiUkm->nama }}</p>@endif
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-sm text-slate-500 dark:text-zinc-400">Status akun</p>
                <p class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-300">Aktif</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Akses terbatas pada {{ $assignedUkm->nama }}</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-sm text-slate-500 dark:text-zinc-400">Pintasan ruang UKM</p>
                <a href="{{ route('ukm.catalog') }}" wire:navigate class="mt-2 inline-flex items-center gap-2 font-semibold text-blue-700 hover:underline dark:text-blue-300">Lihat katalog <flux:icon name="arrow-right" class="size-4" /></a>
            </article>
        </section>
    @else
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs dark:border-zinc-700 dark:bg-zinc-800 sm:p-8">
            <p class="text-sm font-semibold tracking-wide text-blue-700 dark:text-blue-300">DIREKTORI KEGIATAN MAHASISWA</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                Selamat datang, {{ $user->name }}
            </h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-zinc-300">
                Jelajahi {{ number_format($activeUkmCount) }} UKM aktif, lihat deskripsi dan status pendaftaran anggota, lalu pilih komunitas yang sesuai minatmu.
            </p>
            <div class="mt-6">
                <flux:button variant="primary" :href="route('ukm.catalog')" icon="building-office-2" wire:navigate>
                    Jelajahi Katalog UKM
                </flux:button>
            </div>
        </section>
    @endif
</div>
