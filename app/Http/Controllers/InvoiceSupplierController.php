<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSupplier;
use App\Models\Item;
use App\Models\Store;
use App\Models\Supplier;
use App\Services\InvoiceSupplierService;
use Exception;
use Illuminate\Http\Request;

class InvoiceSupplierController extends Controller
{
    protected InvoiceSupplierService $invoiceSupplierService;

    public function __construct(InvoiceSupplierService $invoiceSupplierService)
    {
        $this->invoiceSupplierService = $invoiceSupplierService;
    }

    public function index(Request $request)
    {
        $store = Store::first();
        $query = InvoiceSupplier::with(['supplier', 'payment.paymentHistories', 'invoiceSupplierDetails.item', 'shipments']);

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhere('note', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $invoiceSuppliers = $query->orderBy('date', 'desc')->paginate(12)->withQueryString();
        $suppliers = Supplier::orderBy('name', 'asc')->get();

        return view('invoice_suppliers.index', compact('invoiceSuppliers', 'suppliers', 'store'));
    }

    public function create()
    {
        $store = Store::first();
        $suppliers = Supplier::with('supplierPrices')->orderBy('name', 'asc')->get();
        $items = Item::with(['category', 'supplierPrices'])->orderBy('name', 'asc')->get();

        return view('invoice_suppliers.create', compact('store', 'suppliers', 'items'));
    }

    public function store(Request $request)
    {
        $store = Store::first();

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'note' => 'nullable|string',
            'initial_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'payment_note' => 'nullable|string',
            'has_incoming_shipment' => 'nullable|boolean',
            'shipment_date' => 'nullable|date',
            'shipment_status' => 'nullable|in:pending,shipping,delivered',
            'shipment_fee' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            $invoiceSupplier = $this->invoiceSupplierService->createPurchaseInvoice($store, $validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Faktur pembelian stok supplier berhasil dibuat.',
                    'invoice_supplier' => $invoiceSupplier,
                ], 201);
            }

            return redirect()->route('invoice-suppliers.show', $invoiceSupplier->id)
                ->with('success', 'Faktur pembelian ' . $invoiceSupplier->invoice_number . ' berhasil diproses dan stok telah ditambahkan.');
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
        $invoiceSupplier = InvoiceSupplier::with([
            'store',
            'supplier',
            'payment.paymentHistories',
            'invoiceSupplierDetails.item',
            'shipments.shipmentDetails.item'
        ])->findOrFail($id);

        return view('invoice_suppliers.show', compact('invoiceSupplier'));
    }
}
