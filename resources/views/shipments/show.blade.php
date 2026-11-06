@extends('layouts.app')

@section('title', 'Surat Jalan ' . $shipment->shipment_number . ' — Prima Bangunan')

@section('content')
<div class="d-flex flex-column gap-4">
    <!-- Top Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 d-print-none">
        <div class="d-flex align-items-center gap-2">
            <x-button href="{{ route('shipments.index') }}" variant="secondary" size="sm">
                <x-icon name="arrow-left" style="width: 14px; height: 14px;" class="me-1" /> Kembali
            </x-button>
            <span class="text-muted small">/</span>
            <span class="text-dark small fw-semibold tabular-nums">{{ $shipment->shipment_number }}</span>
        </div>
        <div class="d-flex gap-2">
            <x-button type="button" variant="warning" size="sm" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus">
                <x-icon name="refresh" style="width: 14px; height: 14px;" class="me-1" /> Perbarui Status
            </x-button>
            <x-button type="button" variant="outline" size="sm" onclick="window.print()">
                <x-icon name="printer" style="width: 14px; height: 14px;" class="me-1" /> Cetak Surat Jalan
            </x-button>
        </div>
    </div>

    <!-- Surat Jalan Card -->
    <x-card class="p-4 p-md-5" id="printableSuratJalan">
        <div class="d-flex flex-wrap justify-content-between align-items-start border-bottom pb-4 mb-4 gap-3">
            <div>
                <x-badge variant="neutral" class="mb-2">SURAT JALAN & LOGISTIK PENGIRIMAN</x-badge>
                <h2 class="h4 fw-bold mb-1 text-dark">Prima Bangunan</h2>
                <div class="text-secondary small">Armada Pengiriman & Logistik Gudang</div>
            </div>

            <div class="text-md-end">
                <div class="h5 fw-bold text-primary tabular-nums mb-1">{{ $shipment->shipment_number }}</div>
                <div class="text-muted small">Tanggal: <span class="fw-medium text-dark tabular-nums">{{ $shipment->date->format('d F Y, H:i') }} WIB</span></div>
                <div class="mt-2">
                    @if($shipment->status === 'delivered')
                        <x-badge variant="success" class="fs-6 px-3 py-1">
                            <x-icon name="check" style="width: 14px; height: 14px;" class="me-1" /> Terkirim (Delivered)
                        </x-badge>
                    @elseif($shipment->status === 'shipping')
                        <x-badge variant="warning" class="fs-6 px-3 py-1">
                            <x-icon name="truck" style="width: 14px; height: 14px;" class="me-1" /> Di Jalan (Shipping)
                        </x-badge>
                    @elseif($shipment->status === 'pending')
                        <x-badge variant="neutral" class="fs-6 px-3 py-1">
                            <x-icon name="clock" style="width: 14px; height: 14px;" class="me-1" /> Menunggu (Pending)
                        </x-badge>
                    @else
                        <x-badge variant="danger" class="fs-6 px-3 py-1">
                            <x-icon name="alert-triangle" style="width: 14px; height: 14px;" class="me-1" /> Retur (Returned)
                        </x-badge>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="p-3.5 bg-light rounded-3 border" style="background-color: var(--pb-bg-muted) !important; border-color: var(--pb-border) !important; padding: 16px;">
                    <div class="text-muted small mb-1">
                        @if($shipment->type === 'outgoing')
                            Tujuan Pengiriman ke Pelanggan:
                        @else
                            Asal Pengiriman dari Supplier:
                        @endif
                    </div>
                    @if($shipment->invoice)
                        <div class="fs-5 fw-bold text-dark">{{ $shipment->invoice->client_name }}</div>
                        <div class="small text-muted mb-2">No. Nota: <span class="fw-semibold text-dark tabular-nums">{{ $shipment->invoice->invoice_number }}</span></div>
                    @elseif($shipment->invoiceSupplier)
                        <div class="fs-5 fw-bold text-dark">{{ $shipment->invoiceSupplier->supplier->name ?? 'Supplier' }}</div>
                        <div class="small text-muted mb-2">Faktur Masuk: <span class="fw-semibold text-dark tabular-nums">{{ $shipment->invoiceSupplier->invoice_number }}</span></div>
                    @endif

                    <div class="small text-dark mt-2">
                        <span class="text-secondary">Alamat:</span> {{ $shipment->address }}
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="p-3.5 bg-light rounded-3 border h-100" style="background-color: var(--pb-bg-muted) !important; border-color: var(--pb-border) !important; padding: 16px;">
                    <div class="text-muted small mb-1">Detail Ekspedisi / Kurir:</div>
                    <div class="small text-dark mb-1">
                        Ongkos Kirim: <span class="text-dark fw-semibold tabular-nums">Rp {{ number_format($shipment->fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="small text-muted mt-2">
                        Catatan: <span class="text-dark">{{ $shipment->notes ?: 'Tidak ada catatan tambahan.' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Barang yang Dikirim -->
        <div class="table-responsive mb-4">
            <table class="table pb-table align-middle">
                <thead>
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
                        <td class="ps-3 text-center text-muted small tabular-nums">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $sd->item->name ?? 'Barang' }}</div>
                        </td>
                        <td>
                            <x-badge variant="neutral">{{ $sd->item->category->name ?? '-' }}</x-badge>
                            @if($sd->item && $sd->item->brand)
                                <small class="text-muted ms-1">{{ $sd->item->brand }}</small>
                            @endif
                        </td>
                        <td class="text-center fw-bold text-dark tabular-nums">{{ $sd->quantity }} unit</td>
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
    </x-card>
</div>

<!-- Modal Update Status Pengiriman -->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm rounded-4">
            <form action="{{ route('shipments.status.update', $shipment->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark fs-6">Perbarui Status Pengiriman</h5>
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
                        <textarea name="notes" rows="3" class="form-control" placeholder="Contoh: Diterima oleh Pak Slamet / Masuk gudang jam 14:00">{{ $shipment->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <x-button type="button" variant="secondary" data-bs-dismiss="modal">Batal</x-button>
                    <x-button type="submit" variant="primary">Simpan Status</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
