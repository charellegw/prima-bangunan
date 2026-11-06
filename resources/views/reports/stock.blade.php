@extends('layouts.app')

@section('title', 'Laporan Mutasi & Riwayat Stok — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                <i class="bi bi-arrow-left me-1"></i> Pusat Laporan
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Laporan Mutasi & Nilai Aset Stok</h4>
                <p class="text-muted small mb-0">Periode Pergerakan: {{ $dateFrom->format('d M Y') }} s/d {{ $dateTo->format('d M Y') }}</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success rounded-pill px-3 py-1 small fw-semibold shadow-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel (.csv)
            </a>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank" class="btn btn-dark rounded-pill px-3 py-1 small fw-semibold shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak / Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('reports.stock') }}" id="filterForm">
            <div class="row g-2 align-items-center">
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

                <div class="col-2 col-md-1 text-end pt-3">
                    <button type="submit" class="btn btn-sm btn-primary-custom w-100" title="Terapkan Filter">
                        <i class="bi bi-filter"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Box -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card card-custom p-3 bg-light border">
                <span class="text-muted small">Total Persediaan Fisik:</span>
                <span class="fs-4 fw-bold text-dark font-monospace mt-1">{{ number_format($totalStockUnits, 0, ',', '.') }}</span>
                <small class="text-muted">{{ $stockReportData->count() }} Jenis Material</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card card-custom p-3 bg-light border">
                <span class="text-muted small">Barang Masuk (Supplier):</span>
                <span class="fs-4 fw-bold text-success font-monospace mt-1">+{{ number_format($totalReceivedUnits, 0, ',', '.') }}</span>
                <small class="text-success"><i class="bi bi-box-arrow-in-down"></i> Masuk Gudang</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card card-custom p-3 bg-light border">
                <span class="text-muted small">Barang Keluar (Penjualan):</span>
                <span class="fs-4 fw-bold text-primary font-monospace mt-1">-{{ number_format($totalSoldUnits, 0, ',', '.') }}</span>
                <small class="text-primary"><i class="bi bi-cart-check"></i> Terjual ke Konsumen</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card card-custom p-3 bg-light border">
                <span class="text-muted small">Estimasi Nilai Aset Stok:</span>
                <span class="fs-4 fw-bold text-success font-monospace mt-1">Rp {{ number_format($totalValuation, 0, ',', '.') }}</span>
                <small class="text-muted">Total Nilai Persediaan</small>
            </div>
        </div>
    </div>

    <!-- Tabel Mutasi Stok -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Nama Barang Material</th>
                        <th>Kategori</th>
                        <th class="text-center">Masuk (Supplier)</th>
                        <th class="text-center">Keluar (Terjual)</th>
                        <th class="text-center">Sisa Stok Kini</th>
                        <th class="text-end">Harga Modal (Avg)</th>
                        <th class="text-end">Estimasi Nilai Aset</th>
                        <th class="text-end">Harga Eceran (Orang)</th>
                        <th class="text-end">Harga Grosir (Retail)</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockReportData as $idx => $row)
                    @php $item = $row['item']; @endphp
                    <tr>
                        <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark small">
                                <a href="{{ route('item.show', $item->id) }}" class="text-decoration-none text-dark hover-primary">
                                    {{ $item->name }}
                                </a>
                            </div>
                            @if($item->brand)
                                <small class="text-muted">Merek: {{ $item->brand }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border" style="font-size: 11px;">
                                {{ $item->category->name ?? 'Material Umum' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($row['qty_received'] > 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 font-monospace">
                                    +{{ $row['qty_received'] }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['qty_sold'] > 0)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 font-monospace">
                                    -{{ $row['qty_sold'] }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->stock <= 0)
                                <span class="badge bg-danger text-white px-2 py-1">Habis (0)</span>
                            @elseif($item->stock <= 10)
                                <span class="badge bg-warning text-dark px-2 py-1">Sisa {{ $item->stock }}</span>
                            @else
                                <span class="badge bg-light text-dark border px-2 py-1 fw-bold">{{ $item->stock }} unit</span>
                            @endif
                        </td>
                        <td class="text-end font-monospace small">
                            Rp {{ number_format($row['cost_price'], 0, ',', '.') }}
                        </td>
                        <td class="text-end font-monospace fw-bold text-success small">
                            Rp {{ number_format($row['valuation'], 0, ',', '.') }}
                        </td>
                        <td class="text-end font-monospace text-primary small">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </td>
                        <td class="text-end font-monospace text-success small">
                            Rp {{ number_format($item->retail_price, 0, ',', '.') }}
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('item.show', $item->id) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1" title="Lihat Detail & Supplier">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted">
                            <i class="bi bi-boxes fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Tidak ada barang material yang cocok dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
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
