<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian Supplier - {{ $dateFrom->format('d/m/Y') }} s/d {{ $dateTo->format('d/m/Y') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .print-container {
            max-width: 900px;
            margin: 20px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .store-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .table-print th {
            background-color: #f1f5f9 !important;
            font-size: 12px;
            text-transform: uppercase;
        }
        .table-print td {
            font-size: 12px;
        }
        @media print {
            body { background: #fff !important; }
            .print-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Floating Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print pb-2 border-bottom">
            <button onclick="window.history.back()" class="btn btn-outline-secondary btn-sm">
                &larr; Kembali
            </button>
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">
                Cetak / Simpan PDF
            </button>
        </div>

        <!-- Letterhead Kop Surat -->
        <div class="store-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1 text-dark">{{ $store->name ?? 'PRIMA BANGUNAN' }}</h3>
                <div class="text-secondary small">{{ $store->address ?? 'Jl. Toko Bangunan No. 1, Indonesia' }}</div>
                <div class="text-secondary small">Telp: {{ $store->phone ?? '(021) 555-1234' }}</div>
            </div>
            <div class="text-end">
                <h5 class="fw-bold text-primary mb-1">LAPORAN PEMBELIAN SUPPLIER</h5>
                <div class="small text-muted">Periode: <strong>{{ $dateFrom->format('d/m/Y') }}</strong> s/d <strong>{{ $dateTo->format('d/m/Y') }}</strong></div>
                <div class="small text-muted">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Summary KPIs -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="p-2 border rounded bg-light">
                    <small class="text-muted d-block" style="font-size: 11px;">TOTAL BELANJA STOK</small>
                    <strong class="fs-6 text-dark font-monospace">Rp {{ number_format($invoices->sum('grand_total'), 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 border rounded bg-light">
                    <small class="text-muted d-block" style="font-size: 11px;">TOTAL TERBAYAR</small>
                    <strong class="fs-6 text-success font-monospace">Rp {{ number_format($invoices->sum(fn($i) => $i->payment->total_paid ?? 0), 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 border rounded bg-light">
                    <small class="text-muted d-block" style="font-size: 11px;">SISA HUTANG SUPPLIER</small>
                    <strong class="fs-6 text-danger font-monospace">Rp {{ number_format($invoices->sum(fn($i) => $i->payment->remaining_balance ?? 0), 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Table Data -->
        <table class="table table-bordered table-print align-middle mb-4">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Status</th>
                    <th class="text-end">Total (Rp)</th>
                    <th class="text-end">Terbayar (Rp)</th>
                    <th class="text-end">Hutang (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $idx => $inv)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-monospace fw-bold">{{ $inv->invoice_number }}</td>
                        <td>{{ $inv->date ? $inv->date->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $inv->supplier->name ?? 'Supplier' }}</td>
                        <td>
                            @if(($inv->payment->status ?? '') === 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @elseif(($inv->payment->status ?? '') === 'sebagian')
                                <span class="badge bg-warning text-dark">Sebagian</span>
                            @elseif(($inv->payment->status ?? '') === 'jatuh_tempo')
                                <span class="badge bg-danger">Jatuh Tempo</span>
                            @else
                                <span class="badge bg-secondary">Belum Bayar</span>
                            @endif
                        </td>
                        <td class="text-end font-monospace">{{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                        <td class="text-end font-monospace text-success">{{ number_format($inv->payment->total_paid ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end font-monospace text-danger">{{ number_format($inv->payment->remaining_balance ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data pembelian supplier pada rentang waktu ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold bg-light font-monospace">
                    <td colspan="5" class="text-end">TOTAL KESELURUHAN:</td>
                    <td class="text-end">Rp {{ number_format($invoices->sum('grand_total'), 0, ',', '.') }}</td>
                    <td class="text-end text-success">Rp {{ number_format($invoices->sum(fn($i) => $i->payment->total_paid ?? 0), 0, ',', '.') }}</td>
                    <td class="text-end text-danger">Rp {{ number_format($invoices->sum(fn($i) => $i->payment->remaining_balance ?? 0), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="row text-center mt-5 pt-3">
            <div class="col-6">
                <div class="small text-muted mb-5">Petugas Gudang / Pembelian:</div>
                <div class="fw-bold border-top border-dark d-inline-block px-4 pt-1">Bagian Logistik</div>
            </div>
            <div class="col-6">
                <div class="small text-muted mb-5">Mengetahui & Menyetujui:</div>
                <div class="fw-bold border-top border-dark d-inline-block px-4 pt-1">Pemilik / Manajer Toko</div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === '1') {
                window.print();
            }
        });
    </script>
</body>
</html>
