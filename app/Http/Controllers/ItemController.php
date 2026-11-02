<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierPrice;
use App\Models\ItemCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showItems()
    {
        return view('items.index');
    }

    public function show($itemId)
    {
        $item = Item::findOrFail($itemId);

        $suppliers = Supplier::whereHas('supplierPrices', function ($query) use ($itemId) {
            $query->where('item_id', $itemId);
        })->get();

        // ambil semua location unik dari supplier
        $locations = Supplier::select('location')
            ->distinct()
            ->pluck('location')
            ->filter()
            ->values();

        // struktur harga: [supplier_id][location] = price data
        $prices = [];

        foreach ($suppliers as $supplier) {
            $itemPrices = $item->supplierPrices()
                ->where('supplier_id', $supplier->id)
                ->get();

            foreach ($itemPrices as $price) {
                $loc = $supplier->location ?? 'UNKNOWN LOCATION';

                $prices[$supplier->id][$loc] = [
                    'pcs' => $price->unit_price,
                    'lusin' => $price->dozen_price,
                ];
            }
        }

        return view('items.details', compact(
            'item',
            'suppliers',
            'locations',
            'prices'
        ));
    }


    public function getAllItems(Request $request)
    {
        $items = Item::query()
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
            )

            ->when($request->category, fn($q) =>
                $q->whereHas('category', fn($c) =>
                    $c->where('name', $request->category)
                )
            )

            ->when($request->brand, fn($q) =>
                $q->where('brand', $request->brand)
            )

            ->when($request->stock === 'empty', fn($q) =>
                $q->where('stock', 0)
            )

            ->when($request->stock === 'low', fn($q) =>
                $q->where('stock', '<', 10)
            )

            ->with('category')
            ->latest()
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category->name ?? '-',
                'brand' => $item->brand,
                'stock' => $item->stock,
                'price' => number_format($item->price, 0, ',', '.'),
                'reseller_price' => number_format($item->reseller_price, 0, ',', '.'),
                'image' => $item->image_url 
                    ? asset('storage/'.$item->image_url)
                    : asset('image.png')
            ]);

        return response()->json($items);
    }

    public function getBrands(Request $request)
    {
        return response()->json(
            Item::query()
                ->select('brand')
                ->whereNotNull('brand')
                ->when($request->search, fn($q) =>
                    $q->where('brand', 'like', "%{$request->search}%")
                )
                ->distinct()
                ->orderBy('brand')
                ->pluck('brand')
        );
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'brand' => 'required',
                'category_id' => 'required|exists:item_categories,id',
                'stock' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'reseller_price' => 'nullable|numeric',
                'description' => 'nullable',
                'image' => 'nullable|image|max:2048'
            ]);

            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('items', 'public');
            }

            $user = auth()->user();

            $store = Store::where('owner_id', $user->id)->first();

            if (!$store) {
                return response()->json([
                    'message' => 'User tidak punya store 💀'
                ], 400);
            }

            Item::create([
                ...$validated,
                'image_url' => $imagePath,
                'store_id' => $store->id
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Item $item)
    {
        //
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        //
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        $item->suppliers()->detach();

        $item->delete();

        return redirect()
            ->route('item.index')
            ->with('success', 'Item berhasil dihapus!');
    }
}
