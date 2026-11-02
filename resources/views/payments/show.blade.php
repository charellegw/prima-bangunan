@extends('layouts.app')

@section('title', 'Detail Pembayaran — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pembayaran
            </a>
        </div>
        <div>
            @if($payment->invoice)
                <a href="{{ route('invoices.show', $payment->invoice->id) }}" class="btn btn-primary-custom rounded-pill px-3 py-1 small">
                    <i class="bi bi-receipt me-1"></i> Buka Nota Invoice
                </a>
            @elseif($payment->invoiceSupplier)
                <a href="{{ route('invoice-suppliers.show', $payment->invoiceSupplier->id) }}" class="btn btn-primary-custom rounded-pill px-3 py-1 small">
                    <i class="bi bi-file-earmark-text me-1"></i> Buka Faktur Supplier
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Info Ringkasan Tagihan & Status -->
        <div class="col-12 col-lg-5">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-credit-card me-2 text-primary"></i> Ringkasan Tagihan</h5>

                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="text-muted small">Status Pembayaran Saat Ini:</div>
                    <div class="mt-2">
                        @if($payment->status === 'lunas')
                            <span class="badge-lunas fs-6"><i class="bi bi-check-circle-fill me-1"></i> LUNAS</span>
                        @elseif($payment->status === 'sebagian')
                            <span class="badge-sebagian fs-6"><i class="bi bi-hourglass-split me-1"></i> DIBAYAR SEBAGIAN</span>
                        @elseif($payment->status === 'jatuh_tempo')
                            <span class="badge-jatuh-tempo fs-6"><i class="bi bi-exclamation-triangle-fill me-1"></i> JATUH TEMPO</span>
                        @else
                            <span class="badge-belum-bayar fs-6"><i class="bi bi-x-circle-fill me-1"></i> BELUM BAYAR</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <div class="text-muted small mb-1">Dokumen Transaksi:</div>
                    @if($payment->invoice)
                        <div class="fw-bold text-dark fs-6">{{ $payment->invoice->invoice_number }}</div>
                        <small class="text-muted">Pelanggan: <strong>{{ $payment->invoice->client_name }}</strong> ({{ $payment->invoice->client_type }})</small>
                    @elseif($payment->invoiceSupplier)
                        <div class="fw-bold text-dark fs-6">{{ $payment->invoiceSupplier->invoice_number }}</div>
                        <small class="text-muted">Supplier: <strong>{{ $payment->invoiceSupplier->supplier->name ?? 'Supplier' }}</strong></small>
                    @endif
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-2 border">
                            <small class="text-muted d-block">Total Tagihan:</small>
                            <span class="fw-bold text-dark font-monospace fs-6">Rp {{ number_format($payment->amount_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-2 border">
                            <small class="text-muted d-block">Sudah Dibayar:</small>
                            <span class="fw-bold text-success font-monospace fs-6">Rp {{ number_format($payment->total_paid, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-2 border border-danger border-opacity-25">
                            <small class="text-muted d-block">Sisa yang Belum Terbayar:</small>
                            <span class="fw-bold text-danger font-monospace fs-4">Rp {{ number_format($payment->remaining_balance, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <small class="text-muted d-block">Jatuh Tempo:</small>
                    <span class="fw-bold {{ $payment->status === 'jatuh_tempo' ? 'text-danger' : 'text-dark' }}">
                        {{ $payment->deadline ? $payment->deadline->format('d F Y, H:i') : '-' }}
                    </span>
                    @if($payment->deadline)
                        <small class="text-muted ms-1">({{ $payment->deadline->diffForHumans() }})</small>
                    @endif
                </div>
            </div>

            <!-- Form Catat Pembayaran Langsung (Jika Belum Lunas) -->
            @if($payment->remaining_balance > 0)
            <div class="card card-custom p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cash-coin me-2 text-success"></i> Catat Pembayaran / Cicilan</h6>
                <form action="{{ route('payments.history.store', $payment->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number"
                                   name="amount"
                                   class="form-control fw-bold fs-6"
                                   min="1"
                                   max="{{ (int)$payment->remaining_balance }}"
                                   value="{{ (int)$payment->remaining_balance }}"
                                   required>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">Maksimal pembayaran: Rp {{ number_format($payment->remaining_balance, 0, ',', '.') }}.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="method" class="form-select" required>
                            <option value="Tunai (Cash)">Tunai (Cash)</option>
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Giro / Cek">Giro / Cek</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Tanggal Bayar</label>
                        <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Catatan Pembayaran</label>
                        <input type="text" name="note" class="form-control" placeholder="Misal: Pelunasan sisa bon / transfer via kasir">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Kolom Kanan: Tabel Riwayat Pembayaran (Payment History) -->
        <div class="col-12 col-lg-7">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i> Riwayat Pembayaran (Payment Histories)</h5>
                    <span class="badge bg-light text-secondary border">{{ $payment->paymentHistories->count() }} Transaksi</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-secondary">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No.</th>
                                <th>Tanggal & Waktu</th>
                                <th>Metode Bayar</th>
                                <th>Catatan</th>
                                <th class="text-end pe-3">Jumlah Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payment->paymentHistories as $idx => $hist)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $hist->date->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $hist->date->format('H:i') }} WIB</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $hist->method }}</span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $hist->note ?: '-' }}</span>
                                </td>
                                <td class="text-end pe-3 fw-bold font-monospace text-success fs-6">
                                    Rp {{ number_format($hist->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pembayaran yang tercatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <th colspan="4" class="text-end py-3">Total Akumulasi Terbayar:</th>
                                <th class="text-end pe-3 py-3 fw-bold text-success font-monospace fs-5">
                                    Rp {{ number_format($payment->total_paid, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
