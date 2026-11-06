@extends('layouts.app')

@section('title', $item->name . ' — Prima Bangunan')

@section('content')
<!-- Breadcrumbs & Header Actions -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3">
    <div class="d-flex align-items-center gap-2">
        <x-button :href="route('item.index')" variant="secondary" size="sm" icon="arrow-left">
            Katalog Barang
        </x-button>
        <span class="text-muted small">/</span>
        <span class="text-secondary small">{{ $item->category->name ?? 'Material' }}</span>
        <span class="text-muted small">/</span>
        <span class="text-main small fw-bold">{{ $item->name }}</span>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <x-button type="button" variant="secondary" size="sm" icon="edit" data-bs-toggle="modal" data-bs-target="#modalEditBarang">
            Edit Barang
        </x-button>
        <form action="{{ route('item.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini? Data transaksi lama tetap tersimpan.');">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="danger" size="sm" icon="trash">
                Hapus
            </x-button>
        </form>
    </div>
</div>

<!-- Main Item Card -->
<div class="pb-card p-4 mb-4">
    <div class="row g-4 align-items-center">
        <!-- Foto Barang with Fallback -->
        <div class="col-12 col-md-4 col-lg-3">
            <div class="rounded-4 overflow-hidden d-flex align-items-center justify-content-center p-2 border border-light-subtle" 
                 style="height: 220px; background-color: var(--pb-primary-light); border-color: var(--pb-border) !important;">
                @if($item->image_url)
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-100 h-100 object-fit-cover rounded-3 pb-product-img">
                @else
                    <div class="text-primary text-center opacity-75">
                        <x-icon name="package" size="48" stroke="1.5" />
                        <div class="small text-secondary mt-2" style="font-size: 12px;">Foto Belum Tersedia</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detail Pokok Barang -->
        <div class="col-12 col-md-8 col-lg-9">
            <div class="d-flex align-items-center gap-2 mb-2">
                <x-badge variant="info">{{ $item->category->name ?? 'Material Umum' }}</x-badge>
                @if($item->brand)
                    <x-badge variant="neutral">Merek: {{ $item->brand }}</x-badge>
                @endif
            </div>

            <h2 class="fw-bold text-main mb-2" style="font-size: 24px; letter-spacing: -0.02em;">{{ $item->name }}</h2>
            <p class="text-secondary small mb-4" style="font-size: 13.5px; line-height: 1.5;">
                {{ $item->description ?: 'Belum ada deskripsi tambahan untuk material ini.' }}
            </p>

            <div class="row g-3">
                <!-- Stok Fisik -->
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-light-subtle rounded-3 border border-light-subtle" style="border-color: var(--pb-border) !important;">
                        <div class="text-secondary small fw-medium mb-1" style="font-size: 12px;">STOK FISIK:</div>
                        <div class="fs-4 fw-bold tabular-nums {{ $item->stock <= 0 ? 'text-danger' : ($item->stock <= 10 ? 'text-warning-emphasis' : 'text-main') }}">
                            {{ $item->stock }} <span class="fs-6 fw-normal text-muted">unit</span>
                        </div>
                    </div>
                </div>

                <!-- Harga Eceran (Orang) -->
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-light-subtle rounded-3 border border-light-subtle" style="border-color: var(--pb-border) !important;">
                        <div class="text-secondary small fw-medium mb-1" style="font-size: 12px;">HARGA ECERAN (ORANG):</div>
                        <div class="fs-4 fw-bold text-primary nowrap tabular-nums">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Harga Grosir (Retail) -->
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-light-subtle rounded-3 border border-light-subtle" style="border-color: var(--pb-border) !important;">
                        <div class="text-secondary small fw-medium mb-1" style="font-size: 12px;">HARGA GROSIR (RETAIL):</div>
                        <div class="fs-4 fw-bold text-main nowrap tabular-nums">
                            Rp {{ number_format($item->retail_price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Matriks Supplier & Harga Beli -->
<div class="pb-card mb-4 overflow-hidden">
    <div class="p-4 border-bottom border-light-subtle d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-color: var(--pb-border) !important;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <x-icon name="truck" size="18" class="text-primary" />
                <h5 class="fw-semibold text-main mb-0 fs-6">Informasi Pemasok (Supplier Price Matrix)</h5>
            </div>
            <p class="text-secondary small mb-0 mt-0.5" style="font-size: 12.5px;">Perbandingan harga beli satuan dan lusin beserta lokasi distributor.</p>
        </div>
        <div>
            <x-button type="button" variant="primary" size="sm" icon="plus" data-bs-toggle="modal" data-bs-target="#modalTambahSupplierPrice">
                Hubungkan Supplier
            </x-button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="pb-table">
            <thead>
                <tr>
                    <th>Nama Supplier</th>
                    <th>Lokasi Gudang</th>
                    <th>Kontak Telepon</th>
                    <th class="text-end">Harga Satuan (Unit)</th>
                    <th class="text-end">Harga Lusin (12 pcs)</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($item->supplierPrices as $sp)
                <tr>
                    <td class="fw-semibold text-main">
                        {{ $sp->supplier->name ?? 'Supplier' }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center text-secondary small gap-1.5" style="font-size: 12.5px;">
                            <x-icon name="map-pin" size="14" class="text-muted" />
                            <span>{{ $sp->supplier->location ?: 'Lokasi belum dicatat' }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="text-secondary small d-flex align-items-center gap-1.5" style="font-size: 12.5px;">
                            <x-icon name="phone" size="14" class="text-muted" />
                            <span>{{ $sp->supplier->phone ?: '-' }}</span>
                        </div>
                    </td>
                    <td class="text-end fw-bold text-main nowrap tabular-nums">
                        Rp {{ number_format($sp->unit_price, 0, ',', '.') }}
                    </td>
                    <td class="text-end nowrap tabular-nums">
                        @if($sp->lusin_price > 0)
                            <div class="fw-bold text-primary">Rp {{ number_format($sp->lusin_price, 0, ',', '.') }}</div>
                            <div class="text-muted small" style="font-size: 11px;">(Rp {{ number_format($sp->lusin_price / 12, 0, ',', '.') }}/unit)</div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end nowrap">
                        <form action="{{ route('item.supplier.destroy', [$item->id, $sp->supplier_id]) }}" method="POST" onsubmit="return confirm('Hapus relasi harga dari supplier ini?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="danger" size="sm" icon="trash">
                                Hapus
                            </x-button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state 
                            icon="truck"
                            title="Belum ada supplier yang terhubung"
                            description="Hubungkan distributor atau pemasok untuk membandingkan harga modal pembelian barang ini."
                            actionLabel="Hubungkan Supplier Sekarang"
                            data-bs-toggle="modal"
                            data-bs-target="#modalTambahSupplierPrice"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Relasi Supplier & Harga -->
<div class="modal fade" id="modalTambahSupplierPrice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--pb-radius-xl);">
            <form action="{{ route('item.supplier.store', $item->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4" style="border-color: var(--pb-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <x-icon name="truck" size="20" class="text-primary" />
                        <h5 class="modal-title fw-bold text-main fs-6 mb-0">Hubungkan Supplier & Harga Beli</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="pb-label">Pilih Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Pilih Supplier...</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->location ?: 'Lokasi -' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Harga Beli Satuan (Unit Price) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                            <input type="number" name="unit_price" class="form-control" min="0" placeholder="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Harga Beli Per Lusin (Lusin Price)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                            <input type="number" name="lusin_price" class="form-control" min="0" placeholder="0">
                        </div>
                        <small class="text-muted" style="font-size: 11.5px;">Biarkan kosong bila dihitung otomatis 12x harga satuan.</small>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4" style="border-color: var(--pb-border) !important;">
                    <x-button type="button" variant="secondary" data-bs-dismiss="modal">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" icon="check">
                        Simpan Supplier
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Barang -->
<div class="modal fade" id="modalEditBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--pb-radius-xl);">
            <form action="{{ route('item.update', $item->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-bottom py-3 px-4" style="border-color: var(--pb-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <x-icon name="edit" size="20" class="text-primary" />
                        <h5 class="modal-title fw-bold text-main fs-6 mb-0">Edit Data Barang</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="pb-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="pb-label">Merek / Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ $item->brand }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="pb-label">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="pb-label">Stok Fisik <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" min="0" value="{{ $item->stock }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="pb-label">Harga Eceran ("Orang") <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                                <input type="number" name="price" class="form-control" min="0" value="{{ (int)$item->price }}" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="pb-label">Harga Grosir ("Retail") <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                                <input type="number" name="retail_price" class="form-control" min="0" value="{{ (int)$item->retail_price }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="pb-label">URL Gambar Barang</label>
                            <input type="url" name="image_url" class="form-control" value="{{ $item->image_url }}">
                        </div>

                        <div class="col-12">
                            <label class="pb-label">Deskripsi</label>
                            <textarea name="description" rows="3" class="form-control">{{ $item->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4" style="border-color: var(--pb-border) !important;">
                    <x-button type="button" variant="secondary" data-bs-dismiss="modal">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" icon="check">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
