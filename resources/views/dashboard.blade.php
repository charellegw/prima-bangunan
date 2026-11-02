@extends('layouts.app')

@section('title', 'Beranda Utama — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Beranda Manajemen Toko</h4>
            <p class="text-muted small mb-0">Ringkasan inventori, kasir penjualan, status pembayaran, dan pengiriman material.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('invoices.create') }}" class="btn btn-primary-custom d-flex align-items-center shadow-sm">
                <i class="bi bi-cart-plus me-2"></i> Kasir / Transaksi Baru
            </a>
            <a href="{{ route('invoice-suppliers.create') }}" class="btn btn-outline-secondary d-flex align-items-center bg-white shadow-sm">
                <i class="bi bi-box-arrow-in-down me-2"></i> Restock Supplier
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Penjualan Hari Ini -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-medium">Penjualan Hari Ini</div>
                        <div class="fs-4 fw-bold text-dark mt-1">Rp {{ number_format($salesToday, 0, ',', '.') }}</div>
                        <div class="text-success small mt-1"><i class="bi bi-calendar-check me-1"></i> {{ date('d M Y') }}</div>
                    </div>
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Piutang / Tagihan Belum Lunas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-medium">Sisa Piutang / Belum Lunas</div>
                        <div class="fs-4 fw-bold text-dark mt-1">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</div>
                        <div class="text-muted small mt-1">Total sisa yang belum tertagih</div>
                    </div>
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tagihan Jatuh Tempo -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-medium">Tagihan Jatuh Tempo</div>
                        <div class="fs-4 fw-bold {{ $overduePaymentsCount > 0 ? 'text-danger' : 'text-dark' }} mt-1">
                            {{ $overduePaymentsCount }} <span class="fs-6 fw-normal text-muted">Tagihan</span>
                        </div>
                        <div class="{{ $overduePaymentsCount > 0 ? 'text-danger fw-semibold' : 'text-muted' }} small mt-1">
                            <i class="bi bi-exclamation-octagon me-1"></i> Perlu penagihan segera
                        </div>
                    </div>
                    <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3">
                        <i class="bi bi-alarm-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Barang & Status Stok -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-medium">Katalog Barang</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $totalItems }} <span class="fs-6 fw-normal text-muted">Item</span></div>
                        <div class="small mt-1 {{ $outOfStockCount > 0 ? 'text-danger' : 'text-muted' }}">
                            {{ $outOfStockCount }} stok habis &bull; {{ $lowStockItems->count() }} stok menipis
                        </div>
                    </div>
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-3">
                        <i class="bi bi-boxes fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Alerts Section (If any) -->
    @if($lowStockItems->count() > 0)
    <div class="card card-custom mb-4 border-warning border-opacity-50">
        <div class="card-header bg-warning bg-opacity-10 border-0 py-3 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-5 me-2"></i>
                <h6 class="mb-0 fw-bold text-dark">Peringatan Stok Menipis & Habis ({{ $lowStockItems->count() }} Barang)</h6>
            </div>
            <a href="{{ route('invoice-suppliers.create') }}" class="btn btn-sm btn-warning fw-semibold px-3 py-1 rounded-pill">
                <i class="bi bi-plus-circle me-1"></i> Beli Stok Supplier
            </a>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-4">Nama Barang</th>
                        <th>Kategori</th>
                        <th>Sisa Stok</th>
                        <th>Harga Eceran</th>
                        <th>Harga Grosir (Retail)</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockItems->take(5) as $item)
                    <tr>
                        <td class="ps-4 fw-semibold text-dark">
                            <a href="{{ route('item.show', $item->id) }}" class="text-decoration-none text-dark hover-primary">
                                {{ $item->name }}
                            </a>
                        </td>
                        <td><span class="badge bg-light text-secondary border">{{ $item->category->name ?? '-' }}</span></td>
                        <td>
                            @if($item->stock <= 0)
                                <span class="badge bg-danger text-white px-2 py-1">Habis (0)</span>
                            @else
                                <span class="badge bg-warning text-dark px-2 py-1">Tersisa {{ $item->stock }}</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->retail_price, 0, ',', '.') }}</td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('item.show', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Detail & Supplier
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Transaksi Penjualan Terbaru -->
        <div class="col-12 col-xl-7">
            <div class="card card-custom h-100">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2 text-primary"></i> Transaksi Penjualan Terbaru</h6>
                    <a href="{{ route('invoices.index') }}" class="small text-primary text-decoration-none fw-semibold">Lihat Semua &rarr;</a>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-secondary">
                            <tr>
                                <th class="ps-4">No. Invoice</th>
                                <th>Pelanggan</th>
                                <th>Tipe</th>
                                <th>Grand Total</th>
                                <th>Status Pembayaran</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $inv)
                            <tr>
                                <td class="ps-4 fw-bold font-monospace text-primary">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="text-decoration-none">
                                        {{ $inv->invoice_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $inv->client_name }}</div>
                                    <small class="text-muted">{{ $inv->date->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    @if($inv->client_type === 'retail')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Retail / Toko</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border">Orang / Eceran</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
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
                                <td class="pe-4 text-end">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-light border rounded-pill">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi penjualan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pengiriman Terjadwal & Berjalan -->
        <div class="col-12 col-xl-5">
            <div class="card card-custom h-100">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-truck me-2 text-primary"></i> Pengiriman Aktif</h6>
                    <a href="{{ route('shipments.index') }}" class="small text-primary text-decoration-none fw-semibold">Lihat Semua &rarr;</a>
                </div>
                <div class="card-body p-3">
                    @forelse($activeShipments as $shipment)
                    <div class="p-3 mb-2 rounded-3 border bg-light">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold font-monospace text-dark">{{ $shipment->shipment_number }}</span>
                            @if($shipment->status === 'shipping')
                                <span class="badge bg-warning text-dark"><i class="bi bi-truck me-1"></i> Sedang Dikirim</span>
                            @else
                                <span class="badge bg-secondary text-white"><i class="bi bi-hourglass me-1"></i> Pending</span>
                            @endif
                        </div>
                        <div class="small text-muted mb-1">
                            @if($shipment->type === 'outgoing')
                                <i class="bi bi-arrow-up-right text-primary me-1"></i> Kirim ke: <strong>{{ $shipment->invoice->client_name ?? 'Pelanggan' }}</strong>
                            @else
                                <i class="bi bi-arrow-down-left text-success me-1"></i> Kiriman Masuk: <strong>{{ $shipment->invoiceSupplier->supplier->name ?? 'Supplier' }}</strong>
                            @endif
                        </div>
                        <div class="small text-secondary text-truncate">
                            <i class="bi bi-geo-alt me-1"></i> {{ $shipment->address }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check2-circle fs-1 text-secondary opacity-50 d-block mb-2"></i>
                        Tidak ada pengiriman yang sedang pending atau jalan.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
