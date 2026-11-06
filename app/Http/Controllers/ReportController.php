<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceSupplier;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Payment;
use App\Models\Store;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Dashboard & Pusat Menu Laporan
     */
    public function index()
    {
        $store = Store::first();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Omzet Penjualan Bulan Ini
        $salesThisMonth = Invoice::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('grand_total');
        $salesCountThisMonth = Invoice::whereBetween('date', [$startOfMonth, $endOfMonth])->count();

        // Pengeluaran Pembelian Supplier Bulan Ini
        $purchasesThisMonth = InvoiceSupplier::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('grand_total');
        $purchasesCountThisMonth = InvoiceSupplier::whereBetween('date', [$startOfMonth, $endOfMonth])->count();

        // Nilai Aset Stok Material Saat Ini
        $items = Item::with('supplierPrices')->get();
        $totalStockValue = $items->sum(function ($it) {
            $cost = $it->supplierPrices->first()?->unit_price ?? ($it->price * 0.8);
            return $it->stock * $cost;
        });
        $totalItemsCount = $items->count();
        $lowStockCount = $items->where('stock', '<=', 10)->count();

        // Piutang Pelanggan & Tagihan Jatuh Tempo
        $unpaidSalesPayments = Payment::has('invoice')
            ->whereIn('status', ['belum_bayar', 'sebagian', 'jatuh_tempo'])
            ->get();
        $totalReceivables = $unpaidSalesPayments->sum('remaining_balance');

        $unpaidSupplierPayments = Payment::has('invoiceSupplier')
            ->whereIn('status', ['belum_bayar', 'sebagian', 'jatuh_tempo'])
            ->get();
        $totalPayables = $unpaidSupplierPayments->sum('remaining_balance');

        $overdueCount = Payment::where('status', 'jatuh_tempo')->count();

        return view('reports.index', compact(
            'store',
            'salesThisMonth',
            'salesCountThisMonth',
            'purchasesThisMonth',
            'purchasesCountThisMonth',
            'totalStockValue',
            'totalItemsCount',
            'lowStockCount',
            'totalReceivables',
            'totalPayables',
            'overdueCount'
        ));
    }

    /**
     * Laporan Penjualan (Sales Report)
     */
    public function sales(Request $request)
    {
        $store = Store::first();
        $period = $request->get('period', 'this_month');

        $dates = $this->resolveDateRange($period, $request->date_from, $request->date_to);
        $dateFrom = $dates['from'];
        $dateTo = $dates['to'];

        $query = Invoice::with(['payment.paymentHistories', 'invoiceDetails.item'])
            ->whereBetween('date', [$dateFrom->startOfDay(), $dateTo->copy()->endOfDay()]);

        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($pq) use ($request) {
                $pq->where('status', $request->payment_status);
            });
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhere('client_name', 'LIKE', "%{$search}%");
            });
        }

        // Export Excel
        if ($request->export === 'excel') {
            return $this->exportSalesExcel($query->orderBy('date', 'desc')->get(), $dateFrom, $dateTo);
        }

        // Export PDF / Print
        if ($request->export === 'pdf') {
            $invoices = $query->orderBy('date', 'desc')->get();
            return view('reports.print_sales', compact('invoices', 'store', 'dateFrom', 'dateTo'));
        }

        $invoices = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        // Hitung akumulasi
        $statsQuery = clone $query;
        $allMatched = $statsQuery->get();
        $totalOmzet = $allMatched->sum('grand_total');
        $totalPaid = $allMatched->sum(fn($i) => $i->payment->total_paid ?? 0);
        $totalUnpaid = $allMatched->sum(fn($i) => $i->payment->remaining_balance ?? 0);

        return view('reports.sales', compact(
            'invoices',
            'store',
            'period',
            'dateFrom',
            'dateTo',
            'totalOmzet',
            'totalPaid',
            'totalUnpaid'
        ));
    }

    /**
     * Laporan Pembelian Supplier (Purchases Report)
     */
    public function purchases(Request $request)
    {
        $store = Store::first();
        $period = $request->get('period', 'this_month');

        $dates = $this->resolveDateRange($period, $request->date_from, $request->date_to);
        $dateFrom = $dates['from'];
        $dateTo = $dates['to'];

        $query = InvoiceSupplier::with(['supplier', 'payment.paymentHistories', 'invoiceSupplierDetails.item'])
            ->whereBetween('date', [$dateFrom->startOfDay(), $dateTo->copy()->endOfDay()]);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($pq) use ($request) {
                $pq->where('status', $request->payment_status);
            });
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'LIKE', "%{$search}%"));
            });
        }

        if ($request->export === 'excel') {
            return $this->exportPurchasesExcel($query->orderBy('date', 'desc')->get(), $dateFrom, $dateTo);
        }

        if ($request->export === 'pdf') {
            $invoices = $query->orderBy('date', 'desc')->get();
            return view('reports.print_purchases', compact('invoices', 'store', 'dateFrom', 'dateTo'));
        }

        $invoices = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name', 'asc')->get();

        $allMatched = (clone $query)->get();
        $totalPurchases = $allMatched->sum('grand_total');
        $totalPaid = $allMatched->sum(fn($i) => $i->payment->total_paid ?? 0);
        $totalUnpaid = $allMatched->sum(fn($i) => $i->payment->remaining_balance ?? 0);

        return view('reports.purchases', compact(
            'invoices',
            'suppliers',
            'store',
            'period',
            'dateFrom',
            'dateTo',
            'totalPurchases',
            'totalPaid',
            'totalUnpaid'
        ));
    }

    /**
     * Laporan Riwayat & Mutasi Stok (Stock Movement Report)
     */
    public function stock(Request $request)
    {
        $store = Store::first();
        $period = $request->get('period', 'this_month');

        $dates = $this->resolveDateRange($period, $request->date_from, $request->date_to);
        $dateFrom = $dates['from'];
        $dateTo = $dates['to'];

        $query = Item::with(['category', 'supplierPrices.supplier']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'low') {
                $query->where('stock', '>', 0)->where('stock', '<=', 10);
            } elseif ($request->stock_status === 'available') {
                $query->where('stock', '>', 10);
            }
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%");
            });
        }

        $items = $query->orderBy('name', 'asc')->get();

        // Hitung mutasi keluar (penjualan) & mutasi masuk (supplier) dalam periode
        $stockReportData = $items->map(function ($it) use ($dateFrom, $dateTo) {
            $qtySold = $it->invoiceDetails()
                ->whereHas('invoice', fn($iq) => $iq->whereBetween('date', [$dateFrom->startOfDay(), $dateTo->copy()->endOfDay()]))
                ->sum('quantity');

            $qtyReceived = $it->invoiceSupplierDetails()
                ->whereHas('invoiceSupplier', fn($isq) => $isq->whereBetween('date', [$dateFrom->startOfDay(), $dateTo->copy()->endOfDay()]))
                ->sum('quantity');

            $costPrice = (float)($it->supplierPrices->first()?->unit_price ?? ($it->price * 0.8));
            $valuation = $it->stock * $costPrice;

            return [
                'item' => $it,
                'qty_sold' => (int)$qtySold,
                'qty_received' => (int)$qtyReceived,
                'current_stock' => $it->stock,
                'cost_price' => $costPrice,
                'valuation' => $valuation,
            ];
        });

        if ($request->export === 'excel') {
            return $this->exportStockExcel($stockReportData, $dateFrom, $dateTo);
        }

        if ($request->export === 'pdf') {
            return view('reports.print_stock', compact('stockReportData', 'store', 'dateFrom', 'dateTo'));
        }

        $categories = ItemCategory::orderBy('name', 'asc')->get();
        $totalValuation = $stockReportData->sum('valuation');
        $totalStockUnits = $stockReportData->sum('current_stock');
        $totalSoldUnits = $stockReportData->sum('qty_sold');
        $totalReceivedUnits = $stockReportData->sum('qty_received');

        return view('reports.stock', compact(
            'stockReportData',
            'categories',
            'store',
            'period',
            'dateFrom',
            'dateTo',
            'totalValuation',
            'totalStockUnits',
            'totalSoldUnits',
            'totalReceivedUnits'
        ));
    }

    /**
     * Laporan Piutang Pelanggan & Hutang Supplier (Aging Report)
     */
    public function debts(Request $request)
    {
        $store = Store::first();

        // 1. Piutang dari Penjualan Konsumen
        $receivables = Payment::has('invoice')
            ->whereIn('status', ['belum_bayar', 'sebagian', 'jatuh_tempo'])
            ->with(['invoice', 'paymentHistories'])
            ->orderBy('deadline', 'asc')
            ->get();

        // 2. Hutang Toko ke Supplier
        $payables = Payment::has('invoiceSupplier')
            ->whereIn('status', ['belum_bayar', 'sebagian', 'jatuh_tempo'])
            ->with(['invoiceSupplier.supplier', 'paymentHistories'])
            ->orderBy('deadline', 'asc')
            ->get();

        if ($request->export === 'excel') {
            return $this->exportDebtsExcel($receivables, $payables);
        }

        if ($request->export === 'pdf') {
            return view('reports.print_debts', compact('receivables', 'payables', 'store'));
        }

        $totalReceivableSum = $receivables->sum('remaining_balance');
        $totalPayableSum = $payables->sum('remaining_balance');

        return view('reports.debts', compact(
            'receivables',
            'payables',
            'store',
            'totalReceivableSum',
            'totalPayableSum'
        ));
    }

    private function resolveDateRange(string $period, $customFrom = null, $customTo = null): array
    {
        if ($period === 'today') {
            return ['from' => Carbon::today(), 'to' => Carbon::today()];
        } elseif ($period === 'yesterday') {
            return ['from' => Carbon::yesterday(), 'to' => Carbon::yesterday()];
        } elseif ($period === 'this_week') {
            return ['from' => Carbon::now()->startOfWeek(), 'to' => Carbon::now()->endOfWeek()];
        } elseif ($period === 'last_month') {
            return ['from' => Carbon::now()->subMonth()->startOfMonth(), 'to' => Carbon::now()->subMonth()->endOfMonth()];
        } elseif ($period === 'this_year') {
            return ['from' => Carbon::now()->startOfYear(), 'to' => Carbon::now()->endOfYear()];
        } elseif ($period === 'custom' && $customFrom && $customTo) {
            return ['from' => Carbon::parse($customFrom), 'to' => Carbon::parse($customTo)];
        }

        // default: this_month
        return ['from' => Carbon::now()->startOfMonth(), 'to' => Carbon::now()->endOfMonth()];
    }

    private function exportSalesExcel($invoices, $from, $to)
    {
        $filename = 'Laporan_Penjualan_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';

        return response()->stream(function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['LAPORAN PENJUALAN - PRIMA BANGUNAN']);
            fputcsv($handle, []);
            fputcsv($handle, ['No. Nota', 'Tanggal', 'Nama Pelanggan', 'Tipe Harga', 'Status Pembayaran', 'Total Belanja (Rp)', 'Sudah Dibayar (Rp)', 'Sisa Piutang (Rp)', 'Jatuh Tempo']);

            foreach ($invoices as $inv) {
                fputcsv($handle, [
                    $inv->invoice_number,
                    $inv->date->format('d/m/Y H:i'),
                    $inv->client_name,
                    strtoupper($inv->client_type),
                    strtoupper($inv->payment->status ?? 'BELUM BAYAR'),
                    (float)$inv->grand_total,
                    (float)($inv->payment->total_paid ?? 0),
                    (float)($inv->payment->remaining_balance ?? 0),
                    $inv->payment->deadline ? $inv->payment->deadline->format('d/m/Y') : '-',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportPurchasesExcel($invoices, $from, $to)
    {
        $filename = 'Laporan_Pembelian_Supplier_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';

        return response()->stream(function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['LAPORAN PEMBELIAN STOK SUPPLIER - PRIMA BANGUNAN']);
            fputcsv($handle, []);
            fputcsv($handle, ['No. Faktur', 'Tanggal', 'Nama Supplier', 'Status Pembayaran', 'Total Pembelian (Rp)', 'Sudah Dibayar (Rp)', 'Sisa Hutang (Rp)', 'Jatuh Tempo', 'Catatan']);

            foreach ($invoices as $inv) {
                fputcsv($handle, [
                    $inv->invoice_number,
                    $inv->date->format('d/m/Y H:i'),
                    $inv->supplier->name ?? 'Supplier',
                    strtoupper($inv->payment->status ?? 'BELUM BAYAR'),
                    (float)$inv->grand_total,
                    (float)($inv->payment->total_paid ?? 0),
                    (float)($inv->payment->remaining_balance ?? 0),
                    $inv->payment->deadline ? $inv->payment->deadline->format('d/m/Y') : '-',
                    $inv->note ?: '-',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportStockExcel($stockData, $from, $to)
    {
        $filename = 'Laporan_Mutasi_Stok_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';

        return response()->stream(function () use ($stockData) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['LAPORAN MUTASI & NILAI ASET STOK - PRIMA BANGUNAN']);
            fputcsv($handle, []);
            fputcsv($handle, ['Nama Barang', 'Kategori', 'Merek', 'Stok Saat Ini (Unit)', 'Barang Masuk (Periode)', 'Barang Keluar (Periode)', 'Harga Modal (Rp)', 'Estimasi Nilai Aset (Rp)', 'Harga Eceran (Rp)', 'Harga Grosir (Rp)']);

            foreach ($stockData as $row) {
                $item = $row['item'];
                fputcsv($handle, [
                    $item->name,
                    $item->category->name ?? 'Material Umum',
                    $item->brand ?: '-',
                    $row['current_stock'],
                    $row['qty_received'],
                    $row['qty_sold'],
                    $row['cost_price'],
                    $row['valuation'],
                    (float)$item->price,
                    (float)$item->retail_price,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportDebtsExcel($receivables, $payables)
    {
        $filename = 'Laporan_Piutang_dan_Hutang_' . date('Ymd') . '.csv';

        return response()->stream(function () use ($receivables, $payables) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['LAPORAN PIUTANG PELANGGAN & HUTANG SUPPLIER - PRIMA BANGUNAN']);
            fputcsv($handle, []);
            fputcsv($handle, ['=== 1. DAFTAR PIUTANG PENJUALAN KONSUMEN ===']);
            fputcsv($handle, ['No. Nota', 'Nama Pelanggan', 'Tipe', 'Total Tagihan (Rp)', 'Sudah Bayar (Rp)', 'Sisa Piutang (Rp)', 'Status', 'Jatuh Tempo']);

            foreach ($receivables as $r) {
                fputcsv($handle, [
                    $r->invoice->invoice_number ?? '-',
                    $r->invoice->client_name ?? '-',
                    strtoupper($r->invoice->client_type ?? '-'),
                    (float)$r->amount_total,
                    (float)$r->total_paid,
                    (float)$r->remaining_balance,
                    strtoupper($r->status),
                    $r->deadline ? $r->deadline->format('d/m/Y') : '-',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['=== 2. DAFTAR HUTANG TOKO KE SUPPLIER ===']);
            fputcsv($handle, ['No. Faktur', 'Nama Supplier', 'Lokasi', 'Total Tagihan (Rp)', 'Sudah Bayar (Rp)', 'Sisa Hutang (Rp)', 'Status', 'Jatuh Tempo']);

            foreach ($payables as $p) {
                fputcsv($handle, [
                    $p->invoiceSupplier->invoice_number ?? '-',
                    $p->invoiceSupplier->supplier->name ?? '-',
                    $p->invoiceSupplier->supplier->location ?? '-',
                    (float)$p->amount_total,
                    (float)$p->total_paid,
                    (float)$p->remaining_balance,
                    strtoupper($p->status),
                    $p->deadline ? $p->deadline->format('d/m/Y') : '-',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
