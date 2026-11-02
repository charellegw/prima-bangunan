<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function showItems(Request $request)
    {
        $store = Store::first();
        $query = Item::with(['category', 'supplierPrices.supplier']);

        // Search tidak ketat (substring / partial matching misal: 'pom' cocok dengan 'pompa')
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter Status Stok
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'low') {
                $query->where('stock', '>', 0)->where('stock', '<=', 10);
            } elseif ($request->stock_status === 'available') {
                $query->where('stock', '>', 10);
            }
        }

        $items = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();
        $categories = ItemCategory::orderBy('name', 'asc')->get();
        $suppliers = Supplier::orderBy('name', 'asc')->get();

        return view('items.index', compact('items', 'categories', 'suppliers', 'store'));
    }

    /**
     * API untuk live search & filter (dengan response JSON)
     */
    public function getAllItems(Request $request)
    {
        $query = Item::with('category');

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->orderBy('name', 'asc')->limit(30)->get();

        return response()->json($items);
    }

    public function show($itemId)
    {
        $item = Item::with(['category', 'supplierPrices.supplier', 'invoiceDetails.invoice'])
            ->findOrFail($itemId);

        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $categories = ItemCategory::orderBy('name', 'asc')->get();

        return view('items.details', compact('item', 'suppliers', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:item_categories,id',
            'brand' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_price' => 'nullable|numeric|min:0',
            'lusin_price' => 'nullable|numeric|min:0',
        ]);

        $store = Store::first();

        $item = Item::create([
            'id' => (string) Str::uuid(),
            'store_id' => $store->id,
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'stock' => $validated['stock'],
            'price' => $validated['price'],
            'retail_price' => $validated['retail_price'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        // Hubungkan supplier awal jika disertakan
        if (!empty($validated['supplier_id']) && !empty($validated['unit_price'])) {
            SupplierPrice::create([
                'item_id' => $item->id,
                'supplier_id' => $validated['supplier_id'],
                'unit_price' => $validated['unit_price'],
                'lusin_price' => $validated['lusin_price'] ?? ($validated['unit_price'] * 12),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Barang berhasil ditambahkan', 'item' => $item], 201);
        }

        return redirect()->route('item.index')->with('success', 'Barang "' . $item->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:item_categories,id',
            'brand' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
        ]);

        $item->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Barang berhasil diperbarui', 'item' => $item]);
        }

        return redirect()->route('item.show', $item->id)->with('success', 'Informasi barang berhasil diperbarui.');
    }

    public function destroy($itemId)
    {
        $item = Item::findOrFail($itemId);
        $itemName = $item->name;
        $item->delete();

        return redirect()->route('item.index')->with('success', 'Barang "' . $itemName . '" berhasil dihapus.');
    }
}
