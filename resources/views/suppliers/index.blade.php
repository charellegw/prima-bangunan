@extends('layouts.app')

@section('title', 'Data Supplier — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Data Supplier & Distributor Material</h4>
            <p class="text-muted small mb-0">Kelola informasi mitra pabrik dan distributor bahan bangunan, lokasi gudang, dan kontak.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary-custom d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
                <i class="bi bi-plus-lg me-2"></i> Tambah Supplier Baru
            </button>
        </div>
    </div>

    <!-- Search Bar with Debounce -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('suppliers.index') }}" id="filterForm">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text"
                               name="q"
                               id="searchInput"
                               value="{{ request('q') }}"
                               placeholder="Cari nama supplier, kota / lokasi, kontak..."
                               class="form-control border-start-0 ps-0"
                               autocomplete="off">
                    </div>
                </div>

                <div class="col-12 col-md-1 text-end">
                    @if(request()->filled('q'))
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary w-100" title="Reset Pencarian">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Supplier -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Nama Supplier</th>
                        <th>Lokasi / Alamat Gudang</th>
                        <th>No. Telepon / Kontak</th>
                        <th>Material yang Disuplai</th>
                        <th>Faktur Pembelian</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $idx => $sup)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $suppliers->firstItem() + $idx }}</td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $sup->name }}</div>
                        </td>
                        <td>
                            <div class="text-secondary small">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $sup->location ?: 'Lokasi belum dicatat' }}
                            </div>
                        </td>
                        <td>
                            <div class="text-muted small">
                                <i class="bi bi-telephone me-1"></i> {{ $sup->phone ?: '-' }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border">{{ $sup->items_count }} Barang</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border">{{ $sup->invoice_suppliers_count }} Faktur</span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-inline-flex gap-1">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary border-0 rounded-circle"
                                        title="Edit Supplier"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditSupplier{{ $sup->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" onsubmit="return confirm('Hapus data supplier {{ $sup->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Hapus Supplier">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Modal Edit Supplier -->
                            <div class="modal fade text-start" id="modalEditSupplier{{ $sup->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <form action="{{ route('suppliers.update', $sup->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header border-bottom py-3 px-4">
                                                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i> Edit Data Supplier</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-dark">Nama Supplier <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $sup->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-dark">Lokasi Gudang / Kota</label>
                                                    <input type="text" name="location" class="form-control" value="{{ $sup->location }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-dark">No. Telepon / Kontak</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $sup->phone }}">
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Tidak ada data supplier yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $suppliers->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Tambah Supplier Baru -->
<div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-building me-2 text-primary"></i> Tambah Mitra Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: PT Semen Indonesia, CV Rucika Jaya" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Lokasi Gudang / Alamat / Kota</label>
                        <input type="text" name="location" class="form-control" placeholder="Contoh: Kawasan Industri Gresik, Surabaya">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">No. Telepon / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="Contoh: 031-8977112 / 08123456789">
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');

        if (searchInput) {
            searchInput.addEventListener('input', debounce(() => filterForm.submit(), 350));
        }
    });
</script>
@endpush
