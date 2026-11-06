@extends('layouts.app')

@section('title', 'Kasir Penjualan Baru — Prima Bangunan')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3">
    <div>
        <h1 class="page-title">Kasir / Transaksi Penjualan Baru</h1>
        <p class="page-subtitle">Pilih material belanjaan, tentukan skema harga pembeli (orang vs retail), dan catat pembayaran.</p>
    </div>
    <div>
        <x-button :href="route('invoices.index')" variant="secondary" icon="arrow-left">
            Daftar Invoice
        </x-button>
    </div>
</div>

<form action="{{ route('invoices.store') }}" method="POST" id="salesForm">
    @csrf
    <div class="row g-4">
        <!-- Kolom Kiri: Pemilihan Barang & Keranjang Belanja -->
        <div class="col-12 col-lg-8">
            <!-- Pengaturan Tipe Pelanggan & Nama -->
            <div class="pb-card p-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                    <x-icon name="users" size="18" class="text-primary" />
                    <h5 class="fw-semibold text-main mb-0 fs-6">Data Pelanggan & Skema Harga</h5>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="pb-label">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="client_name" class="form-control" value="{{ old('client_name', 'Pelanggan Umum') }}" placeholder="Nama pembeli / proyek / CV" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="pb-label">Tipe Pelanggan (Skema Harga) <span class="text-danger">*</span></label>
                        <select name="client_type" id="clientTypeSelect" class="form-select fw-semibold" required>
                            <option value="orang" {{ old('client_type', 'orang') === 'orang' ? 'selected' : '' }}>
                                Orang / Konsumen Umum (Harga Eceran)
                            </option>
                            <option value="retail" {{ old('client_type') === 'retail' ? 'selected' : '' }}>
                                Retail / Rekanan Borongan (Harga Grosir)
                            </option>
                        </select>
                        <small class="text-secondary d-block mt-1" id="clientTypeHelp" style="font-size: 11.5px;">
                            Menggunakan harga eceran standar untuk pembeli perorangan.
                        </small>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="pb-label">Tanggal Transaksi</label>
                        <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="pb-label">Catatan Nota / Nama Proyek (Opsional)</label>
                        <input type="text" name="note" class="form-control" placeholder="Contoh: Proyek Ruko Meruya, Renovasi Dapur">
                    </div>
                </div>
            </div>

            <!-- Keranjang / Daftar Barang yang Dibeli -->
            <div class="pb-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <x-icon name="shopping-cart" size="18" class="text-primary" />
                        <h5 class="fw-semibold text-main mb-0 fs-6">Daftar Barang Belanjaan</h5>
                    </div>
                    <x-button type="button" variant="primary" size="sm" icon="plus" id="btnAddItemRow">
                        Tambah Baris
                    </x-button>
                </div>

                <div class="table-responsive">
                    <table class="pb-table" id="cartTable">
                        <thead>
                            <tr>
                                <th style="min-width: 250px;">Material Bangunan</th>
                                <th style="width: 100px;">Stok</th>
                                <th style="width: 130px;" class="text-end">Harga Satuan</th>
                                <th style="width: 90px;" class="text-center">Jumlah</th>
                                <th style="width: 140px;" class="text-end">Subtotal</th>
                                <th style="width: 45px;" class="text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="cartItemsBody">
                            <tr class="cart-row" data-index="0">
                                <td>
                                    <select name="items[0][item_id]" class="form-select form-select-sm item-select" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($items as $it)
                                            <option value="{{ $it->id }}"
                                                    data-price="{{ $it->price }}"
                                                    data-retail-price="{{ $it->retail_price }}"
                                                    data-stock="{{ $it->stock }}">
                                                {{ $it->name }} (Stok: {{ $it->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <span class="stock-badge pb-badge pb-badge-neutral pb-badge-sm">-</span>
                                </td>
                                <td class="text-end nowrap tabular-nums">
                                    <span class="fw-medium text-main unit-price-display">Rp 0</span>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm text-center item-qty" value="1" min="1" required style="height: 36px !important;">
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

            <!-- Opsi Pengiriman Barang (Shipment) -->
            <div class="pb-card p-4">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="switchShipment" name="needs_shipment" value="1">
                    <label class="form-check-label fw-semibold text-main" for="switchShipment" style="font-size: 13.5px;">
                        <x-icon name="truck" size="16" class="me-1 text-primary" />
                        Butuh Layanan Pengiriman Armada ke Alamat Pelanggan?
                    </label>
                </div>

                <div id="shipmentFormSection" style="display: none;" class="pt-3 mt-3 border-top border-light-subtle" style="border-color: var(--pb-border) !important;">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="pb-label">Alamat Pengiriman <span class="text-danger">*</span></label>
                            <textarea name="shipment_address" id="shipmentAddressInput" rows="2" class="form-control" placeholder="Jl. Raya Proyek No. 10, RT 02/05, Kelurahan..."></textarea>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="pb-label">Ongkos Kirim (Rp)</label>
                            <input type="number" name="shipment_fee" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="pb-label">Jadwal Pengiriman</label>
                            <input type="datetime-local" name="shipment_date" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime('+2 hours')) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="pb-label">Catatan Kurir / Supir Truk</label>
                            <input type="text" name="shipment_notes" class="form-control" placeholder="Truk colt diesel / pick up, hubungi mandor, dsb.">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Total & Pembayaran Kasir -->
        <div class="col-12 col-lg-4">
            <div class="pb-card p-4 sticky-top" style="top: 20px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-light-subtle" style="border-color: var(--pb-border) !important;">
                    <x-icon name="credit-card" size="18" class="text-primary" />
                    <h5 class="fw-semibold text-main mb-0 fs-6">Ringkasan Pembayaran</h5>
                </div>

                <!-- Grand Total Box -->
                <div class="p-3 rounded-3 mb-3 border border-light-subtle" style="background-color: var(--pb-primary-light); border-color: var(--pb-primary-border) !important;">
                    <div class="text-secondary small fw-medium" style="font-size: 12px;">TOTAL TAGIHAN (GRAND TOTAL):</div>
                    <div class="fs-3 fw-bold text-primary mt-1 nowrap tabular-nums" id="grandTotalDisplay">Rp 0</div>
                    <small class="text-secondary" style="font-size: 11px;">*Diverifikasi & dikalkulasi ulang di server</small>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                        <label class="pb-label mb-0">Uang Diterima / Bayar (Rp)</label>
                        <div class="form-check form-switch m-0" style="font-size: 11.5px;">
                            <input class="form-check-input" type="checkbox" id="switchSplitPayment">
                            <label class="form-check-label text-secondary" for="switchSplitPayment">Split Payment</label>
                        </div>
                    </div>

                    <!-- Single Payment Box -->
                    <div id="singlePaymentBox">
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                            <input type="number" name="initial_paid" id="initialPaidInput" class="form-control fw-bold fs-6 tabular-nums" min="0" value="0">
                        </div>
                        <div class="mb-2">
                            <select name="payment_method" id="paymentMethodSelect" class="form-select">
                                <option value="Tunai (Cash)">Tunai (Cash)</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                                <option value="Giro / Cek">Giro / Cek</option>
                            </select>
                        </div>
                    </div>

                    <!-- Split Payment Box -->
                    <div id="splitPaymentBox" class="p-3 rounded-3 border border-light-subtle mb-3" style="display: none; background-color: var(--pb-surface-alt); border-color: var(--pb-border) !important;">
                        <div class="small fw-semibold text-main mb-2 d-flex align-items-center gap-1.5">
                            <x-icon name="wallet" size="14" class="text-primary" />
                            <span>Rincian Pembayaran Majemuk:</span>
                        </div>
                        <div class="mb-2">
                            <label class="text-secondary small mb-1" style="font-size: 11.5px;">1. Pembayaran Tunai (Cash)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                                <input type="number" name="payments[0][amount]" id="splitCashInput" class="form-control fw-bold split-pay-input tabular-nums" min="0" value="0">
                                <input type="hidden" name="payments[0][method]" value="Tunai (Cash)">
                            </div>
                        </div>
                        <div class="mb-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="text-secondary small" style="font-size: 11.5px;">2. Pembayaran Non-Tunai</label>
                                <select name="payments[1][method]" class="form-select form-select-sm py-0 px-2" style="width: 140px; font-size: 11px;">
                                    <option value="QRIS">QRIS</option>
                                    <option value="Transfer Bank BCA">Transfer BCA</option>
                                    <option value="Transfer Bank Mandiri">Transfer Mandiri</option>
                                    <option value="Giro / Cek">Giro</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle text-muted">Rp</span>
                                <input type="number" name="payments[1][amount]" id="splitNonCashInput" class="form-control fw-bold split-pay-input tabular-nums" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <!-- Quick helper buttons -->
                    <div class="d-flex gap-1.5 mt-2">
                        <button type="button" class="pb-btn pb-btn-secondary pb-btn-sm flex-grow-1" id="btnPayFull">Bayar Lunas</button>
                        <button type="button" class="pb-btn pb-btn-secondary pb-btn-sm flex-grow-1" id="btnPayZero">Bon / Tempo (DP 0)</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="pb-label">Tenggat Waktu / Jatuh Tempo</label>
                    <input type="date" name="deadline" class="form-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}">
                    <small class="text-secondary" style="font-size: 11px;">Batas waktu pelunasan jika ada sisa piutang (bon toko).</small>
                </div>

                <!-- Balance Status Box -->
                <div class="p-3 rounded-3 mb-4 border border-light-subtle bg-white" style="border-color: var(--pb-border) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-secondary small" style="font-size: 12px;">Sisa Piutang:</span>
                        <span class="fw-bold text-danger nowrap tabular-nums" id="remainingBalanceDisplay">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small" style="font-size: 12px;">Status Tagihan:</span>
                        <span class="pb-badge pb-badge-neutral" id="paymentStatusBadge">Belum Bayar</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <x-button type="submit" variant="primary" size="lg" class="w-100 justify-content-center" icon="check">
                    Proses Transaksi Penjualan
                </x-button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cartTable = document.getElementById('cartTable');
        const cartItemsBody = document.getElementById('cartItemsBody');
        const btnAddItemRow = document.getElementById('btnAddItemRow');
        const clientTypeSelect = document.getElementById('clientTypeSelect');
        const clientTypeHelp = document.getElementById('clientTypeHelp');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');
        const initialPaidInput = document.getElementById('initialPaidInput');
        const remainingBalanceDisplay = document.getElementById('remainingBalanceDisplay');
        const paymentStatusBadge = document.getElementById('paymentStatusBadge');
        const btnPayFull = document.getElementById('btnPayFull');
        const btnPayZero = document.getElementById('btnPayZero');
        const switchShipment = document.getElementById('switchShipment');
        const shipmentFormSection = document.getElementById('shipmentFormSection');
        const shipmentAddressInput = document.getElementById('shipmentAddressInput');

        const switchSplitPayment = document.getElementById('switchSplitPayment');
        const singlePaymentBox = document.getElementById('singlePaymentBox');
        const splitPaymentBox = document.getElementById('splitPaymentBox');
        const splitCashInput = document.getElementById('splitCashInput');
        const splitNonCashInput = document.getElementById('splitNonCashInput');

        let isSplitMode = false;

        if (switchSplitPayment) {
            switchSplitPayment.addEventListener('change', function () {
                isSplitMode = this.checked;
                if (isSplitMode) {
                    singlePaymentBox.style.display = 'none';
                    splitPaymentBox.style.display = 'block';
                    initialPaidInput.disabled = true;
                    splitCashInput.disabled = false;
                    splitNonCashInput.disabled = false;
                } else {
                    singlePaymentBox.style.display = 'block';
                    splitPaymentBox.style.display = 'none';
                    initialPaidInput.disabled = false;
                    splitCashInput.disabled = true;
                    splitNonCashInput.disabled = true;
                }
                recalculateCart();
            });
            splitCashInput.disabled = true;
            splitNonCashInput.disabled = true;
        }

        if (splitCashInput) splitCashInput.addEventListener('input', recalculateCart);
        if (splitNonCashInput) splitNonCashInput.addEventListener('input', recalculateCart);

        let rowCount = 1;

        // Toggle Shipment Form
        switchShipment.addEventListener('change', function () {
            if (this.checked) {
                shipmentFormSection.style.display = 'block';
                shipmentAddressInput.setAttribute('required', 'required');
            } else {
                shipmentFormSection.style.display = 'none';
                shipmentAddressInput.removeAttribute('required');
            }
        });

        // Toggle Client Type (orang vs retail)
        clientTypeSelect.addEventListener('change', function () {
            if (this.value === 'retail') {
                clientTypeHelp.textContent = 'Menggunakan harga grosir/retail untuk toko rekanan atau pembelian borongan.';
            } else {
                clientTypeHelp.textContent = 'Menggunakan harga eceran standar untuk pembeli perorangan.';
            }
            recalculateCart();
        });

        // Add Row
        btnAddItemRow.addEventListener('click', function () {
            const firstRow = cartItemsBody.querySelector('.cart-row');
            const newRow = firstRow.cloneNode(true);
            newRow.setAttribute('data-index', rowCount);

            const select = newRow.querySelector('.item-select');
            select.name = `items[${rowCount}][item_id]`;
            select.selectedIndex = 0;

            const qty = newRow.querySelector('.item-qty');
            qty.name = `items[${rowCount}][quantity]`;
            qty.value = 1;

            newRow.querySelector('.stock-badge').textContent = '-';
            newRow.querySelector('.stock-badge').className = 'stock-badge pb-badge pb-badge-neutral pb-badge-sm';
            newRow.querySelector('.unit-price-display').textContent = 'Rp 0';
            newRow.querySelector('.row-subtotal-display').textContent = 'Rp 0';

            const btnRemove = newRow.querySelector('.btn-remove-row');
            btnRemove.removeAttribute('disabled');

            cartItemsBody.appendChild(newRow);
            rowCount++;
            updateRemoveButtons();
            attachRowEvents(newRow);
        });

        function attachRowEvents(row) {
            const select = row.querySelector('.item-select');
            const qtyInput = row.querySelector('.item-qty');
            const btnRemove = row.querySelector('.btn-remove-row');

            select.addEventListener('change', function () {
                updateRowPrice(row);
                recalculateCart();
            });

            qtyInput.addEventListener('input', function () {
                updateRowPrice(row);
                recalculateCart();
            });

            btnRemove.addEventListener('click', function () {
                const rows = cartItemsBody.querySelectorAll('.cart-row');
                if (rows.length > 1) {
                    row.remove();
                    updateRemoveButtons();
                    recalculateCart();
                }
            });
        }

        function updateRemoveButtons() {
            const rows = cartItemsBody.querySelectorAll('.cart-row');
            rows.forEach((r, idx) => {
                const btn = r.querySelector('.btn-remove-row');
                if (rows.length === 1) {
                    btn.setAttribute('disabled', 'disabled');
                } else {
                    btn.removeAttribute('disabled');
                }
            });
        }

        function updateRowPrice(row) {
            const select = row.querySelector('.item-select');
            const qtyInput = row.querySelector('.item-qty');
            const unitPriceDisplay = row.querySelector('.unit-price-display');
            const subtotalDisplay = row.querySelector('.row-subtotal-display');
            const stockBadge = row.querySelector('.stock-badge');

            const selectedOption = select.options[select.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                unitPriceDisplay.textContent = 'Rp 0';
                subtotalDisplay.textContent = 'Rp 0';
                stockBadge.textContent = '-';
                stockBadge.className = 'stock-badge pb-badge pb-badge-neutral pb-badge-sm';
                return;
            }

            const clientType = clientTypeSelect.value;
            const price = parseFloat(clientType === 'retail' ? selectedOption.dataset.retailPrice : selectedOption.dataset.price) || 0;
            const stock = parseInt(selectedOption.dataset.stock) || 0;
            const qty = parseInt(qtyInput.value) || 0;

            stockBadge.textContent = `${stock} unit`;
            if (stock <= 0) {
                stockBadge.className = 'stock-badge pb-badge pb-badge-danger pb-badge-sm';
            } else if (stock <= 10) {
                stockBadge.className = 'stock-badge pb-badge pb-badge-warning pb-badge-sm';
            } else {
                stockBadge.className = 'stock-badge pb-badge pb-badge-success pb-badge-sm';
            }

            const subtotal = price * qty;
            unitPriceDisplay.textContent = formatRupiah(price);
            subtotalDisplay.textContent = formatRupiah(subtotal);
        }

        function recalculateCart() {
            let grandTotal = 0;
            const rows = cartItemsBody.querySelectorAll('.cart-row');

            rows.forEach(row => {
                const select = row.querySelector('.item-select');
                const qtyInput = row.querySelector('.item-qty');
                const selectedOption = select.options[select.selectedIndex];

                if (selectedOption && selectedOption.value) {
                    const clientType = clientTypeSelect.value;
                    const price = parseFloat(clientType === 'retail' ? selectedOption.dataset.retailPrice : selectedOption.dataset.price) || 0;
                    const qty = parseInt(qtyInput.value) || 0;
                    grandTotal += (price * qty);
                }
            });

            grandTotalDisplay.textContent = formatRupiah(grandTotal);

            let paid = 0;
            if (isSplitMode) {
                const cash = parseFloat(splitCashInput.value) || 0;
                const nonCash = parseFloat(splitNonCashInput.value) || 0;
                paid = cash + nonCash;
            } else {
                paid = parseFloat(initialPaidInput.value) || 0;
            }

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
                paymentStatusBadge.textContent = 'Belum Bayar (Bon/Kredit)';
                paymentStatusBadge.className = 'pb-badge pb-badge-danger';
            }
        }

        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        // Quick Pay buttons
        btnPayFull.addEventListener('click', function () {
            let total = 0;
            cartItemsBody.querySelectorAll('.cart-row').forEach(row => {
                const select = row.querySelector('.item-select');
                const qtyInput = row.querySelector('.item-qty');
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const price = parseFloat(clientTypeSelect.value === 'retail' ? selectedOption.dataset.retailPrice : selectedOption.dataset.price) || 0;
                    total += price * (parseInt(qtyInput.value) || 0);
                }
            });

            if (isSplitMode) {
                splitCashInput.value = total;
                splitNonCashInput.value = 0;
            } else {
                initialPaidInput.value = total;
            }
            recalculateCart();
        });

        btnPayZero.addEventListener('click', function () {
            if (isSplitMode) {
                splitCashInput.value = 0;
                splitNonCashInput.value = 0;
            } else {
                initialPaidInput.value = 0;
            }
            recalculateCart();
        });

        if (initialPaidInput) {
            initialPaidInput.addEventListener('input', recalculateCart);
        }

        // Init first row
        attachRowEvents(cartItemsBody.querySelector('.cart-row'));
        updateRemoveButtons();
    });
</script>
@endpush
