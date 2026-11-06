@extends('layouts.app')

@section('title', 'Laporan Mutasi & Riwayat Stok — Prima Bangunan')

@section('content')
<div class="d-flex flex-column gap-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <x-button href="{{ route('reports.index') }}" variant="secondary" size="sm">
                <x-icon name="arrow-left" style="width: 14px; height: 14px;" class="me-1" /> Pusat Laporan
            </x-button>
            <div>
                <h1 class="page-title mb-0 fs-4">Laporan Mutasi & Nilai Stok</h1>
                <p class="page-subtitle mb-0 small">Periode: {{ $dateFrom->format('d M Y') }} s/d {{ $dateTo->format('d M Y') }}</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <x-button href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" variant="outline" size="sm">
                <x-icon name="download" style="width: 14px; height: 14px;" class="me-1" /> Export Excel
            </x-button>
            <x-button href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank" variant="secondary" size="sm">
                <x-icon name="printer" style="width: 14px; height: 14px;" class="me-1" /> Cetak / PDF
            </x-button>
        </div>
    </div>

    <!-- Filter Card -->
    <x-card :flush="true" class="p-3">
        <form method="GET" action="{{ route('reports.stock') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Periode Cepat -->
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Periode Mutasi:</label>
                    <select name="period" id="periodSelect" class="form-select form-select-sm" onchange="toggleCustomDates(this.value)">
                        <option value="this_month" {{ request('period', 'this_month') === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="this_week" {{ request('period') === 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="last_month" {{ request('period') === 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                        <option value="this_year" {{ request('period') === 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Kustom Tanggal</option>
                    </select>
                </div>

                <!-- Custom Tanggal -->
                <div class="col-6 col-md-2" id="boxDateFrom" style="{{ request('period') === 'custom' ? '' : 'display:none;' }}">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Mulai Tanggal:</label>
                    <input type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2" id="boxDateTo" style="{{ request('period') === 'custom' ? '' : 'display:none;' }}">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Sampai Tanggal:</label>
                    <input type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}" class="form-control form-control-sm">
                </div>

                <!-- Filter Kategori -->
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Kategori:</label>
                    <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Stok -->
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Kondisi Stok:</label>
                    <select name="stock_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Stok</option>
                        <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Tersedia (> 10)</option>
                        <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Menipis (1 - 10)</option>
                        <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Habis (0)</option>
                    </select>
                </div>

                <!-- Cari -->
                <div class="col-10 col-md-2">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Cari Nama / Merek:</label>
                    <input type="text" name="q" id="searchInput" value="{{ request('q') }}" placeholder="Cari..." class="form-control form-control-sm" autocomplete="off">
                </div>

                <div class="col-2 col-md-1 text-end">
                    <x-button type="submit" variant="secondary" size="sm" class="w-100" title="Terapkan Filter">
                        <x-icon name="search" style="width: 14px; height: 14px;" />
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Summary Box -->
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-md-3">
            <x-card class="p-3">
                <span class="text-muted small">Total Fisik Persediaan:</span>
                <div class="fs-4 fw-bold text-dark tabular-nums mt-1">{{ number_format($totalStockUnits, 0, ',', '.') }} unit</div>
                <small class="text-muted">{{ $stockReportData->count() }} Jenis Material</small>
            </x-card>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <x-card class="p-3">
                <span class="text-muted small">Barang Masuk (Supplier):</span>
                <div class="fs-4 fw-bold text-success tabular-nums mt-1">+{{ number_format($totalReceivedUnits, 0, ',', '.') }}</div>
                <small class="text-success">Unit restock gudang</small>
            </x-card>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <x-card class="p-3">
                <span class="text-muted small">Barang Keluar (Terjual):</span>
                <div class="fs-4 fw-bold text-primary tabular-nums mt-1">-{{ number_format($totalSoldUnits, 0, ',', '.') }}</div>
                <small class="text-primary">Terjual ke konsumen</small>
            </x-card>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <x-card class="p-3">
                <span class="text-muted small">Estimasi Valuasi Aset:</span>
                <div class="fs-4 fw-bold text-success tabular-nums mt-1">Rp {{ number_format($totalValuation, 0, ',', '.') }}</div>
                <small class="text-muted">Total nilai persediaan</small>
            </x-card>
        </div>
    </div>

    <!-- Tabel Mutasi Stok -->
    <x-card :flush="true" class="p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table pb-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Barang Material</th>
                        <th>Kategori</th>
                        <th class="text-center">Masuk (Beli)</th>
                        <th class="text-center">Keluar (Jual)</th>
                        <th class="text-center">Sisa Stok</th>
                        <th class="text-end">Modal (Avg)</th>
                        <th class="text-end">Nilai Aset</th>
                        <th class="text-end">Harga Eceran</th>
                        <th class="text-end">Harga Grosir</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockReportData as $idx => $row)
                    @php $item = $row['item']; @endphp
                    <tr>
                        <td class="ps-4 text-muted small tabular-nums">{{ $idx + 1 }}</td>
                        <td>
                            <div class="fw-semibold text-dark small">
                                <a href="{{ route('item.show', $item->id) }}" class="text-decoration-none text-dark hover-primary">
                                    {{ $item->name }}
                                </a>
                            </div>
                            @if($item->brand)
                                <small class="text-muted">{{ $item->brand }}</small>
                            @endif
                        </td>
                        <td class="nowrap">
                            <x-badge variant="neutral">{{ $item->category->name ?? 'Material Umum' }}</x-badge>
                        </td>
                        <td class="text-center nowrap">
                            @if($row['qty_received'] > 0)
                                <x-badge variant="success" class="tabular-nums">+{{ $row['qty_received'] }}</x-badge>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center nowrap">
                            @if($row['qty_sold'] > 0)
                                <x-badge variant="info" class="tabular-nums">-{{ $row['qty_sold'] }}</x-badge>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center nowrap">
                            @if($item->stock <= 0)
                                <x-badge variant="danger">Habis (0)</x-badge>
                            @elseif($item->stock <= 10)
                                <x-badge variant="warning">Sisa {{ $item->stock }}</x-badge>
                            @else
                                <x-badge variant="neutral" class="fw-semibold">{{ $item->stock }} unit</x-badge>
                            @endif
                        </td>
                        <td class="text-end tabular-nums small text-secondary nowrap">
                            Rp {{ number_format($row['cost_price'], 0, ',', '.') }}
                        </td>
                        <td class="text-end tabular-nums fw-bold text-success small nowrap">
                            Rp {{ number_format($row['valuation'], 0, ',', '.') }}
                        </td>
                        <td class="text-end tabular-nums text-primary small nowrap">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </td>
                        <td class="text-end tabular-nums text-success small nowrap">
                            Rp {{ number_format($item->retail_price, 0, ',', '.') }}
                        </td>
                        <td class="pe-4 text-end nowrap">
                            <x-button href="{{ route('item.show', $item->id) }}" variant="ghost" size="sm" title="Lihat Detail & Supplier">
                                <x-icon name="eye" style="width: 14px; height: 14px;" />
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <x-empty-state
                                icon="box"
                                title="Tidak ada barang material"
                                description="Tidak ada barang material yang cocok dengan filter pencarian stok Anda."
                            />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
    function toggleCustomDates(val) {
        const from = document.getElementById('boxDateFrom');
        const to = document.getElementById('boxDateTo');
        if (val === 'custom') {
            from.style.display = 'block';
            to.style.display = 'block';
        } else {
            from.style.display = 'none';
            to.style.display = 'none';
            document.getElementById('filterForm').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(() => filterForm.submit(), 350));
        }
    });
</script>
@endpush
