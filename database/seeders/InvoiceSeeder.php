<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Item;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\Shipment;
use App\Models\ShipmentDetail;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();
        if (!$store) return;

        $items = Item::all()->keyBy('name');

        // Invoice 1: Orang, Lunas
        $itemPompa = $items->get('Pompa Air Shimizu PS-128 BIT');
        $itemSemen = $items->get('Semen Gresik PPC 50kg');

        if ($itemPompa && $itemSemen) {
            $payment1 = Payment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $store->id,
                'status' => 'lunas',
                'deadline' => Carbon::now()->addDays(7),
                'amount_total' => 656000,
                'amount_paid' => 656000,
            ]);

            $inv1 = Invoice::create([
                'id' => (string) Str::uuid(),
                'invoice_number' => 'INV-20261101-001',
                'store_id' => $store->id,
                'payment_id' => $payment1->id,
                'date' => Carbon::now()->subDays(2),
                'client_name' => 'Budi Santoso',
                'client_type' => 'orang',
                'note' => 'Pembelian pompa dan semen untuk renovasi kamar mandi',
                'grand_total' => 656000,
            ]);

            InvoiceDetail::create([
                'invoice_id' => $inv1->id,
                'item_id' => $itemPompa->id,
                'quantity' => 1,
                'price' => $itemPompa->price, // 520,000
                'sub_total' => 520000,
            ]);

            InvoiceDetail::create([
                'invoice_id' => $inv1->id,
                'item_id' => $itemSemen->id,
                'quantity' => 2,
                'price' => $itemSemen->price, // 68,000
                'sub_total' => 136000,
            ]);

            PaymentHistory::create([
                'payment_id' => $payment1->id,
                'date' => Carbon::now()->subDays(2),
                'amount' => 656000,
                'method' => 'Transfer Bank BCA',
                'note' => 'Pembayaran lunas via transfer',
            ]);

            $shipment1 = Shipment::create([
                'id' => (string) Str::uuid(),
                'shipment_number' => 'SHP-20261101-001',
                'invoice_id' => $inv1->id,
                'type' => 'outgoing',
                'date' => Carbon::now()->subDays(2)->addHours(2),
                'address' => 'Jl. Kebon Jeruk No. 12, Kebon Jeruk, Jakarta Barat',
                'fee' => 25000,
                'status' => 'delivered',
                'notes' => 'Terkirim langsung dengan armada pickup toko',
            ]);

            ShipmentDetail::create([
                'shipment_id' => $shipment1->id,
                'item_id' => $itemPompa->id,
                'quantity' => 1,
            ]);
            ShipmentDetail::create([
                'shipment_id' => $shipment1->id,
                'item_id' => $itemSemen->id,
                'quantity' => 2,
            ]);
        }

        // Invoice 2: Retail, Sebagian (DP)
        $itemBesi = $items->get('Besi Beton Polos 8mm (12 Meter SNI)');
        if ($itemSemen && $itemBesi) {
            $grandTotal2 = (20 * $itemSemen->retail_price) + (10 * $itemBesi->retail_price); // 20 * 64,000 + 10 * 53,000 = 1,810,000

            $payment2 = Payment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $store->id,
                'status' => 'sebagian',
                'deadline' => Carbon::now()->addDays(14),
                'amount_total' => $grandTotal2,
                'amount_paid' => 1000000,
            ]);

            $inv2 = Invoice::create([
                'id' => (string) Str::uuid(),
                'invoice_number' => 'INV-20261102-002',
                'store_id' => $store->id,
                'payment_id' => $payment2->id,
                'date' => Carbon::now()->subDays(1),
                'client_name' => 'PT Bangun Graha Konstruksi',
                'client_type' => 'retail',
                'note' => 'Pengadaan material proyek perumahan',
                'grand_total' => $grandTotal2,
            ]);

            InvoiceDetail::create([
                'invoice_id' => $inv2->id,
                'item_id' => $itemSemen->id,
                'quantity' => 20,
                'price' => $itemSemen->retail_price,
                'sub_total' => 20 * $itemSemen->retail_price,
            ]);

            InvoiceDetail::create([
                'invoice_id' => $inv2->id,
                'item_id' => $itemBesi->id,
                'quantity' => 10,
                'price' => $itemBesi->retail_price,
                'sub_total' => 10 * $itemBesi->retail_price,
            ]);

            PaymentHistory::create([
                'payment_id' => $payment2->id,
                'date' => Carbon::now()->subDays(1),
                'amount' => 1000000,
                'method' => 'Transfer Bank Mandiri',
                'note' => 'Uang muka / DP 55%',
            ]);

            $shipment2 = Shipment::create([
                'id' => (string) Str::uuid(),
                'shipment_number' => 'SHP-20261102-002',
                'invoice_id' => $inv2->id,
                'type' => 'outgoing',
                'date' => Carbon::now()->addHours(1),
                'address' => 'Proyek Cluster Harmoni Blok B3 No. 8, Tangerang',
                'fee' => 75000,
                'status' => 'shipping',
                'notes' => 'Sedang dalam pengantaran oleh sopir Pak Yanto',
            ]);

            ShipmentDetail::create([
                'shipment_id' => $shipment2->id,
                'item_id' => $itemSemen->id,
                'quantity' => 20,
            ]);
            ShipmentDetail::create([
                'shipment_id' => $shipment2->id,
                'item_id' => $itemBesi->id,
                'quantity' => 10,
            ]);
        }

        // Invoice 3: Orang, Jatuh Tempo (Belum Lunas & Melewati Deadline)
        $itemCat = $items->get('Cat Tembok Avitex White 5kg');
        $itemNoDrop = $items->get('Cat Pelapis Anti Bocor No Drop 4kg');

        if ($itemCat && $itemNoDrop) {
            $grandTotal3 = (4 * $itemCat->price) + (2 * $itemNoDrop->price); // 4 * 125,000 + 2 * 180,000 = 860,000

            $payment3 = Payment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $store->id,
                'status' => 'jatuh_tempo',
                'deadline' => Carbon::now()->subDays(3), // sudah lewat deadline
                'amount_total' => $grandTotal3,
                'amount_paid' => 0,
            ]);

            $inv3 = Invoice::create([
                'id' => (string) Str::uuid(),
                'invoice_number' => 'INV-20261025-003',
                'store_id' => $store->id,
                'payment_id' => $payment3->id,
                'date' => Carbon::now()->subDays(10),
                'client_name' => 'Haji Mansyur',
                'client_type' => 'orang',
                'note' => 'Cat rumah tinggal Pak Haji, janji bayar 7 hari',
                'grand_total' => $grandTotal3,
            ]);

            InvoiceDetail::create([
                'invoice_id' => $inv3->id,
                'item_id' => $itemCat->id,
                'quantity' => 4,
                'price' => $itemCat->price,
                'sub_total' => 4 * $itemCat->price,
            ]);

            InvoiceDetail::create([
                'invoice_id' => $inv3->id,
                'item_id' => $itemNoDrop->id,
                'quantity' => 2,
                'price' => $itemNoDrop->price,
                'sub_total' => 2 * $itemNoDrop->price,
            ]);
        }
    }
}
