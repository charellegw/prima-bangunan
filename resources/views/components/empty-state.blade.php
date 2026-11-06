@props([
    'icon' => 'box',
    'title' => 'Belum ada data',
    'description' => 'Data yang Anda cari tidak ditemukan atau belum pernah ditambahkan.',
    'actionLabel' => null,
    'actionUrl' => null,
    'actionIcon' => 'plus',
])

<div class="text-center py-5 px-3">
    <div class="pb-empty-icon mx-auto mb-3">
        <x-icon :name="$icon" size="32" stroke="1.5" class="text-primary" />
    </div>
    <h6 class="fw-semibold text-main mb-1" style="font-size: 16px;">{{ $title }}</h6>
    <p class="text-secondary small mx-auto mb-4" style="max-width: 420px; font-size: 13px;">
        {{ $description }}
    </p>
    @if($actionLabel && $actionUrl)
        <x-button :href="$actionUrl" variant="primary" size="md" :icon="$actionIcon">
            {{ $actionLabel }}
        </x-button>
    @endif
</div>
