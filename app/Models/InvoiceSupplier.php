<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InvoiceSupplier extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceSupplierFactory> */
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'store_id',
        'supplier_id',
        'payment_id',
        'datetime',
        'status',
        'notes',
        'grand_total',
        'visible',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceSupplierDetail::class, 'invoice_supplier_id');
    }
}
