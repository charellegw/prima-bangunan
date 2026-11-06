@props([
    'variant' => 'info', // success, warning, danger, info
    'title' => null,
    'icon' => null,
    'dismissible' => true,
    'class' => '',
])

@php
    $icons = [
        'success' => 'check-circle',
        'warning' => 'alert-triangle',
        'danger' => 'alert-triangle',
        'info' => 'info',
    ];
    $iconName = $icon ?? ($icons[$variant] ?? 'info');
@endphp

<div {{ $attributes->merge(['class' => "pb-alert pb-alert-{$variant} d-flex align-items-start {$class}"]) }} role="alert">
    <div class="pb-alert-icon me-3 flex-shrink-0 mt-0.5">
        <x-icon :name="$iconName" size="20" stroke="2" />
    </div>
    <div class="flex-grow-1">
        @if($title)
            <div class="fw-semibold mb-0.5">{{ $title }}</div>
        @endif
        <div class="small leading-relaxed">{{ $slot }}</div>
    </div>
    @if($dismissible)
        <button type="button" class="btn-close ms-2 flex-shrink-0" data-bs-dismiss="alert" aria-label="Close" style="font-size: 11px;"></button>
    @endif
</div>
