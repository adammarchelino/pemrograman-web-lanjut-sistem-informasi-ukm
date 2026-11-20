@php
    $navUser = auth()->user();

    // Helper isMahasiswa() juga mengembalikan true untuk pengurus UKM,
    // jadi menu tiket harus dicek langsung dari kolom role.
    $navMahasiswa = $navUser?->role === \App\Models\User::ROLE_MAHASISWA;

    $linkClass = 'app-nav-link group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors';
    $activeClass = 'bg-blue-600 text-white shadow-xs';
    $idleClass = 'text-blue-100/80 hover:bg-white/10 hover:text-white';
    $groupClass = 'app-nav-group px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-blue-200/70';
@endphp

<nav class="app-nav" aria-label="Navigasi utama">
    <div>
        <p class="{{ $groupClass }}">Platform</p>
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" title="Dashboard" @class([$linkClass, request()->routeIs('dashboard') ? $activeClass : $idleClass]) wire:navigate>
                <flux:icon name="home" class="size-4 shrink-0" />
                <span class="app-nav-label">Dashboard</span>
            </a>
        </div>
    </div>

    @if ($navMahasiswa)
        <div>
            <p class="{{ $groupClass }}">Acara Saya</p>
            <div class="space-y-1">
                <a href="{{ route('ukm.catalog') }}" title="Katalog UKM" @class([$linkClass, request()->routeIs('ukm.catalog') ? $activeClass : $idleClass]) wire:navigate>
                    <flux:icon name="building-library" class="size-4 shrink-0" />
                    <span class="app-nav-label">Katalog UKM</span>
                </a>
                <a href="{{ route('tiket.index') }}" title="Tiket Saya" @class([$linkClass, request()->routeIs('tiket.*') ? $activeClass : $idleClass]) wire:navigate>
                    <flux:icon name="ticket" class="size-4 shrink-0" />
                    <span class="app-nav-label">Tiket Saya</span>
                </a>
            </div>
        </div>
    @endif

    @if ($navUser->isKemahasiswaan())
        <div>
            <p class="{{ $groupClass }}">Administrasi</p>
            <div class="space-y-1">
                <a href="{{ route('ukm.index') }}" title="Data UKM" @class([$linkClass, request()->routeIs('ukm.index') ? $activeClass : $idleClass]) wire:navigate>
                    <flux:icon name="building-library" class="size-4 shrink-0" />
                    <span class="app-nav-label">Data UKM</span>
                </a>
                <a href="{{ route('users.index') }}" title="User &amp; Role" @class([$linkClass, request()->routeIs('users.*') ? $activeClass : $idleClass]) wire:navigate>
                    <flux:icon name="users" class="size-4 shrink-0" />
                    <span class="app-nav-label">User &amp; Role</span>
                </a>
            </div>
        </div>
    @elseif ($navUser->isPengurusUkm())
        <div>
            <p class="{{ $groupClass }}">Ruang Pengurus</p>
            <div class="space-y-1">
                @if ($navUser->isKetuaUkm())
                    <a href="{{ route('ukm.profile') }}" title="Profil UKM" @class([$linkClass, request()->routeIs('ukm.profile') ? $activeClass : $idleClass]) wire:navigate>
                        <flux:icon name="building-library" class="size-4 shrink-0" />
                        <span class="app-nav-label">Profil UKM</span>
                    </a>
                @endif

                @if ($navUser->canManageTeam())
                    <a href="{{ route('ukm.team') }}" title="Divisi &amp; Pengurus" @class([$linkClass, request()->routeIs('ukm.team') ? $activeClass : $idleClass]) wire:navigate>
                        <flux:icon name="user-group" class="size-4 shrink-0" />
                        <span class="app-nav-label">Divisi &amp; Pengurus</span>
                    </a>
                @endif

                <a href="{{ route('kegiatan.index') }}" title="Kegiatan" @class([$linkClass, request()->routeIs('kegiatan.*') ? $activeClass : $idleClass]) wire:navigate>
                    <flux:icon name="calendar-days" class="size-4 shrink-0" />
                    <span class="app-nav-label">Kegiatan</span>
                </a>

                <a href="{{ route('pengurus.presensi.index') }}" title="Presensi" @class([$linkClass, request()->routeIs('pengurus.presensi.*') || request()->routeIs('pengurus.kegiatan.presensi') ? $activeClass : $idleClass]) wire:navigate>
                    <flux:icon name="qr-code" class="size-4 shrink-0" />
                    <span class="app-nav-label">Presensi</span>
                </a>

                <a href="{{ route('ukm.catalog') }}" title="Katalog UKM" @class([$linkClass, request()->routeIs('ukm.catalog') ? $activeClass : $idleClass]) wire:navigate>
                    <flux:icon name="magnifying-glass" class="size-4 shrink-0" />
                    <span class="app-nav-label">Katalog UKM</span>
                </a>
            </div>
        </div>
    @endif
</nav>
