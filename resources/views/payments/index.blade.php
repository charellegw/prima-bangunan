@extends('layouts.app')

@section('title', 'Pembayaran & Tagihan — Prima Bangunan')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3">
    <div>
        <h1 class="page-title">Manajemen Pembayaran, Piutang & Hutang</h1>
        <p class="page-subtitle">Pantau seluruh tagihan penjualan konsumen dan kewajiban hutang dagang supplier secara real-time.</p>
    </div>
</div>

<!-- Summary Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <x-stat-card 
            title="TAGIHAN JATUH TEMPO"
            value="{{ $totalJatuhTempo }} Tagihan"
            sub="Lewat batas pelunasan"
            icon="clock"
            badge="Perhatian"
            badgeVariant="danger"
            iconBg="bg-danger-subtle"
            iconColor="text-danger"
        />
    </div>
    <div class="col-6 col-md-3">
        <x-stat-card 
            title="DIBAYAR SEBAGIAN (DP)"
            value="{{ $totalSebagian }} Tagihan"
            sub="Sedang mencicil"
            icon="wallet"
            badge="Cicilan"
            badgeVariant="warning"
            iconBg="bg-warning-subtle"
            iconColor="text-warning-emphasis"
        />
    </div>
    <div class="col-6 col-md-3">
        <x-stat-card 
            title="BELUM DIBAYAR"
            value="{{ $totalBelumBayar }} Tagihan"
            sub="Belum ada pembayaran"
            icon="credit-card"
            badge="Menunggu"
            badgeVariant="neutral"
            iconBg="bg-secondary-subtle"
            iconColor="text-secondary"
        />
    </div>
    <div class="col-6 col-md-3">
        <x-stat-card 
            title="LUNAS SEPENUHNYA"
            value="{{ $totalLunas }} Tagihan"
            sub="Selesai terlunasi"
            icon="check-circle"
            badge="Lunas"
            badgeVariant="success"
            iconBg="bg-success-subtle"
            iconColor="text-success"
        />
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="pb-card p-3 mb-4">
    <form method="GET" action="{{ route('payments.index') }}" id="filterForm">
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
                           placeholder="Cari no. invoice, nama pelanggan, supplier..."
                           class="form-control border-start-0 ps-2"
                           autocomplete="off">
                </div>
            </div>

            <!-- Filter Tipe Transaksi -->
            <div class="col-6 col-md-3">
                <select name="type" id="typeFilter" class="form-select">
                    <option value="">Semua Tagihan (Penjualan & Supplier)</option>
                    <option value="sales" {{ request('type') === 'sales' ? 'selected' : '' }}>Piutang Penjualan (Customer)</option>
                    <option value="supplier" {{ request('type') === 'supplier' ? 'selected' : '' }}>Hutang Dagang (Supplier)</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="col-6 col-md-3">
                <select name="status" id="statusFilter" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="jatuh_tempo" {{ request('status') === 'jatuh_tempo' ? 'selected' : '' }}>Jatuh Tempo</option>
                    <option value="sebagian" {{ request('status') === 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                    <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div class="col-12 col-md-1 d-flex">
                @if(request()->anyFilled(['q', 'type', 'status']))
                    <a href="{{ route('payments.index') }}" class="pb-btn pb-btn-secondary w-100" title="Reset Filter">
                        <x-icon name="x" size="16" />
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Tabel Daftar Pembayaran -->
<div class="pb-table-card">
    <div class="table-responsive">
        <table class="pb-table">
            <thead>
                <tr>
                    <th>No. Dokumen</th>
                    <th>Jenis Transaksi</th>
                    <th>Pihak Terkait</th>
                    <th class="text-end">Total Tagihan</th>
                    <th class="text-end">Sudah Dibayar</th>
                    <th class="text-end">Sisa Tagihan</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $pay)
                <tr>
                    <td class="nowrap">
                        @if($pay->invoice)
                            <a href="{{ route('invoices.show', $pay->invoice->id) }}" class="fw-semibold text-primary text-decoration-none tabular-nums">
                                {{ $pay->invoice->invoice_number }}
                            </a>
                        @elseif($pay->invoiceSupplier)
                            <a href="{{ route('invoice-suppliers.show', $pay->invoiceSupplier->id) }}" class="fw-semibold text-primary text-decoration-none tabular-nums">
                                {{ $pay->invoiceSupplier->invoice_number }}
                            </a>
                        @else
                            <span class="text-muted tabular-nums">-</span>
                        @endif
                    </td>
                    <td>
                        @if($pay->invoice)
                            <x-badge variant="info" size="sm">
                                <x-icon name="shopping-cart" size="12" class="me-1" /> Piutang Konsumen
                            </x-badge>
                        @elseif($pay->invoiceSupplier)
                            <x-badge variant="warning" size="sm">
                                <x-icon name="bag-plus" size="12" class="me-1" /> Hutang Supplier
                            </x-badge>
                        @endif
                    </td>
                    <td>
                        @if($pay->invoice)
                            <div class="fw-semibold text-main">{{ $pay->invoice->client_name }}</div>
                            <div class="text-secondary small" style="font-size: 11.5px;">Pelanggan {{ $pay->invoice->client_type }}</div>
                        @elseif($pay->invoiceSupplier)
                            <div class="fw-semibold text-main">{{ $pay->invoiceSupplier->supplier->name ?? 'Supplier' }}</div>
                            <div class="text-secondary small" style="font-size: 11.5px;">Penyedia Material</div>
                        @endif
                    </td>
                    <td class="text-end fw-bold text-main nowrap tabular-nums">
                        Rp {{ number_format($pay->amount_total, 0, ',', '.') }}
                    </td>
                    <td class="text-end fw-semibold text-success nowrap tabular-nums">
                        Rp {{ number_format($pay->total_paid, 0, ',', '.') }}
                    </td>
                    <td class="text-end fw-bold text-danger nowrap tabular-nums">
                        Rp {{ number_format($pay->remaining_balance, 0, ',', '.') }}
                    </td>
                    <td class="nowrap">
                        @if($pay->deadline)
                            <div class="small fw-semibold {{ $pay->status === 'jatuh_tempo' ? 'text-danger' : 'text-main' }}">
                                {{ $pay->deadline->format('d M Y') }}
                            </div>
                            <small class="text-muted" style="font-size: 11px;">{{ $pay->deadline->diffForHumans() }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center nowrap">
                        @if($pay->status === 'lunas')
                            <x-badge variant="success">Lunas</x-badge>
                        @elseif($pay->status === 'sebagian')
                            <x-badge variant="warning">Sebagian</x-badge>
                        @elseif($pay->status === 'jatuh_tempo')
                            <x-badge variant="danger">Jatuh Tempo</x-badge>
                        @else
                            <x-badge variant="neutral">Belum Bayar</x-badge>
                        @endif
                    </td>
                    <td class="text-end nowrap">
                        <x-button :href="route('payments.show', $pay->id)" variant="outline" size="sm" icon="credit-card">
                            Riwayat
                        </x-button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <x-empty-state 
                            icon="credit-card"
                            title="Tidak ada data pembayaran"
                            description="Belum ada data tagihan atau pembayaran yang sesuai dengan filter pencarian Anda."
                            actionLabel="Reset Pencarian"
                            :actionUrl="route('payments.index')"
                            actionIcon="arrow-left"
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
    {{ $payments->links('pagination::bootstrap-5') }}
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
