@props([
    'title' => '',
    'value' => '',
    'sub' => null,
    'icon' => 'bar-chart',
    'badge' => null,
    'badgeVariant' => 'success',
    'iconBg' => 'bg-primary-subtle',
    'iconColor' => 'text-primary',
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'pb-stat-card ' . $class]) }}>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="pb-stat-label">{{ $title }}</span>
        <div class="pb-stat-icon-wrapper {{ $iconBg }} {{ $iconColor }}">
            <x-icon :name="$icon" size="20" stroke="2" />
        </div>
    </div>
    
    <div class="pb-stat-value tabular-nums">{{ $value }}</div>
    
    @if($sub || $badge)
        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-light-subtle" style="border-color: var(--pb-border-subtle) !important;">
            @if($sub)
                <span class="pb-stat-sub text-secondary">{{ $sub }}</span>
            @endif
            @if($badge)
                <x-badge :variant="$badgeVariant" size="sm">{{ $badge }}</x-badge>
            @endif
        </div>
    @endif
</div>
