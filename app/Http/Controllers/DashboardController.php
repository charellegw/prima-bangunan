<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $lowStockCount = Item::where('stock', '<', 10)->count();
        $lowStockItems = Item::with('category')
            ->where('stock', '<', 10)
            ->limit(5)
            ->get();

        $todaySales = Invoice::whereDate('created_at', Carbon::today())->sum('grand_total');

        $duePaymentsCount = Payment::where('status', 'unpaid')
            ->whereDate('due_date', '<=', Carbon::tomorrow())
            ->count();
            
        $duePaymentsList = Payment::where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->limit(3)
            ->get();
        
        return view('dashboard', compact(
            'lowStockCount', 
            'lowStockItems', 
            'todaySales', 
            'duePaymentsCount',
            'duePaymentsList'
        ));
    }
}
