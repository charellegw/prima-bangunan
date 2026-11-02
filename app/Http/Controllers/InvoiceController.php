<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Store;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        $store = Store::first();
        $query = Invoice::with(['payment.paymentHistories', 'invoiceDetails.item', 'shipments']);

        // Search tidak ketat (nomor invoice, nama pelanggan, catatan)
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhere('client_name', 'LIKE', "%{$search}%")
                  ->orWhere('note', 'LIKE', "%{$search}%");
            });
        }

        // Filter Tipe Pelanggan (orang / retail)
        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        // Filter Status Pembayaran
        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }

        // Filter Rentang Tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('date', 'desc')->paginate(12)->withQueryString();

        return view('invoices.index', compact('invoices', 'store'));
    }

    public function create()
    {
        $store = Store::first();
        $items = Item::with('category')->orderBy('name', 'asc')->get();

        return view('invoices.create', compact('store', 'items'));
    }

    public function store(Request $request)
    {
        $store = Store::first();

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_type' => 'required|in:orang,retail',
            'date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'note' => 'nullable|string',
            'initial_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'payment_note' => 'nullable|string',
            'needs_shipment' => 'nullable|boolean',
            'shipment_address' => 'nullable|string',
            'shipment_fee' => 'nullable|numeric|min:0',
            'shipment_date' => 'nullable|date',
            'shipment_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $invoice = $this->invoiceService->createSalesInvoice($store, $validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice penjualan berhasil dibuat.',
                    'invoice' => $invoice,
                ], 201);
            }

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice penjualan ' . $invoice->invoice_number . ' berhasil diproses.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with([
            'store',
            'payment.paymentHistories',
            'invoiceDetails.item',
            'shipments.shipmentDetails.item'
        ])->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }
}
