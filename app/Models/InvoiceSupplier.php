<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSupplier extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'invoice_number',
        'store_id',
        'supplier_id',
        'payment_id',
        'date',
        'note',
        'grand_total',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'grand_total' => 'decimal:2',
        ];
    }

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
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function invoiceSupplierDetails()
    {
        return $this->hasMany(InvoiceSupplierDetail::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
