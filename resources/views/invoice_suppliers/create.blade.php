@extends('layouts.app')

@section('title', 'Faktur Pembelian Supplier — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Catat Pembelian Stok dari Supplier</h4>
            <p class="text-muted small mb-0">Input penerimaan material dari supplier/pabrik. Stok barang akan otomatis bertambah saat faktur disimpan.</p>
        </div>
        <div>
            <a href="{{ route('invoice-suppliers.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <form action="{{ route('invoice-suppliers.store') }}" method="POST" id="purchaseForm">
        @csrf
        <div class="row g-4">
            <!-- Kolom Kiri: Data Supplier & Barang Masuk -->
            <div class="col-12 col-lg-8">
                <!-- Info Supplier -->
                <div class="card card-custom p-4 mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-building me-2 text-primary"></i> Data Supplier & Tanggal Faktur</h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Pilih Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplierSelect" class="form-select" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" data-location="{{ $sup->location }}" data-phone="{{ $sup->phone }}">
                                        {{ $sup->name }} ({{ $sup->location ?: 'Lokasi -' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Tanggal Pembelian / Faktur</label>
                            <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">Catatan Pembelian (Nomor PO / Keterangan)</label>
                            <input type="text" name="note" class="form-control" placeholder="Misal: PO-2026/XI/001, Kiriman armada truk Semen Gresik">
                        </div>
                    </div>
                </div>

                <!-- Daftar Barang Masuk (Stok akan ditambah) -->
                <div class="card card-custom p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-boxes me-2 text-primary"></i> Barang yang Dipesan / Masuk</h6>
                            <small class="text-muted" style="font-size: 11px;">Stok barang di sistem akan otomatis bertambah sesuai jumlah yang dicatat.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary-custom rounded-pill" id="btnAddRow">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="purchaseTable">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th style="min-width: 250px;">Nama Barang Material</th>
                                    <th style="width: 100px;">Stok Kini</th>
                                    <th style="width: 160px;">Harga Beli Satuan (Rp)</th>
                                    <th style="width: 100px;">Jumlah Masuk</th>
                                    <th style="width: 150px;">Subtotal</th>
                                    <th style="width: 50px;" class="text-center">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseTableBody">
                                <tr class="purchase-row" data-index="0">
                                    <td>
                                        <select name="items[0][item_id]" class="form-select item-select" required>
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach($items as $it)
                                                <option value="{{ $it->id }}" data-stock="{{ $it->stock }}" data-unit-price="{{ $it->supplierPrices->first()?->unit_price ?? 0 }}">
                                                    {{ $it->name }} (Stok saat ini: {{ $it->stock }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border current-stock-badge">-</span>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][unit_price]" class="form-control form-control-sm unit-price-input" min="0" placeholder="0" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control form-control-sm text-center item-qty" value="1" min="1" required>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark row-subtotal-display">Rp 0</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle btn-remove-row" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Opsi Catat Pengiriman Masuk (Incoming Shipment) -->
                <div class="card card-custom p-4">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="switchIncomingShipment" name="has_incoming_shipment" value="1" checked>
                        <label class="form-check-label fw-bold text-dark" for="switchIncomingShipment">
                            <i class="bi bi-truck-flatbed me-1 text-primary"></i> Catat Log Pengiriman Masuk (Incoming Shipment dari Supplier)
                        </label>
                    </div>
                    <small class="text-muted d-block mb-3">Membuat rekam jejak penerimaan material masuk pada modul Pengiriman.</small>

                    <div id="incomingShipmentSection">
                        <div class="row g-3 pt-2 border-top">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">Status Penerimaan Barang</label>
                                <select name="shipment_status" class="form-select">
                                    <option value="delivered">Sudah Diterima Lengkap di Gudang (Delivered)</option>
                                    <option value="shipping">Sedang Dalam Perjalanan / Pengiriman Truk</option>
                                    <option value="pending">Menunggu Jadwal Pengiriman Supplier</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">Tanggal Penerimaan / Kirim</label>
                                <input type="datetime-local" name="shipment_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Hutang & Pembayaran Supplier -->
            <div class="col-12 col-lg-4">
                <div class="card card-custom p-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-cash-stack me-2 text-primary"></i> Hutang & Pembayaran</h5>

                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="text-muted small">Total Tagihan Faktur:</div>
                        <div class="fs-2 fw-bold text-primary mt-1" id="grandTotalDisplay">Rp 0</div>
                        <small class="text-muted" style="font-size: 11px;">*Dihitung dan diverifikasi server saat disimpan.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Uang Muka / Pembayaran Awal (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="initial_paid" id="initialPaidInput" class="form-control fw-bold fs-6" min="0" value="0">
                        </div>
                        <div class="d-flex gap-1 mt-1">
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" id="btnPayFull">Bayar Lunas</button>
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" id="btnPayZero">Hutang Penuh</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select">
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            <option value="Giro / Cek">Giro / Cek</option>
                            <option value="Tunai (Cash)">Tunai (Cash)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Jatuh Tempo Pembayaran</label>
                        <input type="date" name="deadline" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                        <small class="text-muted" style="font-size: 11px;">Standar tempo supplier 30 hari.</small>
                    </div>

                    <div class="p-3 rounded-3 mb-4 border bg-white">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Sisa Hutang Toko:</span>
                            <span class="fw-bold text-danger" id="remainingBalanceDisplay">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Status Hutang:</span>
                            <span class="badge-belum-bayar small" id="paymentStatusBadge">Belum Bayar</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary-custom py-2 fw-bold fs-6 shadow">
                            <i class="bi bi-box-arrow-in-down me-1"></i> Simpan Faktur & Tambah Stok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const purchaseTableBody = document.getElementById('purchaseTableBody');
        const btnAddRow = document.getElementById('btnAddRow');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');
        const initialPaidInput = document.getElementById('initialPaidInput');
        const remainingBalanceDisplay = document.getElementById('remainingBalanceDisplay');
        const paymentStatusBadge = document.getElementById('paymentStatusBadge');
        const btnPayFull = document.getElementById('btnPayFull');
        const btnPayZero = document.getElementById('btnPayZero');
        const switchIncomingShipment = document.getElementById('switchIncomingShipment');
        const incomingShipmentSection = document.getElementById('incomingShipmentSection');

        let rowCount = 1;

        switchIncomingShipment.addEventListener('change', function () {
            incomingShipmentSection.style.display = this.checked ? 'block' : 'none';
        });

        btnAddRow.addEventListener('click', function () {
            const firstRow = purchaseTableBody.querySelector('.purchase-row');
            const newRow = firstRow.cloneNode(true);
            newRow.setAttribute('data-index', rowCount);

            const select = newRow.querySelector('.item-select');
            select.name = `items[${rowCount}][item_id]`;
            select.selectedIndex = 0;

            const unitPrice = newRow.querySelector('.unit-price-input');
            unitPrice.name = `items[${rowCount}][unit_price]`;
            unitPrice.value = '';

            const qty = newRow.querySelector('.item-qty');
            qty.name = `items[${rowCount}][quantity]`;
            qty.value = 1;

            newRow.querySelector('.current-stock-badge').textContent = '-';
            newRow.querySelector('.row-subtotal-display').textContent = 'Rp 0';

            const btnRemove = newRow.querySelector('.btn-remove-row');
            btnRemove.removeAttribute('disabled');

            purchaseTableBody.appendChild(newRow);
            rowCount++;
            updateRemoveButtons();
            bindRowEvents(newRow);
        });

        function updateRemoveButtons() {
            const rows = purchaseTableBody.querySelectorAll('.purchase-row');
            rows.forEach(r => {
                const btn = r.querySelector('.btn-remove-row');
                if (rows.length > 1) {
                    btn.removeAttribute('disabled');
                } else {
                    btn.setAttribute('disabled', 'disabled');
                }
            });
        }

        function bindRowEvents(row) {
            const select = row.querySelector('.item-select');
            const unitPrice = row.querySelector('.unit-price-input');
            const qty = row.querySelector('.item-qty');
            const stockBadge = row.querySelector('.current-stock-badge');
            const btnRemove = row.querySelector('.btn-remove-row');

            select.addEventListener('change', function () {
                const opt = select.options[select.selectedIndex];
                if (opt && opt.value) {
                    stockBadge.textContent = (opt.getAttribute('data-stock') || '0') + ' unit';
                    const defaultUnit = opt.getAttribute('data-unit-price');
                    if (defaultUnit && parseFloat(defaultUnit) > 0 && !unitPrice.value) {
                        unitPrice.value = parseInt(defaultUnit);
                    }
                } else {
                    stockBadge.textContent = '-';
                }
                recalculate();
            });

            unitPrice.addEventListener('input', recalculate);
            qty.addEventListener('input', recalculate);

            btnRemove.addEventListener('click', function () {
                if (purchaseTableBody.querySelectorAll('.purchase-row').length > 1) {
                    row.remove();
                    updateRemoveButtons();
                    recalculate();
                }
            });
        }

        bindRowEvents(purchaseTableBody.querySelector('.purchase-row'));

        function recalculate() {
            let grandTotal = 0;
            const rows = purchaseTableBody.querySelectorAll('.purchase-row');

            rows.forEach(row => {
                const unitPrice = parseFloat(row.querySelector('.unit-price-input').value || '0');
                const qty = parseInt(row.querySelector('.item-qty').value || '1');
                const subtotal = unitPrice * qty;
                grandTotal += subtotal;

                row.querySelector('.row-subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            });

            grandTotalDisplay.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            let initialPaid = parseFloat(initialPaidInput.value || '0');
            if (initialPaid > grandTotal) {
                initialPaid = grandTotal;
                initialPaidInput.value = grandTotal;
            }

            const remaining = Math.max(0, grandTotal - initialPaid);
            remainingBalanceDisplay.textContent = 'Rp ' + remaining.toLocaleString('id-ID');

            if (grandTotal > 0 && initialPaid >= grandTotal) {
                paymentStatusBadge.textContent = 'Lunas';
                paymentStatusBadge.className = 'badge-lunas small';
            } else if (initialPaid > 0 && initialPaid < grandTotal) {
                paymentStatusBadge.textContent = 'Sebagian (DP)';
                paymentStatusBadge.className = 'badge-sebagian small';
            } else {
                paymentStatusBadge.textContent = 'Belum Bayar';
                paymentStatusBadge.className = 'badge-belum-bayar small';
            }
        }

        initialPaidInput.addEventListener('input', recalculate);

        btnPayFull.addEventListener('click', function () {
            const currentTotalText = grandTotalDisplay.textContent.replace(/[^\d]/g, '');
            initialPaidInput.value = parseInt(currentTotalText || '0');
            recalculate();
        });

        btnPayZero.addEventListener('click', function () {
            initialPaidInput.value = 0;
            recalculate();
        });
    });
</script>
@endpush
