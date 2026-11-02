<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPrice extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierPricemFactory> */
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'item_id',
        'unit_price',
        'dozen_price',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
