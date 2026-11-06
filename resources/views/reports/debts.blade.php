@extends('layouts.app')

@section('title', 'Buku Piutang & Hutang — Prima Bangunan')

@section('content')
<div class="d-flex flex-column gap-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <x-button href="{{ route('reports.index') }}" variant="secondary" size="sm">
                <x-icon name="arrow-left" style="width: 14px; height: 14px;" class="me-1" /> Pusat Laporan
            </x-button>
            <div>
                <h1 class="page-title mb-0 fs-4">Buku Piutang & Hutang Dagang</h1>
                <p class="page-subtitle mb-0 small">Pemantauan arus kas tagihan belum lunas, tempo pembayaran, dan prioritas penagihan</p>
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

    <!-- Ringkasan Saldo Tagihan (3 Stat Cards) -->
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <x-stat-card
                title="Total Piutang Pelanggan"
                :value="'Rp ' . number_format($totalReceivableSum, 0, ',', '.') . ''"
                icon="credit-card"
                color="warning"
                :description="$receivables->count() . ' nota penjualan belum lunas'"
            />
        </div>

        <div class="col-12 col-md-4">
            <x-stat-card
                title="Total Hutang ke Supplier"
                :value="'Rp ' . number_format($totalPayableSum, 0, ',', '.') . ''"
                icon="shopping-cart"
                color="danger"
                :description="$payables->count() . ' faktur supplier belum lunas'"
            />
        </div>

        @php
            $overdueReceivables = $receivables->filter(fn($r) => $r->status === 'jatuh_tempo' || ($r->deadline && $r->deadline->isPast() && $r->status !== 'lunas'));
            $overduePayables = $payables->filter(fn($p) => $p->status === 'jatuh_tempo' || ($p->deadline && $p->deadline->isPast() && $p->status !== 'lunas'));
            $totalOverdueCount = $overdueReceivables->count() + $overduePayables->count();
        @endphp
        <div class="col-12 col-md-4">
            <x-stat-card
                title="Tagihan Jatuh Tempo"
                :value="$totalOverdueCount . ' Tagihan'"
                icon="alert-triangle"
                color="danger"
                :description="$overdueReceivables->count() . ' piutang + ' . $overduePayables->count() . ' hutang lewat tempo'"
            />
        </div>
    </div>

    <!-- Tabs Nav -->
    <ul class="nav nav-pills gap-2" id="debtTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-primary active rounded-pill px-4 py-2 small fw-semibold" id="receivable-tab" data-bs-toggle="pill" data-bs-target="#receivable-content" type="button" role="tab">
                <x-icon name="credit-card" style="width: 14px; height: 14px;" class="me-1.5" /> Piutang Pelanggan ({{ $receivables->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-primary rounded-pill px-4 py-2 small fw-semibold" id="payable-tab" data-bs-toggle="pill" data-bs-target="#payable-content" type="button" role="tab">
                <x-icon name="shopping-cart" style="width: 14px; height: 14px;" class="me-1.5" /> Hutang ke Supplier ({{ $payables->count() }})
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="debtTabContents">
        <!-- 1. TAB PIUTANG PELANGGAN -->
        <div class="tab-pane fade show active" id="receivable-content" role="tabpanel">
            <x-card :flush="true" class="p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table pb-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No. Nota</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Total Nota</th>
                                <th class="text-end">Terbayar</th>
                                <th class="text-end">Sisa Piutang</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Aksi</th>
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
                                    <td class="ps-4 nowrap">
                                        <a href="{{ route('invoices.show', $inv->id) }}" class="fw-semibold text-primary text-decoration-none tabular-nums">
                                            {{ $inv->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="nowrap">
                                        <span class="text-dark small tabular-nums">{{ $inv->date ? $inv->date->format('d/m/Y') : '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark small">{{ $inv->client_name }}</div>
                                        <x-badge variant="neutral" style="font-size: 10px;">{{ strtoupper($inv->client_type) }}</x-badge>
                                    </td>
                                    <td class="nowrap">
                                        @if($pay->deadline)
                                            <div class="{{ $isOverdue ? 'text-danger fw-semibold' : 'text-dark' }} small tabular-nums">
                                                {{ $pay->deadline->format('d/m/Y') }}
                                            </div>
                                            @if($isOverdue)
                                                <x-badge variant="danger" style="font-size: 10px;">
                                                    Lewat {{ abs($daysPast) }} hari
                                                </x-badge>
                                            @else
                                                <small class="text-muted tabular-nums" style="font-size: 10px;">
                                                    Sisa {{ abs($daysPast) }} hari
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end tabular-nums text-dark nowrap">
                                        Rp {{ number_format($inv->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end tabular-nums text-success nowrap">
                                        Rp {{ number_format($pay->total_paid, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end tabular-nums fw-bold text-danger nowrap">
                                        Rp {{ number_format($pay->remaining_balance, 0, ',', '.') }}
                                    </td>
                                    <td class="nowrap">
                                        @if($pay->status === 'lunas')
                                            <x-badge variant="success">Lunas</x-badge>
                                        @elseif($isOverdue)
                                            <x-badge variant="danger">Jatuh Tempo</x-badge>
                                        @elseif($pay->status === 'sebagian')
                                            <x-badge variant="warning">Sebagian</x-badge>
                                        @else
                                            <x-badge variant="neutral">Belum Bayar</x-badge>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end nowrap">
                                        <div class="d-inline-flex gap-1">
                                            <x-button href="{{ route('payments.show', $pay->id) }}" variant="outline" size="sm" title="Catat Cicilan">
                                                <x-icon name="credit-card" style="width: 14px; height: 14px;" class="me-1" /> Cicil
                                            </x-button>
                                            <x-button href="{{ route('invoices.show', $inv->id) }}" variant="ghost" size="sm" title="Lihat Nota">
                                                <x-icon name="eye" style="width: 14px; height: 14px;" />
                                            </x-button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <x-empty-state
                                            icon="check"
                                            title="Semua piutang pelanggan lunas!"
                                            description="Tidak ada tagihan piutang berjalan yang belum diselesaikan pelanggan saat ini."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($receivables->isNotEmpty())
                        <tfoot class="border-top" style="background-color: var(--pb-bg-muted) !important;">
                            <tr class="fw-bold tabular-nums">
                                <td colspan="4" class="ps-4 text-end text-secondary small">TOTAL PIUTANG PELANGGAN:</td>
                                <td class="text-end text-dark">Rp {{ number_format($receivables->sum(fn($r) => $r->invoice->grand_total ?? 0), 0, ',', '.') }}</td>
                                <td class="text-end text-success">Rp {{ number_format($receivables->sum('total_paid'), 0, ',', '.') }}</td>
                                <td class="text-end text-danger fs-6">Rp {{ number_format($totalReceivableSum, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </x-card>
        </div>

        <!-- 2. TAB HUTANG KE SUPPLIER -->
        <div class="tab-pane fade" id="payable-content" role="tabpanel">
            <x-card :flush="true" class="p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table pb-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No. Faktur</th>
                                <th>Tanggal</th>
                                <th>Supplier & Kontak</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Total Faktur</th>
                                <th class="text-end">Terbayar</th>
                                <th class="text-end">Sisa Hutang</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Aksi</th>
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
                                    <td class="ps-4 nowrap">
                                        <a href="{{ route('invoice-suppliers.show', $inv->id) }}" class="fw-semibold text-primary text-decoration-none tabular-nums">
                                            {{ $inv->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="nowrap">
                                        <span class="text-dark small tabular-nums">{{ $inv->date ? $inv->date->format('d/m/Y') : '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark small">{{ $sup->name ?? 'Supplier' }}</div>
                                        <small class="text-muted tabular-nums">{{ $sup->phone ?? '-' }}</small>
                                    </td>
                                    <td class="nowrap">
                                        @if($pay->deadline)
                                            <div class="{{ $isOverdue ? 'text-danger fw-semibold' : 'text-dark' }} small tabular-nums">
                                                {{ $pay->deadline->format('d/m/Y') }}
                                            </div>
                                            @if($isOverdue)
                                                <x-badge variant="danger" style="font-size: 10px;">
                                                    Lewat {{ abs($daysPast) }} hari
                                                </x-badge>
                                            @else
                                                <small class="text-muted tabular-nums" style="font-size: 10px;">
                                                    Sisa {{ abs($daysPast) }} hari
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end tabular-nums text-dark nowrap">
                                        Rp {{ number_format($inv->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end tabular-nums text-success nowrap">
                                        Rp {{ number_format($pay->total_paid, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end tabular-nums fw-bold text-danger nowrap">
                                        Rp {{ number_format($pay->remaining_balance, 0, ',', '.') }}
                                    </td>
                                    <td class="nowrap">
                                        @if($pay->status === 'lunas')
                                            <x-badge variant="success">Lunas</x-badge>
                                        @elseif($isOverdue)
                                            <x-badge variant="danger">Jatuh Tempo</x-badge>
                                        @elseif($pay->status === 'sebagian')
                                            <x-badge variant="warning">Sebagian</x-badge>
                                        @else
                                            <x-badge variant="neutral">Belum Bayar</x-badge>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end nowrap">
                                        <div class="d-inline-flex gap-1">
                                            <x-button href="{{ route('payments.show', $pay->id) }}" variant="outline" size="sm" title="Bayar Hutang">
                                                <x-icon name="credit-card" style="width: 14px; height: 14px;" class="me-1" /> Lunasi
                                            </x-button>
                                            <x-button href="{{ route('invoice-suppliers.show', $inv->id) }}" variant="ghost" size="sm" title="Lihat Faktur">
                                                <x-icon name="eye" style="width: 14px; height: 14px;" />
                                            </x-button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <x-empty-state
                                            icon="check"
                                            title="Semua hutang supplier lunas!"
                                            description="Tidak ada hutang pembelian material ke supplier yang belum diselesaikan saat ini."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($payables->isNotEmpty())
                        <tfoot class="border-top" style="background-color: var(--pb-bg-muted) !important;">
                            <tr class="fw-bold tabular-nums">
                                <td colspan="4" class="ps-4 text-end text-secondary small">TOTAL HUTANG KE SUPPLIER:</td>
                                <td class="text-end text-dark">Rp {{ number_format($payables->sum(fn($p) => $p->invoiceSupplier->grand_total ?? 0), 0, ',', '.') }}</td>
                                <td class="text-end text-success">Rp {{ number_format($payables->sum('total_paid'), 0, ',', '.') }}</td>
                                <td class="text-end text-danger fs-6">Rp {{ number_format($totalPayableSum, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
