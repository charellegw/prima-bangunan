<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\InvoiceSupplierService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    protected Store $store;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Admin Prima',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('prima3003'),
            'status' => 'verified',
            'otp_verified' => true,
        ]);

        $this->store = Store::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'owner_id' => $this->user->id,
            'name' => 'Prima Bangunan',
            'timezone' => 'Asia/Jakarta',
        ]);
    }

    /**
     * Aturan Bisnis 1: Pembuatan invoice penjualan mengurangi stock item
     */
    public function test_sales_invoice_reduces_item_stock(): void
    {
        $item = Item::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'store_id' => $this->store->id,
            'name' => 'Semen Gresik 50kg',
            'stock' => 50,
            'price' => 70000,
            'retail_price' => 65000,
        ]);

        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createSalesInvoice($this->store, [
            'client_name' => 'Pak Joko',
            'client_type' => 'orang',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 15],
            ],
        ]);

        $item->refresh();
        $this->assertEquals(35, $item->stock);
        $this->assertEquals(15 * 70000, $invoice->grand_total);
    }

    /**
     * Aturan Bisnis 2: Tolak penjualan bila stock tidak cukup dengan pesan yang jelas
     */
    public function test_sales_invoice_rejects_insufficient_stock_with_clear_message(): void
    {
        $item = Item::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'store_id' => $this->store->id,
            'name' => 'Pompa Air Shimizu',
            'stock' => 3,
            'price' => 500000,
            'retail_price' => 450000,
        ]);

        $invoiceService = app(InvoiceService::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Stok barang 'Pompa Air Shimizu' tidak mencukupi! Tersedia: 3, diminta: 5.");

        $invoiceService->createSalesInvoice($this->store, [
            'client_name' => 'Pak Joko',
            'client_type' => 'orang',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 5],
            ],
        ]);
    }

    /**
     * Aturan Bisnis 3: Invoice supplier menambah stock item
     */
    public function test_supplier_invoice_increases_item_stock(): void
    {
        $supplier = Supplier::create([
            'store_id' => $this->store->id,
            'name' => 'PT Semen Gresik',
            'location' => 'Gresik',
        ]);

        $item = Item::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'store_id' => $this->store->id,
            'name' => 'Semen Gresik 50kg',
            'stock' => 20,
            'price' => 70000,
            'retail_price' => 65000,
        ]);

        $supplierService = app(InvoiceSupplierService::class);
        $invoiceSupplier = $supplierService->createPurchaseInvoice($this->store, [
            'supplier_id' => $supplier->id,
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 80,
                    'unit_price' => 55000,
                ],
            ],
        ]);

        $item->refresh();
        $this->assertEquals(100, $item->stock);
        $this->assertEquals(80 * 55000, $invoiceSupplier->grand_total);
    }

    /**
     * Aturan Bisnis 4: invoice_detail menyimpan snapshot harga saat transaksi,
     * jadi perubahan harga item di masa depan tidak mengubah invoice lama.
     */
    public function test_invoice_detail_stores_price_snapshot(): void
    {
        $item = Item::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'store_id' => $this->store->id,
            'name' => 'Cat No Drop 4kg',
            'stock' => 10,
            'price' => 150000,
            'retail_price' => 140000,
        ]);

        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createSalesInvoice($this->store, [
            'client_name' => 'Bu Ratna',
            'client_type' => 'orang',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 2],
            ],
        ]);

        $detail = $invoice->invoiceDetails->first();
        $this->assertEquals(150000, (float) $detail->price);
        $this->assertEquals(300000, (float) $invoice->grand_total);

        // Ubah harga barang di katalog
        $item->update(['price' => 180000]);

        // Invoice lama harus tetap memiliki snapshot harga 150000
        $detail->refresh();
        $invoice->refresh();
        $this->assertEquals(150000, (float) $detail->price);
        $this->assertEquals(300000, (float) $invoice->grand_total);
    }

    /**
     * Aturan Bisnis 5: Harga yang dipakai mengikuti client_type:
     * price untuk "orang", retail_price untuk "retail".
     */
    public function test_pricing_tier_follows_client_type(): void
    {
        $item = Item::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'store_id' => $this->store->id,
            'name' => 'Pipa PVC Rucika 2 inch',
            'stock' => 50,
            'price' => 45000,       // price untuk orang
            'retail_price' => 40000, // retail_price untuk retail
        ]);

        $invoiceService = app(InvoiceService::class);

        // 1. Transaksi untuk client_type 'orang'
        $invoiceOrang = $invoiceService->createSalesInvoice($this->store, [
            'client_name' => 'Konsumen Biasa',
            'client_type' => 'orang',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 2],
            ],
        ]);
        $this->assertEquals(45000, (float) $invoiceOrang->invoiceDetails->first()->price);
        $this->assertEquals(90000, (float) $invoiceOrang->grand_total);

        // 2. Transaksi untuk client_type 'retail'
        $invoiceRetail = $invoiceService->createSalesInvoice($this->store, [
            'client_name' => 'Toko Sub-Distributor',
            'client_type' => 'retail',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 2],
            ],
        ]);
        $this->assertEquals(40000, (float) $invoiceRetail->invoiceDetails->first()->price);
        $this->assertEquals(80000, (float) $invoiceRetail->grand_total);
    }

    /**
     * Aturan Bisnis 6: Status payment diturunkan dari total payment_history:
     * belum bayar / sebagian / lunas, dan ditandai "jatuh tempo" bila lewat deadline dan belum lunas.
     */
    public function test_payment_status_lifecycle_and_overdue(): void
    {
        $item = Item::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'store_id' => $this->store->id,
            'name' => 'Besi Beton 10mm',
            'stock' => 20,
            'price' => 100000,
            'retail_price' => 90000,
        ]);

        $invoiceService = app(InvoiceService::class);
        $paymentService = app(PaymentService::class);

        // Buat invoice dengan deadline kemarin (sudah overdue)
        $invoice = $invoiceService->createSalesInvoice($this->store, [
            'client_name' => 'Pak Hendra',
            'client_type' => 'orang',
            'deadline' => Carbon::now()->subDay(),
            'items' => [
                ['item_id' => $item->id, 'quantity' => 1], // total 100,000
            ],
        ]);

        $payment = $invoice->payment;

        // Saat belum bayar dan lewat deadline -> jatuh tempo
        $payment->recalculateStatus();
        $this->assertEquals('jatuh_tempo', $payment->status);

        // Bayar sebagian (Rp 40,000 dari total Rp 100,000), tapi masih lewat deadline -> tetap jatuh_tempo
        $paymentService->recordPayment($payment, [
            'amount' => 40000,
            'method' => 'cash',
        ]);
        $payment->refresh();
        $this->assertEquals('jatuh_tempo', $payment->status);
        $this->assertEquals(60000, (float) $payment->remaining_balance);

        // Bayar sisa pelunasan (Rp 60,000) -> status harus otomatis 'lunas'
        $paymentService->recordPayment($payment, [
            'amount' => 60000,
            'method' => 'transfer',
        ]);
        $payment->refresh();
        $this->assertEquals('lunas', $payment->status);
        $this->assertEquals(0, (float) $payment->remaining_balance);
    }

    /**
     * Aturan Bisnis 7: Payment history tidak boleh melebihi sisa
     */
    public function test_payment_history_cannot_exceed_remaining_balance(): void
    {
        $item = Item::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'store_id' => $this->store->id,
            'name' => 'Kuas Cat 3 inch',
            'stock' => 10,
            'price' => 20000,
            'retail_price' => 18000,
        ]);

        $invoiceService = app(InvoiceService::class);
        $paymentService = app(PaymentService::class);

        $invoice = $invoiceService->createSalesInvoice($this->store, [
            'client_name' => 'Pak Danu',
            'client_type' => 'orang',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 2], // total 40,000
            ],
        ]);

        $payment = $invoice->payment;
        $this->assertEquals(40000, (float) $payment->remaining_balance);

        // Coba bayar Rp 50,000 (melebihi sisa Rp 40,000) -> harus ditolak!
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("melebihi sisa tagihan");

        $paymentService->recordPayment($payment, [
            'amount' => 50000,
            'method' => 'cash',
        ]);
    }
}
