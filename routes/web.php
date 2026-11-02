<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPriceController;
use App\Http\Middleware\SetTimezone;


Route::middleware('guest')->group(function () {
    // LOGIN
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // REGISTER
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
    
Route::middleware(['auth', SetTimezone::class])->group(function () {
    // DASHBOARD
    Route::get('/', [DashboardController::class, 'showDashboard'])->name('dashboard');

    // ITEMS
    Route::get('/items', [ItemController::class, 'showItems'])->name('item.index');
    Route::get('/api/items', [ItemController::class, 'getAllItems']);
    Route::post('/api/items', [ItemController::class, 'store']);
    Route::get('/api/categories', [ItemCategoryController::class, 'getAllCategories']);
    Route::get('/api/brands', [ItemController::class, 'getBrands']);
    
    // ITEM DETAIL
    Route::get('/items/{itemId}', [ItemController::class, 'show'])->name('item.show');
    Route::patch('/items/{itemId}', [ItemController::class, 'edit'])->name('item.edit');
    Route::delete('/items/{itemId}', [ItemController::class, 'destroy'])->name('item.destroy');
    
    // SUPPLIER
    Route::post('/items/{item}/supplier', [SupplierController::class, 'store'])->name('supplier.price.store');
    Route::patch('/items/{item}/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.price.update');
    Route::delete('/items/{item}/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.price.destroy');

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
});