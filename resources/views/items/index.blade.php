@extends('layouts.app')

@section('title', 'Katalog Barang & Stok — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Katalog Barang & Stok Material</h4>
            <p class="text-muted small mb-0">Kelola stok barang, harga eceran (orang), harga grosir (retail), dan relasi supplier.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary-custom d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                <i class="bi bi-plus-lg me-2"></i> Tambah Barang Baru
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('item.index') }}" id="filterForm">
            <div class="row g-2 align-items-center">
                <!-- Search Input with Debounce -->
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text"
                               name="q"
                               id="searchInput"
                               value="{{ request('q') }}"
                               placeholder="Cari nama, merek, kata kunci (mis. 'pom' untuk pompa)..."
                               class="form-control border-start-0 ps-0"
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

                <!-- Reset / Submit Buttons -->
                <div class="col-12 col-md-1 d-flex gap-1">
                    @if(request()->anyFilled(['q', 'category_id', 'stock_status']))
                        <a href="{{ route('item.index') }}" class="btn btn-outline-secondary w-100" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Grid Barang -->
    <div class="row g-3" id="itemsGrid">
        @forelse($items as $item)
        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
            <div class="card card-custom h-100 d-flex flex-column p-3 position-relative">
                <!-- Image Container -->
                <div class="rounded-3 bg-light overflow-hidden mb-3 d-flex align-items-center justify-content-center" style="height: 160px;">
                    @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-100 h-100 object-fit-cover">
                    @else
                        <div class="text-secondary opacity-50 text-center">
                            <i class="bi bi-box-seam fs-1"></i>
                            <div class="small mt-1">No Image</div>
                        </div>
                    @endif
                </div>

                <!-- Kategori & Brand -->
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px;">
                        {{ $item->category->name ?? 'Material Umum' }}
                    </span>
                    @if($item->brand)
                        <small class="text-muted fw-semibold">{{ $item->brand }}</small>
                    @endif
                </div>

                <!-- Nama Barang -->
                <h6 class="fw-bold text-dark mb-2 text-truncate-2" style="min-height: 40px;" title="{{ $item->name }}">
                    <a href="{{ route('item.show', $item->id) }}" class="text-dark text-decoration-none hover-primary">
                        {{ $item->name }}
                    </a>
                </h6>

                <!-- Stok Badge -->
                <div class="mb-3">
                    @if($item->stock <= 0)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                            <i class="bi bi-x-circle me-1"></i> Stok Habis (0)
                        </span>
                    @elseif($item->stock <= 10)
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                            <i class="bi bi-exclamation-circle me-1"></i> Stok Sisa {{ $item->stock }}
                        </span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                            <i class="bi bi-check-circle me-1"></i> Stok {{ $item->stock }} unit
                        </span>
                    @endif
                </div>

                <!-- Pricing Information (Orang vs Retail) -->
                <div class="mt-auto pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <small class="text-muted" style="font-size: 11px;">Harga Orang (Eceran):</small>
                        <span class="fw-bold text-primary">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline mb-3">
                        <small class="text-muted" style="font-size: 11px;">Harga Retail (Grosir):</small>
                        <span class="fw-semibold text-success">Rp {{ number_format($item->retail_price, 0, ',', '.') }}</span>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('item.show', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold">
                            <i class="bi bi-info-circle me-1"></i> Detail & Harga Supplier
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card card-custom text-center py-5">
                <i class="bi bi-search fs-1 text-secondary opacity-50 mb-3"></i>
                <h5 class="fw-bold text-dark">Barang tidak ditemukan</h5>
                <p class="text-muted mb-3">Coba gunakan kata kunci pencarian lain atau hilangkan filter yang aktif.</p>
                <div>
                    <a href="{{ route('item.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                        Reset Pencarian
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Tambah Barang Baru -->
<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('item.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-box-seam me-2 text-primary"></i> Tambah Barang Material Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-semibold text-dark">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Semen Gresik 50kg, Pipa PVC 2 inch" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Merek / Brand</label>
                            <input type="text" name="brand" class="form-control" placeholder="Contoh: Rucika, Avian, Shimizu">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" min="0" value="0" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Harga Eceran ("Orang") <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="price" class="form-control" min="0" placeholder="0" required>
                            </div>
                            <small class="text-muted" style="font-size: 11px;">Harga yang otomatis dikenakan untuk pelanggan biasa (orang).</small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Harga Grosir ("Retail") <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="retail_price" class="form-control" min="0" placeholder="0" required>
                            </div>
                            <small class="text-muted" style="font-size: 11px;">Harga yang otomatis dikenakan untuk pelanggan toko/retail.</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">URL Gambar (Opsional)</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://example.com/foto-barang.jpg">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">Deskripsi & Spesifikasi</label>
                            <textarea name="description" rows="2" class="form-control" placeholder="Spesifikasi ukuran, fungsi, dsb."></textarea>
                        </div>

                        <hr class="my-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-truck me-1 text-primary"></i> Data Supplier Utama (Opsional)</h6>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Supplier</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">Belum Ditentukan</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Harga Beli Satuan (Unit Price)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="unit_price" class="form-control" min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Harga Beli Per Lusin (Lusin Price)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="lusin_price" class="form-control" min="0" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Barang</button>
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

        // Search optimizer dengan debounce 350ms
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
