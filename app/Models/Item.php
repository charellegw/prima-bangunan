<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'store_id',
        'category_id',
        'name',
        'brand',
        'stock',
        'price',
        'retail_price',
        'image_url',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'price' => 'decimal:2',
            'retail_price' => 'decimal:2',
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function supplierPrices()
    {
        return $this->hasMany(SupplierPrice::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_prices')
            ->withPivot('id', 'unit_price', 'lusin_price')
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

    /**
     * Dapatkan harga berdasarkan client_type (orang vs retail)
     */
    public function getPriceForClientType(string $clientType): float
    {
        return $clientType === 'retail' ? (float) $this->retail_price : (float) $this->price;
    }
}
