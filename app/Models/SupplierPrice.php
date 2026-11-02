<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'supplier_id',
        'unit_price',
        'lusin_price',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'lusin_price' => 'decimal:2',
        ];
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
