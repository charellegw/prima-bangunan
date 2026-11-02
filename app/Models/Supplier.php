<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'location',
        'phone',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function supplierPrices()
    {
        return $this->hasMany(SupplierPrice::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'supplier_prices')
            ->withPivot('unit_price', 'lusin_price')
            ->withTimestamps();
    }

    public function invoiceSuppliers()
    {
        return $this->hasMany(InvoiceSupplier::class);
    }
}
