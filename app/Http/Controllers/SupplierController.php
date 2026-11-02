<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierPrice;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $store = Store::first();
        $query = Supplier::withCount(['items', 'invoiceSuppliers']);

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers', 'store'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $store = Store::first();

        $supplier = Supplier::create([
            'store_id' => $store->id,
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Supplier "' . $supplier->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $supplier->update($validated);

        return redirect()->back()->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $name = $supplier->name;
        $supplier->delete();

        return redirect()->back()->with('success', 'Supplier "' . $name . '" berhasil dihapus.');
    }

    /**
     * Tambah atau perbarui relasi harga supplier untuk suatu barang
     */
    public function storeItemSupplierPrice(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'lusin_price' => 'nullable|numeric|min:0',
        ]);

        $lusinPrice = !empty($validated['lusin_price'])
            ? $validated['lusin_price']
            : ($validated['unit_price'] * 12);

        SupplierPrice::updateOrCreate(
            [
                'item_id' => $item->id,
                'supplier_id' => $validated['supplier_id'],
            ],
            [
                'unit_price' => $validated['unit_price'],
                'lusin_price' => $lusinPrice,
            ]
        );

        return redirect()->route('item.show', $item->id)->with('success', 'Harga supplier untuk barang ini berhasil disimpan.');
    }

    /**
     * Hapus relasi harga supplier dari suatu barang
     */
    public function destroyItemSupplierPrice($itemId, $supplierId)
    {
        SupplierPrice::where('item_id', $itemId)
            ->where('supplier_id', $supplierId)
            ->delete();

        return redirect()->route('item.show', $itemId)->with('success', 'Harga supplier berhasil dihapus dari barang ini.');
    }
}
