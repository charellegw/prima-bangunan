@extends('layouts.app')

@section('title', 'Buku Piutang & Hutang — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                <i class="bi bi-arrow-left me-1"></i> Pusat Laporan
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Buku Piutang & Hutang (Aging / Jatuh Tempo)</h4>
                <p class="text-muted small mb-0">Pemantauan arus kas tagihan belum lunas & status jatuh tempo</p>
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

    <!-- Ringkasan Saldo Tagihan -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card card-custom p-3 border-start border-4 border-warning bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">TOTAL PIUTANG PELANGGAN</div>
                        <h3 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($totalReceivableSum, 0, ',', '.') }}</h3>
                        <small class="text-muted">{{ $receivables->count() }} nota penjualan belum lunas</small>
                    </div>
                    <div class="bg-warning bg-opacity-25 text-warning-emphasis p-3 rounded-circle">
                        <i class="bi bi-box-arrow-in-down-right fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card card-custom p-3 border-start border-4 border-danger bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">TOTAL HUTANG KE SUPPLIER</div>
                        <h3 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($totalPayableSum, 0, ',', '.') }}</h3>
                        <small class="text-muted">{{ $payables->count() }} faktur supplier belum lunas</small>
                    </div>
                    <div class="bg-danger bg-opacity-25 text-danger-emphasis p-3 rounded-circle">
                        <i class="bi bi-box-arrow-up-right fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            @php
                $overdueReceivables = $receivables->filter(fn($r) => $r->status === 'jatuh_tempo' || ($r->deadline && $r->deadline->isPast() && $r->status !== 'lunas'));
                $overduePayables = $payables->filter(fn($p) => $p->status === 'jatuh_tempo' || ($p->deadline && $p->deadline->isPast() && $p->status !== 'lunas'));
                $totalOverdueCount = $overdueReceivables->count() + $overduePayables->count();
            @endphp
            <div class="card card-custom p-3 border-start border-4 border-dark bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">TAGIHAN JATUH TEMPO</div>
                        <h3 class="fw-bold text-danger mt-1 mb-0">{{ $totalOverdueCount }} Tagihan</h3>
                        <small class="text-muted">{{ $overdueReceivables->count() }} piutang + {{ $overduePayables->count() }} hutang lewat tempo</small>
                    </div>
                    <div class="bg-dark bg-opacity-10 text-dark p-3 rounded-circle">
                        <i class="bi bi-exclamation-octagon fs-4 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Nav -->
    <ul class="nav nav-pills mb-3 gap-2" id="debtTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2 rounded-pill fw-semibold" id="receivable-tab" data-bs-toggle="pill" data-bs-target="#receivable-content" type="button" role="tab">
                <i class="bi bi-cash-stack me-1"></i> Piutang Pelanggan ({{ $receivables->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 rounded-pill fw-semibold" id="payable-tab" data-bs-toggle="pill" data-bs-target="#payable-content" type="button" role="tab">
                <i class="bi bi-receipt me-1"></i> Hutang ke Supplier ({{ $payables->count() }})
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="debtTabContents">
        <!-- 1. TAB PIUTANG PELANGGAN -->
        <div class="tab-pane fade show active" id="receivable-content" role="tabpanel">
            <div class="card card-custom border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Nota</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Total Nota</th>
                                <th class="text-end">Terbayar</th>
                                <th class="text-end">Sisa Piutang</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receivables as $pay)
                                @php
                                    $inv = $pay->invoice;
                                    $isOverdue = $pay->status === 'jatuh_tempo' || ($pay->deadline && $pay->deadline->isPast() && $pay->status !== 'lunas');
                                    $daysPast = $pay->deadline ? (int) $pay->deadline->diffInDays(now(), false) : null;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('invoices.show', $inv->id) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                            {{ $inv->invoice_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $inv->date ? $inv->date->format('d/m/Y') : '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $inv->client_name }}</div>
                                        <span class="badge bg-secondary bg-opacity-25 text-dark small" style="font-size: 10px;">{{ strtoupper($inv->client_type) }}</span>
                                    </td>
                                    <td>
                                        @if($pay->deadline)
                                            <div class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-dark' }}">
                                                {{ $pay->deadline->format('d/m/Y') }}
                                            </div>
                                            @if($isOverdue)
                                                <small class="badge bg-danger text-white" style="font-size: 10px;">
                                                    Lewat {{ abs($daysPast) }} hari
                                                </small>
                                            @else
                                                <small class="text-muted" style="font-size: 10px;">
                                                    Sisa {{ abs($daysPast) }} hari
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-monospace">
                                        Rp {{ number_format($inv->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end font-monospace text-success">
                                        Rp {{ number_format($pay->total_paid, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-danger">
                                        Rp {{ number_format($pay->remaining_balance, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if($pay->status === 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @elseif($isOverdue)
                                            <span class="badge bg-danger">Jatuh Tempo</span>
                                        @elseif($pay->status === 'sebagian')
                                            <span class="badge bg-warning text-dark">Sebagian</span>
                                        @else
                                            <span class="badge bg-secondary">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('payments.show', $pay->id) }}" class="btn btn-outline-primary" title="Catat Cicilan / Riwayat">
                                                <i class="bi bi-credit-card me-1"></i> Bayar
                                            </a>
                                            <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-outline-secondary" title="Lihat Nota">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                                        Hebat! Semua tagihan piutang pelanggan sudah lunas atau belum ada data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($receivables->isNotEmpty())
                        <tfoot class="table-light fw-bold font-monospace">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL KESELURUHAN PIUTANG:</td>
                                <td class="text-end">Rp {{ number_format($receivables->sum(fn($r) => $r->invoice->grand_total ?? 0), 0, ',', '.') }}</td>
                                <td class="text-end text-success">Rp {{ number_format($receivables->sum('total_paid'), 0, ',', '.') }}</td>
                                <td class="text-end text-danger fs-6">Rp {{ number_format($totalReceivableSum, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. TAB HUTANG KE SUPPLIER -->
        <div class="tab-pane fade" id="payable-content" role="tabpanel">
            <div class="card card-custom border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Faktur</th>
                                <th>Tanggal</th>
                                <th>Supplier & Kontak</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Total Faktur</th>
                                <th class="text-end">Terbayar</th>
                                <th class="text-end">Sisa Hutang</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payables as $pay)
                                @php
                                    $inv = $pay->invoiceSupplier;
                                    $sup = $inv->supplier;
                                    $isOverdue = $pay->status === 'jatuh_tempo' || ($pay->deadline && $pay->deadline->isPast() && $pay->status !== 'lunas');
                                    $daysPast = $pay->deadline ? (int) $pay->deadline->diffInDays(now(), false) : null;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('invoice-suppliers.show', $inv->id) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                            {{ $inv->invoice_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $inv->date ? $inv->date->format('d/m/Y') : '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $sup->name ?? 'Supplier' }}</div>
                                        <small class="text-muted">{{ $sup->phone ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($pay->deadline)
                                            <div class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-dark' }}">
                                                {{ $pay->deadline->format('d/m/Y') }}
                                            </div>
                                            @if($isOverdue)
                                                <small class="badge bg-danger text-white" style="font-size: 10px;">
                                                    Lewat {{ abs($daysPast) }} hari
                                                </small>
                                            @else
                                                <small class="text-muted" style="font-size: 10px;">
                                                    Sisa {{ abs($daysPast) }} hari
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-monospace">
                                        Rp {{ number_format($inv->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end font-monospace text-success">
                                        Rp {{ number_format($pay->total_paid, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-danger">
                                        Rp {{ number_format($pay->remaining_balance, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if($pay->status === 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @elseif($isOverdue)
                                            <span class="badge bg-danger">Jatuh Tempo</span>
                                        @elseif($pay->status === 'sebagian')
                                            <span class="badge bg-warning text-dark">Sebagian</span>
                                        @else
                                            <span class="badge bg-secondary">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('payments.show', $pay->id) }}" class="btn btn-outline-danger" title="Bayar Hutang / Cicil">
                                                <i class="bi bi-cash-coin me-1"></i> Lunasi
                                            </a>
                                            <a href="{{ route('invoice-suppliers.show', $inv->id) }}" class="btn btn-outline-secondary" title="Lihat Faktur">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="bi bi-shield-check text-success fs-1 d-block mb-2"></i>
                                        Tidak ada hutang ke supplier yang belum lunas. Pembukuan toko bersih!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($payables->isNotEmpty())
                        <tfoot class="table-light fw-bold font-monospace">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL KESELURUHAN HUTANG:</td>
                                <td class="text-end">Rp {{ number_format($payables->sum(fn($p) => $p->invoiceSupplier->grand_total ?? 0), 0, ',', '.') }}</td>
                                <td class="text-end text-success">Rp {{ number_format($payables->sum('total_paid'), 0, ',', '.') }}</td>
                                <td class="text-end text-danger fs-6">Rp {{ number_format($totalPayableSum, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
