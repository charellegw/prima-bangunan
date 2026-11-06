@extends('layouts.app')

@section('title', 'Nota ' . $invoice->invoice_number . ' — Prima Bangunan')

@section('content')
<!-- Top Action Bar -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3 d-print-none">
    <div class="d-flex align-items-center gap-2">
        <x-button :href="route('invoices.index')" variant="secondary" size="sm" icon="arrow-left">
            Daftar Invoice
        </x-button>
        <span class="text-muted small">/</span>
        <span class="text-main small fw-bold tabular-nums">{{ $invoice->invoice_number }}</span>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        @if($invoice->payment && $invoice->payment->remaining_balance > 0)
            <x-button type="button" variant="primary" size="sm" icon="credit-card" data-bs-toggle="modal" data-bs-target="#modalCatatPembayaran">
                Catat Pembayaran
            </x-button>
        @endif
        <x-button :href="route('invoices.receipt', $invoice->id)" target="_blank" variant="secondary" size="sm" icon="printer">
            Cetak Struk (POS)
        </x-button>
        <x-button type="button" variant="outline" size="sm" icon="printer" onclick="window.print()">
            Cetak Nota A4
        </x-button>
    </div>
</div>

<!-- Official Invoice / Nota Card -->
<div class="pb-card p-4 p-md-5 mb-4 shadow-sm" id="printableNota">
    <!-- Header Nota -->
    <div class="d-flex flex-wrap justify-content-between align-items-start border-bottom border-light-subtle pb-4 mb-4 gap-3" style="border-color: var(--pb-border) !important;">
        <div>
            <div class="d-flex align-items-center gap-2.5 mb-2">
                <div class="pb-brand-logo" style="width: 36px; height: 36px;">
                    <x-icon name="building" size="18" stroke="2" />
                </div>
                <h4 class="fw-bold mb-0 text-main fs-5">{{ $invoice->store->name ?? 'Prima Bangunan' }}</h4>
            </div>
            <div class="text-secondary small" style="font-size: 12.5px;">{{ $invoice->store->address ?? 'Jl. Raya Industri Bangunan No. 88, Jakarta Barat' }}</div>
            <div class="text-secondary small" style="font-size: 12.5px;">Telp: {{ $invoice->store->phone ?? '021-5558899' }}</div>
        </div>

        <div class="text-md-end">
            <h5 class="fw-bold text-primary mb-1 tabular-nums fs-5">{{ $invoice->invoice_number }}</h5>
            <div class="text-secondary small" style="font-size: 12.5px;">Tanggal: <span class="fw-semibold text-main">{{ $invoice->date ? $invoice->date->format('d F Y, H:i') . ' WIB' : '-' }}</span></div>
            <div class="mt-2">
                @php $status = $invoice->payment->status ?? 'belum_bayar'; @endphp
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

    <!-- Info Pelanggan & Transaksi -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="p-3 bg-light-subtle rounded-3 border border-light-subtle h-100" style="border-color: var(--pb-border) !important;">
                <div class="text-secondary small mb-1" style="font-size: 11.5px;">DITUJUKAN KEPADA:</div>
                <div class="fs-5 fw-bold text-main">{{ $invoice->client_name }}</div>
                <div class="mt-1.5">
                    @if($invoice->client_type === 'retail')
                        <x-badge variant="info" size="sm">Pelanggan Retail / Grosir</x-badge>
                    @else
                        <x-badge variant="neutral" size="sm">Pelanggan Umum (Eceran)</x-badge>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="p-3 bg-light-subtle rounded-3 border border-light-subtle h-100" style="border-color: var(--pb-border) !important;">
                <div class="text-secondary small mb-1" style="font-size: 11.5px;">INFORMASI TAGIHAN:</div>
                <div class="small text-main mb-1" style="font-size: 13px;">
                    Jatuh Tempo: <strong>{{ $invoice->payment->deadline ? $invoice->payment->deadline->format('d M Y') : '-' }}</strong>
                </div>
                @if($invoice->note)
                    <div class="small text-secondary" style="font-size: 12.5px;">
                        Catatan: <em>{{ $invoice->note }}</em>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Detail Barang (Snapshot Harga) -->
    <div class="table-responsive mb-4">
        <table class="pb-table">
            <thead>
                <tr>
                    <th style="width: 45px;">No.</th>
                    <th>Material Bangunan</th>
                    <th class="text-center" style="width: 90px;">Jumlah</th>
                    <th class="text-end" style="width: 160px;">Harga Satuan</th>
                    <th class="text-end" style="width: 170px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceDetails as $index => $detail)
                <tr>
                    <td class="text-secondary small">{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-semibold text-main">{{ $detail->item->name ?? 'Barang' }}</div>
                        @if($detail->item && $detail->item->brand)
                            <div class="text-secondary small" style="font-size: 11.5px;">Merek: {{ $detail->item->brand }}</div>
                        @endif
                    </td>
                    <td class="text-center fw-medium text-main tabular-nums">{{ $detail->quantity }}</td>
                    <td class="text-end nowrap tabular-nums">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold text-main nowrap tabular-nums">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-top" style="border-color: var(--pb-border) !important;">
                    <td colspan="4" class="text-end fw-semibold text-main py-2.5">Grand Total:</td>
                    <td class="text-end py-2.5 fs-5 fw-bold text-primary nowrap tabular-nums">
                        Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end text-success fw-medium py-1.5">Sudah Dibayar:</td>
                    <td class="text-end fw-bold text-success nowrap tabular-nums py-1.5">
                        Rp {{ number_format($invoice->payment->total_paid ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end text-danger fw-medium py-1.5">Sisa Tagihan:</td>
                    <td class="text-end fw-bold text-danger nowrap tabular-nums py-1.5">
                        Rp {{ number_format($invoice->payment->remaining_balance ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Bagian Pengiriman (Shipment) Jika Ada -->
    @if($invoice->shipments->count() > 0)
    <div class="mb-4 p-3.5 bg-light-subtle rounded-3 border border-light-subtle" style="border-color: var(--pb-border) !important;">
        <div class="d-flex align-items-center gap-1.5 mb-2 fw-semibold text-main small">
            <x-icon name="truck" size="16" class="text-primary" />
            <span>Data Pengiriman Armada</span>
        </div>
        @foreach($invoice->shipments as $sh)
        <div class="row g-2 small">
            <div class="col-12 col-md-3">
                <span class="text-secondary" style="font-size: 11px;">No. Pengiriman:</span>
                <span class="fw-semibold text-main tabular-nums d-block">{{ $sh->shipment_number }}</span>
            </div>
            <div class="col-12 col-md-4">
                <span class="text-secondary" style="font-size: 11px;">Alamat Tujuan:</span>
                <span class="text-main d-block">{{ $sh->address }}</span>
            </div>
            <div class="col-12 col-md-2">
                <span class="text-secondary" style="font-size: 11px;">Ongkir:</span>
                <span class="text-main fw-bold d-block nowrap tabular-nums">Rp {{ number_format($sh->fee, 0, ',', '.') }}</span>
            </div>
            <div class="col-12 col-md-3">
                <span class="text-secondary" style="font-size: 11px;">Status:</span>
                <div>
                    <x-badge variant="info" size="sm">{{ strtoupper($sh->status) }}</x-badge>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Riwayat Pembayaran (Payment History) -->
    <div class="mt-4 pt-3 border-top border-light-subtle" style="border-color: var(--pb-border) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <x-icon name="credit-card" size="16" class="text-primary" />
                <h6 class="fw-semibold text-main mb-0" style="font-size: 14px;">Riwayat Pembayaran (Payment History)</h6>
            </div>
            @if($invoice->payment && $invoice->payment->remaining_balance > 0)
                <x-button type="button" variant="outline" size="sm" icon="plus" data-bs-toggle="modal" data-bs-target="#modalCatatPembayaran" class="d-print-none">
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
                    @forelse($invoice->payment->paymentHistories ?? [] as $hist)
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
                        <td colspan="4" class="text-center py-3 text-secondary small">Belum ada riwayat pembayaran yang tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tanda Tangan Nota (Print Friendly) -->
    <div class="row mt-5 pt-4 text-center d-none d-print-flex">
        <div class="col-6">
            <div class="small text-secondary mb-5">Penerima / Pelanggan,</div>
            <div class="fw-bold text-main">({{ $invoice->client_name }})</div>
        </div>
        <div class="col-6">
            <div class="small text-secondary mb-5">Hormat Kami,</div>
            <div class="fw-bold text-main">(Kasir Prima Bangunan)</div>
        </div>
    </div>
</div>

<!-- Modal Catat Pembayaran Baru -->
@if($invoice->payment && $invoice->payment->remaining_balance > 0)
<div class="modal fade" id="modalCatatPembayaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--pb-radius-xl);">
            <form action="{{ route('payments.history.store', $invoice->payment->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4" style="border-color: var(--pb-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <x-icon name="credit-card" size="18" class="text-primary" />
                        <h5 class="modal-title fw-bold text-main fs-6 mb-0">Catat Pembayaran / Pelunasan</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light-subtle rounded-3 mb-3 border border-light-subtle" style="border-color: var(--pb-border) !important;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-secondary small">Total Tagihan:</span>
                            <span class="fw-bold text-main nowrap tabular-nums">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary small">Sisa Belum Dibayar:</span>
                            <span class="fw-bold text-danger nowrap tabular-nums">Rp {{ number_format($invoice->payment->remaining_balance, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                            <input type="number"
                                   name="amount"
                                   class="form-control fw-bold fs-6 tabular-nums"
                                   min="1"
                                   max="{{ (int)$invoice->payment->remaining_balance }}"
                                   value="{{ (int)$invoice->payment->remaining_balance }}"
                                   required>
                        </div>
                        <small class="text-muted" style="font-size: 11.5px;">*Maksimal pembayaran adalah sisa tagihan (Rp {{ number_format($invoice->payment->remaining_balance, 0, ',', '.') }}).</small>
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="method" class="form-select" required>
                            <option value="Tunai (Cash)">Tunai (Cash)</option>
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Giro / Cek">Giro / Cek</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Tanggal Pembayaran</label>
                        <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label class="pb-label">Catatan Pembayaran</label>
                        <input type="text" name="note" class="form-control" placeholder="Contoh: Cicilan ke-2, transfer dari rek Mandiri">
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
