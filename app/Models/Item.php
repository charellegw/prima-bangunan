<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'category_id',
        'store_id',
        'name',
        'brand',
        'stock',
        'description',
        'price',
        'reseller_price',
        'image_url',
    ];

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function supplierPrices()
    {
        return $this->hasMany(SupplierPrice::class);
    }

    public function suppliers() 
    {
        return $this->belongsToMany(Supplier::class, 'supplier_prices')
            ->withPivot('unit_price', 'dozen_price')
            ->withTimestamps();
    }

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function invoiceSupplierDetails()
    {
        return $this->hasMany(InvoiceSupplierDetail::class);
    }

    public function shipmentDetails()
    {
        return $this->hasMany(ShipmentDetail::class);
    }
}
