<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-svh bg-slate-100/80 antialiased dark:bg-zinc-950">
        @php($user = auth()->user())

        <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-navy-950 focus:shadow-lg">
            Lewati ke konten
        </a>

        <div class="min-h-svh lg:flex">
            <aside id="app-sidebar" class="app-sidebar fixed inset-y-0 left-0 z-40 hidden w-72 flex-col bg-navy-950 text-white transition-[width] duration-200 lg:flex">
                <div class="app-sidebar-header flex h-16 shrink-0 items-center border-b border-white/10 px-5">
                    <x-app-logo :sidebar="true" :href="route('dashboard')" wire:navigate />
                </div>

                <div class="flex-1 overflow-y-auto px-3 py-6">
                    @include('layouts.app.nav')
                </div>

                <div class="app-sidebar-footer shrink-0 border-t border-white/10 p-3">
                    <div class="app-user-card rounded-lg bg-white/5 px-3 py-2">
                        <div class="app-user-identity flex items-center gap-3">
                            <x-user-avatar :user="$user" size="size-9" font="text-xs" />
                            <div class="app-user-text min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-white">{{ $user->name }}</p>
                                <p class="truncate text-xs text-blue-200/70">{{ $user->roleLabel() }}</p>
                            </div>
                        </div>

                        <div class="app-user-actions mt-2 flex items-center gap-1.5">
                            <x-theme-toggle tone="navy" label="Tema" />

                            <button
                                type="button"
                                data-sidebar-collapse
                                aria-expanded="true"
                                aria-controls="app-sidebar"
                                aria-label="Ciutkan sidebar"
                                title="Ciutkan sidebar"
                                class="inline-flex h-9 shrink-0 flex-1 items-center justify-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2 text-xs font-medium text-white transition-colors hover:border-white/40 hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                            >
                                <flux:icon name="chevron-left" class="app-collapse-icon size-4 shrink-0" />
                                <flux:icon name="chevron-right" class="app-expand-icon size-4 shrink-0" />
                                <span class="app-nav-label truncate">Ciutkan</span>
                            </button>
                        </div>
                    </div>

                    <div class="app-user-links mt-1 flex items-center gap-1">
                        <a href="{{ route('profile.edit') }}" title="Pengaturan" class="flex flex-1 items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-blue-100/75 transition-colors hover:bg-white/10 hover:text-white" wire:navigate>
                            <flux:icon name="cog" class="size-3.5 shrink-0" />
                            <span class="app-nav-label">Pengaturan</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" data-test="logout-button" title="Keluar" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-blue-100/75 transition-colors hover:bg-white/10 hover:text-white">
                                <flux:icon name="arrow-right-start-on-rectangle" class="size-3.5 shrink-0" />
                                <span class="app-nav-label">Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="app-content flex min-w-0 flex-1 flex-col transition-[padding] duration-200 lg:pl-72">
                <header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white/95 px-4 backdrop-blur lg:hidden dark:border-white/10 dark:bg-zinc-950/95">
                    <x-app-logo :href="route('dashboard')" wire:navigate />

                    <div class="flex items-center gap-1">
                        <x-theme-toggle />

                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="subtle" icon="bars-2" aria-label="Buka menu navigasi" />

                        <flux:menu>
                            <div class="px-2 py-2">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                <p class="truncate text-xs text-slate-500 dark:text-zinc-400">{{ $user->roleLabel() }}</p>
                            </div>

                            <flux:menu.separator />

                            <flux:menu.item icon="home" :href="route('dashboard')" wire:navigate>Dashboard</flux:menu.item>

                            @if ($user->isMahasiswa())
                                <flux:menu.item icon="building-library" :href="route('ukm.catalog')" wire:navigate>Katalog UKM</flux:menu.item>
                            @endif

                            @if ($user->role === \App\Models\User::ROLE_MAHASISWA)
                                <flux:menu.item icon="user-plus" :href="route('pendaftaran-anggota.create')" wire:navigate>Pendaftaran UKM</flux:menu.item>
                            @endif

                            {{-- Tiket hanya untuk mahasiswa murni; isMahasiswa() juga true untuk pengurus UKM. --}}
                            @if ($user->role === \App\Models\User::ROLE_MAHASISWA)
                                <flux:menu.item icon="ticket" :href="route('tiket.index')" wire:navigate>Tiket Saya</flux:menu.item>
                            @endif

                            @if ($user->isKemahasiswaan())
                                <flux:menu.item icon="building-library" :href="route('ukm.index')" wire:navigate>Data UKM</flux:menu.item>
                                <flux:menu.item icon="users" :href="route('users.index')" wire:navigate>User &amp; Role</flux:menu.item>
                            @elseif ($user->isPengurusUkm())
                                <flux:menu.item icon="clipboard-document-list" :href="route('pengurus.pengajuan-anggota')" wire:navigate>Pengajuan Anggota</flux:menu.item>
                                @if ($user->isKetuaUkm())
                                    <flux:menu.item icon="building-library" :href="route('ukm.profile')" wire:navigate>Profil UKM</flux:menu.item>
                                @endif
                                @if ($user->canManageTeam())
                                    <flux:menu.item icon="user-group" :href="route('ukm.team')" wire:navigate>Divisi &amp; Pengurus</flux:menu.item>
                                @endif
                                <flux:menu.item icon="calendar-days" :href="route('kegiatan.index')" wire:navigate>Kegiatan</flux:menu.item>
                                <flux:menu.item icon="qr-code" :href="route('pengurus.presensi.index')" wire:navigate>Presensi</flux:menu.item>
                            @endif

                            <flux:menu.separator />

                            <flux:menu.item icon="cog" :href="route('profile.edit')" wire:navigate>Pengaturan</flux:menu.item>

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer" data-test="logout-button">
                                    Keluar
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                        </flux:dropdown>
                    </div>
                </header>

                <main id="konten" class="flex-1">
                    {{ $slot }}
                </main>

                <footer class="border-t border-slate-200 px-4 py-5 sm:px-6 lg:px-10 dark:border-white/10">
                    <div class="flex flex-col gap-1 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between dark:text-zinc-400">
                        <p>SIP-UKM · Sistem Informasi Pengelolaan UKM</p>
                        <p>Politeknik Negeri Banyuwangi</p>
                    </div>
                </footer>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
