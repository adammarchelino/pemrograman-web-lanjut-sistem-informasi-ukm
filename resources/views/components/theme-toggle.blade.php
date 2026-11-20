@props([
    'tone' => 'surface',
    'label' => null,
])

@php
    $toneClass = match ($tone) {
        'paper' => 'border border-navy-950/20 bg-paper text-navy-900 hover:border-navy-950 hover:bg-paper-deep dark:border-white/25 dark:bg-navy-950 dark:text-paper dark:hover:border-white/50 dark:hover:bg-white/10',
        'navy' => 'border border-white/20 bg-white/10 text-white hover:border-white/40 hover:bg-white/20',
        default => 'border border-slate-300 bg-white text-slate-700 hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:border-white/20 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-white/35 dark:hover:bg-white/10 dark:hover:text-white',
    };

    $sizeClass = match (true) {
        $label !== null => 'h-9 flex-1 px-2',
        $tone === 'paper' => 'size-11',
        default => 'size-9',
    };
@endphp

<button
    type="button"
    data-theme-toggle
    aria-pressed="false"
    aria-label="Ganti mode gelap"
    title="Ganti mode gelap"
    @class([
        'inline-flex shrink-0 items-center justify-center gap-1.5 rounded-lg text-xs font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400',
        $sizeClass,
        $toneClass,
    ])
>
    <flux:icon name="moon" class="size-4 shrink-0 dark:hidden" />
    <flux:icon name="sun" class="hidden size-4 shrink-0 dark:block" />

    @if ($label !== null)
        <span class="app-nav-label truncate">{{ $label }}</span>
    @endif
</button>
