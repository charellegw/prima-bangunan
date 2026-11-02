<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'shipment_number',
        'invoice_id',
        'invoice_supplier_id',
        'type',
        'date',
        'address',
        'fee',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'fee' => 'decimal:2',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoiceSupplier()
    {
        return $this->belongsTo(InvoiceSupplier::class);
    }

    public function shipmentDetails()
    {
        return $this->hasMany(ShipmentDetail::class);
    }
}
