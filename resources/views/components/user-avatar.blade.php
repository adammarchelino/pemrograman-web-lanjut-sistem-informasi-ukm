@props([
    'user',
    'src' => null,
    'size' => 'size-9',
    'font' => 'text-xs',
])

@php($foto = $src ?? $user->fotoProfilUrl())

@if ($foto)
    <img
        src="{{ $foto }}"
        alt="Foto profil {{ $user->name }}"
        class="{{ $size }} shrink-0 rounded-full border border-slate-200 bg-white object-cover dark:border-white/15 dark:bg-zinc-900"
        loading="lazy"
    >
@else
    <span class="flex {{ $size }} shrink-0 items-center justify-center rounded-full bg-blue-600 {{ $font }} font-semibold text-white" aria-hidden="true">
        {{ $user->initials() }}
    </span>
@endif
