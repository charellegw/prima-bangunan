<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSupplierDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_supplier_id',
        'item_id',
        'quantity',
        'unit_price',
        'sub_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'sub_total' => 'decimal:2',
        ];
    }

    public function invoiceSupplier()
    {
        return $this->belongsTo(InvoiceSupplier::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
