@extends('layouts.app')

@section('title', 'Laporan Pembelian Supplier — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                <i class="bi bi-arrow-left me-1"></i> Pusat Laporan
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Laporan Pembelian Stok Supplier</h4>
                <p class="text-muted small mb-0">Periode: {{ $dateFrom->format('d M Y') }} s/d {{ $dateTo->format('d M Y') }}</p>
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
        <form method="GET" action="{{ route('reports.purchases') }}" id="filterForm">
            <div class="row g-2 align-items-center">
                <!-- Periode Cepat -->
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Pilihan Periode:</label>
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

                <!-- Filter Supplier -->
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Pilih Supplier:</label>
                    <select name="supplier_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Hutang -->
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Status Hutang:</label>
                    <select name="payment_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('payment_status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="sebagian" {{ request('payment_status') === 'sebagian' ? 'selected' : '' }}>Sebagian (DP/Tempo)</option>
                        <option value="belum_bayar" {{ request('payment_status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="jatuh_tempo" {{ request('payment_status') === 'jatuh_tempo' ? 'selected' : '' }}>Jatuh Tempo</option>
                    </select>
                </div>

                <!-- Cari -->
                <div class="col-10 col-md-2">
                    <label class="form-label small text-muted mb-1" style="font-size: 11px;">Cari Faktur / Supplier:</label>
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
        <div class="col-12 col-md-4">
            <div class="card card-custom p-3 bg-light border">
                <span class="text-muted small">Total Pembelian Material:</span>
                <span class="fs-4 fw-bold text-dark font-monospace mt-1">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</span>
                <small class="text-muted">{{ $invoices->total() }} Faktur Masuk</small>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-custom p-3 bg-light border">
                <span class="text-muted small">Total Sudah Dibayar ke Supplier:</span>
                <span class="fs-4 fw-bold text-success font-monospace mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                <small class="text-success"><i class="bi bi-check2-circle"></i> Kas Keluar Toko</small>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-custom p-3 bg-light border">
                <span class="text-muted small">Sisa Hutang Toko ke Supplier:</span>
                <span class="fs-4 fw-bold text-danger font-monospace mt-1">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</span>
                <small class="text-danger"><i class="bi bi-clock-history"></i> Kewajiban Pelunasan</small>
            </div>
        </div>
    </div>

    <!-- Tabel Data Laporan -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-4">No. Faktur</th>
                        <th>Waktu Pembelian</th>
                        <th>Nama Supplier & Lokasi</th>
                        <th>Material yang Dipesan</th>
                        <th class="text-end">Total Pembelian</th>
                        <th class="text-end">Sudah Dibayar</th>
                        <th class="text-end">Sisa Hutang</th>
                        <th>Status</th>
                        <th>Jatuh Tempo</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('invoice-suppliers.show', $inv->id) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td>
                            <div class="text-dark small fw-medium">{{ $inv->date->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $inv->date->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">{{ $inv->supplier->name ?? 'Supplier' }}</div>
                            <small class="text-muted">{{ $inv->supplier->location ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="small text-muted text-truncate" style="max-width: 220px;" title="{{ $inv->invoiceSupplierDetails->map(fn($d) => ($d->item->name ?? 'Barang') . ' (' . $d->quantity . ')')->join(', ') }}">
                                {{ $inv->invoiceSupplierDetails->map(fn($d) => ($d->item->name ?? 'Barang') . ' (' . $d->quantity . ')')->join(', ') }}
                            </div>
                        </td>
                        <td class="text-end font-monospace fw-bold text-dark">
                            Rp {{ number_format($inv->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="text-end font-monospace fw-semibold text-success">
                            Rp {{ number_format($inv->payment->total_paid ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-end font-monospace fw-bold text-danger">
                            Rp {{ number_format($inv->payment->remaining_balance ?? 0, 0, ',', '.') }}
                        </td>
                        <td>
                            @php $status = $inv->payment->status ?? 'belum_bayar'; @endphp
                            @if($status === 'lunas')
                                <span class="badge-lunas" style="font-size: 11px;">Lunas</span>
                            @elseif($status === 'sebagian')
                                <span class="badge-sebagian" style="font-size: 11px;">Sebagian</span>
                            @elseif($status === 'jatuh_tempo')
                                <span class="badge-jatuh-tempo" style="font-size: 11px;">Jatuh Tempo</span>
                            @else
                                <span class="badge-belum-bayar" style="font-size: 11px;">Belum Bayar</span>
                            @endif
                        </td>
                        <td class="small">
                            {{ $inv->payment->deadline ? $inv->payment->deadline->format('d/m/Y') : '-' }}
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('invoice-suppliers.show', $inv->id) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Buka Faktur">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-bag-x fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Tidak ada faktur pembelian supplier pada kriteria ini.
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
