@extends('layouts.app')

@section('title', 'Detail Pembayaran — Prima Bangunan')

@section('content')
<!-- Header & Back Navigation -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3">
    <div class="d-flex align-items-center gap-2">
        <x-button :href="route('payments.index')" variant="secondary" size="sm" icon="arrow-left">
            Daftar Pembayaran
        </x-button>
        <span class="text-muted small">/</span>
        <span class="text-main small fw-bold">Detail Tagihan</span>
    </div>
    <div>
        @if($payment->invoice)
            <x-button :href="route('invoices.show', $payment->invoice->id)" variant="primary" size="sm" icon="shopping-cart">
                Buka Nota Penjualan
            </x-button>
        @elseif($payment->invoiceSupplier)
            <x-button :href="route('invoice-suppliers.show', $payment->invoiceSupplier->id)" variant="primary" size="sm" icon="bag-plus">
                Buka Faktur Supplier
            </x-button>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Info Ringkasan Tagihan & Status -->
    <div class="col-12 col-lg-5">
        <div class="pb-card p-4 mb-4">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                <x-icon name="credit-card" size="18" class="text-primary" />
                <h5 class="fw-semibold text-main mb-0 fs-6">Ringkasan Tagihan</h5>
            </div>

            <div class="p-3 rounded-3 mb-3 border border-light-subtle" style="background-color: var(--pb-surface-alt); border-color: var(--pb-border) !important;">
                <div class="text-secondary small fw-medium mb-1" style="font-size: 11.5px;">STATUS PEMBAYARAN:</div>
                <div class="mt-1">
                    @if($payment->status === 'lunas')
                        <x-badge variant="success">Lunas</x-badge>
                    @elseif($payment->status === 'sebagian')
                        <x-badge variant="warning">Dibayar Sebagian</x-badge>
                    @elseif($payment->status === 'jatuh_tempo')
                        <x-badge variant="danger">Jatuh Tempo</x-badge>
                    @else
                        <x-badge variant="neutral">Belum Bayar</x-badge>
                    @endif
                </div>
            </div>

            <div class="mb-3 pb-3 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                <div class="text-secondary small mb-1" style="font-size: 11.5px;">DOKUMEN TRANSAKSI:</div>
                @if($payment->invoice)
                    <div class="fw-bold text-main fs-6 tabular-nums">{{ $payment->invoice->invoice_number }}</div>
                    <small class="text-secondary" style="font-size: 12px;">Pelanggan: <strong class="text-main">{{ $payment->invoice->client_name }}</strong> ({{ $payment->invoice->client_type }})</small>
                @elseif($payment->invoiceSupplier)
                    <div class="fw-bold text-main fs-6 tabular-nums">{{ $payment->invoiceSupplier->invoice_number }}</div>
                    <small class="text-secondary" style="font-size: 12px;">Supplier: <strong class="text-main">{{ $payment->invoiceSupplier->supplier->name ?? 'Supplier' }}</strong></small>
                @endif
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-2.5 bg-light-subtle rounded-3 border border-light-subtle" style="border-color: var(--pb-border) !important;">
                        <small class="text-secondary d-block" style="font-size: 11px;">Total Tagihan:</small>
                        <span class="fw-bold text-main nowrap tabular-nums" style="font-size: 14.5px;">Rp {{ number_format($payment->amount_total, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2.5 bg-light-subtle rounded-3 border border-light-subtle" style="border-color: var(--pb-border) !important;">
                        <small class="text-secondary d-block" style="font-size: 11px;">Sudah Dibayar:</small>
                        <span class="fw-bold text-success nowrap tabular-nums" style="font-size: 14.5px;">Rp {{ number_format($payment->total_paid, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 rounded-3 border border-danger-subtle bg-danger-subtle bg-opacity-25">
                        <small class="text-danger d-block fw-medium" style="font-size: 11.5px;">SISA YANG BELUM TERBAYAR:</small>
                        <span class="fw-bold text-danger nowrap tabular-nums fs-4">Rp {{ number_format($payment->remaining_balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <small class="text-secondary d-block" style="font-size: 11.5px;">Batas Jatuh Tempo:</small>
                <div class="fw-semibold mt-0.5 {{ $payment->status === 'jatuh_tempo' ? 'text-danger' : 'text-main' }}">
                    {{ $payment->deadline ? $payment->deadline->format('d F Y, H:i') : '-' }}
                    @if($payment->deadline)
                        <small class="text-muted ms-1 font-normal">({{ $payment->deadline->diffForHumans() }})</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Catat Pembayaran Langsung (Jika Belum Lunas) -->
        @if($payment->remaining_balance > 0)
        <div class="pb-card p-4">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                <x-icon name="wallet" size="18" class="text-primary" />
                <h5 class="fw-semibold text-main mb-0 fs-6">Catat Pembayaran / Cicilan</h5>
            </div>
            
            <form action="{{ route('payments.history.store', $payment->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="pb-label">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                        <input type="number"
                               name="amount"
                               class="form-control fw-bold fs-6 tabular-nums"
                               min="1"
                               max="{{ (int)$payment->remaining_balance }}"
                               value="{{ (int)$payment->remaining_balance }}"
                               required>
                    </div>
                    <small class="text-muted" style="font-size: 11.5px;">Maksimal pembayaran: Rp {{ number_format($payment->remaining_balance, 0, ',', '.') }}.</small>
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
                    <label class="pb-label">Tanggal Bayar</label>
                    <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                </div>

                <div class="mb-3">
                    <label class="pb-label">Catatan Pembayaran</label>
                    <input type="text" name="note" class="form-control" placeholder="Contoh: Cicilan pelunasan via kasir">
                </div>

                <x-button type="submit" variant="primary" class="w-100 justify-content-center" icon="check">
                    Simpan Pembayaran
                </x-button>
            </form>
        </div>
        @endif
    </div>

    <!-- Kolom Kanan: Tabel Riwayat Pembayaran (Payment History) -->
    <div class="col-12 col-lg-7">
        <div class="pb-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                <div class="d-flex align-items-center gap-2">
                    <x-icon name="clock" size="18" class="text-primary" />
                    <h5 class="fw-semibold text-main mb-0 fs-6">Riwayat Pembayaran (Payment Histories)</h5>
                </div>
                <x-badge variant="neutral">{{ $payment->paymentHistories->count() }} Transaksi</x-badge>
            </div>

            <div class="table-responsive">
                <table class="pb-table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">No.</th>
                            <th>Tanggal & Waktu</th>
                            <th>Metode Bayar</th>
                            <th>Catatan</th>
                            <th class="text-end">Jumlah Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payment->paymentHistories as $idx => $hist)
                        <tr>
                            <td class="text-secondary small">{{ $idx + 1 }}</td>
                            <td class="nowrap">
                                <div class="fw-medium text-main">{{ $hist->date ? $hist->date->format('d M Y') : '-' }}</div>
                                <div class="text-secondary small" style="font-size: 11.5px;">{{ $hist->date ? $hist->date->format('H:i') . ' WIB' : '' }}</div>
                            </td>
                            <td>
                                <x-badge variant="neutral" size="sm">{{ $hist->method }}</x-badge>
                            </td>
                            <td>
                                <span class="small text-secondary" style="font-size: 12px;">{{ $hist->note ?: '-' }}</span>
                            </td>
                            <td class="text-end fw-bold text-success nowrap tabular-nums">
                                Rp {{ number_format($hist->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary small">
                                Belum ada riwayat pembayaran yang tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-top" style="border-color: var(--pb-border) !important;">
                        <tr>
                            <td colspan="4" class="text-end py-3 fw-semibold text-main">Total Akumulasi Terbayar:</td>
                            <td class="text-end py-3 fw-bold text-success fs-5 nowrap tabular-nums">
                                Rp {{ number_format($payment->total_paid, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
