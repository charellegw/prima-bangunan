<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceSupplierSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();
        if (!$store) return;

        $items = Item::all()->keyBy('name');
        $suppliers = Supplier::all()->keyBy('name');

        // Supplier Invoice 1: Pompa Shimizu dari PT Shimizu Pump Indonesia
        $supShimizu = $suppliers->get('PT Shimizu Pump Indonesia');
        $itemPompa = $items->get('Pompa Air Shimizu PS-128 BIT');

        if ($supShimizu && $itemPompa) {
            $grandTotal1 = 10 * 430000; // 4,300,000

            $paymentSup1 = Payment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $store->id,
                'status' => 'lunas',
                'deadline' => Carbon::now()->addDays(14),
                'amount_total' => $grandTotal1,
                'amount_paid' => $grandTotal1,
            ]);

            $invSup1 = InvoiceSupplier::create([
                'id' => (string) Str::uuid(),
                'invoice_number' => 'INVS-20261028-001',
                'store_id' => $store->id,
                'supplier_id' => $supShimizu->id,
                'payment_id' => $paymentSup1->id,
                'date' => Carbon::now()->subDays(5),
                'note' => 'Restock bulanan pompa air shimizu 10 unit',
                'grand_total' => $grandTotal1,
            ]);

            InvoiceSupplierDetail::create([
                'invoice_supplier_id' => $invSup1->id,
                'item_id' => $itemPompa->id,
                'quantity' => 10,
                'unit_price' => 430000,
                'sub_total' => $grandTotal1,
            ]);

            PaymentHistory::create([
                'payment_id' => $paymentSup1->id,
                'date' => Carbon::now()->subDays(5),
                'amount' => $grandTotal1,
                'method' => 'Transfer Bank BCA',
                'note' => 'Pembayaran tagihan invoice supplier lunas',
            ]);

            // Pengiriman masuk dari supplier (incoming)
            $shipmentIn1 = Shipment::create([
                'id' => (string) Str::uuid(),
                'shipment_number' => 'SHP-IN-20261028-001',
                'invoice_supplier_id' => $invSup1->id,
                'type' => 'incoming',
                'date' => Carbon::now()->subDays(5)->addHours(4),
                'address' => 'Gudang Prima Bangunan, Jakarta Barat',
                'fee' => 0,
                'status' => 'delivered',
                'notes' => 'Barang diterima lengkap dan kondisi baik oleh kepala gudang',
            ]);

            ShipmentDetail::create([
                'shipment_id' => $shipmentIn1->id,
                'item_id' => $itemPompa->id,
                'quantity' => 10,
            ]);
        }

        // Supplier Invoice 2: Semen dari PT Semen Gresik Distribusi (Tempo 30 Hari)
        $supGresik = $suppliers->get('PT Semen Gresik Distribusi');
        $itemSemen = $items->get('Semen Gresik PPC 50kg');

        if ($supGresik && $itemSemen) {
            $grandTotal2 = 100 * 57000; // 5,700,000

            $paymentSup2 = Payment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $store->id,
                'status' => 'sebagian',
                'deadline' => Carbon::now()->addDays(20),
                'amount_total' => $grandTotal2,
                'amount_paid' => 3000000,
            ]);

            $invSup2 = InvoiceSupplier::create([
                'id' => (string) Str::uuid(),
                'invoice_number' => 'INVS-20261101-002',
                'store_id' => $store->id,
                'supplier_id' => $supGresik->id,
                'payment_id' => $paymentSup2->id,
                'date' => Carbon::now()->subDays(1),
                'note' => 'Pengiriman 100 sak Semen Gresik langsung ke toko',
                'grand_total' => $grandTotal2,
            ]);

            InvoiceSupplierDetail::create([
                'invoice_supplier_id' => $invSup2->id,
                'item_id' => $itemSemen->id,
                'quantity' => 100,
                'unit_price' => 57000,
                'sub_total' => $grandTotal2,
            ]);

            PaymentHistory::create([
                'payment_id' => $paymentSup2->id,
                'date' => Carbon::now()->subDays(1),
                'amount' => 3000000,
                'method' => 'Giro / Cek',
                'note' => 'Pembayaran tahap 1 (DP)',
            ]);

            $shipmentIn2 = Shipment::create([
                'id' => (string) Str::uuid(),
                'shipment_number' => 'SHP-IN-20261101-002',
                'invoice_supplier_id' => $invSup2->id,
                'type' => 'incoming',
                'date' => Carbon::now()->subDays(1)->addHours(3),
                'address' => 'Gudang Prima Bangunan, Jakarta Barat',
                'fee' => 0,
                'status' => 'delivered',
                'notes' => 'Diturunkan dari truk fuso PT Semen Gresik',
            ]);

            ShipmentDetail::create([
                'shipment_id' => $shipmentIn2->id,
                'item_id' => $itemSemen->id,
                'quantity' => 100,
            ]);
        }
    }
}
