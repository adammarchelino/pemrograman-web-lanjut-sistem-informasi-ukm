@props([
    'title',
    'description',
])

<div class="space-y-1.5">
    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-700 dark:text-blue-300">SIP-UKM</p>

    <h1 class="text-xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $title }}</h1>

    <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">{{ $description }}</p>
</div>
