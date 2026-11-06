@extends('layouts.app')

@section('title', 'Faktur Masuk ' . $invoiceSupplier->invoice_number . ' — Prima Bangunan')

@section('content')
<!-- Top Action Bar -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3 d-print-none">
    <div class="d-flex align-items-center gap-2">
        <x-button :href="route('invoice-suppliers.index')" variant="secondary" size="sm" icon="arrow-left">
            Daftar Faktur
        </x-button>
        <span class="text-muted small">/</span>
        <span class="text-main small fw-bold tabular-nums">{{ $invoiceSupplier->invoice_number }}</span>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        @if($invoiceSupplier->payment && $invoiceSupplier->payment->remaining_balance > 0)
            <x-button type="button" variant="primary" size="sm" icon="credit-card" data-bs-toggle="modal" data-bs-target="#modalBayarHutang">
                Bayar Hutang Supplier
            </x-button>
        @endif
        <x-button type="button" variant="outline" size="sm" icon="printer" onclick="window.print()">
            Cetak Faktur
        </x-button>
    </div>
</div>

<!-- Faktur Pembelian Card -->
<div class="pb-card p-4 p-md-5 mb-4 shadow-sm" id="printableFaktur">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-start border-bottom border-light-subtle pb-4 mb-4 gap-3" style="border-color: var(--pb-border) !important;">
        <div>
            <div class="d-flex align-items-center gap-2.5 mb-2">
                <div class="pb-brand-logo" style="width: 36px; height: 36px;">
                    <x-icon name="bag-plus" size="18" stroke="2" />
                </div>
                <h4 class="fw-bold mb-0 text-main fs-5">{{ $invoiceSupplier->store->name ?? 'Prima Bangunan' }}</h4>
            </div>
            <div class="text-secondary small" style="font-size: 12.5px;">Gudang Penerima: {{ $invoiceSupplier->store->address ?? 'Jakarta Barat' }}</div>
            <div class="text-secondary small" style="font-size: 12.5px;">Faktur Pembelian Stok Material Masuk</div>
        </div>

        <div class="text-md-end">
            <h5 class="fw-bold text-primary mb-1 tabular-nums fs-5">{{ $invoiceSupplier->invoice_number }}</h5>
            <div class="text-secondary small" style="font-size: 12.5px;">Tanggal: <span class="fw-semibold text-main">{{ $invoiceSupplier->date ? $invoiceSupplier->date->format('d F Y, H:i') . ' WIB' : '-' }}</span></div>
            <div class="mt-2">
                @php $status = $invoiceSupplier->payment->status ?? 'belum_bayar'; @endphp
                @if($status === 'lunas')
                    <x-badge variant="success">Lunas</x-badge>
                @elseif($status === 'sebagian')
                    <x-badge variant="warning">Dibayar Sebagian</x-badge>
                @elseif($status === 'jatuh_tempo')
                    <x-badge variant="danger">Jatuh Tempo</x-badge>
                @else
                    <x-badge variant="neutral">Belum Bayar</x-badge>
                @endif
            </div>
        </div>
    </div>

    <!-- Supplier & Payment Details -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="p-3 bg-light-subtle rounded-3 border border-light-subtle h-100" style="border-color: var(--pb-border) !important;">
                <div class="text-secondary small mb-1" style="font-size: 11.5px;">IDENTITAS PEMASOK (SUPPLIER):</div>
                <div class="fs-5 fw-bold text-main">{{ $invoiceSupplier->supplier->name ?? 'Supplier' }}</div>
                <div class="small text-secondary mt-1 d-flex align-items-center gap-1.5" style="font-size: 12.5px;">
                    <x-icon name="map-pin" size="14" class="text-muted" />
                    <span>{{ $invoiceSupplier->supplier->location ?: 'Lokasi tidak dicatat' }}</span>
                </div>
                <div class="small text-secondary mt-0.5 d-flex align-items-center gap-1.5" style="font-size: 12.5px;">
                    <x-icon name="phone" size="14" class="text-muted" />
                    <span>{{ $invoiceSupplier->supplier->phone ?: '-' }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="p-3 bg-light-subtle rounded-3 border border-light-subtle h-100" style="border-color: var(--pb-border) !important;">
                <div class="text-secondary small mb-1" style="font-size: 11.5px;">KETERANGAN HUTANG:</div>
                <div class="small text-main mb-1" style="font-size: 13px;">
                    Jatuh Tempo: <strong>{{ $invoiceSupplier->payment->deadline ? $invoiceSupplier->payment->deadline->format('d M Y') : '-' }}</strong>
                </div>
                @if($invoiceSupplier->note)
                    <div class="small text-secondary" style="font-size: 12.5px;">
                        Catatan: <em>{{ $invoiceSupplier->note }}</em>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Barang Diterima -->
    <div class="table-responsive mb-4">
        <table class="pb-table">
            <thead>
                <tr>
                    <th style="width: 45px;">No.</th>
                    <th>Barang Material Masuk</th>
                    <th class="text-center" style="width: 120px;">Jumlah Masuk</th>
                    <th class="text-end" style="width: 170px;">Harga Beli Satuan</th>
                    <th class="text-end" style="width: 180px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoiceSupplier->invoiceSupplierDetails as $index => $detail)
                <tr>
                    <td class="text-secondary small">{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-semibold text-main">{{ $detail->item->name ?? 'Barang' }}</div>
                        @if($detail->item && $detail->item->brand)
                            <div class="text-secondary small" style="font-size: 11.5px;">Merek: {{ $detail->item->brand }}</div>
                        @endif
                    </td>
                    <td class="text-center fw-medium text-main tabular-nums">{{ $detail->quantity }} unit</td>
                    <td class="text-end nowrap tabular-nums">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold text-main nowrap tabular-nums">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-top" style="border-color: var(--pb-border) !important;">
                    <td colspan="4" class="text-end fw-semibold text-main py-2.5">Total Pembelian:</td>
                    <td class="text-end py-2.5 fs-5 fw-bold text-primary nowrap tabular-nums">
                        Rp {{ number_format($invoiceSupplier->grand_total, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end text-success fw-medium py-1.5">Sudah Dibayar ke Supplier:</td>
                    <td class="text-end fw-bold text-success nowrap tabular-nums py-1.5">
                        Rp {{ number_format($invoiceSupplier->payment->total_paid ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end text-danger fw-medium py-1.5">Sisa Hutang Toko:</td>
                    <td class="text-end fw-bold text-danger nowrap tabular-nums py-1.5">
                        Rp {{ number_format($invoiceSupplier->payment->remaining_balance ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Log Pengiriman Masuk (Shipment Incoming) -->
    @if($invoiceSupplier->shipments->count() > 0)
    <div class="mb-4 p-3.5 bg-light-subtle rounded-3 border border-light-subtle" style="border-color: var(--pb-border) !important;">
        <div class="d-flex align-items-center gap-1.5 mb-2 fw-semibold text-main small">
            <x-icon name="truck" size="16" class="text-primary" />
            <span>Data Kiriman Masuk dari Supplier</span>
        </div>
        @foreach($invoiceSupplier->shipments as $sh)
        <div class="row g-2 small">
            <div class="col-12 col-md-3">
                <span class="text-secondary" style="font-size: 11px;">No. Pengiriman:</span>
                <span class="fw-semibold text-main tabular-nums d-block">{{ $sh->shipment_number }}</span>
            </div>
            <div class="col-12 col-md-4">
                <span class="text-secondary" style="font-size: 11px;">Alamat Gudang:</span>
                <span class="text-main d-block">{{ $sh->address }}</span>
            </div>
            <div class="col-12 col-md-2">
                <span class="text-secondary" style="font-size: 11px;">Tanggal:</span>
                <span class="text-main d-block">{{ $sh->date ? $sh->date->format('d M Y') : '-' }}</span>
            </div>
            <div class="col-12 col-md-3">
                <span class="text-secondary" style="font-size: 11px;">Status:</span>
                <div>
                    <x-badge variant="success" size="sm">{{ strtoupper($sh->status) }}</x-badge>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Riwayat Pembayaran ke Supplier -->
    <div class="mt-4 pt-3 border-top border-light-subtle" style="border-color: var(--pb-border) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <x-icon name="credit-card" size="16" class="text-primary" />
                <h6 class="fw-semibold text-main mb-0" style="font-size: 14px;">Riwayat Pembayaran Hutang Dagang</h6>
            </div>
            @if($invoiceSupplier->payment && $invoiceSupplier->payment->remaining_balance > 0)
                <x-button type="button" variant="outline" size="sm" icon="plus" data-bs-toggle="modal" data-bs-target="#modalBayarHutang" class="d-print-none">
                    Catat Pembayaran
                </x-button>
            @endif
        </div>

        <div class="table-responsive">
            <table class="pb-table">
                <thead>
                    <tr>
                        <th>Waktu Bayar</th>
                        <th>Metode Pembayaran</th>
                        <th>Catatan</th>
                        <th class="text-end">Jumlah Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoiceSupplier->payment->paymentHistories ?? [] as $hist)
                    <tr>
                        <td class="small text-main tabular-nums">{{ $hist->date ? $hist->date->format('d M Y, H:i') : '-' }}</td>
                        <td><x-badge variant="neutral" size="sm">{{ $hist->method }}</x-badge></td>
                        <td class="small text-secondary">{{ $hist->note ?: '-' }}</td>
                        <td class="text-end fw-bold text-success nowrap tabular-nums">
                            Rp {{ number_format($hist->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-3 text-secondary small">Belum ada riwayat pembayaran untuk faktur supplier ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bayar Hutang Supplier -->
@if($invoiceSupplier->payment && $invoiceSupplier->payment->remaining_balance > 0)
<div class="modal fade" id="modalBayarHutang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--pb-radius-xl);">
            <form action="{{ route('payments.history.store', $invoiceSupplier->payment->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4" style="border-color: var(--pb-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <x-icon name="credit-card" size="18" class="text-primary" />
                        <h5 class="modal-title fw-bold text-main fs-6 mb-0">Bayar Hutang ke Supplier</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light-subtle rounded-3 mb-3 border border-light-subtle" style="border-color: var(--pb-border) !important;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-secondary small">Total Faktur:</span>
                            <span class="fw-bold text-main nowrap tabular-nums">Rp {{ number_format($invoiceSupplier->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary small">Sisa Hutang:</span>
                            <span class="fw-bold text-danger nowrap tabular-nums">Rp {{ number_format($invoiceSupplier->payment->remaining_balance, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Nominal Bayar (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                            <input type="number"
                                   name="amount"
                                   class="form-control fw-bold fs-6 tabular-nums"
                                   min="1"
                                   max="{{ (int)$invoiceSupplier->payment->remaining_balance }}"
                                   value="{{ (int)$invoiceSupplier->payment->remaining_balance }}"
                                   required>
                        </div>
                        <small class="text-muted" style="font-size: 11.5px;">*Maksimal pembayaran adalah sisa hutang (Rp {{ number_format($invoiceSupplier->payment->remaining_balance, 0, ',', '.') }}).</small>
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="method" class="form-select" required>
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            <option value="Giro / Cek">Giro / Cek</option>
                            <option value="Tunai (Cash)">Tunai (Cash)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Tanggal Bayar</label>
                        <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Catatan Pembayaran</label>
                        <input type="text" name="note" class="form-control" placeholder="Contoh: Transfer rekening BCA supplier, pelunasan tempo">
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4" style="border-color: var(--pb-border) !important;">
                    <x-button type="button" variant="secondary" data-bs-dismiss="modal">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" icon="check">
                        Simpan Pembayaran
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
