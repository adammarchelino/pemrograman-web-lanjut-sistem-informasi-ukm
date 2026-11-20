@props([
    'title' => null,
    'description' => null,
    'icon' => null,
    'flush' => false,
])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900']) }}>
    @if ($title || isset($actions))
        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
            <div class="flex items-start gap-3">
                @if ($icon)
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                        <flux:icon :name="$icon" class="size-4" />
                    </span>
                @endif

                <div>
                    @if ($title)
                        <h2 class="text-sm font-semibold tracking-tight text-slate-950 dark:text-white">{{ $title }}</h2>
                    @endif

                    @if ($description)
                        <p class="mt-0.5 text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ $description }}</p>
                    @endif
                </div>
            </div>

            @if (isset($actions))
                <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
            @endif
        </div>
    @endif

    <div @class(['p-5' => ! $flush])>{{ $slot }}</div>
</section>
