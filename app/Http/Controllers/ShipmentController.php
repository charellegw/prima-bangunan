<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Store;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $store = Store::first();
        $query = Shipment::with([
            'invoice',
            'invoiceSupplier.supplier',
            'shipmentDetails.item'
        ]);

        // Filter tipe pengiriman (outgoing vs incoming)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search tidak ketat (nomor pengiriman, alamat, nama pelanggan, nama supplier)
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%")
                  ->orWhereHas('invoice', function ($iq) use ($search) {
                      $iq->where('client_name', 'LIKE', "%{$search}%")
                         ->orWhere('invoice_number', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('invoiceSupplier.supplier', function ($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $shipments = $query->orderBy('date', 'desc')->paginate(12)->withQueryString();

        return view('shipments.index', compact('shipments', 'store'));
    }

    public function show($id)
    {
        $shipment = Shipment::with([
            'invoice',
            'invoiceSupplier.supplier',
            'shipmentDetails.item'
        ])->findOrFail($id);

        return view('shipments.show', compact('shipment'));
    }

    public function updateStatus(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,shipping,delivered,returned',
            'notes' => 'nullable|string',
        ]);

        $shipment->update($validated);

        return redirect()->back()->with('success', 'Status pengiriman ' . $shipment->shipment_number . ' diperbarui menjadi: ' . ucfirst($validated['status']));
    }
}
