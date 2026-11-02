@extends('layouts.app')

@section('title', 'Nota ' . $invoice->invoice_number . ' — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Action Bar -->
    <div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <span class="text-muted small">/</span>
            <span class="text-dark small fw-bold font-monospace">{{ $invoice->invoice_number }}</span>
        </div>
        <div class="d-flex gap-2">
            @if($invoice->payment && $invoice->payment->remaining_balance > 0)
                <button type="button" class="btn btn-success rounded-pill px-3 py-1 small fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCatatPembayaran">
                    <i class="bi bi-cash-coin me-1"></i> Catat Pembayaran / Pelunasan
                </button>
            @endif
            <button type="button" class="btn btn-primary-custom rounded-pill px-3 py-1 small fw-semibold shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Nota
            </button>
        </div>
    </div>

    <!-- Official Invoice / Nota Card -->
    <div class="card card-custom p-4 p-md-5 mb-4 shadow-sm" id="printableNota">
        <!-- Header Nota -->
        <div class="d-flex flex-wrap justify-content-between align-items-start border-bottom pb-4 mb-4 gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-building-fill fs-5"></i>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark">{{ $invoice->store->name ?? 'Prima Bangunan' }}</h4>
                </div>
                <div class="text-secondary small">{{ $invoice->store->address ?? 'Jl. Raya Industri Bangunan No. 88, Jakarta Barat' }}</div>
                <div class="text-secondary small">Telp: {{ $invoice->store->phone ?? '021-5558899' }}</div>
            </div>

            <div class="text-md-end">
                <h5 class="fw-bold text-primary font-monospace mb-1">{{ $invoice->invoice_number }}</h5>
                <div class="text-muted small">Tanggal: <span class="fw-semibold text-dark">{{ $invoice->date->format('d F Y, H:i') }} WIB</span></div>
                <div class="mt-2">
                    @php $status = $invoice->payment->status ?? 'belum_bayar'; @endphp
                    @if($status === 'lunas')
                        <span class="badge-lunas fs-6"><i class="bi bi-check-circle-fill me-1"></i> LUNAS</span>
                    @elseif($status === 'sebagian')
                        <span class="badge-sebagian fs-6"><i class="bi bi-hourglass-split me-1"></i> DIBAYAR SEBAGIAN</span>
                    @elseif($status === 'jatuh_tempo')
                        <span class="badge-jatuh-tempo fs-6"><i class="bi bi-exclamation-triangle-fill me-1"></i> JATUH TEMPO</span>
                    @else
                        <span class="badge-belum-bayar fs-6"><i class="bi bi-x-circle-fill me-1"></i> BELUM BAYAR</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Info Pelanggan & Transaksi -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-muted small mb-1">Ditujukan Kepada:</div>
                    <div class="fs-5 fw-bold text-dark">{{ $invoice->client_name }}</div>
                    <div class="mt-1">
                        @if($invoice->client_type === 'retail')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Pelanggan Retail / Toko (Harga Grosir)</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Pelanggan Konsumen (Harga Eceran)</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <div class="text-muted small mb-1">Informasi Penagihan:</div>
                    <div class="small text-dark mb-1">
                        Jatuh Tempo: <strong>{{ $invoice->payment->deadline ? $invoice->payment->deadline->format('d M Y') : '-' }}</strong>
                    </div>
                    @if($invoice->note)
                        <div class="small text-muted">
                            Catatan: <em>{{ $invoice->note }}</em>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabel Detail Barang (Snapshot Harga) -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No.</th>
                        <th>Deskripsi Barang Material</th>
                        <th class="text-center" style="width: 100px;">Jumlah</th>
                        <th class="text-end" style="width: 160px;">Harga Satuan (Snapshot)</th>
                        <th class="text-end pe-3" style="width: 180px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->invoiceDetails as $index => $detail)
                    <tr>
                        <td class="ps-3 text-center text-muted small">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $detail->item->name ?? 'Barang' }}</div>
                            @if($detail->item && $detail->item->brand)
                                <small class="text-muted">Merek: {{ $detail->item->brand }}</small>
                            @endif
                        </td>
                        <td class="text-center fw-semibold text-dark">{{ $detail->quantity }}</td>
                        <td class="text-end font-monospace">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                        <td class="text-end pe-3 fw-bold font-monospace text-dark">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-top">
                    <tr>
                        <th colspan="4" class="text-end py-3 fs-6">Grand Total:</th>
                        <th class="text-end pe-3 py-3 fs-5 fw-bold text-primary font-monospace">
                            Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                        </th>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end text-success fw-semibold">Sudah Dibayar:</td>
                        <td class="text-end pe-3 fw-bold font-monospace text-success">
                            Rp {{ number_format($invoice->payment->total_paid ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end text-danger fw-semibold">Sisa Tagihan:</td>
                        <td class="text-end pe-3 fw-bold font-monospace text-danger">
                            Rp {{ number_format($invoice->payment->remaining_balance ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Bagian Pengiriman (Shipment) Jika Ada -->
        @if($invoice->shipments->count() > 0)
        <div class="mb-4 p-3 bg-light rounded-3 border">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-truck me-2 text-primary"></i> Data Pengiriman Barang</h6>
            @foreach($invoice->shipments as $sh)
            <div class="row g-2 small">
                <div class="col-12 col-md-3">
                    <span class="text-muted">No. Pengiriman:</span>
                    <span class="fw-bold font-monospace text-dark d-block">{{ $sh->shipment_number }}</span>
                </div>
                <div class="col-12 col-md-4">
                    <span class="text-muted">Alamat Tujuan:</span>
                    <span class="text-dark d-block">{{ $sh->address }}</span>
                </div>
                <div class="col-12 col-md-2">
                    <span class="text-muted">Ongkir:</span>
                    <span class="text-dark fw-bold d-block">Rp {{ number_format($sh->fee, 0, ',', '.') }}</span>
                </div>
                <div class="col-12 col-md-3">
                    <span class="text-muted">Status Pengiriman:</span>
                    <span class="badge bg-primary text-white d-block text-capitalize py-1 mt-1">{{ $sh->status }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Riwayat Pembayaran (Payment History) -->
        <div class="mt-4 pt-3 border-top">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i> Riwayat Pembayaran (Payment History)</h6>
                @if($invoice->payment && $invoice->payment->remaining_balance > 0)
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill d-print-none" data-bs-toggle="modal" data-bs-target="#modalCatatPembayaran">
                        <i class="bi bi-plus-lg me-1"></i> Catat Pembayaran
                    </button>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light small text-secondary">
                        <tr>
                            <th>Waktu Bayar</th>
                            <th>Metode Pembayaran</th>
                            <th>Catatan</th>
                            <th class="text-end pe-3">Jumlah Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->payment->paymentHistories ?? [] as $hist)
                        <tr>
                            <td class="small text-dark">{{ $hist->date->format('d M Y, H:i') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $hist->method }}</span></td>
                            <td class="small text-muted">{{ $hist->note ?: '-' }}</td>
                            <td class="text-end pe-3 fw-bold font-monospace text-success">
                                Rp {{ number_format($hist->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted small">Belum ada riwayat pembayaran yang tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan Nota (Print Friendly) -->
        <div class="row mt-5 pt-4 text-center d-none d-print-flex">
            <div class="col-6">
                <div class="small text-muted mb-5">Penerima / Pelanggan,</div>
                <div class="fw-bold text-dark">({{ $invoice->client_name }})</div>
            </div>
            <div class="col-6">
                <div class="small text-muted mb-5">Hormat Kami,</div>
                <div class="fw-bold text-dark">(Kasir Prima Bangunan)</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Catat Pembayaran Baru -->
@if($invoice->payment && $invoice->payment->remaining_balance > 0)
<div class="modal fade" id="modalCatatPembayaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('payments.history.store', $invoice->payment->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-cash-coin me-2 text-success"></i> Catat Pembayaran / Pelunasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Tagihan:</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Sisa yang Belum Dibayar:</span>
                            <span class="fw-bold text-danger">Rp {{ number_format($invoice->payment->remaining_balance, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number"
                                   name="amount"
                                   class="form-control fw-bold fs-6"
                                   min="1"
                                   max="{{ (int)$invoice->payment->remaining_balance }}"
                                   value="{{ (int)$invoice->payment->remaining_balance }}"
                                   required>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">*Maksimal pembayaran adalah sisa tagihan saat ini (Rp {{ number_format($invoice->payment->remaining_balance, 0, ',', '.') }}).</small>
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
                        <label class="form-label small fw-semibold text-dark">Tanggal Pembayaran</label>
                        <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Catatan Pembayaran</label>
                        <input type="text" name="note" class="form-control" placeholder="Misal: Pelunasan sisa bon, transfer via BCA Pak Budi">
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
