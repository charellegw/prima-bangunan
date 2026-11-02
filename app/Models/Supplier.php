<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'location',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function supplierPrices()
    {
        return $this->hasMany(SupplierPrice::class);
    }
}
