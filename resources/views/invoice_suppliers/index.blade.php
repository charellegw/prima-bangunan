@extends('layouts.app')

@section('title', 'Pembelian Stok Supplier — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Faktur Pembelian Stok Supplier</h4>
            <p class="text-muted small mb-0">Kelola pesanan material masuk dari pabrik / distributor supplier dan hutang dagang toko.</p>
        </div>
        <div>
            <a href="{{ route('invoice-suppliers.create') }}" class="btn btn-primary-custom d-flex align-items-center shadow-sm">
                <i class="bi bi-box-arrow-in-down me-2"></i> Beli Stok / Faktur Masuk
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('invoice-suppliers.index') }}" id="filterForm">
            <div class="row g-2 align-items-center">
                <!-- Search Input with Debounce -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text"
                               name="q"
                               id="searchInput"
                               value="{{ request('q') }}"
                               placeholder="Cari nomor faktur, nama supplier..."
                               class="form-control border-start-0 ps-0"
                               autocomplete="off">
                    </div>
                </div>

                <!-- Filter Supplier -->
                <div class="col-6 col-md-3">
                    <select name="supplier_id" id="supplierFilter" class="form-select">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Pembayaran -->
                <div class="col-6 col-md-3">
                    <select name="payment_status" id="paymentStatusFilter" class="form-select">
                        <option value="">Semua Status Bayar</option>
                        <option value="lunas" {{ request('payment_status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="sebagian" {{ request('payment_status') === 'sebagian' ? 'selected' : '' }}>Sebagian (DP/Tempo)</option>
                        <option value="belum_bayar" {{ request('payment_status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="jatuh_tempo" {{ request('payment_status') === 'jatuh_tempo' ? 'selected' : '' }}>Jatuh Tempo</option>
                    </select>
                </div>

                <!-- Filter Tanggal & Reset -->
                <div class="col-10 col-md-1">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control px-2" title="Mulai Tanggal" onchange="this.form.submit()">
                </div>
                <div class="col-2 col-md-1 text-end">
                    @if(request()->anyFilled(['q', 'supplier_id', 'payment_status', 'date_from']))
                        <a href="{{ route('invoice-suppliers.index') }}" class="btn btn-outline-secondary w-100" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Faktur Supplier -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-4">No. Faktur</th>
                        <th>Tanggal Pembelian</th>
                        <th>Supplier</th>
                        <th>Total Tagihan</th>
                        <th>Status Pembayaran</th>
                        <th>Sisa Hutang Toko</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoiceSuppliers as $inv)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('invoice-suppliers.show', $inv->id) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td>
                            <div class="text-dark fw-medium">{{ $inv->date->format('d M Y') }}</div>
                            <small class="text-muted">{{ $inv->date->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $inv->supplier->name ?? 'Supplier' }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $inv->supplier->location ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="fw-bold text-dark fs-6">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @php $status = $inv->payment->status ?? 'belum_bayar'; @endphp
                            @if($status === 'lunas')
                                <span class="badge-lunas"><i class="bi bi-check-circle me-1"></i> Lunas</span>
                            @elseif($status === 'sebagian')
                                <span class="badge-sebagian"><i class="bi bi-hourglass-split me-1"></i> Sebagian</span>
                            @elseif($status === 'jatuh_tempo')
                                <span class="badge-jatuh-tempo"><i class="bi bi-exclamation-triangle me-1"></i> Jatuh Tempo</span>
                            @else
                                <span class="badge-belum-bayar"><i class="bi bi-x-circle me-1"></i> Belum Bayar</span>
                            @endif
                        </td>
                        <td>
                            @php $remaining = $inv->payment->remaining_balance ?? 0; @endphp
                            @if($remaining > 0)
                                <span class="text-danger fw-bold">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                            @else
                                <span class="text-success small"><i class="bi bi-check2-all"></i> Lunas</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('invoice-suppliers.show', $inv->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold">
                                <i class="bi bi-file-earmark-text me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-bag-plus fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Tidak ada data faktur pembelian supplier yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $invoiceSuppliers->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const supplierFilter = document.getElementById('supplierFilter');
        const paymentStatusFilter = document.getElementById('paymentStatusFilter');
        const filterForm = document.getElementById('filterForm');

        const triggerSearch = debounce(() => {
            filterForm.submit();
        }, 350);

        if (searchInput) {
            searchInput.addEventListener('input', triggerSearch);
        }
        if (supplierFilter) {
            supplierFilter.addEventListener('change', () => filterForm.submit());
        }
        if (paymentStatusFilter) {
            paymentStatusFilter.addEventListener('change', () => filterForm.submit());
        }
    });
</script>
@endpush
