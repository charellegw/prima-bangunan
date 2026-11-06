@props([
    'title' => null,
    'subtitle' => null,
    'action' => null,
    'padding' => 'p-4 sm:p-5',
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'pb-card ' . $class]) }}>
    @if($title || $subtitle || $action)
        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
            <div>
                @if($title)
                    <h5 class="fw-semibold text-main mb-0 fs-6">{{ $title }}</h5>
                @endif
                @if($subtitle)
                    <p class="text-secondary small mb-0 mt-0.5" style="font-size: 13px;">{{ $subtitle }}</p>
                @endif
            </div>
            @if($action)
                <div>{!! $action !!}</div>
            @endif
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
