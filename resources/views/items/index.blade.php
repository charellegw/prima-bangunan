@extends('layouts.app')

@section('title', 'Katalog Barang & Stok — Prima Bangunan')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3">
    <div>
        <h1 class="page-title">Katalog Barang & Stok Material</h1>
        <p class="page-subtitle">Kelola stok material, harga bertingkat (orang vs retail), serta data komparasi supplier.</p>
    </div>
    <div>
        <x-button type="button" variant="primary" icon="plus" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
            Tambah Barang Baru
        </x-button>
    </div>
</div>

<!-- Search & Filter Bar (Single clean 44px bar) -->
<div class="pb-card p-3 mb-4">
    <form method="GET" action="{{ route('item.index') }}" id="filterForm">
        <div class="row g-2 align-items-center">
            <!-- Search Input with Debounce -->
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                        <x-icon name="search" size="16" />
                    </span>
                    <input type="text"
                           name="q"
                           id="searchInput"
                           value="{{ request('q') }}"
                           placeholder="Cari nama, merek (misal: 'pom' untuk pompa air)..."
                           class="form-control border-start-0 ps-2"
                           autocomplete="off">
                </div>
            </div>

            <!-- Filter Kategori -->
            <div class="col-6 col-md-3">
                <select name="category_id" id="categoryFilter" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Stok -->
            <div class="col-6 col-md-3">
                <select name="stock_status" id="stockFilter" class="form-select">
                    <option value="">Semua Status Stok</option>
                    <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Tersedia (> 10)</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Menipis (1 - 10)</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Habis (0)</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div class="col-12 col-md-1 d-flex">
                @if(request()->anyFilled(['q', 'category_id', 'stock_status']))
                    <a href="{{ route('item.index') }}" class="pb-btn pb-btn-secondary w-100" title="Reset Filter">
                        <x-icon name="x" size="16" />
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Product Cards Grid -->
<div class="row g-3" id="itemsGrid">
    @forelse($items as $item)
    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
        <div class="pb-card pb-card-interactive h-100 d-flex flex-column p-3">
            
            <!-- Uniform Ratio Image Container with Fallback -->
            <div class="rounded-3 overflow-hidden mb-3 position-relative" style="height: 150px; background-color: var(--pb-primary-light);">
                @if($item->image_url)
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-100 h-100 object-fit-cover pb-product-img">
                @else
                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-primary opacity-75">
                        <x-icon name="package" size="36" stroke="1.5" />
                        <span class="small text-secondary mt-1" style="font-size: 11px;">Material Bangunan</span>
                    </div>
                @endif
                
                <!-- Stock status badge on top right -->
                <div class="position-absolute top-0 end-0 m-2">
                    @if($item->stock <= 0)
                        <x-badge variant="danger" size="sm">Habis (0)</x-badge>
                    @elseif($item->stock <= 10)
                        <x-badge variant="warning" size="sm">Sisa {{ $item->stock }}</x-badge>
                    @else
                        <x-badge variant="success" size="sm">{{ $item->stock }} unit</x-badge>
                    @endif
                </div>
            </div>

            <!-- Category & Brand -->
            <div class="d-flex align-items-center justify-content-between mb-1.5">
                <span class="text-secondary small fw-medium" style="font-size: 11.5px;">
                    {{ $item->category->name ?? 'Material Umum' }}
                </span>
                @if($item->brand)
                    <span class="text-muted small" style="font-size: 11.5px;">{{ $item->brand }}</span>
                @endif
            </div>

            <!-- Item Name (Max 2 lines) -->
            <h6 class="fw-semibold text-main mb-3" style="font-size: 14.5px; line-height: 1.35; min-height: 38px;">
                <a href="{{ route('item.show', $item->id) }}" class="text-main text-decoration-none hover-primary">
                    {{ $item->name }}
                </a>
            </h6>

            <!-- Pricing Section (Eceran & Grosir) -->
            <div class="mt-auto pt-3 border-top border-light-subtle" style="border-color: var(--pb-border-subtle) !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-secondary small" style="font-size: 12px;">Eceran (Orang):</span>
                    <span class="fw-bold text-primary nowrap tabular-nums" style="font-size: 14.5px;">
                        Rp {{ number_format($item->price, 0, ',', '.') }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary small" style="font-size: 12px;">Grosir (Retail):</span>
                    <span class="fw-semibold text-main nowrap tabular-nums" style="font-size: 13.5px;">
                        Rp {{ number_format($item->retail_price, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Action Button -->
                <x-button :href="route('item.show', $item->id)" variant="outline" size="sm" class="w-100 justify-content-center" icon="eye">
                    Detail & Supplier
                </x-button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="pb-card">
            <x-empty-state 
                icon="search"
                title="Barang tidak ditemukan"
                description="Coba gunakan kata kunci pencarian lain atau hilangkan filter kategori/stok yang aktif."
                actionLabel="Reset Pencarian"
                :actionUrl="route('item.index')"
                actionIcon="arrow-left"
            />
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-4 d-flex justify-content-center">
    {{ $items->links('pagination::bootstrap-5') }}
</div>

<!-- Modal Tambah Barang Baru -->
<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--pb-radius-xl);">
            <form action="{{ route('item.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4" style="border-color: var(--pb-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <x-icon name="box" size="20" class="text-primary" />
                        <h5 class="modal-title fw-bold text-main fs-6 mb-0">Tambah Barang Material Baru</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="pb-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Semen Gresik 50kg, Pipa PVC 2 inch" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="pb-label">Merek / Brand</label>
                            <input type="text" name="brand" class="form-control" placeholder="Contoh: Rucika, Avian, Shimizu">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="pb-label">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="pb-label">Stok Awal Fisik <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" min="0" value="0" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="pb-label">Harga Eceran ("Orang") <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                                <input type="number" name="price" class="form-control" min="0" placeholder="0" required>
                            </div>
                            <small class="text-muted" style="font-size: 11.5px;">Dikenakan untuk pembeli umum/konsumen.</small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="pb-label">Harga Grosir ("Retail") <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                                <input type="number" name="retail_price" class="form-control" min="0" placeholder="0" required>
                            </div>
                            <small class="text-muted" style="font-size: 11.5px;">Dikenakan untuk kontraktor / toko rekanan.</small>
                        </div>

                        <div class="col-12">
                            <label class="pb-label">URL Gambar Barang (Opsional)</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://example.com/foto-barang.jpg">
                        </div>

                        <div class="col-12">
                            <label class="pb-label">Deskripsi & Spesifikasi</label>
                            <textarea name="description" rows="2" class="form-control" placeholder="Spesifikasi dimensi, ketebalan, daya tampung, dsb."></textarea>
                        </div>

                        <div class="col-12 pt-2">
                            <div class="pb-alert pb-alert-info mb-0">
                                <div class="fw-semibold mb-2 d-flex align-items-center gap-1.5">
                                    <x-icon name="truck" size="16" />
                                    <span>Pemasok / Supplier Utama (Opsional)</span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12 col-md-4">
                                        <label class="small text-secondary mb-1">Pilih Supplier:</label>
                                        <select name="supplier_id" class="form-select form-select-sm">
                                            <option value="">Belum Ditentukan</option>
                                            @foreach($suppliers as $sup)
                                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small text-secondary mb-1">Harga Beli Satuan (Rp):</label>
                                        <input type="number" name="unit_price" class="form-control form-control-sm" min="0" placeholder="0">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small text-secondary mb-1">Harga Beli Lusin (Rp):</label>
                                        <input type="number" name="lusin_price" class="form-control form-control-sm" min="0" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4" style="border-color: var(--pb-border) !important;">
                    <x-button type="button" variant="secondary" data-bs-dismiss="modal">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" icon="check">
                        Simpan Barang
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const stockFilter = document.getElementById('stockFilter');
        const filterForm = document.getElementById('filterForm');

        const triggerSearch = debounce(() => {
            filterForm.submit();
        }, 350);

        if (searchInput) {
            searchInput.addEventListener('input', triggerSearch);
        }
        if (categoryFilter) {
            categoryFilter.addEventListener('change', () => filterForm.submit());
        }
        if (stockFilter) {
            stockFilter.addEventListener('change', () => filterForm.submit());
        }
    });
</script>
@endpush
