@extends('layouts.app')

@section('title', 'Logistik & Pengiriman — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Logistik & Status Pengiriman Material</h4>
            <p class="text-muted small mb-0">Kelola armada pengantaran barang ke pelanggan serta penerimaan pasokan masuk dari supplier.</p>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('shipments.index') }}" id="filterForm">
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
                               placeholder="Cari no. resi pengiriman, alamat, pelanggan, supplier..."
                               class="form-control border-start-0 ps-0"
                               autocomplete="off">
                    </div>
                </div>

                <!-- Filter Tipe Pengiriman -->
                <div class="col-6 col-md-3">
                    <select name="type" id="typeFilter" class="form-select">
                        <option value="">Semua Jenis Pengiriman</option>
                        <option value="outgoing" {{ request('type') === 'outgoing' ? 'selected' : '' }}>Kirim ke Pelanggan (Outgoing)</option>
                        <option value="incoming" {{ request('type') === 'incoming' ? 'selected' : '' }}>Masuk dari Supplier (Incoming)</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="col-6 col-md-3">
                    <select name="status" id="statusFilter" class="form-select">
                        <option value="">Semua Status Logistik</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                        <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Shipping (Sedang Jalan)</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered (Sampai)</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned (Retur)</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="col-12 col-md-1 text-end">
                    @if(request()->anyFilled(['q', 'type', 'status']))
                        <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary w-100" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Pengiriman -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-4">No. Pengiriman</th>
                        <th>Tipe</th>
                        <th>Dokumen Terkait</th>
                        <th>Tujuan / Alamat</th>
                        <th>Tanggal Pengiriman</th>
                        <th>Ongkir</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $sh)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('shipments.show', $sh->id) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $sh->shipment_number }}
                            </a>
                        </td>
                        <td>
                            @if($sh->type === 'outgoing')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i class="bi bi-arrow-up-right me-1"></i> Ke Pelanggan
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-arrow-down-left me-1"></i> Dari Supplier
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($sh->invoice)
                                <a href="{{ route('invoices.show', $sh->invoice->id) }}" class="text-decoration-none font-monospace small fw-bold text-dark">
                                    {{ $sh->invoice->invoice_number }}
                                </a>
                                <small class="text-muted d-block">({{ $sh->invoice->client_name }})</small>
                            @elseif($sh->invoiceSupplier)
                                <a href="{{ route('invoice-suppliers.show', $sh->invoiceSupplier->id) }}" class="text-decoration-none font-monospace small fw-bold text-dark">
                                    {{ $sh->invoiceSupplier->invoice_number }}
                                </a>
                                <small class="text-muted d-block">({{ $sh->invoiceSupplier->supplier->name ?? 'Supplier' }})</small>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark small text-truncate" style="max-width: 250px;" title="{{ $sh->address }}">
                                <i class="bi bi-geo-alt me-1 text-danger"></i> {{ $sh->address }}
                            </div>
                        </td>
                        <td>
                            <div class="text-dark fw-medium small">{{ $sh->date->format('d M Y') }}</div>
                            <small class="text-muted">{{ $sh->date->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            <span class="small font-monospace fw-semibold">Rp {{ number_format($sh->fee, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @if($sh->status === 'delivered')
                                <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i> Sampai</span>
                            @elseif($sh->status === 'shipping')
                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-truck me-1"></i> Di Jalan</span>
                            @elseif($sh->status === 'pending')
                                <span class="badge bg-secondary text-white px-2 py-1"><i class="bi bi-clock me-1"></i> Pending</span>
                            @else
                                <span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-arrow-return-left me-1"></i> Retur</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('shipments.show', $sh->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Tidak ada data pengiriman material yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $shipments->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const statusFilter = document.getElementById('statusFilter');
        const filterForm = document.getElementById('filterForm');

        const triggerSearch = debounce(() => {
            filterForm.submit();
        }, 350);

        if (searchInput) searchInput.addEventListener('input', triggerSearch);
        if (typeFilter) typeFilter.addEventListener('change', () => filterForm.submit());
        if (statusFilter) statusFilter.addEventListener('change', () => filterForm.submit());
    });
</script>
@endpush
