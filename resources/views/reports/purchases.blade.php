@extends('layouts.app')

@section('title', 'Laporan Pembelian Supplier — Prima Bangunan')

@section('content')
<div class="d-flex flex-column gap-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <x-button href="{{ route('reports.index') }}" variant="secondary" size="sm">
                <x-icon name="arrow-left" style="width: 14px; height: 14px;" class="me-1" /> Pusat Laporan
            </x-button>
            <div>
                <h1 class="page-title mb-0 fs-4">Laporan Pembelian Supplier</h1>
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
        <form method="GET" action="{{ route('reports.purchases') }}" id="filterForm">
            <div class="row g-2 align-items-end">
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
        <div class="col-12 col-md-4">
            <x-card class="p-3">
                <span class="text-muted small">Total Pembelian Material:</span>
                <div class="fs-4 fw-bold text-dark tabular-nums mt-1">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</div>
                <small class="text-muted">{{ $invoices->total() }} Faktur Masuk</small>
            </x-card>
        </div>
        <div class="col-12 col-md-4">
            <x-card class="p-3">
                <span class="text-muted small">Total Kas Keluar (Terbayar):</span>
                <div class="fs-4 fw-bold text-success tabular-nums mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <small class="text-success">Sudah dilunasi ke supplier</small>
            </x-card>
        </div>
        <div class="col-12 col-md-4">
            <x-card class="p-3">
                <span class="text-muted small">Sisa Hutang Toko ke Supplier:</span>
                <div class="fs-4 fw-bold text-danger tabular-nums mt-1">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
                <small class="text-danger">Kewajiban pelunasan</small>
            </x-card>
        </div>
    </div>

    <!-- Tabel Data Laporan -->
    <x-card :flush="true" class="p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table pb-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No. Faktur</th>
                        <th>Waktu</th>
                        <th>Supplier & Lokasi</th>
                        <th>Material Dipesan</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Terbayar</th>
                        <th class="text-end">Sisa Hutang</th>
                        <th>Status</th>
                        <th>Jatuh Tempo</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="ps-4 nowrap">
                            <a href="{{ route('invoice-suppliers.show', $inv->id) }}" class="fw-semibold text-primary text-decoration-none tabular-nums">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td class="nowrap">
                            <span class="text-dark small fw-medium tabular-nums">{{ $inv->date->format('d/m/Y') }}</span>
                            <span class="text-muted small d-block tabular-nums">{{ $inv->date->format('H:i') }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $inv->supplier->name ?? 'Supplier' }}</div>
                            <small class="text-muted">{{ $inv->supplier->location ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="small text-muted text-truncate" style="max-width: 220px;" title="{{ $inv->invoiceSupplierDetails->map(fn($d) => ($d->item->name ?? 'Barang') . ' (' . $d->quantity . ')')->join(', ') }}">
                                {{ $inv->invoiceSupplierDetails->map(fn($d) => ($d->item->name ?? 'Barang') . ' (' . $d->quantity . ')')->join(', ') }}
                            </div>
                        </td>
                        <td class="text-end tabular-nums fw-bold text-dark nowrap">
                            Rp {{ number_format($inv->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="text-end tabular-nums fw-medium text-success nowrap">
                            Rp {{ number_format($inv->payment->total_paid ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-end tabular-nums fw-bold text-danger nowrap">
                            Rp {{ number_format($inv->payment->remaining_balance ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="nowrap">
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
                        <td class="small nowrap tabular-nums">
                            {{ $inv->payment->deadline ? $inv->payment->deadline->format('d/m/Y') : '-' }}
                        </td>
                        <td class="pe-4 text-end nowrap">
                            <x-button href="{{ route('invoice-suppliers.show', $inv->id) }}" variant="ghost" size="sm" title="Buka Faktur">
                                <x-icon name="eye" style="width: 14px; height: 14px;" />
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <x-empty-state
                                icon="shopping-cart"
                                title="Tidak ada faktur pembelian"
                                description="Tidak ada faktur pembelian supplier yang cocok pada rentang kriteria ini."
                            />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
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
