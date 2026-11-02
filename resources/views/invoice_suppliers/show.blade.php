@extends('layouts.app')

@section('title', 'Faktur Masuk ' . $invoiceSupplier->invoice_number . ' — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <!-- Action Bar -->
    <div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('invoice-suppliers.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Faktur
            </a>
            <span class="text-muted small">/</span>
            <span class="text-dark small fw-bold font-monospace">{{ $invoiceSupplier->invoice_number }}</span>
        </div>
        <div class="d-flex gap-2">
            @if($invoiceSupplier->payment && $invoiceSupplier->payment->remaining_balance > 0)
                <button type="button" class="btn btn-success rounded-pill px-3 py-1 small fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBayarHutang">
                    <i class="bi bi-cash-coin me-1"></i> Bayar Hutang Supplier
                </button>
            @endif
            <button type="button" class="btn btn-primary-custom rounded-pill px-3 py-1 small fw-semibold shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Bukti Faktur
            </button>
        </div>
    </div>

    <!-- Faktur Pembelian Card -->
    <div class="card card-custom p-4 p-md-5 mb-4 shadow-sm" id="printableFaktur">
        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-start border-bottom pb-4 mb-4 gap-3">
            <div>
                <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 mb-2">FAKTUR PEMBELIAN MATERIAL DARI SUPPLIER</span>
                <h4 class="fw-bold mb-1 text-dark">{{ $invoiceSupplier->store->name ?? 'Prima Bangunan' }}</h4>
                <div class="text-secondary small">Gudang Penerima: {{ $invoiceSupplier->store->address ?? 'Jakarta Barat' }}</div>
            </div>

            <div class="text-md-end">
                <h5 class="fw-bold text-primary font-monospace mb-1">{{ $invoiceSupplier->invoice_number }}</h5>
                <div class="text-muted small">Tanggal: <span class="fw-semibold text-dark">{{ $invoiceSupplier->date->format('d F Y, H:i') }} WIB</span></div>
                <div class="mt-2">
                    @php $status = $invoiceSupplier->payment->status ?? 'belum_bayar'; @endphp
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

        <!-- Supplier & Payment Details -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-muted small mb-1">Identitas Supplier:</div>
                    <div class="fs-5 fw-bold text-dark">{{ $invoiceSupplier->supplier->name ?? 'Supplier' }}</div>
                    <div class="small text-secondary mt-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $invoiceSupplier->supplier->location ?: 'Lokasi tidak dicatat' }}
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-telephone me-1"></i> {{ $invoiceSupplier->supplier->phone ?: '-' }}
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <div class="text-muted small mb-1">Keterangan Hutang:</div>
                    <div class="small text-dark mb-1">
                        Jatuh Tempo: <strong>{{ $invoiceSupplier->payment->deadline ? $invoiceSupplier->payment->deadline->format('d M Y') : '-' }}</strong>
                    </div>
                    @if($invoiceSupplier->note)
                        <div class="small text-muted">
                            Catatan: <em>{{ $invoiceSupplier->note }}</em>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabel Barang Diterima -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No.</th>
                        <th>Barang Material Masuk</th>
                        <th class="text-center" style="width: 120px;">Jumlah Masuk</th>
                        <th class="text-end" style="width: 170px;">Harga Beli Satuan</th>
                        <th class="text-end pe-3" style="width: 180px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoiceSupplier->invoiceSupplierDetails as $index => $detail)
                    <tr>
                        <td class="ps-3 text-center text-muted small">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $detail->item->name ?? 'Barang' }}</div>
                            @if($detail->item && $detail->item->brand)
                                <small class="text-muted">Merek: {{ $detail->item->brand }}</small>
                            @endif
                        </td>
                        <td class="text-center fw-semibold text-dark">{{ $detail->quantity }} unit</td>
                        <td class="text-end font-monospace">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                        <td class="text-end pe-3 fw-bold font-monospace text-dark">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-top">
                    <tr>
                        <th colspan="4" class="text-end py-3 fs-6">Total Pembelian:</th>
                        <th class="text-end pe-3 py-3 fs-5 fw-bold text-primary font-monospace">
                            Rp {{ number_format($invoiceSupplier->grand_total, 0, ',', '.') }}
                        </th>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end text-success fw-semibold">Sudah Dibayar ke Supplier:</td>
                        <td class="text-end pe-3 fw-bold font-monospace text-success">
                            Rp {{ number_format($invoiceSupplier->payment->total_paid ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end text-danger fw-semibold">Sisa Hutang Toko:</td>
                        <td class="text-end pe-3 fw-bold font-monospace text-danger">
                            Rp {{ number_format($invoiceSupplier->payment->remaining_balance ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Log Pengiriman Masuk (Shipment Incoming) -->
        @if($invoiceSupplier->shipments->count() > 0)
        <div class="mb-4 p-3 bg-light rounded-3 border">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-truck me-2 text-primary"></i> Data Kiriman Masuk dari Supplier</h6>
            @foreach($invoiceSupplier->shipments as $sh)
            <div class="row g-2 small">
                <div class="col-12 col-md-3">
                    <span class="text-muted">No. Pengiriman:</span>
                    <span class="fw-bold font-monospace text-dark d-block">{{ $sh->shipment_number }}</span>
                </div>
                <div class="col-12 col-md-4">
                    <span class="text-muted">Alamat Gudang:</span>
                    <span class="text-dark d-block">{{ $sh->address }}</span>
                </div>
                <div class="col-12 col-md-2">
                    <span class="text-muted">Tanggal:</span>
                    <span class="text-dark d-block">{{ $sh->date->format('d M Y') }}</span>
                </div>
                <div class="col-12 col-md-3">
                    <span class="text-muted">Status Logistik:</span>
                    <span class="badge bg-success text-white d-block text-capitalize py-1 mt-1">{{ $sh->status }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Riwayat Pembayaran ke Supplier -->
        <div class="mt-4 pt-3 border-top">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i> Riwayat Pembayaran Hutang Dagang</h6>
                @if($invoiceSupplier->payment && $invoiceSupplier->payment->remaining_balance > 0)
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill d-print-none" data-bs-toggle="modal" data-bs-target="#modalBayarHutang">
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
                        @forelse($invoiceSupplier->payment->paymentHistories ?? [] as $hist)
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
                            <td colspan="4" class="text-center py-3 text-muted small">Belum ada riwayat pembayaran untuk faktur supplier ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bayar Hutang Supplier -->
@if($invoiceSupplier->payment && $invoiceSupplier->payment->remaining_balance > 0)
<div class="modal fade" id="modalBayarHutang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('payments.history.store', $invoiceSupplier->payment->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-cash-coin me-2 text-success"></i> Catat Pembayaran Hutang ke Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Pembelian:</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($invoiceSupplier->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Sisa Hutang:</span>
                            <span class="fw-bold text-danger">Rp {{ number_format($invoiceSupplier->payment->remaining_balance, 0, ',', '.') }}</span>
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
                                   max="{{ (int)$invoiceSupplier->payment->remaining_balance }}"
                                   value="{{ (int)$invoiceSupplier->payment->remaining_balance }}"
                                   required>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">*Maksimal pembayaran adalah sisa hutang (Rp {{ number_format($invoiceSupplier->payment->remaining_balance, 0, ',', '.') }}).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="method" class="form-select" required>
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            <option value="Giro / Cek">Giro / Cek</option>
                            <option value="Tunai (Cash)">Tunai (Cash)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Tanggal Pembayaran</label>
                        <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Catatan / Bukti Transfer</label>
                        <input type="text" name="note" class="form-control" placeholder="Misal: Pelunasan via Mandiri ref#78901">
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
