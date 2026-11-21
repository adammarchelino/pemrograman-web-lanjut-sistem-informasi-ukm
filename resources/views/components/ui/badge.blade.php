@props([
    'tone' => 'neutral',
    'marker' => true,
])

@php
    $tones = [
        'neutral' => [
            'box' => 'border-slate-300 bg-white text-slate-700 dark:border-white/15 dark:bg-white/5 dark:text-zinc-200',
            'dot' => 'bg-slate-400 dark:bg-zinc-500',
        ],
        'positive' => [
            'box' => 'border-moss-700/25 bg-moss-50 text-moss-700 dark:border-moss-700/50 dark:bg-moss-700/20 dark:text-moss-50',
            'dot' => 'bg-moss-700 dark:bg-moss-400',
        ],
        'negative' => [
            'box' => 'border-red-600/25 bg-red-50 text-red-700 dark:border-red-500/40 dark:bg-red-950/50 dark:text-red-200',
            'dot' => 'bg-red-600 dark:bg-red-400',
        ],
        'warning' => [
            'box' => 'border-amber-600/30 bg-amber-50 text-amber-800 dark:border-amber-500/40 dark:bg-amber-950/50 dark:text-amber-200',
            'dot' => 'bg-amber-500 dark:bg-amber-300',
        ],
        'accent' => [
            'box' => 'border-sunrise-700/20 bg-sunrise-100 text-sunrise-700 dark:border-sunrise-700/50 dark:bg-sunrise-700/25 dark:text-sunrise-100',
            'dot' => 'bg-sunrise-700 dark:bg-sunrise-300',
        ],
    ];

    $tone = $tones[$tone] ?? $tones['neutral'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex h-6 shrink-0 items-center gap-1.5 rounded-[3px] border px-2 text-[11px] font-medium whitespace-nowrap '.$tone['box']]) }}>
    @if ($marker)
        <span @class(['size-1.5 shrink-0 rounded-[1px]', $tone['dot']]) aria-hidden="true"></span>
    @endif

    {{ $slot }}
</span>
