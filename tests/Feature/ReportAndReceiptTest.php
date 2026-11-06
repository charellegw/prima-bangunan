<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\InvoiceSupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReportAndReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected Store $store;
    protected User $user;
    protected Item $item;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Admin Prima',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('prima3003'),
            'status' => 'verified',
            'otp_verified' => true,
        ]);

        $this->store = Store::create([
            'id' => (string) Str::uuid(),
            'owner_id' => $this->user->id,
            'name' => 'Prima Bangunan',
            'phone' => '08123456789',
            'address' => 'Jl. Raya Bangunan No. 88, Jakarta Barat',
            'timezone' => 'Asia/Jakarta',
        ]);

        $this->item = Item::create([
            'id' => (string) Str::uuid(),
            'store_id' => $this->store->id,
            'name' => 'Cat Dulux Weathershield 2.5L',
            'stock' => 50,
            'price' => 280000,
            'retail_price' => 265000,
        ]);

        $this->supplier = Supplier::create([
            'id' => (string) Str::uuid(),
            'store_id' => $this->store->id,
            'name' => 'PT Akzo Nobel Paints',
            'location' => 'Kawasan Industri Pulogadung',
            'phone' => '021-4600100',
        ]);
    }

    /**
     * Test Cetak Struk Kasir / POS Receipt View
     */
    public function test_receipt_view_renders_successfully(): void
    {
        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createSalesInvoice($this->store, [
            'client_name' => 'Pak Budi Hartono',
            'client_type' => 'orang',
            'initial_paid' => 280000,
            'payment_method' => 'tunai',
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 1]
            ]
        ]);

        $response = $this->actingAs($this->user)->get(route('invoices.receipt', $invoice->id));

        $response->assertStatus(200);
        $response->assertSee('Prima Bangunan');
        $response->assertSee('Pak Budi Hartono');
        $response->assertSee('Cat Dulux Weathershield 2.5L');
        $response->assertSee('280.000');
    }

    /**
     * Test Pusat Laporan Index
     */
    public function test_reports_index_renders_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Laporan');
        $response->assertSee('Laporan Penjualan');
        $response->assertSee('Laporan Pembelian Supplier');
        $response->assertSee('Mutasi');
        $response->assertSee('Laporan Piutang');
    }

    /**
     * Test Laporan Penjualan (HTML, CSV Export, Print View)
     */
    public function test_sales_report_and_exports(): void
    {
        $invoiceService = app(InvoiceService::class);
        $invoiceService->createSalesInvoice($this->store, [
            'client_name' => 'Proyek Rumah Mewah',
            'client_type' => 'retail',
            'initial_paid' => 100000,
            'payment_method' => 'transfer_bca',
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 2]
            ]
        ]);

        // HTML Page
        $htmlResponse = $this->actingAs($this->user)->get(route('reports.sales'));
        $htmlResponse->assertStatus(200);
        $htmlResponse->assertSee('Proyek Rumah Mewah');

        // CSV Export
        $excelResponse = $this->actingAs($this->user)->get(route('reports.sales', ['export' => 'excel']));
        $excelResponse->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $excelResponse->headers->get('content-type'));

        // Print/PDF View
        $pdfResponse = $this->actingAs($this->user)->get(route('reports.sales', ['export' => 'pdf']));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertSee('LAPORAN PENJUALAN');
    }

    /**
     * Test Laporan Pembelian Supplier
     */
    public function test_purchases_report_and_exports(): void
    {
        $invoiceSupplierService = app(InvoiceSupplierService::class);
        $invoiceSupplierService->createPurchaseInvoice($this->store, [
            'supplier_id' => $this->supplier->id,
            'initial_paid' => 500000,
            'payment_method' => 'transfer_mandiri',
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 10, 'unit_price' => 220000]
            ]
        ]);

        // HTML Page
        $htmlResponse = $this->actingAs($this->user)->get(route('reports.purchases'));
        $htmlResponse->assertStatus(200);
        $htmlResponse->assertSee('PT Akzo Nobel Paints');

        // CSV Export
        $excelResponse = $this->actingAs($this->user)->get(route('reports.purchases', ['export' => 'excel']));
        $excelResponse->assertStatus(200);

        // Print/PDF View
        $pdfResponse = $this->actingAs($this->user)->get(route('reports.purchases', ['export' => 'pdf']));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertSee('LAPORAN PEMBELIAN SUPPLIER');
    }

    /**
     * Test Laporan Mutasi & Valuasi Stok
     */
    public function test_stock_report_and_exports(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.stock'));
        $response->assertStatus(200);
        $response->assertSee('Cat Dulux Weathershield 2.5L');

        // CSV Export
        $excelResponse = $this->actingAs($this->user)->get(route('reports.stock', ['export' => 'excel']));
        $excelResponse->assertStatus(200);

        // Print View
        $pdfResponse = $this->actingAs($this->user)->get(route('reports.stock', ['export' => 'pdf']));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertSee('LAPORAN MUTASI');
    }

    /**
     * Test Buku Piutang & Hutang (Aging Report)
     */
    public function test_debts_report_and_exports(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.debts'));
        $response->assertStatus(200);
        $response->assertSee('Buku Piutang');

        // CSV Export
        $excelResponse = $this->actingAs($this->user)->get(route('reports.debts', ['export' => 'excel']));
        $excelResponse->assertStatus(200);

        // Print View
        $pdfResponse = $this->actingAs($this->user)->get(route('reports.debts', ['export' => 'pdf']));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertSee('BUKU REKAP PIUTANG');
    }
}
