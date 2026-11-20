@props([
    'sidebar' => false,
    'onPaper' => false,
])

<a {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-blue-500']) }}>
    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white ring-1 ring-navy-950/10 dark:ring-white/20">
        <x-app-logo-icon class="h-8 w-auto" />
    </span>

    <span class="app-logo-text leading-none">
        <span @class([
            'block text-sm font-semibold tracking-tight',
            'text-white' => $sidebar,
            'text-slate-950 dark:text-white' => ! $sidebar && ! $onPaper,
            'text-navy-950 dark:text-paper' => $onPaper,
        ])>SIP-UKM</span>
        <span @class([
            'mt-1.5 block text-[10px] font-medium uppercase tracking-[0.16em]',
            'text-blue-200/70' => $sidebar,
            'text-slate-500 dark:text-zinc-400' => ! $sidebar && ! $onPaper,
            'text-slate-600 dark:text-slate-400' => $onPaper,
        ])>Politeknik Negeri Banyuwangi</span>
    </span>
</a>
