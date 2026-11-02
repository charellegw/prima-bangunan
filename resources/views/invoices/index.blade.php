@extends('layouts.app')

@section('title', 'Daftar Invoice Penjualan — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Invoice & Penjualan Kasir</h4>
            <p class="text-muted small mb-0">Riwayat transaksi penjualan material ke pelanggan umum (orang) dan toko rekanan (retail).</p>
        </div>
        <div>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary-custom d-flex align-items-center shadow-sm">
                <i class="bi bi-cart-plus me-2"></i> Transaksi Kasir Baru
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('invoices.index') }}" id="filterForm">
            <div class="row g-2 align-items-center">
                <!-- Search Input with Debounce -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text"
                               name="q"
                               id="searchInput"
                               value="{{ request('q') }}"
                               placeholder="Cari nomor nota, nama pelanggan..."
                               class="form-control border-start-0 ps-0"
                               autocomplete="off">
                    </div>
                </div>

                <!-- Filter Tipe Pelanggan -->
                <div class="col-6 col-md-2">
                    <select name="client_type" id="clientTypeFilter" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="orang" {{ request('client_type') === 'orang' ? 'selected' : '' }}>Orang (Eceran)</option>
                        <option value="retail" {{ request('client_type') === 'retail' ? 'selected' : '' }}>Retail (Toko)</option>
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
                <div class="col-2 col-md-1 text-end">
                    @if(request()->anyFilled(['q', 'client_type', 'payment_status', 'date_from', 'date_to']))
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary w-100" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Invoice -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-4">No. Invoice</th>
                        <th>Tanggal Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Tipe Harga</th>
                        <th>Total Belanja</th>
                        <th>Status Pembayaran</th>
                        <th>Sisa Tagihan</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('invoices.show', $inv->id) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td>
                            <div class="text-dark fw-medium">{{ $inv->date->format('d M Y') }}</div>
                            <small class="text-muted">{{ $inv->date->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $inv->client_name }}</div>
                            @if($inv->note)
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 180px;">{{ $inv->note }}</small>
                            @endif
                        </td>
                        <td>
                            @if($inv->client_type === 'retail')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Retail (Grosir)</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Orang (Eceran)</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-dark fs-6">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                        </td>
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
                        <td>
                            @php $remaining = $inv->payment->remaining_balance ?? 0; @endphp
                            @if($remaining > 0)
                                <span class="text-danger fw-bold">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                            @else
                                <span class="text-success small"><i class="bi bi-check2-all"></i> Terbayar</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold">
                                <i class="bi bi-receipt me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Tidak ada data invoice penjualan yang cocok.
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
