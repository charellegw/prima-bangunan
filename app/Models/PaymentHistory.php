<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentHistory extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentHistoryFactory> */
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'payment_id',
        'datetime',
        'amount',
        'method',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
