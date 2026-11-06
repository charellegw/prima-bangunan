<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi & Nilai Stok - {{ $dateFrom->format('d/m/Y') }} s/d {{ $dateTo->format('d/m/Y') }}</title>
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
                <h5 class="fw-bold text-primary mb-1">LAPORAN MUTASI & NILAI STOK</h5>
                <div class="small text-muted">Periode: <strong>{{ $dateFrom->format('d/m/Y') }}</strong> s/d <strong>{{ $dateTo->format('d/m/Y') }}</strong></div>
                <div class="small text-muted">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Summary KPIs -->
        <div class="row g-2 mb-3">
            <div class="col-3">
                <div class="p-2 border rounded bg-light">
                    <small class="text-muted d-block" style="font-size: 11px;">TOTAL ASET STOK</small>
                    <strong class="fs-6 text-dark font-monospace">Rp {{ number_format($stockReportData->sum('valuation'), 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-3">
                <div class="p-2 border rounded bg-light">
                    <small class="text-muted d-block" style="font-size: 11px;">TOTAL FISIK BARANG</small>
                    <strong class="fs-6 text-dark font-monospace">{{ number_format($stockReportData->sum('current_stock'), 0, ',', '.') }} unit</strong>
                </div>
            </div>
            <div class="col-3">
                <div class="p-2 border rounded bg-light">
                    <small class="text-muted d-block" style="font-size: 11px;">BARANG TERJUAL (KELUAR)</small>
                    <strong class="fs-6 text-primary font-monospace">{{ number_format($stockReportData->sum('qty_sold'), 0, ',', '.') }} unit</strong>
                </div>
            </div>
            <div class="col-3">
                <div class="p-2 border rounded bg-light">
                    <small class="text-muted d-block" style="font-size: 11px;">BARANG MASUK (SUPPLIER)</small>
                    <strong class="fs-6 text-success font-monospace">{{ number_format($stockReportData->sum('qty_received'), 0, ',', '.') }} unit</strong>
                </div>
            </div>
        </div>

        <!-- Table Data -->
        <table class="table table-bordered table-print align-middle mb-4">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>Nama Barang / Material</th>
                    <th>Kategori</th>
                    <th class="text-center">Masuk (Supplier)</th>
                    <th class="text-center">Keluar (Terjual)</th>
                    <th class="text-center">Sisa Stok</th>
                    <th class="text-end">Harga Pokok (Rp)</th>
                    <th class="text-end">Valuasi Stok (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockReportData as $idx => $row)
                    @php $it = $row['item']; @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $it->name }}</div>
                            <small class="text-muted">{{ $it->brand ?? '-' }}</small>
                        </td>
                        <td>{{ $it->category->name ?? 'Material' }}</td>
                        <td class="text-center font-monospace text-success">+{{ $row['qty_received'] }}</td>
                        <td class="text-center font-monospace text-primary">-{{ $row['qty_sold'] }}</td>
                        <td class="text-center font-monospace fw-bold {{ $row['current_stock'] <= 5 ? 'text-danger' : 'text-dark' }}">
                            {{ $row['current_stock'] }}
                        </td>
                        <td class="text-end font-monospace">{{ number_format($row['cost_price'], 0, ',', '.') }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($row['valuation'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data item material.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold bg-light font-monospace">
                    <td colspan="3" class="text-end">TOTAL KESELURUHAN:</td>
                    <td class="text-center text-success">+{{ number_format($stockReportData->sum('qty_received'), 0, ',', '.') }}</td>
                    <td class="text-center text-primary">-{{ number_format($stockReportData->sum('qty_sold'), 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($stockReportData->sum('current_stock'), 0, ',', '.') }}</td>
                    <td></td>
                    <td class="text-end">Rp {{ number_format($stockReportData->sum('valuation'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="row text-center mt-5 pt-3">
            <div class="col-6">
                <div class="small text-muted mb-5">Kepala Gudang:</div>
                <div class="fw-bold border-top border-dark d-inline-block px-4 pt-1">Gudang & Inventaris</div>
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
