@extends('layouts.app')

@section('title', 'Pusat Laporan & Rekapitulasi — Prima Bangunan')

@section('content')
<div class="d-flex flex-column gap-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="page-title mb-1">Pusat Laporan & Rekapitulasi</h1>
            <p class="page-subtitle mb-0">Analitik performa penjualan kasir, audit pembelian stok, mutasi gudang, dan umur piutang/hutang</p>
        </div>
        <div>
            <x-badge variant="neutral" class="px-3 py-2">
                <x-icon name="clock" style="width: 14px; height: 14px;" class="me-1.5" /> Periode: {{ date('F Y') }}
            </x-badge>
        </div>
    </div>

    <!-- Ringkasan Keuangan & Stok Bulan Ini (4 Stat Cards) -->
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card
                title="Omzet Penjualan (Bulan Ini)"
                :value="'Rp ' . number_format($salesThisMonth, 0, ',', '.') . ''"
                icon="credit-card"
                color="primary"
                :description="$salesCountThisMonth . ' transaksi penjualan'"
            />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card
                title="Belanja Supplier (Bulan Ini)"
                :value="'Rp ' . number_format($purchasesThisMonth, 0, ',', '.') . ''"
                icon="shopping-cart"
                color="info"
                :description="$purchasesCountThisMonth . ' faktur masuk'"
            />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card
                title="Valuasi Aset Stok"
                :value="'Rp ' . number_format($totalStockValue, 0, ',', '.') . ''"
                icon="box"
                color="success"
                :description="$totalItemsCount . ' jenis barang material'"
            />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card
                title="Sisa Piutang Belum Lunas"
                :value="'Rp ' . number_format($totalReceivables, 0, ',', '.') . ''"
                icon="alert-triangle"
                color="danger"
                :description="$overdueCount . ' tagihan lewat tempo'"
            />
        </div>
    </div>

    <!-- Pilihan 4 Modul Laporan Utama -->
    <div class="row g-4">
        <!-- 1. Laporan Penjualan -->
        <div class="col-12 col-md-6">
            <x-card class="h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-wrapper primary me-3" style="width: 44px; height: 44px;">
                            <x-icon name="credit-card" style="width: 22px; height: 22px;" />
                        </div>
                        <div>
                            <h2 class="h5 fw-bold text-dark mb-1">Laporan Penjualan & Kasir</h2>
                            <x-badge variant="neutral">Harian &bull; Bulanan &bull; Custom</x-badge>
                        </div>
                    </div>
                    <p class="text-secondary small mb-4">
                        Pantau seluruh transaksi nota kasir, omzet berkala, perbandingan pembeli eceran vs grosir retail, dan status pelunasan bon.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-3 border-top" style="border-color: var(--pb-border) !important;">
                    <x-button href="{{ route('reports.sales') }}" variant="primary" class="flex-grow-1">
                        Buka Laporan Penjualan
                    </x-button>
                    <x-button href="{{ route('reports.sales', ['export' => 'excel']) }}" variant="outline" title="Export Excel">
                        <x-icon name="download" style="width: 15px; height: 15px;" />
                    </x-button>
                </div>
            </x-card>
        </div>

        <!-- 2. Laporan Pembelian Supplier -->
        <div class="col-12 col-md-6">
            <x-card class="h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-wrapper info me-3" style="width: 44px; height: 44px;">
                            <x-icon name="shopping-cart" style="width: 22px; height: 22px;" />
                        </div>
                        <div>
                            <h2 class="h5 fw-bold text-dark mb-1">Laporan Pembelian Supplier</h2>
                            <x-badge variant="neutral">Distributor &bull; Pengeluaran Restock</x-badge>
                        </div>
                    </div>
                    <p class="text-secondary small mb-4">
                        Audit belanja material ke pabrik semen, cat, besi, pipa, dan pompa. Pantau total pengeluaran dan hutang dagang toko.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-3 border-top" style="border-color: var(--pb-border) !important;">
                    <x-button href="{{ route('reports.purchases') }}" variant="primary" class="flex-grow-1">
                        Buka Laporan Pembelian
                    </x-button>
                    <x-button href="{{ route('reports.purchases', ['export' => 'excel']) }}" variant="outline" title="Export Excel">
                        <x-icon name="download" style="width: 15px; height: 15px;" />
                    </x-button>
                </div>
            </x-card>
        </div>

        <!-- 3. Laporan Mutasi & Riwayat Stok -->
        <div class="col-12 col-md-6">
            <x-card class="h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-wrapper success me-3" style="width: 44px; height: 44px;">
                            <x-icon name="box" style="width: 22px; height: 22px;" />
                        </div>
                        <div>
                            <h2 class="h5 fw-bold text-dark mb-1">Laporan Mutasi & Nilai Stok</h2>
                            <x-badge variant="neutral">Kartu Stok &bull; Nilai Aset Fisik</x-badge>
                        </div>
                    </div>
                    <p class="text-secondary small mb-4">
                        Rekapitulasi pergerakan barang: stok awal, unit masuk dari supplier, unit terjual, sisa stok akhir gudang, dan valuasi aset persediaan.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-3 border-top" style="border-color: var(--pb-border) !important;">
                    <x-button href="{{ route('reports.stock') }}" variant="primary" class="flex-grow-1">
                        Buka Mutasi Stok
                    </x-button>
                    <x-button href="{{ route('reports.stock', ['export' => 'excel']) }}" variant="outline" title="Export Excel">
                        <x-icon name="download" style="width: 15px; height: 15px;" />
                    </x-button>
                </div>
            </x-card>
        </div>

        <!-- 4. Laporan Piutang & Hutang (Aging Report) -->
        <div class="col-12 col-md-6">
            <x-card class="h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-wrapper danger me-3" style="width: 44px; height: 44px;">
                            <x-icon name="clock" style="width: 22px; height: 22px;" />
                        </div>
                        <div>
                            <h2 class="h5 fw-bold text-dark mb-1">Laporan Piutang & Hutang Dagang</h2>
                            <x-badge variant="neutral">Buku Aging &bull; Jatuh Tempo</x-badge>
                        </div>
                    </div>
                    <p class="text-secondary small mb-4">
                        Daftar lengkap tagihan bon konsumen yang belum lunas dan hutang toko ke supplier dengan deteksi keterlambatan jatuh tempo.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-3 border-top" style="border-color: var(--pb-border) !important;">
                    <x-button href="{{ route('reports.debts') }}" variant="primary" class="flex-grow-1">
                        Buka Laporan Piutang & Hutang
                    </x-button>
                    <x-button href="{{ route('reports.debts', ['export' => 'excel']) }}" variant="outline" title="Export Excel">
                        <x-icon name="download" style="width: 15px; height: 15px;" />
                    </x-button>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
