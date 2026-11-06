<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Item;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\Shipment;
use App\Models\ShipmentDetail;
use App\Models\Store;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Buat invoice penjualan baru dengan transaksi database atomic
     *
     * @param Store $store
     * @param array $data
     * @return Invoice
     * @throws Exception
     */
    public function createSalesInvoice(Store $store, array $data): Invoice
    {
        return DB::transaction(function () use ($store, $data) {
            $clientType = in_array($data['client_type'] ?? '', ['orang', 'retail'])
                ? $data['client_type']
                : 'orang';

            $itemsData = $data['items'] ?? [];
            if (empty($itemsData)) {
                throw new Exception("Daftar barang belanja tidak boleh kosong.");
            }

            // Hitung grand total di server & kurangi stok
            $calculatedGrandTotal = 0;
            $preparedDetails = [];

            foreach ($itemsData as $row) {
                $itemId = $row['item_id'] ?? null;
                $quantity = (int) ($row['quantity'] ?? 0);

                if ($quantity <= 0) {
                    throw new Exception("Jumlah barang harus lebih dari 0.");
                }

                // Kunci baris item untuk update stok (mencegah race condition)
                $item = Item::where('id', $itemId)
                    ->where('store_id', $store->id)
                    ->lockForUpdate()
                    ->first();

                if (!$item) {
                    throw new Exception("Barang tidak ditemukan di toko ini.");
                }

                // Tolak penjualan bila stok tidak cukup dengan pesan jelas
                if ($item->stock < $quantity) {
                    throw new Exception("Stok barang '{$item->name}' tidak mencukupi! Tersedia: {$item->stock}, diminta: {$quantity}.");
                }

                // Snapshot harga sesuai client_type
                $snapshotPrice = $item->getPriceForClientType($clientType);
                $subTotal = $snapshotPrice * $quantity;
                $calculatedGrandTotal += $subTotal;

                // Kurangi stok
                $item->decrement('stock', $quantity);

                $preparedDetails[] = [
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'price' => $snapshotPrice,
                    'sub_total' => $subTotal,
                ];
            }

            // 1. Buat Payment (1-1 dengan Invoice)
            $deadline = !empty($data['deadline'])
                ? Carbon::parse($data['deadline'])
                : Carbon::now()->addDays(14);

            $payment = Payment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $store->id,
                'status' => 'belum_bayar',
                'deadline' => $deadline,
                'amount_total' => $calculatedGrandTotal,
                'amount_paid' => 0,
            ]);

            // 2. Buat Invoice
            $invoiceNumber = $this->generateInvoiceNumber($store);

            $invoice = Invoice::create([
                'id' => (string) Str::uuid(),
                'invoice_number' => $invoiceNumber,
                'store_id' => $store->id,
                'payment_id' => $payment->id,
                'date' => !empty($data['date']) ? Carbon::parse($data['date']) : Carbon::now(),
                'client_name' => trim($data['client_name'] ?? 'Pelanggan Umum'),
                'client_type' => $clientType,
                'note' => $data['note'] ?? null,
                'grand_total' => $calculatedGrandTotal,
            ]);

            // 3. Simpan Invoice Detail (Snapshot harga)
            foreach ($preparedDetails as $detail) {
                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'item_id' => $detail['item_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                    'sub_total' => $detail['sub_total'],
                ]);
            }

            // 4. Pembayaran Awal / Kasir (Mendukung Split Payment atau Single Payment)
            $paymentsList = $data['payments'] ?? [];
            if (empty($paymentsList) && !empty($data['initial_paid']) && (float)$data['initial_paid'] > 0) {
                $paymentsList[] = [
                    'amount' => (float)$data['initial_paid'],
                    'method' => $data['payment_method'] ?? 'Tunai (Cash)',
                    'note' => $data['payment_note'] ?? 'Pembayaran saat transaksi kasir',
                ];
            }

            $totalPaidSum = 0;
            foreach ($paymentsList as $payItem) {
                $payAmount = (float)($payItem['amount'] ?? 0);
                if ($payAmount > 0) {
                    $totalPaidSum += $payAmount;
                }
            }

            if ($totalPaidSum > $calculatedGrandTotal) {
                throw new Exception("Total pembayaran (Rp " . number_format($totalPaidSum, 0, ',', '.') . ") melebihi total belanja (Rp " . number_format($calculatedGrandTotal, 0, ',', '.') . ").");
            }

            foreach ($paymentsList as $payItem) {
                $payAmount = (float)($payItem['amount'] ?? 0);
                if ($payAmount > 0) {
                    PaymentHistory::create([
                        'payment_id' => $payment->id,
                        'date' => Carbon::now(),
                        'amount' => $payAmount,
                        'method' => $payItem['method'] ?? 'Tunai (Cash)',
                        'note' => $payItem['note'] ?? 'Pembayaran saat checkout kasir',
                    ]);
                }
            }

            // Recalculate status payment
            $payment->recalculateStatus();

            // 5. Opsi Buat Pengiriman (Shipment)
            if (!empty($data['needs_shipment']) && !empty($data['shipment_address'])) {
                $shipment = Shipment::create([
                    'id' => (string) Str::uuid(),
                    'shipment_number' => 'SHP-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                    'invoice_id' => $invoice->id,
                    'type' => 'outgoing',
                    'date' => !empty($data['shipment_date']) ? Carbon::parse($data['shipment_date']) : Carbon::now()->addHours(2),
                    'address' => $data['shipment_address'],
                    'fee' => (float) ($data['shipment_fee'] ?? 0),
                    'status' => 'pending',
                    'notes' => $data['shipment_notes'] ?? null,
                ]);

                foreach ($preparedDetails as $detail) {
                    ShipmentDetail::create([
                        'shipment_id' => $shipment->id,
                        'item_id' => $detail['item_id'],
                        'quantity' => $detail['quantity'],
                    ]);
                }
            }

            return $invoice->load(['invoiceDetails.item', 'payment.paymentHistories', 'shipments']);
        });
    }

    private function generateInvoiceNumber(Store $store): string
    {
        $prefix = 'INV-' . date('Ymd');
        $countToday = Invoice::where('store_id', $store->id)
            ->whereDate('created_at', Carbon::today())
            ->count() + 1;

        return sprintf('%s-%04d', $prefix, $countToday);
    }
}
