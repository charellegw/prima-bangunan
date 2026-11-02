<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'store_id',
        'status',
        'deadline',
        'amount_total',
        'amount_paid',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'amount_total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function invoiceSupplier()
    {
        return $this->hasOne(InvoiceSupplier::class);
    }

    public function paymentHistories()
    {
        return $this->hasMany(PaymentHistory::class)->orderBy('date', 'desc');
    }

    /**
     * Hitung sisa tagihan yang belum dibayar
     */
    public function getRemainingBalanceAttribute(): float
    {
        $paid = (float) $this->paymentHistories()->sum('amount');
        return max(0, (float) $this->amount_total - $paid);
    }

    /**
     * Hitung total yang sudah dibayar
     */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->paymentHistories()->sum('amount');
    }

    /**
     * Update status payment berdasarkan total riwayat pembayaran dan deadline
     */
    public function recalculateStatus(): void
    {
        $totalPaid = (float) $this->paymentHistories()->sum('amount');
        $total = (float) $this->amount_total;
        $isOverdue = $this->deadline && Carbon::now()->greaterThan($this->deadline);

        $status = 'belum_bayar';

        if ($totalPaid >= $total && $total > 0) {
            $status = 'lunas';
        } elseif ($totalPaid > 0 && $totalPaid < $total) {
            $status = $isOverdue ? 'jatuh_tempo' : 'sebagian';
        } else {
            $status = $isOverdue ? 'jatuh_tempo' : 'belum_bayar';
        }

        $this->update([
            'amount_paid' => $totalPaid,
            'status' => $status,
        ]);
    }
}
