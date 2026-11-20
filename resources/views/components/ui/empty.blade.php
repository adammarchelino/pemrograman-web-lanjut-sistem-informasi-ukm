@props([
    'icon' => 'inbox',
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-2 px-6 py-12 text-center']) }}>
    <span class="flex size-11 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-zinc-300">
        <flux:icon :name="$icon" class="size-5" />
    </span>

    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $title }}</p>

    @if ($description)
        <p class="max-w-sm text-xs leading-5 text-slate-500 dark:text-zinc-400">{{ $description }}</p>
    @endif

    @if (isset($slot) && ! $slot->isEmpty())
        <div class="mt-2">{{ $slot }}</div>
    @endif
</div>
