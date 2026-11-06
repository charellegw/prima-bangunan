@extends('layouts.app')

@section('title', 'Pembelian Stok Supplier — Prima Bangunan')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3">
    <div>
        <h1 class="page-title">Faktur Pembelian Stok Supplier</h1>
        <p class="page-subtitle">Kelola pesanan material masuk dari pabrik / distributor supplier dan audit hutang dagang toko.</p>
    </div>
    <div>
        <x-button :href="route('invoice-suppliers.create')" variant="primary" icon="bag-plus">
            Beli Stok / Faktur Masuk
        </x-button>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="pb-card p-3 mb-4">
    <form method="GET" action="{{ route('invoice-suppliers.index') }}" id="filterForm">
        <div class="row g-2 align-items-center">
            <!-- Search Input with Debounce -->
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                        <x-icon name="search" size="16" />
                    </span>
                    <input type="text"
                           name="q"
                           id="searchInput"
                           value="{{ request('q') }}"
                           placeholder="Cari nomor faktur, nama supplier..."
                           class="form-control border-start-0 ps-2"
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
            <div class="col-2 col-md-1 d-flex">
                @if(request()->anyFilled(['q', 'supplier_id', 'payment_status', 'date_from']))
                    <a href="{{ route('invoice-suppliers.index') }}" class="pb-btn pb-btn-secondary w-100" title="Reset Filter">
                        <x-icon name="x" size="16" />
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Tabel Faktur Supplier -->
<div class="pb-table-card">
    <div class="table-responsive">
        <table class="pb-table">
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Tanggal Pembelian</th>
                    <th>Supplier</th>
                    <th class="text-end">Total Tagihan</th>
                    <th class="text-center">Status Pembayaran</th>
                    <th class="text-end">Sisa Hutang Toko</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoiceSuppliers as $inv)
                <tr>
                    <td class="nowrap">
                        <a href="{{ route('invoice-suppliers.show', $inv->id) }}" class="fw-semibold text-primary text-decoration-none tabular-nums">
                            {{ $inv->invoice_number }}
                        </a>
                    </td>
                    <td class="nowrap">
                        <div class="text-main fw-medium">{{ $inv->date ? $inv->date->format('d M Y') : '-' }}</div>
                        <div class="text-secondary small" style="font-size: 11.5px;">{{ $inv->date ? $inv->date->format('H:i') . ' WIB' : '' }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-main">{{ $inv->supplier->name ?? 'Supplier' }}</div>
                        <div class="text-secondary small d-flex align-items-center gap-1" style="font-size: 11.5px;">
                            <x-icon name="map-pin" size="12" class="text-muted" />
                            <span>{{ $inv->supplier->location ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="text-end fw-bold text-main nowrap tabular-nums">
                        Rp {{ number_format($inv->grand_total, 0, ',', '.') }}
                    </td>
                    <td class="text-center nowrap">
                        @php $status = $inv->payment->status ?? 'belum_bayar'; @endphp
                        @if($status === 'lunas')
                            <x-badge variant="success">Lunas</x-badge>
                        @elseif($status === 'sebagian')
                            <x-badge variant="warning">Sebagian</x-badge>
                        @elseif($status === 'jatuh_tempo')
                            <x-badge variant="danger">Jatuh Tempo</x-badge>
                        @else
                            <x-badge variant="neutral">Belum Bayar</x-badge>
                        @endif
                    </td>
                    <td class="text-end nowrap tabular-nums">
                        @php $remaining = $inv->payment->remaining_balance ?? 0; @endphp
                        @if($remaining > 0)
                            <span class="text-danger fw-bold">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                        @else
                            <span class="text-success small fw-medium">Lunas</span>
                        @endif
                    </td>
                    <td class="text-end nowrap">
                        <x-button :href="route('invoice-suppliers.show', $inv->id)" variant="outline" size="sm" icon="eye">
                            Detail Faktur
                        </x-button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <x-empty-state 
                            icon="bag-plus"
                            title="Tidak ada faktur pembelian"
                            description="Belum ada transaksi pembelian stok dari supplier yang sesuai dengan pencarian Anda."
                            actionLabel="Beli Stok Sekarang"
                            :actionUrl="route('invoice-suppliers.create')"
                        />
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
