@props([
    'variant' => 'neutral', // success, warning, danger, info, neutral
    'size' => 'md', // sm, md
    'class' => '',
])

@php
    $base = 'pb-badge';
    $variants = [
        'success' => 'pb-badge-success',
        'warning' => 'pb-badge-warning',
        'danger' => 'pb-badge-danger',
        'info' => 'pb-badge-info',
        'neutral' => 'pb-badge-neutral',
    ];
    $variantClass = $variants[$variant] ?? $variants['neutral'];
    $sizeClass = $size === 'sm' ? 'pb-badge-sm' : '';
@endphp

<span {{ $attributes->merge(['class' => "{$base} {$variantClass} {$sizeClass} {$class}"]) }}>
    {{ $slot }}
</span>
