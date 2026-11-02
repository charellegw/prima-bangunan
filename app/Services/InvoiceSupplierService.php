<?php

namespace App\Services;

use App\Models\InvoiceSupplier;
use App\Models\InvoiceSupplierDetail;
use App\Models\Item;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\Shipment;
use App\Models\ShipmentDetail;
use App\Models\Store;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceSupplierService
{
    /**
     * Buat invoice supplier (pembelian stok) dengan transaksi database atomic
     *
     * @param Store $store
     * @param array $data
     * @return InvoiceSupplier
     * @throws Exception
     */
    public function createPurchaseInvoice(Store $store, array $data): InvoiceSupplier
    {
        return DB::transaction(function () use ($store, $data) {
            $supplierId = $data['supplier_id'] ?? null;
            $supplier = Supplier::where('id', $supplierId)
                ->where('store_id', $store->id)
                ->first();

            if (!$supplier) {
                throw new Exception("Supplier tidak ditemukan.");
            }

            $itemsData = $data['items'] ?? [];
            if (empty($itemsData)) {
                throw new Exception("Daftar barang pesanan ke supplier tidak boleh kosong.");
            }

            $calculatedGrandTotal = 0;
            $preparedDetails = [];

            foreach ($itemsData as $row) {
                $itemId = $row['item_id'] ?? null;
                $quantity = (int) ($row['quantity'] ?? 0);
                $unitPrice = (float) ($row['unit_price'] ?? 0);

                if ($quantity <= 0) {
                    throw new Exception("Jumlah barang harus lebih dari 0.");
                }
                if ($unitPrice < 0) {
                    throw new Exception("Harga beli satuan tidak boleh bernilai negatif.");
                }

                $item = Item::where('id', $itemId)
                    ->where('store_id', $store->id)
                    ->lockForUpdate()
                    ->first();

                if (!$item) {
                    throw new Exception("Barang tidak ditemukan di toko ini.");
                }

                $subTotal = $unitPrice * $quantity;
                $calculatedGrandTotal += $subTotal;

                // Tambah stok barang karena barang masuk dari supplier
                $item->increment('stock', $quantity);

                $preparedDetails[] = [
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'sub_total' => $subTotal,
                ];
            }

            // 1. Buat Payment (Hutang ke supplier)
            $deadline = !empty($data['deadline'])
                ? Carbon::parse($data['deadline'])
                : Carbon::now()->addDays(30);

            $payment = Payment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $store->id,
                'status' => 'belum_bayar',
                'deadline' => $deadline,
                'amount_total' => $calculatedGrandTotal,
                'amount_paid' => 0,
            ]);

            // 2. Buat Invoice Supplier
            $invoiceNumber = $this->generateSupplierInvoiceNumber($store);

            $invoiceSupplier = InvoiceSupplier::create([
                'id' => (string) Str::uuid(),
                'invoice_number' => $invoiceNumber,
                'store_id' => $store->id,
                'supplier_id' => $supplier->id,
                'payment_id' => $payment->id,
                'date' => !empty($data['date']) ? Carbon::parse($data['date']) : Carbon::now(),
                'note' => $data['note'] ?? null,
                'grand_total' => $calculatedGrandTotal,
            ]);

            // 3. Simpan Detail Invoice Supplier
            foreach ($preparedDetails as $detail) {
                InvoiceSupplierDetail::create([
                    'invoice_supplier_id' => $invoiceSupplier->id,
                    'item_id' => $detail['item_id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'sub_total' => $detail['sub_total'],
                ]);
            }

            // 4. Pembayaran Awal / DP (opsional)
            $initialPaid = (float) ($data['initial_paid'] ?? 0);
            if ($initialPaid > 0) {
                if ($initialPaid > $calculatedGrandTotal) {
                    throw new Exception("Nominal pembayaran awal (Rp " . number_format($initialPaid, 0, ',', '.') . ") melebihi total tagihan supplier (Rp " . number_format($calculatedGrandTotal, 0, ',', '.') . ").");
                }

                PaymentHistory::create([
                    'payment_id' => $payment->id,
                    'date' => Carbon::now(),
                    'amount' => $initialPaid,
                    'method' => $data['payment_method'] ?? 'transfer',
                    'note' => $data['payment_note'] ?? 'Pembayaran awal pembelian stok supplier',
                ]);

                $payment->recalculateStatus();
            }

            // 5. Opsi Buat Catatan Pengiriman Masuk (Incoming Shipment)
            if (!empty($data['has_incoming_shipment'])) {
                $shipment = Shipment::create([
                    'id' => (string) Str::uuid(),
                    'shipment_number' => 'SHP-IN-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                    'invoice_supplier_id' => $invoiceSupplier->id,
                    'type' => 'incoming',
                    'date' => !empty($data['shipment_date']) ? Carbon::parse($data['shipment_date']) : Carbon::now(),
                    'address' => $store->address ?? 'Gudang Toko Prima Bangunan',
                    'fee' => (float) ($data['shipment_fee'] ?? 0),
                    'status' => $data['shipment_status'] ?? 'delivered',
                    'notes' => 'Penerimaan barang masuk dari supplier: ' . $supplier->name,
                ]);

                foreach ($preparedDetails as $detail) {
                    ShipmentDetail::create([
                        'shipment_id' => $shipment->id,
                        'item_id' => $detail['item_id'],
                        'quantity' => $detail['quantity'],
                    ]);
                }
            }

            return $invoiceSupplier->load(['invoiceSupplierDetails.item', 'supplier', 'payment.paymentHistories', 'shipments']);
        });
    }

    private function generateSupplierInvoiceNumber(Store $store): string
    {
        $prefix = 'INVS-' . date('Ymd');
        $countToday = InvoiceSupplier::where('store_id', $store->id)
            ->whereDate('created_at', Carbon::today())
            ->count() + 1;

        return sprintf('%s-%04d', $prefix, $countToday);
    }
}
