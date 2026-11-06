@extends('layouts.app')

@section('title', 'Data Supplier — Prima Bangunan')

@section('content')
<div class="d-flex flex-column gap-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="page-title mb-1">Data Mitra Supplier</h1>
            <p class="page-subtitle mb-0">Kelola informasi mitra distributor bahan bangunan, lokasi gudang, dan kontak pemesanan</p>
        </div>
        <div>
            <x-button type="button" variant="primary" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
                <x-icon name="plus" style="width: 16px; height: 16px;" class="me-1" /> Tambah Supplier
            </x-button>
        </div>
    </div>

    <!-- Search Bar with Debounce -->
    <x-card :flush="true" class="p-3">
        <form method="GET" action="{{ route('suppliers.index') }}" id="filterForm">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                            <x-icon name="search" style="width: 16px; height: 16px;" />
                        </span>
                        <input type="text"
                               name="q"
                               id="searchInput"
                               value="{{ request('q') }}"
                               placeholder="Cari nama supplier, lokasi / gudang, kontak..."
                               class="form-control border-start-0 ps-1"
                               autocomplete="off">
                    </div>
                </div>

                <div class="col-12 col-md-1 text-end">
                    @if(request()->filled('q'))
                        <x-button href="{{ route('suppliers.index') }}" variant="secondary" class="w-100" title="Reset Pencarian">
                            <x-icon name="x" style="width: 16px; height: 16px;" />
                        </x-button>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    <!-- Tabel Supplier -->
    <x-card :flush="true" class="p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table pb-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 50px;">No.</th>
                        <th>Nama Supplier</th>
                        <th>Lokasi / Gudang</th>
                        <th>Kontak Telepon</th>
                        <th>Material Disuplai</th>
                        <th>Faktur Pembelian</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $idx => $sup)
                    <tr>
                        <td class="ps-4 text-muted small tabular-nums">{{ $suppliers->firstItem() + $idx }}</td>
                        <td>
                            <div class="fw-semibold text-dark fs-6">{{ $sup->name }}</div>
                        </td>
                        <td>
                            <div class="text-secondary small">
                                {{ $sup->location ?: 'Lokasi belum dicatat' }}
                            </div>
                        </td>
                        <td class="nowrap">
                            <div class="text-muted small tabular-nums">
                                {{ $sup->phone ?: '-' }}
                            </div>
                        </td>
                        <td class="nowrap">
                            <x-badge variant="info">{{ $sup->items_count }} Barang</x-badge>
                        </td>
                        <td class="nowrap">
                            <x-badge variant="neutral">{{ $sup->invoice_suppliers_count }} Faktur</x-badge>
                        </td>
                        <td class="pe-4 text-end nowrap">
                            <div class="d-inline-flex gap-1">
                                <x-button type="button"
                                          variant="ghost"
                                          size="sm"
                                          title="Edit Supplier"
                                          data-bs-toggle="modal"
                                          data-bs-target="#modalEditSupplier{{ $sup->id }}">
                                    <x-icon name="edit" style="width: 14px; height: 14px;" />
                                </x-button>
                                <form action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" onsubmit="return confirm('Hapus data supplier {{ $sup->name }}?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" class="text-danger" title="Hapus Supplier">
                                        <x-icon name="trash" style="width: 14px; height: 14px;" />
                                    </x-button>
                                </form>
                            </div>

                            <!-- Modal Edit Supplier -->
                            <div class="modal fade text-start" id="modalEditSupplier{{ $sup->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-sm rounded-4">
                                        <form action="{{ route('suppliers.update', $sup->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header border-bottom py-3 px-4">
                                                <h5 class="modal-title fw-bold text-dark fs-6">Edit Data Supplier</h5>
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
                                                <x-button type="button" variant="secondary" data-bs-dismiss="modal">Batal</x-button>
                                                <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <x-empty-state
                                icon="building"
                                title="Tidak ada data supplier"
                                description="Tidak ada supplier material yang cocok dengan kriteria pencarian Anda."
                            />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $suppliers->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Tambah Supplier Baru -->
<div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm rounded-4">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark fs-6">Tambah Mitra Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: PT Semen Indonesia, CV Rucika Jaya" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Lokasi Gudang / Kota</label>
                        <input type="text" name="location" class="form-control" placeholder="Contoh: Kawasan Industri Gresik, Surabaya">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">No. Telepon / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="Contoh: 031-8977112 / 08123456789">
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <x-button type="button" variant="secondary" data-bs-dismiss="modal">Batal</x-button>
                    <x-button type="submit" variant="primary">Simpan Supplier</x-button>
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
