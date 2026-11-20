@props(['width' => '7xl'])

@php
    $maxWidth = [
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
    ][$width] ?? 'max-w-7xl';
@endphp

<div {{ $attributes->merge(['class' => "mx-auto w-full {$maxWidth} space-y-7 px-4 py-7 sm:px-6 sm:py-9 lg:px-10"]) }}>
    {{ $slot }}
</div>
