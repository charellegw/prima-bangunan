<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use App\Models\Store;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    public function getAllCategories(Request $request)
    {
        $query = ItemCategory::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . trim($request->search) . '%');
        }

        return response()->json($query->orderBy('name', 'asc')->get(['id', 'name']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $store = Store::first();

        $category = ItemCategory::firstOrCreate([
            'store_id' => $store->id,
            'name' => trim($request->name),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kategori berhasil ditambahkan', 'category' => $category], 201);
        }

        return redirect()->back()->with('success', 'Kategori "' . $category->name . '" berhasil ditambahkan.');
    }
}
