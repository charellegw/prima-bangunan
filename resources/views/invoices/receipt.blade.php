<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja — {{ $invoice->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Plus+Jakarta+Sans:wght@500;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #f1f5f9;
            font-family: 'Courier Prime', monospace;
            color: #000;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .action-bar {
            width: 100%;
            max-width: 380px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            transition: 0.2s;
        }
        .btn:hover {
            background: #e2e8f0;
        }
        .btn-primary {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .receipt-container {
            width: 100%;
            max-width: 380px;
            background: #ffffff;
            padding: 24px 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            border: 1px dashed #94a3b8;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 12px;
        }
        .store-name {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .store-sub {
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .divider-double {
            border-bottom: 2px dashed #000;
            margin: 10px 0;
        }
        .divider-single {
            border-bottom: 1px dashed #666;
            margin: 8px 0;
        }
        .meta-table {
            width: 100%;
            font-size: 12px;
            margin-bottom: 6px;
        }
        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .item-row {
            margin-bottom: 8px;
            font-size: 12px;
        }
        .item-name {
            font-weight: 700;
            margin-bottom: 2px;
            word-break: break-word;
        }
        .item-calc {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        .totals-table {
            width: 100%;
            font-size: 13px;
            margin-top: 6px;
        }
        .totals-table td {
            padding: 3px 0;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: 700;
        }
        .receipt-status-box {
            text-align: center;
            padding: 8px 0;
            margin: 10px 0;
            border: 1px dashed #000;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .receipt-footer {
            text-align: center;
            font-size: 11px;
            line-height: 1.4;
            margin-top: 14px;
            color: #444;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .action-bar {
                display: none;
            }
            .receipt-container {
                box-shadow: none;
                border: none;
                max-width: 100%;
                width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Aksi (Kembali / Cetak) - Tersembunyi saat dicetak -->
    <div class="action-bar">
        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn">
            &larr; Nota Dokumen
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Struk (Ctrl+P)
        </button>
    </div>

    <!-- Kontainer Struk Thermal -->
    <div class="receipt-container">
        <!-- Header Struk -->
        <div class="receipt-header">
            <div class="store-name">{{ $invoice->store->name ?? 'PRIMA BANGUNAN' }}</div>
            <div class="store-sub">{{ $invoice->store->address ?? 'Jl. Raya Industri Bangunan No. 88, Jakarta Barat' }}</div>
            <div class="store-sub">Telp / WA: {{ $invoice->store->phone ?? '021-5558899' }}</div>
        </div>

        <div class="divider-double"></div>

        <!-- Meta Data Transaksi -->
        <table class="meta-table">
            <tr>
                <td style="width: 32%;">No. Nota</td>
                <td>: <strong>{{ $invoice->invoice_number }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $invoice->date->format('d/m/Y H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ auth()->user()->name ?? 'Kasir Prima' }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>: {{ $invoice->client_name }} ({{ strtoupper($invoice->client_type) }})</td>
            </tr>
            @if($invoice->note)
            <tr>
                <td>Catatan</td>
                <td>: {{ $invoice->note }}</td>
            </tr>
            @endif
        </table>

        <div class="divider-single"></div>

        <!-- Daftar Barang Belanjaan -->
        <div style="margin: 8px 0;">
            @foreach($invoice->invoiceDetails as $detail)
            <div class="item-row">
                <div class="item-name">{{ $detail->item->name ?? 'Barang Material' }}</div>
                <div class="item-calc">
                    <span>{{ $detail->quantity }} x Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
                    <span class="bold">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="divider-single"></div>

        <!-- Rincian Total & Pembayaran -->
        <table class="totals-table">
            <tr>
                <td class="bold">TOTAL BELANJA</td>
                <td class="text-right bold" style="font-size: 15px;">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
            </tr>

            <!-- Rincian Multi-Pembayaran (Split Payment / DP) -->
            @php 
                $histories = $invoice->payment->paymentHistories ?? collect();
            @endphp
            @if($histories->count() > 0)
                @foreach($histories as $ph)
                <tr style="font-size: 12px; color: #333;">
                    <td style="padding-left: 8px;">&bull; {{ $ph->method }}:</td>
                    <td class="text-right">Rp {{ number_format($ph->amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endif

            <tr>
                <td class="bold" style="padding-top: 4px;">TOTAL TERBAYAR</td>
                <td class="text-right bold" style="padding-top: 4px;">Rp {{ number_format($invoice->payment->total_paid ?? 0, 0, ',', '.') }}</td>
            </tr>

            @php $remaining = $invoice->payment->remaining_balance ?? 0; @endphp
            <tr>
                <td class="bold">SISA TAGIHAN (BON)</td>
                <td class="text-right bold" style="color: {{ $remaining > 0 ? '#b91c1c' : '#15803d' }};">
                    Rp {{ number_format($remaining, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <!-- Status Pembayaran Struk -->
        @php $status = $invoice->payment->status ?? 'belum_bayar'; @endphp
        <div class="receipt-status-box">
            @if($status === 'lunas')
                *** L U N A S ***
            @elseif($status === 'sebagian')
                *** DIBAYAR SEBAGIAN (DP) ***
                @if($invoice->payment->deadline)
                    <div style="font-size: 11px; font-weight: normal; margin-top: 3px;">
                        Jatuh Tempo: {{ $invoice->payment->deadline->format('d/m/Y') }}
                    </div>
                @endif
            @elseif($status === 'jatuh_tempo')
                *** JATUH TEMPO (OVERDUE) ***
                @if($invoice->payment->deadline)
                    <div style="font-size: 11px; font-weight: normal; margin-top: 3px;">
                        Tenggat: {{ $invoice->payment->deadline->format('d/m/Y') }}
                    </div>
                @endif
            @else
                *** BELUM DIBAYAR (BON) ***
                @if($invoice->payment->deadline)
                    <div style="font-size: 11px; font-weight: normal; margin-top: 3px;">
                        Jatuh Tempo: {{ $invoice->payment->deadline->format('d/m/Y') }}
                    </div>
                @endif
            @endif
        </div>

        <div class="divider-double"></div>

        <!-- Footer Struk -->
        <div class="receipt-footer">
            <p>Terima kasih atas kepercayaan Anda!</p>
            <p style="margin-top: 3px; font-size: 10px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan kecuali ada perjanjian sebelumnya.</p>
            <p style="margin-top: 6px; font-size: 10px;">Simpan struk ini sebagai bukti pembayaran yang sah.</p>
        </div>
    </div>

</body>
</html>
