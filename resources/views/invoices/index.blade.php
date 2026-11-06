@extends('layouts.app')

@section('title', 'Daftar Invoice Penjualan — Prima Bangunan')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3">
    <div>
        <h1 class="page-title">Invoice & Penjualan Kasir</h1>
        <p class="page-subtitle">Riwayat transaksi penjualan material ke pelanggan umum (orang) dan toko rekanan (retail).</p>
    </div>
    <div>
        <x-button :href="route('invoices.create')" variant="primary" icon="shopping-cart">
            Transaksi Kasir Baru
        </x-button>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="pb-card p-3 mb-4">
    <form method="GET" action="{{ route('invoices.index') }}" id="filterForm">
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
                           placeholder="Cari nomor nota, nama pelanggan..."
                           class="form-control border-start-0 ps-2"
                           autocomplete="off">
                </div>
            </div>

            <!-- Filter Tipe Pelanggan -->
            <div class="col-6 col-md-2">
                <select name="client_type" id="clientTypeFilter" class="form-select">
                    <option value="">Semua Tipe</option>
                    <option value="orang" {{ request('client_type') === 'orang' ? 'selected' : '' }}>Orang (Eceran)</option>
                    <option value="retail" {{ request('client_type') === 'retail' ? 'selected' : '' }}>Retail (Grosir)</option>
                </select>
            </div>

            <!-- Filter Status Pembayaran -->
            <div class="col-6 col-md-3">
                <select name="payment_status" id="paymentStatusFilter" class="form-select">
                    <option value="">Semua Status Bayar</option>
                    <option value="lunas" {{ request('payment_status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="sebagian" {{ request('payment_status') === 'sebagian' ? 'selected' : '' }}>Sebagian (DP/Cicilan)</option>
                    <option value="belum_bayar" {{ request('payment_status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="jatuh_tempo" {{ request('payment_status') === 'jatuh_tempo' ? 'selected' : '' }}>Jatuh Tempo</option>
                </select>
            </div>

            <!-- Filter Tanggal -->
            <div class="col-10 col-md-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" title="Mulai Tanggal" onchange="this.form.submit()">
            </div>

            <!-- Reset Button -->
            <div class="col-2 col-md-1 d-flex">
                @if(request()->anyFilled(['q', 'client_type', 'payment_status', 'date_from', 'date_to']))
                    <a href="{{ route('invoices.index') }}" class="pb-btn pb-btn-secondary w-100" title="Reset Filter">
                        <x-icon name="x" size="16" />
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Tabel Daftar Invoice -->
<div class="pb-table-card">
    <div class="table-responsive">
        <table class="pb-table">
            <thead>
                <tr>
                    <th>No. Nota</th>
                    <th>Tanggal Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Tipe Harga</th>
                    <th class="text-end">Total Belanja</th>
                    <th class="text-center">Status Pembayaran</th>
                    <th class="text-end">Sisa Piutang</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td class="nowrap">
                        <a href="{{ route('invoices.show', $inv->id) }}" class="fw-semibold text-primary text-decoration-none tabular-nums">
                            {{ $inv->invoice_number }}
                        </a>
                    </td>
                    <td class="nowrap">
                        <div class="text-main fw-medium">{{ $inv->date ? $inv->date->format('d M Y') : '-' }}</div>
                        <div class="text-secondary small" style="font-size: 11.5px;">{{ $inv->date ? $inv->date->format('H:i') . ' WIB' : '' }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-main">{{ $inv->client_name }}</div>
                        @if($inv->note)
                            <div class="text-secondary small text-truncate" style="max-width: 180px; font-size: 11.5px;">{{ $inv->note }}</div>
                        @endif
                    </td>
                    <td>
                        @if($inv->client_type === 'retail')
                            <x-badge variant="info" size="sm">Retail (Grosir)</x-badge>
                        @else
                            <x-badge variant="neutral" size="sm">Orang (Eceran)</x-badge>
                        @endif
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
                        <x-button :href="route('invoices.show', $inv->id)" variant="outline" size="sm" icon="eye">
                            Detail Nota
                        </x-button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <x-empty-state 
                            icon="shopping-cart"
                            title="Tidak ada transaksi yang cocok"
                            description="Belum ada data invoice yang sesuai dengan filter atau kata kunci pencarian Anda."
                            actionLabel="Reset Pencarian"
                            :actionUrl="route('invoices.index')"
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
    {{ $invoices->links('pagination::bootstrap-5') }}
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const clientTypeFilter = document.getElementById('clientTypeFilter');
        const paymentStatusFilter = document.getElementById('paymentStatusFilter');
        const filterForm = document.getElementById('filterForm');

        const triggerSearch = debounce(() => {
            filterForm.submit();
        }, 350);

        if (searchInput) {
            searchInput.addEventListener('input', triggerSearch);
        }
        if (clientTypeFilter) {
            clientTypeFilter.addEventListener('change', () => filterForm.submit());
        }
        if (paymentStatusFilter) {
            paymentStatusFilter.addEventListener('change', () => filterForm.submit());
        }
    });
</script>
@endpush
