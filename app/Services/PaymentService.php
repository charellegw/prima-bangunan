<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Catat riwayat pembayaran baru untuk suatu tagihan
     *
     * @param Payment $payment
     * @param array $data
     * @return PaymentHistory
     * @throws Exception
     */
    public function recordPayment(Payment $payment, array $data): PaymentHistory
    {
        return DB::transaction(function () use ($payment, $data) {
            $amount = (float) ($data['amount'] ?? 0);

            if ($amount <= 0) {
                throw new Exception("Nominal pembayaran harus lebih besar dari 0.");
            }

            // Hitung sisa tagihan saat ini
            $sisa = $payment->remaining_balance;

            // Validasi aturan: Payment history tidak boleh melebihi sisa
            if ($amount > $sisa) {
                $formatAmount = number_format($amount, 0, ',', '.');
                $formatSisa = number_format($sisa, 0, ',', '.');
                throw new Exception("Nominal pembayaran (Rp {$formatAmount}) melebihi sisa tagihan (Rp {$formatSisa}).");
            }

            // Catat history
            $history = PaymentHistory::create([
                'payment_id' => $payment->id,
                'date' => !empty($data['date']) ? Carbon::parse($data['date']) : Carbon::now(),
                'amount' => $amount,
                'method' => $data['method'] ?? 'cash',
                'note' => $data['note'] ?? null,
            ]);

            // Perbarui status payment secara otomatis
            $payment->recalculateStatus();

            return $history;
        });
    }

    /**
     * Periksa dan perbarui status jatuh tempo pada semua tagihan yang belum lunas
     */
    public function syncOverduePayments(): int
    {
        $overduePayments = Payment::whereIn('status', ['belum_bayar', 'sebagian'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', Carbon::now())
            ->get();

        $count = 0;
        foreach ($overduePayments as $payment) {
            $payment->recalculateStatus();
            $count++;
        }

        return $count;
    }
}
