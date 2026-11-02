<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\SupplierPrice;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'dozen_price' => 'nullable|numeric|min:0',
        ]);

        // 🔹 buat supplier baru
        $supplier = Supplier::create([
            'store_id' => $item->store_id,
            'name' => $request->name,
            'location' => $request->location,
        ]);

        // 🔹 masukin harga ke pivot table
        SupplierPrice::create([
            'supplier_id' => $supplier->id,
            'item_id' => $item->id,
            'unit_price' => $request->unit_price,
            'dozen_price' => $request->dozen_price,
        ]);

        return redirect()->back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'dozen_price' => 'nullable|numeric|min:0',
        ]);

        $supplier->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        SupplierPrice::where('supplier_id', $supplier->id)
            ->where('item_id', $item->id)
            ->update([
                'unit_price' => $request->unit_price,
                'dozen_price' => $request->dozen_price,
            ]);

        return back()->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item, Supplier $supplier)
    {
        // 🔹 hapus harga item terkait dulu
        SupplierPrice::where('item_id', $item->id)
            ->where('supplier_id', $supplier->id)
            ->delete();

        // 🔹 hapus supplier (kalau mau global delete)
        $supplier->delete();

        return redirect()->back()->with('success', 'Supplier dihapus (RIP 🪦)');
    }
}
