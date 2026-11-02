<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Store;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $store = Store::first();

        // Sinkronisasi status jatuh tempo
        $this->paymentService->syncOverduePayments();

        $query = Payment::with(['invoice', 'invoiceSupplier.supplier', 'paymentHistories']);

        // Filter tipe transaksi (piutang penjualan vs hutang supplier)
        if ($request->filled('type')) {
            if ($request->type === 'sales') {
                $query->has('invoice');
            } elseif ($request->type === 'supplier') {
                $query->has('invoiceSupplier');
            }
        }

        // Filter status payment
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search tidak ketat (nomor invoice, nama pelanggan, nama supplier)
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->whereHas('invoice', function ($iq) use ($search) {
                    $iq->where('invoice_number', 'LIKE', "%{$search}%")
                       ->orWhere('client_name', 'LIKE', "%{$search}%");
                })->orWhereHas('invoiceSupplier', function ($isq) use ($search) {
                    $isq->where('invoice_number', 'LIKE', "%{$search}%")
                        ->orWhereHas('supplier', function ($sq) use ($search) {
                            $sq->where('name', 'LIKE', "%{$search}%");
                        });
                });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Statistik ringkas pembayaran
        $totalBelumBayar = Payment::whereIn('status', ['belum_bayar', 'jatuh_tempo'])->count();
        $totalSebagian = Payment::where('status', 'sebagian')->count();
        $totalLunas = Payment::where('status', 'lunas')->count();
        $totalJatuhTempo = Payment::where('status', 'jatuh_tempo')->count();

        return view('payments.index', compact(
            'payments',
            'store',
            'totalBelumBayar',
            'totalSebagian',
            'totalLunas',
            'totalJatuhTempo'
        ));
    }

    public function show($id)
    {
        $payment = Payment::with([
            'invoice.invoiceDetails.item',
            'invoiceSupplier.supplier',
            'invoiceSupplier.invoiceSupplierDetails.item',
            'paymentHistories'
        ])->findOrFail($id);

        return view('payments.show', compact('payment'));
    }

    /**
     * Catat pembayaran baru untuk tagihan
     */
    public function recordHistory(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string|max:50',
            'date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            $this->paymentService->recordPayment($payment, $validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil dicatat.',
                    'payment' => $payment->fresh(['paymentHistories']),
                ]);
            }

            return redirect()->back()->with('success', 'Pembayaran sebesar Rp ' . number_format($validated['amount'], 0, ',', '.') . ' berhasil dicatat.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
