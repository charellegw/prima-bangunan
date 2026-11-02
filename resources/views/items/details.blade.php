@extends('layouts.app')

@section('title', $item->name . ' — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Back -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('item.index') }}" class="btn btn-light border rounded-pill px-3 py-1 text-secondary small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
            </a>
            <span class="text-muted small">/</span>
            <span class="text-secondary small fw-medium">{{ $item->category->name ?? 'Material' }}</span>
            <span class="text-muted small">/</span>
            <span class="text-dark small fw-bold">{{ $item->name }}</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1 small fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditBarang">
                <i class="bi bi-pencil-square me-1"></i> Edit Barang
            </button>
            <form action="{{ route('item.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini? Data transaksi lama tetap tersimpan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-pill px-3 py-1 small fw-semibold">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Main Item Card -->
    <div class="card card-custom p-4 mb-4">
        <div class="row g-4 align-items-center">
            <!-- Foto Barang -->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="rounded-4 bg-light overflow-hidden d-flex align-items-center justify-content-center p-3 border" style="height: 240px;">
                    @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-100 h-100 object-fit-cover rounded-3">
                    @else
                        <div class="text-secondary opacity-50 text-center">
                            <i class="bi bi-box-seam fs-1"></i>
                            <div class="small mt-2">Tidak ada foto</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informasi Pokok Barang -->
            <div class="col-12 col-md-8 col-lg-9">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                        {{ $item->category->name ?? 'Material Umum' }}
                    </span>
                    @if($item->brand)
                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill">
                            Merek: {{ $item->brand }}
                        </span>
                    @endif
                </div>

                <h3 class="fw-bold text-dark mb-2">{{ $item->name }}</h3>
                <p class="text-muted small mb-3">{{ $item->description ?: 'Belum ada deskripsi untuk barang ini.' }}</p>

                <div class="row g-3">
                    <!-- Stok Realtime -->
                    <div class="col-6 col-sm-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small mb-1">Stok Tersedia:</div>
                            <div class="fs-4 fw-bold {{ $item->stock <= 0 ? 'text-danger' : ($item->stock <= 10 ? 'text-warning-emphasis' : 'text-dark') }}">
                                {{ $item->stock }} <span class="fs-6 fw-normal text-muted">unit</span>
                            </div>
                        </div>
                    </div>

                    <!-- Harga Orang (Eceran) -->
                    <div class="col-6 col-sm-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small mb-1">Harga Orang (Eceran):</div>
                            <div class="fs-4 fw-bold text-primary">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Harga Retail (Grosir) -->
                    <div class="col-12 col-sm-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small mb-1">Harga Retail (Toko):</div>
                            <div class="fs-4 fw-bold text-success">
                                Rp {{ number_format($item->retail_price, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL SUPPLIER (SESUAI ATURAN SPESIFIKASI PROMPT) -->
    <!-- "Untuk detail barang, sertakan tabel/informasi tentang supplier barang, harganya, dan lokasi nya, dan namanya." -->
    <div class="card card-custom p-0 mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold text-dark mb-1"><i class="bi bi-truck me-2 text-primary"></i> Informasi Supplier & Harga Beli</h5>
                <p class="text-muted small mb-0">Daftar supplier penyedia barang ini beserta lokasi gudang dan perbandingan harga satuan & harga lusin.</p>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-primary-custom rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahSupplierPrice">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Supplier
                </button>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-4">Nama Supplier</th>
                        <th>Lokasi Supplier</th>
                        <th>Kontak Telepon</th>
                        <th>Harga Beli Satuan (Unit)</th>
                        <th>Harga Beli Per Lusin</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($item->supplierPrices as $sp)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $sp->supplier->name ?? 'Supplier' }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center text-secondary small">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                <span>{{ $sp->supplier->location ?: 'Lokasi belum dicatat' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted small">
                                <i class="bi bi-telephone me-1"></i> {{ $sp->supplier->phone ?: '-' }}
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">Rp {{ number_format($sp->unit_price, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @if($sp->lusin_price > 0)
                                <span class="fw-bold text-primary">Rp {{ number_format($sp->lusin_price, 0, ',', '.') }}</span>
                                <small class="text-muted d-block" style="font-size: 11px;">(Rp {{ number_format($sp->lusin_price / 12, 0, ',', '.') }}/pcs)</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <form action="{{ route('item.supplier.destroy', [$item->id, $sp->supplier_id]) }}" method="POST" onsubmit="return confirm('Hapus relasi harga dari supplier ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Hapus Relasi">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-building-exclamation fs-3 text-secondary opacity-50 d-block mb-2"></i>
                            Belum ada data supplier yang terhubung untuk barang ini.
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahSupplierPrice">
                                    <i class="bi bi-plus-lg me-1"></i> Hubungkan Supplier Sekarang
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Relasi Supplier & Harga -->
<div class="modal fade" id="modalTambahSupplierPrice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('item.supplier.store', $item->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-truck me-2 text-primary"></i> Hubungkan Supplier & Harga Beli</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Pilih Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Pilih Supplier...</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->location ?: 'Lokasi -' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Harga Beli Satuan (Unit Price) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" name="unit_price" class="form-control" min="0" placeholder="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Harga Beli Per Lusin (Lusin Price)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" name="lusin_price" class="form-control" min="0" placeholder="0">
                        </div>
                        <small class="text-muted" style="font-size: 11px;">Biarkan kosong bila ingin dihitung otomatis 12x harga satuan.</small>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Barang -->
<div class="modal fade" id="modalEditBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('item.update', $item->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i> Edit Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-semibold text-dark">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Merek / Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ $item->brand }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" min="0" value="{{ $item->stock }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Harga Eceran ("Orang") <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="price" class="form-control" min="0" value="{{ (int)$item->price }}" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Harga Grosir ("Retail") <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="retail_price" class="form-control" min="0" value="{{ (int)$item->retail_price }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">URL Gambar</label>
                            <input type="url" name="image_url" class="form-control" value="{{ $item->image_url }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">Deskripsi</label>
                            <textarea name="description" rows="3" class="form-control">{{ $item->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
