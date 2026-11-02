@extends('layouts.app')

@section('title', 'Pembayaran & Tagihan — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Manajemen Pembayaran, Piutang & Hutang</h4>
            <p class="text-muted small mb-0">Pantau seluruh tagihan penjualan konsumen dan kewajiban hutang dagang supplier secara real-time.</p>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 border-start border-4 border-danger">
                <div class="text-muted small">Tagihan Jatuh Tempo</div>
                <div class="fs-4 fw-bold text-danger mt-1">{{ $totalJatuhTempo }}</div>
                <div class="small text-danger"><i class="bi bi-alarm-fill me-1"></i> Lewat batas waktu</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 border-start border-4 border-warning">
                <div class="text-muted small">Dibayar Sebagian (DP)</div>
                <div class="fs-4 fw-bold text-warning-emphasis mt-1">{{ $totalSebagian }}</div>
                <div class="small text-muted"><i class="bi bi-hourglass-split me-1"></i> Sedang mencicil</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 border-start border-4 border-secondary">
                <div class="text-muted small">Belum Dibayar</div>
                <div class="fs-4 fw-bold text-dark mt-1">{{ $totalBelumBayar }}</div>
                <div class="small text-muted"><i class="bi bi-clock me-1"></i> Belum ada pembayaran</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 border-start border-4 border-success">
                <div class="text-muted small">Lunas Sepenuhnya</div>
                <div class="fs-4 fw-bold text-success mt-1">{{ $totalLunas }}</div>
                <div class="small text-success"><i class="bi bi-check2-circle me-1"></i> Selesai lunas</div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card card-custom p-3 mb-4">
        <form method="GET" action="{{ route('payments.index') }}" id="filterForm">
            <div class="row g-2 align-items-center">
                <!-- Search Input with Debounce -->
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text"
                               name="q"
                               id="searchInput"
                               value="{{ request('q') }}"
                               placeholder="Cari no. invoice, nama pelanggan, nama supplier..."
                               class="form-control border-start-0 ps-0"
                               autocomplete="off">
                    </div>
                </div>

                <!-- Filter Tipe Transaksi -->
                <div class="col-6 col-md-3">
                    <select name="type" id="typeFilter" class="form-select">
                        <option value="">Semua Tagihan (Penjualan & Supplier)</option>
                        <option value="sales" {{ request('type') === 'sales' ? 'selected' : '' }}>Piutang Penjualan (Customer)</option>
                        <option value="supplier" {{ request('type') === 'supplier' ? 'selected' : '' }}>Hutang Dagang (Supplier)</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="col-6 col-md-3">
                    <select name="status" id="statusFilter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="jatuh_tempo" {{ request('status') === 'jatuh_tempo' ? 'selected' : '' }}>Jatuh Tempo</option>
                        <option value="sebagian" {{ request('status') === 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                        <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="col-12 col-md-1 text-end">
                    @if(request()->anyFilled(['q', 'type', 'status']))
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary w-100" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Pembayaran -->
    <div class="card card-custom p-0 overflow-hidden">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-4">No. Dokumen</th>
                        <th>Jenis Transaksi</th>
                        <th>Pihak Terkait</th>
                        <th>Total Tagihan</th>
                        <th>Sudah Dibayar</th>
                        <th>Sisa Tagihan</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $pay)
                    <tr>
                        <td class="ps-4">
                            @if($pay->invoice)
                                <a href="{{ route('invoices.show', $pay->invoice->id) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                    {{ $pay->invoice->invoice_number }}
                                </a>
                            @elseif($pay->invoiceSupplier)
                                <a href="{{ route('invoice-suppliers.show', $pay->invoiceSupplier->id) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                    {{ $pay->invoiceSupplier->invoice_number }}
                                </a>
                            @else
                                <span class="font-monospace text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($pay->invoice)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i class="bi bi-cart-check me-1"></i> Penjualan (Piutang)
                                </span>
                            @elseif($pay->invoiceSupplier)
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                    <i class="bi bi-box-arrow-in-down me-1"></i> Supplier (Hutang)
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($pay->invoice)
                                <div class="fw-bold text-dark">{{ $pay->invoice->client_name }}</div>
                                <small class="text-muted">Pelanggan {{ $pay->invoice->client_type }}</small>
                            @elseif($pay->invoiceSupplier)
                                <div class="fw-bold text-dark">{{ $pay->invoiceSupplier->supplier->name ?? 'Supplier' }}</div>
                                <small class="text-muted">Penyedia Material</small>
                            @endif
                        </td>
                        <td class="fw-bold text-dark font-monospace">Rp {{ number_format($pay->amount_total, 0, ',', '.') }}</td>
                        <td class="fw-semibold text-success font-monospace">Rp {{ number_format($pay->total_paid, 0, ',', '.') }}</td>
                        <td class="fw-bold text-danger font-monospace">Rp {{ number_format($pay->remaining_balance, 0, ',', '.') }}</td>
                        <td>
                            @if($pay->deadline)
                                <div class="small fw-medium {{ $pay->status === 'jatuh_tempo' ? 'text-danger fw-bold' : 'text-dark' }}">
                                    {{ $pay->deadline->format('d M Y') }}
                                </div>
                                <small class="text-muted">{{ $pay->deadline->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($pay->status === 'lunas')
                                <span class="badge-lunas"><i class="bi bi-check-circle me-1"></i> Lunas</span>
                            @elseif($pay->status === 'sebagian')
                                <span class="badge-sebagian"><i class="bi bi-hourglass-split me-1"></i> Sebagian</span>
                            @elseif($pay->status === 'jatuh_tempo')
                                <span class="badge-jatuh-tempo"><i class="bi bi-exclamation-triangle me-1"></i> Jatuh Tempo</span>
                            @else
                                <span class="badge-belum-bayar"><i class="bi bi-x-circle me-1"></i> Belum Bayar</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('payments.show', $pay->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold">
                                <i class="bi bi-cash-coin me-1"></i> Riwayat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-credit-card fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Tidak ada data tagihan atau pembayaran yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const statusFilter = document.getElementById('statusFilter');
        const filterForm = document.getElementById('filterForm');

        const triggerSearch = debounce(() => {
            filterForm.submit();
        }, 350);

        if (searchInput) searchInput.addEventListener('input', triggerSearch);
        if (typeFilter) typeFilter.addEventListener('change', () => filterForm.submit());
        if (statusFilter) statusFilter.addEventListener('change', () => filterForm.submit());
    });
</script>
@endpush
