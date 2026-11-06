@extends('layouts.app')

@section('title', 'Beranda Utama — Prima Bangunan')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3">
    <div>
        <h1 class="page-title">Beranda Manajemen Toko</h1>
        <p class="page-subtitle">Ringkasan inventori, transaksi penjualan kasir, piutang, dan pengiriman armada material.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <x-button :href="route('invoices.create')" variant="primary" icon="shopping-cart">
            Kasir / Transaksi Baru
        </x-button>
        <x-button :href="route('invoice-suppliers.create')" variant="secondary" icon="bag-plus">
            Restock Supplier
        </x-button>
    </div>
</div>

<!-- Stat Cards Grid -->
<div class="row g-3 mb-4">
    <!-- Penjualan Hari Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <x-stat-card 
            title="PENJUALAN HARI INI"
            value="Rp {{ number_format($salesToday, 0, ',', '.') }}"
            sub="{{ date('d M Y') }}"
            icon="wallet"
            badge="Hari Ini"
            badgeVariant="info"
            iconBg="bg-primary-subtle"
            iconColor="text-primary"
        />
    </div>

    <!-- Sisa Piutang -->
    <div class="col-12 col-sm-6 col-xl-3">
        <x-stat-card 
            title="PIUTANG BELUM LUNAS"
            value="Rp {{ number_format($totalOutstanding, 0, ',', '.') }}"
            sub="Saldo belum tertagih"
            icon="credit-card"
            badge="Piutang"
            badgeVariant="warning"
            iconBg="bg-warning-subtle"
            iconColor="text-warning-emphasis"
        />
    </div>

    <!-- Tagihan Jatuh Tempo -->
    <div class="col-12 col-sm-6 col-xl-3">
        <x-stat-card 
            title="TAGIHAN JATUH TEMPO"
            value="{{ $overduePaymentsCount }} Nota"
            sub="{{ $overduePaymentsCount > 0 ? 'Perlu penagihan segera' : 'Semua tagihan lancar' }}"
            icon="clock"
            badge="{{ $overduePaymentsCount > 0 ? 'Perhatian' : 'Aman' }}"
            badgeVariant="{{ $overduePaymentsCount > 0 ? 'danger' : 'success' }}"
            iconBg="bg-danger-subtle"
            iconColor="text-danger"
        />
    </div>

    <!-- Katalog Barang -->
    <div class="col-12 col-sm-6 col-xl-3">
        <x-stat-card 
            title="KATALOG BARANG"
            value="{{ $totalItems }} Item"
            sub="{{ $outOfStockCount }} habis &bull; {{ $lowStockItems->count() }} menipis"
            icon="box"
            badge="Material"
            badgeVariant="neutral"
            iconBg="bg-info-subtle"
            iconColor="text-info-emphasis"
        />
    </div>
</div>

<!-- Soft Amber Stock Alert Banner (If Any) -->
@if($lowStockItems->count() > 0)
<div class="pb-alert pb-alert-warning mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <div class="p-2.5 rounded-circle bg-warning bg-opacity-25 text-warning-emphasis flex-shrink-0">
            <x-icon name="alert-triangle" size="22" stroke="2" />
        </div>
        <div>
            <div class="fw-bold text-main" style="font-size: 14.5px;">Peringatan Stok Menipis & Habis ({{ $lowStockItems->count() }} Barang)</div>
            <div class="text-secondary small mt-0.5" style="font-size: 12.5px;">Beberapa material persediaannya berada di bawah ambang batas aman dan disarankan segera melakukan pemesanan ulang.</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <x-button :href="route('invoice-suppliers.create')" variant="primary" size="sm" icon="plus">
            Beli Stok Supplier
        </x-button>
        <x-button :href="route('item.index')" variant="secondary" size="sm">
            Lihat Stok
        </x-button>
    </div>
</div>
@endif

<!-- Two-Column Layout: Recent Transactions & Shipments -->
<div class="row g-4">
    <!-- Transaksi Penjualan Terbaru -->
    <div class="col-12 col-xl-7">
        <div class="pb-card h-100">
            <div class="p-4 border-bottom border-light-subtle d-flex align-items-center justify-content-between" style="border-color: var(--pb-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <x-icon name="shopping-cart" size="18" class="text-primary" />
                    <h5 class="fw-semibold text-main mb-0 fs-6">Transaksi Penjualan Terbaru</h5>
                </div>
                <a href="{{ route('invoices.index') }}" class="small text-primary text-decoration-none fw-semibold d-flex align-items-center gap-1">
                    <span>Lihat Semua</span>
                    <x-icon name="chevron-right" size="14" />
                </a>
            </div>

            <div class="table-responsive">
                <table class="pb-table">
                    <thead>
                        <tr>
                            <th>No. Nota</th>
                            <th>Pelanggan</th>
                            <th>Tipe</th>
                            <th class="text-end">Grand Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $inv)
                        <tr>
                            <td class="nowrap">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="fw-semibold text-primary text-decoration-none tabular-nums">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-main">{{ $inv->client_name }}</div>
                                <div class="text-secondary small" style="font-size: 11.5px;">{{ $inv->date ? $inv->date->format('d/m/Y H:i') : '-' }}</div>
                            </td>
                            <td>
                                @if($inv->client_type === 'retail')
                                    <x-badge variant="info" size="sm">Retail / Grosir</x-badge>
                                @else
                                    <x-badge variant="neutral" size="sm">Orang / Eceran</x-badge>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-main nowrap tabular-nums">
                                Rp {{ number_format($inv->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
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
                            <td class="text-end nowrap">
                                <x-button :href="route('invoices.show', $inv->id)" variant="outline" size="sm" icon="eye">
                                    Nota
                                </x-button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state 
                                    icon="shopping-cart"
                                    title="Belum ada transaksi penjualan"
                                    description="Belum ada transaksi kasir yang tercatat hari ini."
                                    actionLabel="Mulai Transaksi Baru"
                                    :actionUrl="route('invoices.create')"
                                />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pengiriman Aktif -->
    <div class="col-12 col-xl-5">
        <div class="pb-card h-100 d-flex flex-column">
            <div class="p-4 border-bottom border-light-subtle d-flex align-items-center justify-content-between" style="border-color: var(--pb-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <x-icon name="truck" size="18" class="text-primary" />
                    <h5 class="fw-semibold text-main mb-0 fs-6">Pengiriman Aktif</h5>
                </div>
                <a href="{{ route('shipments.index') }}" class="small text-primary text-decoration-none fw-semibold d-flex align-items-center gap-1">
                    <span>Lihat Semua</span>
                    <x-icon name="chevron-right" size="14" />
                </a>
            </div>

            <div class="p-4 flex-grow-1">
                @forelse($activeShipments as $shipment)
                <div class="p-3 mb-3 rounded-3 border border-light-subtle bg-light-subtle" style="border-color: var(--pb-border) !important; background-color: var(--pb-surface-alt) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="fw-semibold text-main tabular-nums" style="font-size: 13px;">{{ $shipment->shipment_number }}</span>
                        @if($shipment->status === 'shipping')
                            <x-badge variant="info" size="sm">
                                <x-icon name="truck" size="12" class="me-1" /> Sedang Dikirim
                            </x-badge>
                        @elseif($shipment->status === 'delivered')
                            <x-badge variant="success" size="sm">Terkirim</x-badge>
                        @else
                            <x-badge variant="neutral" size="sm">Menunggu (Pending)</x-badge>
                        @endif
                    </div>
                    
                    <div class="small text-secondary mb-1" style="font-size: 12.5px;">
                        @if($shipment->type === 'outgoing')
                            <span class="text-primary fw-medium">Pengiriman ke:</span> {{ $shipment->invoice->client_name ?? 'Pelanggan' }}
                        @else
                            <span class="text-success fw-medium">Masuk dari:</span> {{ $shipment->invoiceSupplier->supplier->name ?? 'Supplier' }}
                        @endif
                    </div>

                    <div class="small text-muted text-truncate" style="font-size: 12px;">
                        <x-icon name="map-pin" size="12" class="me-1" />
                        {{ $shipment->address }}
                    </div>
                </div>
                @empty
                <x-empty-state 
                    icon="truck"
                    title="Tidak ada pengiriman aktif"
                    description="Semua jadwal pengiriman material sudah selesai atau belum ada armada yang dijadwalkan."
                />
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
