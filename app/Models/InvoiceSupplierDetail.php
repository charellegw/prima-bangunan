<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InvoiceSupplierDetail extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceSupplierDetailFactory> */
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'invoice_supplier_id',
        'item_id',
        'quantity',
        'unit_price',
        'sub_total',
    ];

    public function invoiceSupplier()
    {
        return $this->belongsTo(InvoiceSupplier::class, 'invoice_supplier_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
