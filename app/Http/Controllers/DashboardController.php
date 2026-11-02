<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceSupplier;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $store = Store::first();

        // 1. Ringkasan Statistik
        $totalItems = Item::count();
        $lowStockItems = Item::where('stock', '<=', 10)->orderBy('stock', 'asc')->get();
        $outOfStockCount = Item::where('stock', '<=', 0)->count();

        // Penjualan hari ini & total penjualan
        $salesToday = Invoice::whereDate('date', Carbon::today())->sum('grand_total');
        $totalSales = Invoice::sum('grand_total');
        $invoicesCount = Invoice::count();

        // Pembayaran & Piutang / Hutang
        $overduePaymentsCount = Payment::where('status', 'jatuh_tempo')->count();
        $unpaidPayments = Payment::whereIn('status', ['belum_bayar', 'sebagian', 'jatuh_tempo'])->get();
        $totalOutstanding = $unpaidPayments->sum(function ($p) {
            return $p->remaining_balance;
        });

        // Pengiriman aktif
        $activeShipments = Shipment::whereIn('status', ['pending', 'shipping'])
            ->with(['invoice', 'invoiceSupplier.supplier'])
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        // Transaksi penjualan terbaru
        $recentInvoices = Invoice::with(['payment', 'invoiceDetails.item'])
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'store',
            'totalItems',
            'lowStockItems',
            'outOfStockCount',
            'salesToday',
            'totalSales',
            'invoicesCount',
            'overduePaymentsCount',
            'totalOutstanding',
            'activeShipments',
            'recentInvoices'
        ));
    }
}
