@extends('layouts.app')

@section('title', 'Surat Jalan ' . $shipment->shipment_number . ' — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Logistik
            </a>
            <span class="text-muted small">/</span>
            <span class="text-dark small fw-bold font-monospace">{{ $shipment->shipment_number }}</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning rounded-pill px-3 py-1 small fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus">
                <i class="bi bi-arrow-repeat me-1"></i> Perbarui Status
            </button>
            <button type="button" class="btn btn-primary-custom rounded-pill px-3 py-1 small fw-semibold shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Surat Jalan
            </button>
        </div>
    </div>

    <!-- Surat Jalan Card -->
    <div class="card card-custom p-4 p-md-5 mb-4 shadow-sm" id="printableSuratJalan">
        <div class="d-flex flex-wrap justify-content-between align-items-start border-bottom pb-4 mb-4 gap-3">
            <div>
                <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 mb-2">SURAT JALAN & LOGISTIK PENGIRIMAN MATERIAL</span>
                <h4 class="fw-bold mb-1 text-dark">Prima Bangunan</h4>
                <div class="text-secondary small">Armada Pengiriman & Logistik Gudang</div>
            </div>

            <div class="text-md-end">
                <h5 class="fw-bold text-primary font-monospace mb-1">{{ $shipment->shipment_number }}</h5>
                <div class="text-muted small">Tanggal: <span class="fw-semibold text-dark">{{ $shipment->date->format('d F Y, H:i') }} WIB</span></div>
                <div class="mt-2">
                    @if($shipment->status === 'delivered')
                        <span class="badge bg-success text-white px-3 py-1 fs-6"><i class="bi bi-check-circle-fill me-1"></i> TERKIRIM (DELIVERED)</span>
                    @elseif($shipment->status === 'shipping')
                        <span class="badge bg-warning text-dark px-3 py-1 fs-6"><i class="bi bi-truck me-1"></i> SEDANG DI JALAN (SHIPPING)</span>
                    @elseif($shipment->status === 'pending')
                        <span class="badge bg-secondary text-white px-3 py-1 fs-6"><i class="bi bi-hourglass-split me-1"></i> MENUNGGU (PENDING)</span>
                    @else
                        <span class="badge bg-danger text-white px-3 py-1 fs-6"><i class="bi bi-arrow-return-left me-1"></i> RETUR (RETURNED)</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-muted small mb-1">
                        @if($shipment->type === 'outgoing')
                            Tujuan Pengiriman ke Pelanggan:
                        @else
                            Asal Pengiriman dari Supplier:
                        @endif
                    </div>
                    @if($shipment->invoice)
                        <div class="fs-5 fw-bold text-dark">{{ $shipment->invoice->client_name }}</div>
                        <div class="small text-muted mb-2">Invoice: <strong>{{ $shipment->invoice->invoice_number }}</strong></div>
                    @elseif($shipment->invoiceSupplier)
                        <div class="fs-5 fw-bold text-dark">{{ $shipment->invoiceSupplier->supplier->name ?? 'Supplier' }}</div>
                        <div class="small text-muted mb-2">Faktur Masuk: <strong>{{ $shipment->invoiceSupplier->invoice_number }}</strong></div>
                    @endif

                    <div class="small text-dark">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> <strong>Alamat:</strong> {{ $shipment->address }}
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <div class="text-muted small mb-1">Detail Ekspedisi / Kurir:</div>
                    <div class="small text-dark mb-1">
                        Biaya Ongkos Kirim: <strong class="text-primary font-monospace">Rp {{ number_format($shipment->fee, 0, ',', '.') }}</strong>
                    </div>
                    <div class="small text-muted">
                        Catatan Pengiriman: <em>{{ $shipment->notes ?: 'Tidak ada catatan tambahan.' }}</em>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Barang yang Dikirim -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No.</th>
                        <th>Barang Material</th>
                        <th>Kategori / Merek</th>
                        <th class="text-center" style="width: 140px;">Jumlah Muatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shipment->shipmentDetails as $index => $sd)
                    <tr>
                        <td class="ps-3 text-center text-muted small">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $sd->item->name ?? 'Barang' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border">{{ $sd->item->category->name ?? '-' }}</span>
                            @if($sd->item && $sd->item->brand)
                                <small class="text-muted ms-1">{{ $sd->item->brand }}</small>
                            @endif
                        </td>
                        <td class="text-center fw-bold fs-6 text-dark">{{ $sd->quantity }} unit</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan Penerimaan (Print Friendly) -->
        <div class="row mt-5 pt-4 text-center d-none d-print-flex">
            <div class="col-4">
                <div class="small text-muted mb-5">Pengirim / Sopir,</div>
                <div class="fw-bold text-dark">( ......................... )</div>
            </div>
            <div class="col-4">
                <div class="small text-muted mb-5">Petugas Gudang,</div>
                <div class="fw-bold text-dark">( ......................... )</div>
            </div>
            <div class="col-4">
                <div class="small text-muted mb-5">Penerima Barang,</div>
                <div class="fw-bold text-dark">( ......................... )</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Status Pengiriman -->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('shipments.status.update', $shipment->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-arrow-repeat me-2 text-warning"></i> Perbarui Status Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Status Pengiriman Baru <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $shipment->status === 'pending' ? 'selected' : '' }}>Pending (Menunggu Jadwal)</option>
                            <option value="shipping" {{ $shipment->status === 'shipping' ? 'selected' : '' }}>Shipping (Sedang Dalam Perjalanan)</option>
                            <option value="delivered" {{ $shipment->status === 'delivered' ? 'selected' : '' }}>Delivered (Telah Diterima Lengkap)</option>
                            <option value="returned" {{ $shipment->status === 'returned' ? 'selected' : '' }}>Returned (Barang Retur/Dibatalkan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Catatan Kurir / Supir</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Diterima oleh Pak Slamet / Masuk gudang jam 14:00">{{ $shipment->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-semibold">Simpan Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
