@extends('layouts.app')

@section('title', 'Faktur Pembelian Supplier — Prima Bangunan')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3">
    <div>
        <h1 class="page-title">Catat Pembelian Stok dari Supplier</h1>
        <p class="page-subtitle">Input penerimaan material masuk dari pabrik / distributor. Stok barang otomatis bertambah saat faktur disimpan.</p>
    </div>
    <div>
        <x-button :href="route('invoice-suppliers.index')" variant="secondary" icon="arrow-left">
            Daftar Faktur
        </x-button>
    </div>
</div>

<form action="{{ route('invoice-suppliers.store') }}" method="POST" id="purchaseForm">
    @csrf
    <div class="row g-4">
        <!-- Kolom Kiri: Data Supplier & Barang Masuk -->
        <div class="col-12 col-lg-8">
            <!-- Info Supplier -->
            <div class="pb-card p-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                    <x-icon name="building" size="18" class="text-primary" />
                    <h5 class="fw-semibold text-main mb-0 fs-6">Data Pemasok & Tanggal Faktur</h5>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="pb-label">Pilih Supplier / Pabrik <span class="text-danger">*</span></label>
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
                        <label class="pb-label">Tanggal Pembelian / Faktur</label>
                        <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                    </div>

                    <div class="col-12">
                        <label class="pb-label">Catatan Pembelian / No. Surat Jalan Supplier</label>
                        <input type="text" name="note" class="form-control" placeholder="Contoh: No. PO-2026/XI/001, Kiriman armada truk Semen Gresik">
                    </div>
                </div>
            </div>

            <!-- Daftar Barang Masuk (Stok akan ditambah) -->
            <div class="pb-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <x-icon name="package" size="18" class="text-primary" />
                            <h5 class="fw-semibold text-main mb-0 fs-6">Barang Masuk / Restock</h5>
                        </div>
                        <small class="text-secondary" style="font-size: 11.5px;">Stok barang di sistem akan otomatis bertambah sesuai jumlah penerimaan.</small>
                    </div>
                    <x-button type="button" variant="primary" size="sm" icon="plus" id="btnAddRow">
                        Tambah Baris
                    </x-button>
                </div>

                <div class="table-responsive">
                    <table class="pb-table" id="purchaseTable">
                        <thead>
                            <tr>
                                <th style="min-width: 250px;">Material Bangunan</th>
                                <th style="width: 100px;">Stok Kini</th>
                                <th style="width: 160px;" class="text-end">Harga Beli Satuan (Rp)</th>
                                <th style="width: 90px;" class="text-center">Jumlah</th>
                                <th style="width: 140px;" class="text-end">Subtotal</th>
                                <th style="width: 45px;" class="text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseTableBody">
                            <tr class="purchase-row" data-index="0">
                                <td>
                                    <select name="items[0][item_id]" class="form-select form-select-sm item-select" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($items as $it)
                                            <option value="{{ $it->id }}" data-stock="{{ $it->stock }}" data-unit-price="{{ $it->supplierPrices->first()?->unit_price ?? 0 }}">
                                                {{ $it->name }} (Stok kini: {{ $it->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <span class="current-stock-badge pb-badge pb-badge-neutral pb-badge-sm">-</span>
                                </td>
                                <td>
                                    <input type="number" name="items[0][unit_price]" class="form-control form-control-sm text-end unit-price-input tabular-nums" min="0" placeholder="0" required style="height: 36px !important;">
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm text-center item-qty tabular-nums" value="1" min="1" required style="height: 36px !important;">
                                </td>
                                <td class="text-end nowrap tabular-nums">
                                    <span class="fw-bold text-main row-subtotal-display">Rp 0</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="pb-btn pb-btn-ghost pb-btn-sm p-1 text-danger btn-remove-row" disabled>
                                        <x-icon name="trash" size="15" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Opsi Catat Pengiriman Masuk (Incoming Shipment) -->
            <div class="pb-card p-4">
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" role="switch" id="switchIncomingShipment" name="has_incoming_shipment" value="1" checked>
                    <label class="form-check-label fw-semibold text-main" for="switchIncomingShipment" style="font-size: 13.5px;">
                        <x-icon name="truck" size="16" class="me-1 text-primary" />
                        Catat Log Pengiriman Masuk (Incoming Shipment dari Supplier)
                    </label>
                </div>
                <small class="text-secondary d-block mb-3" style="font-size: 12px;">Membuat rekam jejak armada penerimaan material masuk pada modul Pengiriman.</small>

                <div id="incomingShipmentSection" class="pt-3 border-top border-light-subtle" style="border-color: var(--pb-border) !important;">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="pb-label">Status Penerimaan Barang</label>
                            <select name="shipment_status" class="form-select">
                                <option value="delivered">Sudah Diterima Lengkap di Gudang (Delivered)</option>
                                <option value="shipping">Sedang Dalam Perjalanan / Pengiriman Truk</option>
                                <option value="pending">Menunggu Jadwal Pengiriman Supplier</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="pb-label">Tanggal Penerimaan / Kirim</label>
                            <input type="datetime-local" name="shipment_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Hutang & Pembayaran Supplier -->
        <div class="col-12 col-lg-4">
            <div class="pb-card p-4 sticky-top" style="top: 20px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                    <x-icon name="credit-card" size="18" class="text-primary" />
                    <h5 class="fw-semibold text-main mb-0 fs-6">Hutang & Pembayaran</h5>
                </div>

                <!-- Grand Total Box -->
                <div class="p-3 rounded-3 mb-3 border border-light-subtle" style="background-color: var(--pb-primary-light); border-color: var(--pb-primary-border) !important;">
                    <div class="text-secondary small fw-medium" style="font-size: 12px;">TOTAL TAGIHAN FAKTUR:</div>
                    <div class="fs-3 fw-bold text-primary mt-1 nowrap tabular-nums" id="grandTotalDisplay">Rp 0</div>
                    <small class="text-secondary" style="font-size: 11px;">*Diverifikasi & dikalkulasi ulang di server</small>
                </div>

                <div class="mb-3">
                    <label class="pb-label">Uang Muka / Pembayaran Awal (Rp)</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                        <input type="number" name="initial_paid" id="initialPaidInput" class="form-control fw-bold fs-6 tabular-nums" min="0" value="0">
                    </div>
                    <div class="d-flex gap-1.5 mt-1">
                        <button type="button" class="pb-btn pb-btn-secondary pb-btn-sm flex-grow-1" id="btnPayFull">Bayar Lunas</button>
                        <button type="button" class="pb-btn pb-btn-secondary pb-btn-sm flex-grow-1" id="btnPayZero">Hutang Penuh (DP 0)</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="pb-label">Metode Pembayaran</label>
                    <select name="payment_method" class="form-select">
                        <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                        <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                        <option value="Giro / Cek">Giro / Cek</option>
                        <option value="Tunai (Cash)">Tunai (Cash)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="pb-label">Jatuh Tempo Pembayaran</label>
                    <input type="date" name="deadline" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                    <small class="text-secondary" style="font-size: 11px;">Standar tempo supplier 30 hari.</small>
                </div>

                <div class="p-3 rounded-3 mb-4 border border-light-subtle bg-white" style="border-color: var(--pb-border) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-secondary small" style="font-size: 12px;">Sisa Hutang Toko:</span>
                        <span class="fw-bold text-danger nowrap tabular-nums" id="remainingBalanceDisplay">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small" style="font-size: 12px;">Status Hutang:</span>
                        <span class="pb-badge pb-badge-neutral" id="paymentStatusBadge">Belum Bayar</span>
                    </div>
                </div>

                <x-button type="submit" variant="primary" size="lg" class="w-100 justify-content-center" icon="check">
                    Simpan Faktur & Tambah Stok
                </x-button>
            </div>
        </div>
    </div>
</form>
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

        if (switchIncomingShipment) {
            switchIncomingShipment.addEventListener('change', function () {
                incomingShipmentSection.style.display = this.checked ? 'block' : 'none';
            });
        }

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
            newRow.querySelector('.current-stock-badge').className = 'current-stock-badge pb-badge pb-badge-neutral pb-badge-sm';
            newRow.querySelector('.row-subtotal-display').textContent = 'Rp 0';

            const btnRemove = newRow.querySelector('.btn-remove-row');
            btnRemove.removeAttribute('disabled');

            purchaseTableBody.appendChild(newRow);
            rowCount++;
            updateRemoveButtons();
            attachRowEvents(newRow);
        });

        function attachRowEvents(row) {
            const select = row.querySelector('.item-select');
            const unitPriceInput = row.querySelector('.unit-price-input');
            const qtyInput = row.querySelector('.item-qty');
            const btnRemove = row.querySelector('.btn-remove-row');

            select.addEventListener('change', function () {
                const opt = select.options[select.selectedIndex];
                const stockBadge = row.querySelector('.current-stock-badge');
                if (opt && opt.value) {
                    const defaultPrice = opt.dataset.unitPrice || 0;
                    if (!unitPriceInput.value || parseFloat(unitPriceInput.value) === 0) {
                        unitPriceInput.value = defaultPrice;
                    }
                    stockBadge.textContent = `${opt.dataset.stock} unit`;
                    stockBadge.className = 'current-stock-badge pb-badge pb-badge-info pb-badge-sm';
                } else {
                    stockBadge.textContent = '-';
                    stockBadge.className = 'current-stock-badge pb-badge pb-badge-neutral pb-badge-sm';
                }
                updateRowSubtotal(row);
                recalculatePurchase();
            });

            unitPriceInput.addEventListener('input', function () {
                updateRowSubtotal(row);
                recalculatePurchase();
            });

            qtyInput.addEventListener('input', function () {
                updateRowSubtotal(row);
                recalculatePurchase();
            });

            btnRemove.addEventListener('click', function () {
                const rows = purchaseTableBody.querySelectorAll('.purchase-row');
                if (rows.length > 1) {
                    row.remove();
                    updateRemoveButtons();
                    recalculatePurchase();
                }
            });
        }

        function updateRemoveButtons() {
            const rows = purchaseTableBody.querySelectorAll('.purchase-row');
            rows.forEach((r) => {
                const btn = r.querySelector('.btn-remove-row');
                if (rows.length === 1) {
                    btn.setAttribute('disabled', 'disabled');
                } else {
                    btn.removeAttribute('disabled');
                }
            });
        }

        function updateRowSubtotal(row) {
            const unitPriceInput = row.querySelector('.unit-price-input');
            const qtyInput = row.querySelector('.item-qty');
            const subtotalDisplay = row.querySelector('.row-subtotal-display');

            const price = parseFloat(unitPriceInput.value) || 0;
            const qty = parseInt(qtyInput.value) || 0;
            const subtotal = price * qty;

            subtotalDisplay.textContent = formatRupiah(subtotal);
        }

        function recalculatePurchase() {
            let grandTotal = 0;
            const rows = purchaseTableBody.querySelectorAll('.purchase-row');

            rows.forEach(row => {
                const unitPriceInput = row.querySelector('.unit-price-input');
                const qtyInput = row.querySelector('.item-qty');
                const price = parseFloat(unitPriceInput.value) || 0;
                const qty = parseInt(qtyInput.value) || 0;
                grandTotal += (price * qty);
            });

            grandTotalDisplay.textContent = formatRupiah(grandTotal);

            const paid = parseFloat(initialPaidInput.value) || 0;
            const remaining = Math.max(0, grandTotal - paid);
            remainingBalanceDisplay.textContent = formatRupiah(remaining);

            if (grandTotal === 0) {
                paymentStatusBadge.textContent = 'Belum Ada Barang';
                paymentStatusBadge.className = 'pb-badge pb-badge-neutral';
            } else if (paid >= grandTotal) {
                paymentStatusBadge.textContent = 'Lunas';
                paymentStatusBadge.className = 'pb-badge pb-badge-success';
            } else if (paid > 0) {
                paymentStatusBadge.textContent = `Sebagian (DP ${formatRupiah(paid)})`;
                paymentStatusBadge.className = 'pb-badge pb-badge-warning';
            } else {
                paymentStatusBadge.textContent = 'Belum Bayar (Hutang)';
                paymentStatusBadge.className = 'pb-badge pb-badge-danger';
            }
        }

        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        btnPayFull.addEventListener('click', function () {
            let total = 0;
            purchaseTableBody.querySelectorAll('.purchase-row').forEach(row => {
                const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                const qty = parseInt(row.querySelector('.item-qty').value) || 0;
                total += (price * qty);
            });
            initialPaidInput.value = total;
            recalculatePurchase();
        });

        btnPayZero.addEventListener('click', function () {
            initialPaidInput.value = 0;
            recalculatePurchase();
        });

        if (initialPaidInput) {
            initialPaidInput.addEventListener('input', recalculatePurchase);
        }

        attachRowEvents(purchaseTableBody.querySelector('.purchase-row'));
        updateRemoveButtons();
    });
</script>
@endpush
