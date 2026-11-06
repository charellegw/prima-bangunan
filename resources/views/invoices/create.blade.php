@extends('layouts.app')

@section('title', 'Kasir Penjualan Baru — Prima Bangunan')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Kasir / Transaksi Penjualan Baru</h4>
            <p class="text-muted small mb-0">Pilih barang belanjaan, tentukan tipe pelanggan (orang vs retail), dan catat pembayaran.</p>
        </div>
        <div>
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" id="salesForm">
        @csrf
        <div class="row g-4">
            <!-- Kolom Kiri: Pemilihan Barang & Keranjang Belanja -->
            <div class="col-12 col-lg-8">
                <!-- Pengaturan Tipe Pelanggan & Nama -->
                <div class="card card-custom p-4 mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-badge me-2 text-primary"></i> Data Pelanggan & Skema Harga</h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" class="form-control" value="{{ old('client_name', 'Pelanggan Umum') }}" placeholder="Nama pembeli / perusahaan" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Tipe Pelanggan (Skema Harga) <span class="text-danger">*</span></label>
                            <select name="client_type" id="clientTypeSelect" class="form-select fw-semibold" required>
                                <option value="orang" {{ old('client_type', 'orang') === 'orang' ? 'selected' : '' }}>
                                    Orang / Konsumen Umum (Harga Eceran)
                                </option>
                                <option value="retail" {{ old('client_type') === 'retail' ? 'selected' : '' }}>
                                    Retail / Toko Rekanan (Harga Grosir)
                                </option>
                            </select>
                            <small class="text-muted d-block mt-1" id="clientTypeHelp" style="font-size: 11px;">
                                Menggunakan harga eceran standar untuk pembeli perorangan.
                            </small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Tanggal Transaksi</label>
                            <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Catatan / Proyek (Opsional)</label>
                            <input type="text" name="note" class="form-control" placeholder="Misal: Proyek Ruko Lantai 2, Cat Rumah Pak RT">
                        </div>
                    </div>
                </div>

                <!-- Keranjang / Daftar Barang yang Dibeli -->
                <div class="card card-custom p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-cart3 me-2 text-primary"></i> Daftar Barang Belanjaan</h6>
                        <button type="button" class="btn btn-sm btn-primary-custom rounded-pill" id="btnAddItemRow">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baris Barang
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="cartTable">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th style="min-width: 250px;">Pilih Barang Material</th>
                                    <th style="width: 110px;">Stok</th>
                                    <th style="width: 140px;">Harga Satuan</th>
                                    <th style="width: 100px;">Jumlah</th>
                                    <th style="width: 150px;">Subtotal</th>
                                    <th style="width: 50px;" class="text-center">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="cartItemsBody">
                                <!-- Baris pertama default -->
                                <tr class="cart-row" data-index="0">
                                    <td>
                                        <select name="items[0][item_id]" class="form-select item-select" required>
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
                                        <span class="badge bg-light text-secondary border stock-badge">-</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold unit-price-display">Rp 0</span>
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

                <!-- Opsi Pengiriman Barang (Shipment) -->
                <div class="card card-custom p-4">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="switchShipment" name="needs_shipment" value="1">
                        <label class="form-check-label fw-bold text-dark" for="switchShipment">
                            <i class="bi bi-truck me-1 text-primary"></i> Butuh Layanan Pengiriman ke Alamat Pelanggan?
                        </label>
                    </div>

                    <div id="shipmentFormSection" style="display: none;">
                        <div class="row g-3 pt-2 border-top">
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-semibold text-dark">Alamat Pengiriman <span class="text-danger">*</span></label>
                                <textarea name="shipment_address" id="shipmentAddressInput" rows="2" class="form-control" placeholder="Jl. Anggrek No. 10, RT 02/05, Kelurahan..."></textarea>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-dark">Ongkos Kirim (Rp)</label>
                                <input type="number" name="shipment_fee" class="form-control" min="0" value="0">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">Jadwal Pengiriman</label>
                                <input type="datetime-local" name="shipment_date" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime('+2 hours')) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">Catatan Kurir / Supir</label>
                                <input type="text" name="shipment_notes" class="form-control" placeholder="Truk engkel / motor gerobak, titip satpam, dsb.">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Ringkasan Total & Pembayaran Kasir -->
            <div class="col-12 col-lg-4">
                <div class="card card-custom p-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calculator me-2 text-primary"></i> Ringkasan Pembayaran</h5>

                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="text-muted small">Total Tagihan (Grand Total):</div>
                        <div class="fs-2 fw-bold text-primary mt-1" id="grandTotalDisplay">Rp 0</div>
                        <small class="text-muted" style="font-size: 11px;">*Dihitung dan diverifikasi ulang di server saat checkout.</small>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-semibold text-dark mb-0">Pembayaran Kasir / Uang Diterima (Rp)</label>
                            <div class="form-check form-switch m-0" style="font-size: 11px;">
                                <input class="form-check-input" type="checkbox" id="switchSplitPayment">
                                <label class="form-check-label text-muted" for="switchSplitPayment">Split Payment</label>
                            </div>
                        </div>

                        <!-- Single Payment Box -->
                        <div id="singlePaymentBox">
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-white">Rp</span>
                                <input type="number" name="initial_paid" id="initialPaidInput" class="form-control fw-bold fs-6" min="0" value="0">
                            </div>
                            <div class="mb-2">
                                <select name="payment_method" id="paymentMethodSelect" class="form-select form-select-sm">
                                    <option value="Tunai (Cash)">Tunai (Cash)</option>
                                    <option value="QRIS">QRIS</option>
                                    <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                                    <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                                    <option value="Giro / Cek">Giro / Cek</option>
                                </select>
                            </div>
                        </div>

                        <!-- Split Payment Box (Multi Payment: Tunai + Transfer / QRIS) -->
                        <div id="splitPaymentBox" class="p-2 bg-light rounded-3 border mb-2" style="display: none;">
                            <div class="small fw-bold text-dark mb-2"><i class="bi bi-wallet2 me-1 text-primary"></i> Rincian Pembayaran Majemuk:</div>
                            <div class="mb-2">
                                <label class="text-muted" style="font-size: 11px;">1. Pembayaran Tunai (Cash)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="payments[0][amount]" id="splitCashInput" class="form-control fw-bold split-pay-input" min="0" value="0">
                                    <input type="hidden" name="payments[0][method]" value="Tunai (Cash)">
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="text-muted" style="font-size: 11px;">2. Pembayaran Non-Tunai</label>
                                    <select name="payments[1][method]" class="form-select form-select-sm py-0 px-1" style="width: 140px; font-size: 11px;">
                                        <option value="QRIS">QRIS</option>
                                        <option value="Transfer Bank BCA">Transfer BCA</option>
                                        <option value="Transfer Bank Mandiri">Transfer Mandiri</option>
                                        <option value="Giro / Cek">Giro</option>
                                    </select>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="payments[1][amount]" id="splitNonCashInput" class="form-control fw-bold split-pay-input" min="0" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-1 mt-1">
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" id="btnPayFull">Bayar Lunas</button>
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" id="btnPayZero">Belum Bayar (Bon/Kredit)</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Tenggat Waktu / Jatuh Tempo</label>
                        <input type="date" name="deadline" class="form-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}">
                        <small class="text-muted" style="font-size: 11px;">Batas waktu pelunasan jika ada sisa tagihan (bon).</small>
                    </div>

                    <div class="p-3 rounded-3 mb-4 border bg-white">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Sisa Piutang:</span>
                            <span class="fw-bold text-danger" id="remainingBalanceDisplay">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Status Tagihan:</span>
                            <span class="badge-belum-bayar small" id="paymentStatusBadge">Belum Bayar</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary-custom py-2 fw-bold fs-6 shadow">
                            <i class="bi bi-check2-circle me-1"></i> Proses Transaksi Penjualan
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
            // default disabled for hidden inputs
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
            newRow.querySelector('.stock-badge').className = 'badge bg-light text-secondary border stock-badge';
            newRow.querySelector('.unit-price-display').textContent = 'Rp 0';
            newRow.querySelector('.row-subtotal-display').textContent = 'Rp 0';

            const btnRemove = newRow.querySelector('.btn-remove-row');
            btnRemove.removeAttribute('disabled');

            cartItemsBody.appendChild(newRow);
            rowCount++;
            updateRemoveButtons();
            bindRowEvents(newRow);
        });

        function updateRemoveButtons() {
            const rows = cartItemsBody.querySelectorAll('.cart-row');
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
            const qty = row.querySelector('.item-qty');
            const btnRemove = row.querySelector('.btn-remove-row');

            select.addEventListener('change', recalculateCart);
            qty.addEventListener('input', recalculateCart);

            btnRemove.addEventListener('click', function () {
                if (cartItemsBody.querySelectorAll('.cart-row').length > 1) {
                    row.remove();
                    updateRemoveButtons();
                    recalculateCart();
                }
            });
        }

        // Bind first row
        bindRowEvents(cartItemsBody.querySelector('.cart-row'));

        function recalculateCart() {
            const clientType = clientTypeSelect.value;
            let grandTotal = 0;

            const rows = cartItemsBody.querySelectorAll('.cart-row');
            rows.forEach(row => {
                const select = row.querySelector('.item-select');
                const qtyInput = row.querySelector('.item-qty');
                const stockBadge = row.querySelector('.stock-badge');
                const priceDisplay = row.querySelector('.unit-price-display');
                const subtotalDisplay = row.querySelector('.row-subtotal-display');

                const selectedOption = select.options[select.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    stockBadge.textContent = '-';
                    stockBadge.className = 'badge bg-light text-secondary border stock-badge';
                    priceDisplay.textContent = 'Rp 0';
                    subtotalDisplay.textContent = 'Rp 0';
                    return;
                }

                const stock = parseInt(selectedOption.getAttribute('data-stock') || '0');
                const normalPrice = parseFloat(selectedOption.getAttribute('data-price') || '0');
                const retailPrice = parseFloat(selectedOption.getAttribute('data-retail-price') || '0');

                // Tentukan harga berdasarkan client_type
                const activePrice = clientType === 'retail' ? retailPrice : normalPrice;
                const qty = parseInt(qtyInput.value || '1');

                // Tampilkan info stok
                stockBadge.textContent = stock + ' unit';
                if (qty > stock) {
                    stockBadge.className = 'badge bg-danger text-white stock-badge';
                    stockBadge.textContent = `Kurang! (${stock})`;
                } else if (stock <= 5) {
                    stockBadge.className = 'badge bg-warning text-dark stock-badge';
                } else {
                    stockBadge.className = 'badge bg-success-subtle text-success border border-success-subtle stock-badge';
                }

                const subtotal = activePrice * qty;
                grandTotal += subtotal;

                priceDisplay.textContent = 'Rp ' + activePrice.toLocaleString('id-ID');
                subtotalDisplay.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            });

            grandTotalDisplay.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            // Hitung sisa dan status bayar
            let totalPaid = 0;
            if (isSplitMode) {
                const cashVal = parseFloat(splitCashInput.value || '0');
                const nonCashVal = parseFloat(splitNonCashInput.value || '0');
                totalPaid = cashVal + nonCashVal;
            } else {
                totalPaid = parseFloat(initialPaidInput.value || '0');
                if (totalPaid > grandTotal) {
                    totalPaid = grandTotal;
                    initialPaidInput.value = grandTotal;
                }
            }

            const remaining = Math.max(0, grandTotal - totalPaid);
            remainingBalanceDisplay.textContent = 'Rp ' + remaining.toLocaleString('id-ID');

            if (grandTotal > 0 && totalPaid >= grandTotal) {
                paymentStatusBadge.textContent = 'Lunas';
                paymentStatusBadge.className = 'badge-lunas small';
            } else if (totalPaid > 0 && totalPaid < grandTotal) {
                paymentStatusBadge.textContent = 'Sebagian (DP)';
                paymentStatusBadge.className = 'badge-sebagian small';
            } else {
                paymentStatusBadge.textContent = 'Belum Bayar';
                paymentStatusBadge.className = 'badge-belum-bayar small';
            }
        }

        initialPaidInput.addEventListener('input', recalculateCart);

        btnPayFull.addEventListener('click', function () {
            const currentTotalText = grandTotalDisplay.textContent.replace(/[^\d]/g, '');
            const targetVal = parseInt(currentTotalText || '0');
            if (isSplitMode) {
                splitCashInput.value = targetVal;
                splitNonCashInput.value = 0;
            } else {
                initialPaidInput.value = targetVal;
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
    });
</script>
@endpush
