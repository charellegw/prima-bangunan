<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceSupplierController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Middleware\SetTimezone;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth', SetTimezone::class])->group(function () {
    // DASHBOARD
    Route::get('/', [DashboardController::class, 'showDashboard'])->name('dashboard');

    // BARANG & STOK (ITEMS)
    Route::get('/items', [ItemController::class, 'showItems'])->name('item.index');
    Route::post('/items', [ItemController::class, 'store'])->name('item.store');
    Route::get('/items/{itemId}', [ItemController::class, 'show'])->name('item.show');
    Route::match(['put', 'patch'], '/items/{itemId}', [ItemController::class, 'update'])->name('item.update');
    Route::delete('/items/{itemId}', [ItemController::class, 'destroy'])->name('item.destroy');

    // HARGA SUPPLIER PADA BARANG
    Route::post('/items/{itemId}/supplier-price', [SupplierController::class, 'storeItemSupplierPrice'])->name('item.supplier.store');
    Route::delete('/items/{itemId}/supplier-price/{supplierId}', [SupplierController::class, 'destroyItemSupplierPrice'])->name('item.supplier.destroy');

    // INVOICE PENJUALAN (SALES POS & NOTA)
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');

    // INVOICE SUPPLIER (PEMBELIAN STOK DARI SUPPLIER)
    Route::get('/invoice-suppliers', [InvoiceSupplierController::class, 'index'])->name('invoice-suppliers.index');
    Route::get('/invoice-suppliers/create', [InvoiceSupplierController::class, 'create'])->name('invoice-suppliers.create');
    Route::post('/invoice-suppliers', [InvoiceSupplierController::class, 'store'])->name('invoice-suppliers.store');
    Route::get('/invoice-suppliers/{id}', [InvoiceSupplierController::class, 'show'])->name('invoice-suppliers.show');

    // PEMBAYARAN & PIUTANG / HUTANG (PAYMENTS)
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{id}/history', [PaymentController::class, 'recordHistory'])->name('payments.history.store');

    // PENGIRIMAN (SHIPMENTS)
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/{id}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::patch('/shipments/{id}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.status.update');

    // SUPPLIERS
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::match(['put', 'patch'], '/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // KATEGORI
    Route::post('/categories', [ItemCategoryController::class, 'store'])->name('categories.store');

    // API AJAX ENDPOINTS
    Route::get('/api/items', [ItemController::class, 'getAllItems']);
    Route::get('/api/categories', [ItemCategoryController::class, 'getAllCategories']);

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
