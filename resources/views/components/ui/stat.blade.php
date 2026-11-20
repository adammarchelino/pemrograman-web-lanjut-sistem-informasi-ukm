@props([
    'label',
    'value',
    'note' => null,
    'icon' => null,
    'tone' => 'blue',
])

@php
    $accents = [
        'blue' => 'bg-blue-600',
        'navy' => 'bg-navy-900',
        'emerald' => 'bg-emerald-600',
        'amber' => 'bg-amber-500',
        'slate' => 'bg-slate-400',
    ];
@endphp

<article {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-xs dark:border-white/10 dark:bg-zinc-900']) }}>
    <span class="absolute inset-y-0 left-0 w-1 {{ $accents[$tone] ?? $accents['blue'] }}" aria-hidden="true"></span>

    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500 dark:text-zinc-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-slate-950 dark:text-white">{{ number_format($value) }}</p>
            @if ($note)
                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $note }}</p>
            @endif
        </div>

        @if ($icon)
            <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                <flux:icon :name="$icon" class="size-4" />
            </span>
        @endif
    </div>
</article>
