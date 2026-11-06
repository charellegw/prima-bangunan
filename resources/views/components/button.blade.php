@props([
    'variant' => 'primary', // primary, secondary, outline, danger, ghost
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'href' => null,
    'type' => 'button',
    'class' => '',
])

@php
    $variants = [
        'primary' => 'pb-btn-primary',
        'secondary' => 'pb-btn-secondary',
        'outline' => 'pb-btn-outline',
        'danger' => 'pb-btn-danger',
        'ghost' => 'pb-btn-ghost',
    ];
    $sizes = [
        'sm' => 'pb-btn-sm',
        'md' => 'pb-btn-md',
        'lg' => 'pb-btn-lg',
    ];
    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $allClasses = "pb-btn {$variantClass} {$sizeClass} {$class}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $allClasses]) }}>
        @if($icon)
            <x-icon :name="$icon" size="16" class="me-1.5" />
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $allClasses]) }}>
        @if($icon)
            <x-icon :name="$icon" size="16" class="me-1.5" />
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
