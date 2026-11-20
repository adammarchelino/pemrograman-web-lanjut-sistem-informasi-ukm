@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<header {{ $attributes->merge(['class' => 'flex flex-col gap-5 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between dark:border-white/10']) }}>
    <div class="max-w-3xl">
        @if ($eyebrow)
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-700 dark:text-blue-300">{{ $eyebrow }}</p>
        @endif

        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950 sm:text-[1.7rem] dark:text-white">{{ $title }}</h1>

        @if ($description)
            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-300">{{ $description }}</p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endif
</header>
