@extends('layouts.app')

@section('title', 'Pusat Laporan & Rekapitulasi — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Pusat Laporan & Analitik Toko</h4>
            <p class="text-muted small mb-0">Rekapitulasi berkala penjualan, pembelian stok supplier, mutasi persediaan barang, dan audit piutang/hutang.</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill font-monospace">
                <i class="bi bi-calendar3 me-1"></i> Periode: {{ date('F Y') }}
            </span>
        </div>
    </div>

    <!-- Ringkasan Keuangan & Stok Bulan Ini -->
    <div class="row g-3 mb-4">
        <!-- Omzet Penjualan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100 border-start border-4 border-primary">
                <div class="text-muted small">Omzet Penjualan (Bulan Ini)</div>
                <div class="fs-4 fw-bold text-dark mt-1">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</div>
                <div class="text-primary small mt-1 font-monospace">{{ $salesCountThisMonth }} Transaksi Penjualan</div>
            </div>
        </div>

        <!-- Pembelian Stok Supplier -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100 border-start border-4 border-info">
                <div class="text-muted small">Belanja Supplier (Bulan Ini)</div>
                <div class="fs-4 fw-bold text-dark mt-1">Rp {{ number_format($purchasesThisMonth, 0, ',', '.') }}</div>
                <div class="text-info small mt-1 font-monospace">{{ $purchasesCountThisMonth }} Faktur Masuk</div>
            </div>
        </div>

        <!-- Valuasi Aset Stok -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100 border-start border-4 border-success">
                <div class="text-muted small">Estimasi Nilai Aset Stok</div>
                <div class="fs-4 fw-bold text-success mt-1">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</div>
                <div class="text-muted small mt-1">{{ $totalItemsCount }} Jenis Barang Material</div>
            </div>
        </div>

        <!-- Piutang & Jatuh Tempo -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100 border-start border-4 border-danger">
                <div class="text-muted small">Sisa Piutang Belum Lunas</div>
                <div class="fs-4 fw-bold text-danger mt-1">Rp {{ number_format($totalReceivables, 0, ',', '.') }}</div>
                <div class="text-danger small mt-1 fw-semibold"><i class="bi bi-alarm-fill me-1"></i> {{ $overdueCount }} Tagihan Lewat Tempo</div>
            </div>
        </div>
    </div>

    <!-- Pilihan Modul Laporan (4 Menu Utama) -->
    <div class="row g-4">
        <!-- 1. Laporan Penjualan -->
        <div class="col-12 col-md-6">
            <div class="card card-custom h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 me-3">
                            <i class="bi bi-receipt-cutoff fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Laporan Penjualan & Kasir</h5>
                            <span class="badge bg-light text-secondary border">Harian &bull; Bulanan &bull; Custom</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-4">
                        Pantau seluruh transaksi nota penjualan, omzet harian/bulanan, perbandingan omzet pelanggan eceran vs grosir, serta status pelunasan transaksi kasir.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-3 border-top">
                    <a href="{{ route('reports.sales') }}" class="btn btn-primary-custom flex-grow-1">
                        <i class="bi bi-table me-1"></i> Buka Laporan Penjualan
                    </a>
                    <a href="{{ route('reports.sales', ['export' => 'excel']) }}" class="btn btn-outline-success" title="Export Excel">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Laporan Pembelian Supplier -->
        <div class="col-12 col-md-6">
            <div class="card card-custom h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-info bg-opacity-10 text-info p-3 me-3">
                            <i class="bi bi-bag-check-fill fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Laporan Pembelian Supplier</h5>
                            <span class="badge bg-light text-secondary border">Pemasok &bull; Pengeluaran Restock</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-4">
                        Audit belanja material ke distributor semen, cat, pipa, besi, dan pompa. Pantau total uang keluar dan sisa hutang dagang toko yang belum diselesaikan.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-3 border-top">
                    <a href="{{ route('reports.purchases') }}" class="btn btn-primary-custom flex-grow-1">
                        <i class="bi bi-table me-1"></i> Buka Laporan Pembelian
                    </a>
                    <a href="{{ route('reports.purchases', ['export' => 'excel']) }}" class="btn btn-outline-success" title="Export Excel">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. Laporan Mutasi & Riwayat Stok -->
        <div class="col-12 col-md-6">
            <div class="card card-custom h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 me-3">
                            <i class="bi bi-boxes fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Laporan Riwayat & Mutasi Stok</h5>
                            <span class="badge bg-light text-secondary border">Kartu Stok &bull; Nilai Aset Fisik</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-4">
                        Rekapitulasi pergerakan barang: stok awal, jumlah unit masuk dari supplier, unit terjual ke konsumen, sisa stok akhir gudang, dan valuasi aset persediaan toko.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-3 border-top">
                    <a href="{{ route('reports.stock') }}" class="btn btn-primary-custom flex-grow-1">
                        <i class="bi bi-table me-1"></i> Buka Mutasi Stok
                    </a>
                    <a href="{{ route('reports.stock', ['export' => 'excel']) }}" class="btn btn-outline-success" title="Export Excel">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. Laporan Piutang & Hutang (Aging Report) -->
        <div class="col-12 col-md-6">
            <div class="card card-custom h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3 me-3">
                            <i class="bi bi-clock-history fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Laporan Piutang & Hutang Dagang</h5>
                            <span class="badge bg-light text-secondary border">Aging Schedule &bull; Jatuh Tempo</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-4">
                        Daftar lengkap tagihan konsumen (bon/kredit) yang belum lunas dan hutang toko ke supplier. Dilengkapi status deteksi jatuh tempo untuk penagihan prioritas.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-3 border-top">
                    <a href="{{ route('reports.debts') }}" class="btn btn-primary-custom flex-grow-1">
                        <i class="bi bi-table me-1"></i> Buka Laporan Piutang & Hutang
                    </a>
                    <a href="{{ route('reports.debts', ['export' => 'excel']) }}" class="btn btn-outline-success" title="Export Excel">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
