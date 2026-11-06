<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Piutang & Hutang - Prima Bangunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .print-container {
            max-width: 950px;
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
            font-size: 11px;
            text-transform: uppercase;
        }
        .table-print td {
            font-size: 11px;
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
                <h5 class="fw-bold text-primary mb-1">BUKU REKAP PIUTANG & HUTANG</h5>
                <div class="small text-muted">Status Tagihan & Jatuh Tempo</div>
                <div class="small text-muted">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Summary KPIs -->
        <div class="row g-2 mb-4">
            <div class="col-6">
                <div class="p-2 border rounded bg-light border-warning border-2">
                    <small class="text-muted d-block fw-bold" style="font-size: 11px;">TOTAL PIUTANG PELANGGAN</small>
                    <strong class="fs-5 text-dark font-monospace">Rp {{ number_format($receivables->sum('remaining_balance'), 0, ',', '.') }}</strong>
                    <div class="small text-muted">{{ $receivables->count() }} nota belum lunas</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 border rounded bg-light border-danger border-2">
                    <small class="text-muted d-block fw-bold" style="font-size: 11px;">TOTAL HUTANG KE SUPPLIER</small>
                    <strong class="fs-5 text-danger font-monospace">Rp {{ number_format($payables->sum('remaining_balance'), 0, ',', '.') }}</strong>
                    <div class="small text-muted">{{ $payables->count() }} faktur belum lunas</div>
                </div>
            </div>
        </div>

        <!-- SECTION 1: PIUTANG KONSUMEN -->
        <h6 class="fw-bold text-dark border-bottom pb-1 mb-2">1. DAFTAR PIUTANG PELANGGAN</h6>
        <table class="table table-bordered table-print align-middle mb-4">
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">No</th>
                    <th>No. Nota</th>
                    <th>Tanggal</th>
                    <th>Nama Pelanggan</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-end">Total Nota</th>
                    <th class="text-end">Sudah Bayar</th>
                    <th class="text-end">Sisa Piutang</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receivables as $idx => $pay)
                    @php
                        $inv = $pay->invoice;
                        $isOverdue = $pay->status === 'jatuh_tempo' || ($pay->deadline && $pay->deadline->isPast() && $pay->status !== 'lunas');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-monospace fw-bold">{{ $inv->invoice_number }}</td>
                        <td>{{ $inv->date ? $inv->date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $inv->client_name }} ({{ strtoupper($inv->client_type) }})</td>
                        <td class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                            {{ $pay->deadline ? $pay->deadline->format('d/m/Y') : '-' }}
                            @if($isOverdue) <span class="badge bg-danger" style="font-size: 9px;">OVERDUE</span> @endif
                        </td>
                        <td class="text-end font-monospace">{{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                        <td class="text-end font-monospace text-success">{{ number_format($pay->total_paid, 0, ',', '.') }}</td>
                        <td class="text-end font-monospace text-danger fw-bold">{{ number_format($pay->remaining_balance, 0, ',', '.') }}</td>
                        <td class="text-center">{{ strtoupper(str_replace('_', ' ', $pay->status)) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-2 text-muted">Tidak ada data piutang pelanggan belum lunas.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold bg-light font-monospace">
                    <td colspan="5" class="text-end">TOTAL PIUTANG:</td>
                    <td class="text-end">Rp {{ number_format($receivables->sum(fn($r) => $r->invoice->grand_total ?? 0), 0, ',', '.') }}</td>
                    <td class="text-end text-success">Rp {{ number_format($receivables->sum('total_paid'), 0, ',', '.') }}</td>
                    <td class="text-end text-danger">Rp {{ number_format($receivables->sum('remaining_balance'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- SECTION 2: HUTANG SUPPLIER -->
        <h6 class="fw-bold text-dark border-bottom pb-1 mb-2">2. DAFTAR HUTANG KE SUPPLIER</h6>
        <table class="table table-bordered table-print align-middle mb-4">
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">No</th>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Nama Supplier</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-end">Total Faktur</th>
                    <th class="text-end">Sudah Bayar</th>
                    <th class="text-end">Sisa Hutang</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payables as $idx => $pay)
                    @php
                        $inv = $pay->invoiceSupplier;
                        $sup = $inv->supplier;
                        $isOverdue = $pay->status === 'jatuh_tempo' || ($pay->deadline && $pay->deadline->isPast() && $pay->status !== 'lunas');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-monospace fw-bold">{{ $inv->invoice_number }}</td>
                        <td>{{ $inv->date ? $inv->date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $sup->name ?? 'Supplier' }}</td>
                        <td class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                            {{ $pay->deadline ? $pay->deadline->format('d/m/Y') : '-' }}
                            @if($isOverdue) <span class="badge bg-danger" style="font-size: 9px;">OVERDUE</span> @endif
                        </td>
                        <td class="text-end font-monospace">{{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                        <td class="text-end font-monospace text-success">{{ number_format($pay->total_paid, 0, ',', '.') }}</td>
                        <td class="text-end font-monospace text-danger fw-bold">{{ number_format($pay->remaining_balance, 0, ',', '.') }}</td>
                        <td class="text-center">{{ strtoupper(str_replace('_', ' ', $pay->status)) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-2 text-muted">Tidak ada data hutang ke supplier belum lunas.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold bg-light font-monospace">
                    <td colspan="5" class="text-end">TOTAL HUTANG:</td>
                    <td class="text-end">Rp {{ number_format($payables->sum(fn($p) => $p->invoiceSupplier->grand_total ?? 0), 0, ',', '.') }}</td>
                    <td class="text-end text-success">Rp {{ number_format($payables->sum('total_paid'), 0, ',', '.') }}</td>
                    <td class="text-end text-danger">Rp {{ number_format($payables->sum('remaining_balance'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="row text-center mt-5 pt-3">
            <div class="col-6">
                <div class="small text-muted mb-5">Bagian Keuangan:</div>
                <div class="fw-bold border-top border-dark d-inline-block px-4 pt-1">Finance & Kasir</div>
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
