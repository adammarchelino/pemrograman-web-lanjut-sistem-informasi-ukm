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

        <div class="flex min-h-svh flex-col">
            <header class="sticky top-0 z-40 bg-navy-950 text-white">
                <div class="mx-auto flex h-16 w-full max-w-7xl items-center gap-6 px-4 sm:px-6 lg:px-10">
                    <x-app-logo :sidebar="true" :href="route('dashboard')" wire:navigate />

                    <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
                        <a href="{{ route('dashboard') }}" @class(['rounded-lg px-3 py-2 text-sm font-medium transition-colors', request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-blue-100/75 hover:bg-white/10 hover:text-white']) wire:navigate>
                            Dashboard
                        </a>
                        <a href="{{ route('ukm.catalog') }}" @class(['rounded-lg px-3 py-2 text-sm font-medium transition-colors', request()->routeIs('ukm.catalog') ? 'bg-white/10 text-white' : 'text-blue-100/75 hover:bg-white/10 hover:text-white']) wire:navigate>
                            Katalog UKM
                        </a>
                        <a href="{{ route('pendaftaran-anggota.create') }}" @class(['rounded-lg px-3 py-2 text-sm font-medium transition-colors', request()->routeIs('pendaftaran-anggota.*') ? 'bg-white/10 text-white' : 'text-blue-100/75 hover:bg-white/10 hover:text-white']) wire:navigate>
                            Pendaftaran UKM
                        </a>
                        <a href="{{ route('tiket.index') }}" @class(['rounded-lg px-3 py-2 text-sm font-medium transition-colors', request()->routeIs('tiket.*') ? 'bg-white/10 text-white' : 'text-blue-100/75 hover:bg-white/10 hover:text-white']) wire:navigate>
                            Tiket Saya
                        </a>
                    </nav>

                    <div class="ms-auto flex items-center gap-2">
                        <x-theme-toggle tone="navy" />

                        <flux:dropdown position="bottom" align="end">
                            <button type="button" class="flex items-center gap-2 rounded-full p-0.5 transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-400" aria-label="Menu akun">
                                <x-user-avatar :user="$user" size="size-8" font="text-xs" />
                            </button>

                            <flux:menu>
                                <div class="px-2 py-2">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-zinc-400">{{ $user->email }}</p>
                                </div>

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
                </div>

                <div class="border-t border-white/10 lg:hidden">
                    <nav class="flex gap-1 overflow-x-auto px-4 py-2" aria-label="Navigasi utama seluler">
                        <a href="{{ route('dashboard') }}" @class(['shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium', request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-blue-100/75']) wire:navigate>Dashboard</a>
                        <a href="{{ route('ukm.catalog') }}" @class(['shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium', request()->routeIs('ukm.catalog') ? 'bg-white/10 text-white' : 'text-blue-100/75']) wire:navigate>Katalog UKM</a>
                        <a href="{{ route('pendaftaran-anggota.create') }}" @class(['shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium', request()->routeIs('pendaftaran-anggota.*') ? 'bg-white/10 text-white' : 'text-blue-100/75']) wire:navigate>Pendaftaran UKM</a>
                        <a href="{{ route('tiket.index') }}" @class(['shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium', request()->routeIs('tiket.*') ? 'bg-white/10 text-white' : 'text-blue-100/75']) wire:navigate>Tiket Saya</a>
                    </nav>
                </div>
            </header>

            <main id="konten" class="flex-1">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-200 px-4 py-5 sm:px-6 lg:px-10 dark:border-white/10">
                <div class="mx-auto flex w-full max-w-7xl flex-col gap-1 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between dark:text-zinc-400">
                    <p>SIP-UKM · Sistem Informasi Pengelolaan UKM</p>
                    <p>Politeknik Negeri Banyuwangi</p>
                </div>
            </footer>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
